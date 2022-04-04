<?php
/*
作者:D.Young
主页：https://blog.5iux.cn/
github：https://github.com/5iux/sou
日期：2020-05-19
版权所有，请勿删除

本天气插件为申请地址：和风天气-https://dev.heweather.com/
*/
header('Content-Type:application/json; charset=utf-8');
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Headers:x-requested-with,content-type');
$address=$_SERVER["REMOTE_ADDR"];
$key="9d714f8dd6b94c7696f9cea8dc3ed1c5";
$jsonlist = file_get_contents("https://free-api.heweather.net/s6/weather/?location=".$address."&key=".$key);
echo $_GET["callback"].$jsonlist;
?>