<?php
declare(strict_types=1);

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
        // 如果headers已发送，使用JavaScript跳转
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

// 启动session（放在输出前，避免警告）
if (session_status() === PHP_SESSION_NONE) {
    // PHP 5.4+ 兼容性处理
    session_start([
        'use_strict_mode' => version_compare(PHP_VERSION, '5.5.2', '>=') ? 1 : 0,
        'use_cookies' => 1,
        'use_only_cookies' => 1,
        'cookie_httponly' => true,
        'cookie_samesite' => version_compare(PHP_VERSION, '7.3.0', '>=') ? 'Strict' : null
    ]);
}

// 初始化session变量（多版本兼容写法）
if (!isset($_SESSION['list'])) {
    $_SESSION['list'] = [];
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