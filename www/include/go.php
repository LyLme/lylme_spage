<?php

if(!empty($_GET['url'])) {
    $url = $_GET['url'];
    // 验证URL格式
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        header("Location:" . htmlspecialchars($url, ENT_QUOTES, 'UTF-8'));
    }
    exit();
}
include("common.php");

// 启动session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 处理注销登录请求
if(isset($_POST['exit']) && $_POST['exit'] === 'exit') {
    // CSRF验证
    if (!csrf_verify()) {
        exit('<script>alert("安全验证失败，请重试");window.location.href="' . htmlspecialchars($_SERVER['HTTP_REFERER'], ENT_QUOTES, 'UTF-8') . '";</script>');
    }
    
    //注销登录
    $_SESSION['pass'] = null;
    $_SESSION['list'] = array();
    unset($_SESSION['pass']);
    unset($_SESSION['list']);
    
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../';
    header("Location: " . htmlspecialchars($referer, ENT_QUOTES, 'UTF-8'));
    exit('注销登录成功');
}

$pass = isset($_POST['pass']) ? trim($_POST['pass']) : '';

if(!isset($_SESSION['pass']) || $_SESSION['pass'] != 1) {
    //未登录
    if(!empty($pass)) {
        // CSRF验证
        if (!csrf_verify()) {
            exit('<script>alert("安全验证失败，请重试");window.location.href="' . htmlspecialchars($_SERVER['HTTP_REFERER'], ENT_QUOTES, 'UTF-8') . '";</script>');
        }
        
        //用户提交登录
        $show = array();
        
        // 使用安全的转义方式 - 保持与原代码兼容的LIKE查询
        $pass_escaped = $DB->escape($pass);
        // 移除escape()添加的引号(如果有),因为LIKE查询需要直接字符串
        $pass_clean = trim($pass_escaped, "'");
        
        // 使用LIKE查询以保持原功能兼容性
        $query = "SELECT `pwd_id`, `pwd_key` FROM `lylme_pwd` WHERE `pwd_key` LIKE '" . $pass_clean . "'";
        $pwds = $DB->query($query);
        
        if ($pwds) {
            while ($pwd = $DB->fetch($pwds)) {
                $show[] = (int)$pwd['pwd_id'];
            }
        }
        
        if(empty($show)) {
            //无数据
            exit('<script>alert("密码错误！");window.location.href="' . htmlspecialchars($_SERVER['HTTP_REFERER'], ENT_QUOTES, 'UTF-8') . '";</script>');
        } else {
            //有数据 - 登录成功,重定向到首页
            $_SESSION['list'] = $show;
            $_SESSION['pass'] = 1;
            header("Location: ../");
            exit();
        }
    }
} else {
    //已登录
    if(!empty($pass)) {
        $show = array();
        
        // 使用安全的转义方式 - 保持与原代码兼容的LIKE查询
        $pass_escaped = $DB->escape($pass);
        // 移除escape()添加的引号(如果有),因为LIKE查询需要直接字符串
        $pass_clean = trim($pass_escaped, "'");
        
        // 使用LIKE查询以保持原功能兼容性
        $query = "SELECT `pwd_id`, `pwd_key` FROM `lylme_pwd` WHERE `pwd_key` LIKE '" . $pass_clean . "'";
        $pwds = $DB->query($query);
        
        if ($pwds) {
            while ($pwd = $DB->fetch($pwds)) {
                $show[] = (int)$pwd['pwd_id'];
            }
        }
        
        if(empty($show)) {
            $_SESSION['pass'] = null;
            $_SESSION['list'] = array();
            unset($_SESSION['pass']);
            unset($_SESSION['list']);
        }
    }
}

if(basename($_SERVER['PHP_SELF']) != basename(__FILE__)) {
    return;
}
header("Location: ../");
