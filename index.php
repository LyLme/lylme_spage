<?php
@header('Content-Type: text/html; charset=UTF-8');
if (!file_exists('install/install.lock'))
exit('<title>安装程序</title>您还未安装，点击<a href="install"><font color="blue">这里</font></a>开始安装！');
include "./include/common.php";
session_start(); //设置session
$_SESSION['list'] = isset($_SESSION['list'])?$_SESSION['list']:array();
include $template;
?>