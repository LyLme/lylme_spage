<?php

// 安全检查：确保必要常量已定义
if (!defined('IN_CRONLITE')) {
    define('IN_CRONLITE', true);
}

if (!defined('SYS_KEY')) {
    define('SYS_KEY', 'lylme_key');
}

// 初始化全局变量
$islogin = 0;

// 检查管理员Token
if (isset($_COOKIE["admin_token"])) {
    $token_raw = $_COOKIE["admin_token"];

    // 安全检查：token不能为空
    if (!empty($token_raw)) {
        // 解码token
        $token = authcode(daddslashes($token_raw), 'DECODE', SYS_KEY);

        // 安全处理：检查token格式
        if (!empty($token) && strpos($token, "\t") !== false) {
            $token_parts = explode("\t", $token);
            $user = isset($token_parts[0]) ? $token_parts[0] : '';
            $sid = isset($token_parts[1]) ? $token_parts[1] : '';

            // 获取配置中的用户信息
            $admin_user = $conf['admin_user'] ?? '';
            $admin_pwd = $conf['admin_pwd'] ?? '';

            // 计算session
            $session = md5($admin_user . $admin_pwd);

            // 验证
            if ($session === $sid && !empty($user)) {
                $islogin = 1;
            }
        }
    }
}
