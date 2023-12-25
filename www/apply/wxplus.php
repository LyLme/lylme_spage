<?php
include("../include/common.php");
if($_GET['wx'] == "plus") {
$wx_key = $conf["wxplus"];
$plus_time = $conf["wxplustime"] ? $conf["wxplustime"] : "21:00";
if(empty($wx_key)) {
exit("微信推送未开启");
}
$wx_name = $_POST['wx_name'];
$web_title = explode("-", $conf['title'])[0];
$url = parse_url($_POST['wx_url'])['host'];
$wx_url = $url?$url:$_POST['wx_url'];
$admin_url = siteurl()."/admin";//后台目录(默认/admin)
$data='{"wx_name":"'.$wx_name.'","wx_url":"'.$wx_url.'","web_name":"'.$web_title.'","web_url":"'. siteurl().'","web_admin_url":"'.$admin_url.'","plus_time":"'.$plus_time.'","wx_key":"'.$wx_key.'"}';
exit(wxPlus($data));}
?>