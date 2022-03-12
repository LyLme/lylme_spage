<?php
@header('Content-Type: text/html; charset=UTF-8');
define('version', 'v1.1.2');
include "./include/head.php";
include './include/home.php';
if (!file_exists('install/install.lock')) 
exit('<title>六零导航页 - 安装程序</title>您还未安装，点击<a href="install"><font color="blue">这里</font></a>开始安装！');
if (!constant("version") == $conf['version']) 
echo '<script>alert("网站版本已更新，请前往后台=>检查更新=>更新数据库");</script>';
?>
