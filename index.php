<?php
if (ob_get_level() === 0) {
    ob_start();
}
if (!headers_sent()) {
    header('Content-Type: text/html; charset=UTF-8');
}
// 安装检查
$installLockFile = 'install/install.lock';
if (!file_exists($installLockFile) || !is_file($installLockFile)) {
    if (!headers_sent()) {
        header("Location: /install");
    } else {
        echo '<script>window.location.href = "/install";</script>';
    }
    exit;
}

// 包含公共文件
$commonFile = __DIR__ . '/include/common.php';
if (!file_exists($commonFile) || !is_file($commonFile)) {
    error_log("Common file not found: {$commonFile}");
    http_response_code(500);
    echo 'include/common.php文件丢失';
    exit;
}

require $commonFile;

if (session_status() === PHP_SESSION_NONE) {
    if (version_compare(PHP_VERSION, '5.5.2', '>=')) {
        ini_set('session.use_strict_mode', '1');
    }
    ini_set('session.use_cookies', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');
    if (version_compare(PHP_VERSION, '7.3.0', '>=')) {
        ini_set('session.cookie_samesite', 'Strict');
    }
    session_start_safe();
}

if (!isset($_SESSION['list'])) {
    $_SESSION['list'] = array();
}

// 引入模板文件
if (!isset($template) || empty($template)) {
    error_log('主题模板变量未定义');
    http_response_code(500);
    echo '主题模板配置错误 ';
    exit;
}

require $templateFile;

// 清理输出缓冲区
if (ob_get_level() > 0) {
    ob_end_flush();
}