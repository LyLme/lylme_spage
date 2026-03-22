<?php
// 系统常量定义
define('ADMIN_PATH', 'admin');  // 后台目录（修改后需同步调整防火墙规则）
define('DEBUG', true);  // 错误报告，默认关闭调试模式

define('IN_CRONLITE', true);
define('SYS_KEY', 'lylme_key');
define('SYSTEM_ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR);
define('ROOT', dirname(SYSTEM_ROOT) . DIRECTORY_SEPARATOR);

// 设置错误报告级别
if (defined('DEBUG') && DEBUG === true) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
} else {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_NOTICE);
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
}




// 包含版本文件（必须在最开始包含，因为其他文件依赖VERSION常量）
$versionFile = SYSTEM_ROOT . 'version.php';
if (file_exists($versionFile) && is_readable($versionFile)) {
    require $versionFile;
} else {
    // 如果version.php不存在，定义一个默认版本常量
    if (!defined('VERSION')) {
        define('VERSION', 'v1.0.0');
    }
    error_log("Version file missing: {$versionFile}");
}

// 包含配置文件
$configFile = ROOT . 'config.php';
if (!file_exists($configFile) || !is_readable($configFile)) {
    $errorMsg = '配置文件不存在或不可读，请检查 config.php 文件';
    error_log('System Error: ' . $errorMsg);
    exit('<h3>' . htmlspecialchars($errorMsg, ENT_QUOTES, 'UTF-8') . '</h3>');
}

require $configFile;

// 数据库配置验证（多版本兼容处理）
if (!defined('SQLITE')) {
    // 检查是否定义了$dbconfig数组
    if (!isset($dbconfig) || !is_array($dbconfig)) {
        $errorMsg = '数据库配置无效：$dbconfig 未定义或不是数组';
        error_log('Database Config Error: ' . $errorMsg);
        exit('<h3>' . htmlspecialchars($errorMsg, ENT_QUOTES, 'UTF-8') . '</h3>');
    }
    
    // 验证必要配置项
    $requiredFields = ['host', 'user', 'pwd', 'dbname'];
    $missingFields = [];
    
    foreach ($requiredFields as $field) {
        if (!isset($dbconfig[$field]) || empty($dbconfig[$field])) {
            $missingFields[] = $field;
        }
    }
    
    if (!empty($missingFields)) {
        // 数据库配置不完整，尝试清理安装锁文件
        $installLock = ROOT . 'install/install.lock';
        if (file_exists($installLock)) {
            // 使用安全的方式删除文件
            if (is_writable($installLock)) {
                unlink($installLock);
            }
        }
        
        // 重定向到安装页面
        if (!headers_sent()) {
            // 使用绝对路径重定向
            $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $redirectUrl = "{$protocol}://{$host}/install/";
            header('Location: ' . $redirectUrl);
        } else {
            // 如果header已发送，使用meta重定向
            echo '<meta http-equiv="refresh" content="0;url=/install/">';
        }
        exit();
    }
}

// 包含数据库类
$dbClassFile = SYSTEM_ROOT . 'db.class.php';
if (!file_exists($dbClassFile) || !is_readable($dbClassFile)) {
    $errorMsg = '数据库类文件不存在或不可读';
    error_log('Database Class Error: ' . $errorMsg);
    exit('<h3>' . htmlspecialchars($errorMsg, ENT_QUOTES, 'UTF-8') . '</h3>');
}

require $dbClassFile;

// 初始化数据库连接（添加错误处理）
try {
    // 根据PHP版本选择适当的连接方式
    $dbPort = isset($dbconfig['port']) ? (int)$dbconfig['port'] : 3306;
    $dbHost = $dbconfig['host'];
    
    // PHP 8.1+ mysqli_report 配置
    if (version_compare(PHP_VERSION, '8.1.0', '>=')) {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    }
    
    // 创建数据库实例
    $DB = new DB(
        $dbconfig['host'],
        $dbconfig['user'],
        $dbconfig['pwd'],
        $dbconfig['dbname'],
        $dbPort
    );
    
    // 测试数据库连接
    if (method_exists($DB, 'connect')) {
        $DB->connect();
    } elseif (method_exists($DB, 'query')) {
        // 备用连接测试
        $DB->query("SELECT 1");
    }
    
} catch (Exception $e) {
    $errorMsg = '数据库连接失败: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    error_log('Database Connection Error: ' . $e->getMessage());
    
    if (defined('DEBUG') && DEBUG === true) {
        exit('<h3>数据库连接错误</h3><p>' . $errorMsg . '</p>');
    } else {
        exit('<h3>数据库连接失败，请检查数据库配置</h3>');
    }
}

