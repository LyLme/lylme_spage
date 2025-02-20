<?php

if(!empty($_GET['url'])) {
    $url = $_GET['url'];
    header("Location:$url");
    exit();
}
include("common.php");
session_start(); //设置session
if($_POST['exit'] == 'exit') {
    //注销登录
    $_SESSION['pass'] = null;
    $_SESSION['list'] = array();
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit('注销登录成功');
}
$pass = isset($_POST['pass']) ? daddslashes($_POST['pass']) : '';
if($_SESSION['pass'] != 1) {
    //未登录
    if(!empty($pass)) {
        //用户提交登录
        $show = array();
        $pwds = $DB->query("SELECT `pwd_id`, `pwd_key` FROM `lylme_pwd` WHERE `pwd_key` LIKE '" . $pass . "';");
        while ($pwd = $DB->fetch($pwds)) {
            array_push($show, $pwd['pwd_id']);
        }
        if(empty($show)) {
            //无数据
            exit('<script>alert("密码错误！");window.location.href="' . $_SERVER['HTTP_REFERER'] . '";</script>');
        } else {
            //有数据
            $_SESSION['list'] = $show;
            $_SESSION['pass'] = 1;
        }
    }
} else {
    //已登录
    if(!empty($pass)) {
        $show = array();
        $pwds = $DB->query("SELECT `pwd_id`, `pwd_key` FROM `lylme_pwd` WHERE `pwd_key` LIKE '" . $pass . "';");
        while ($pwd = $DB->fetch($pwds)) {
            array_push($show, $pwd['pwd_id']);
        }
        if(empty($show)) {
            $_SESSION['pass'] = null;
            $_SESSION['list'] = array();
        }
    }
}
if(basename($_SERVER['PHP_SELF']) != basename(__FILE__)) {
    return;
}
header("Location: ../");
