<?php

@header('Content-Type: text/html; charset=UTF-8');
if (!file_exists('install/install.lock')) {
    header("Location:/install");
    exit();
}
require "./include/common.php";
session_start(); //设置session
$_SESSION['list'] = isset($_SESSION['list']) ? $_SESSION['list'] : array();
require $template;