$functionFile = SYSTEM_ROOT . 'function.php';
if (file_exists($functionFile) && is_readable($functionFile)) {
    // 定义标志，让function.php知道正在初始化阶段
    define('IN_INITIALIZATION', true);
    require $functionFile;
} elseif (defined('DEBUG') && DEBUG === true) {
    error_log("Function file missing: {$functionFile}");
}

// 同样的，包含其他可能依赖的基础文件
$baseFiles = [
    'lists.php',
    'member.php',
    'site.php',
    'include.php',
    'tj.php',
    'updbase.php'
];

foreach ($baseFiles as $baseFile) {
    $filePath = SYSTEM_ROOT . $baseFile;
    if (file_exists($filePath) && is_readable($filePath)) {
        require $filePath;
    } elseif (defined('DEBUG') && DEBUG === true) {
        error_log("Base file missing: {$baseFile}");
    }
}

// 现在读取数据库配置
try {
    $web_config = $DB->query("SELECT * FROM `lylme_config`");
    
    if ($web_config === false) {
        // 查询失败
        $errorMsg = '系统配置表查询失败';
        $dbError = method_exists($DB, 'error') ? $DB->error() : '未知数据库错误';
        error_log('System Error: ' . $errorMsg . ' - ' . $dbError);
        
        if (defined('DEBUG') && DEBUG === true) {
            exit("<h3>LyLme Spage 系统错误</h3><p>{$errorMsg}</p><p>数据库错误: {$dbError}</p>");
        } else {
            exit("<h3>系统初始化失败，请联系管理员</h3>");
        }
    }
    
    // 检查结果集是否有数据
    $hasRows = false;
    if (method_exists($DB, 'num_rows')) {
        $hasRows = $DB->num_rows($web_config) > 0;
    } else {
        // 兼容性处理：尝试其他方法检查结果
        $hasRows = ($DB->fetch($web_config) !== false);
        if ($hasRows) {
            // 重置指针
            if (method_exists($web_config, 'data_seek')) {
                $web_config->data_seek(0);
            }
        }
    }
    
    if (!$hasRows) {
        $errorMsg = '系统配置表为空，请检查数据库结构';
        error_log('System Error: ' . $errorMsg);
        
        if (defined('DEBUG') && DEBUG === true) {
            exit("<h3>LyLme Spage 系统错误</h3><p>{$errorMsg}</p>");
        } else {
            exit("<h3>系统配置不完整，请联系管理员</h3>");
        }
    }
    
    // 读取网站配置
    global $conf;  // 确保全局作用域
    $conf = [];
    
$conf["mode"] = isset($conf["mode"]) ? $conf["mode"] : 2;
    // 使用原始的方法读取数据
    if (method_exists($DB, 'fetch')) {
        while ($row = $DB->fetch($web_config)) {
            if (isset($row['k']) && isset($row['v'])) {
                $conf[$row['k']] = $row['v'];
            }
        }
    } else {
        // 备用方法：尝试mysqli_fetch_assoc
        if (is_object($web_config) && method_exists($web_config, 'fetch_assoc')) {
            while ($row = $web_config->fetch_assoc()) {
                if (isset($row['k']) && isset($row['v'])) {
                    $conf[$row['k']] = $row['v'];
                }
            }
        }
    }
    
    // 验证必要的配置项
    if (empty($conf)) {
        $errorMsg = '系统配置读取失败，配置数组为空';
        error_log('System Error: ' . $errorMsg);
        exit('<h3>' . htmlspecialchars($errorMsg, ENT_QUOTES, 'UTF-8') . '</h3>');
    }
    
    // 确保$conf是全局可访问的
    if (!isset($GLOBALS['conf'])) {
        $GLOBALS['conf'] = $conf;
    }
    
} catch (Exception $e) {
    $errorMsg = '系统配置读取失败: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    error_log('Config Read Error: ' . $e->getMessage());
    
    if (defined('DEBUG') && DEBUG === true) {
        exit('<h3>系统配置错误</h3><p>' . $errorMsg . '</p>');
    } else {
        exit('<h3>系统初始化失败，请联系管理员</h3>');
    }
}

// 包含表单库文件
$formLibFile = SYSTEM_ROOT . 'lib/Form.php';
if (file_exists($formLibFile) && is_readable($formLibFile)) {
    require $formLibFile;
} elseif (defined('DEBUG') && DEBUG === true) {
    error_log("Form library missing: {$formLibFile}");
}

// 系统初始化完成，清理初始化标志
if (defined('IN_INITIALIZATION')) {
    // 可以在这里执行初始化后的操作
}
