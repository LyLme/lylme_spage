<?php
include("../include/common.php");
if($_GET['wx']=="plus"){
    $wx_key = $conf["wxplus"];
	$plus_time = $conf["plus_time"]?$conf["plus_time"]:"22:00";
    $web_title = explode("-", $conf['title'])[0];
    //$web_title = "六零导航页";
    if(empty($wx_key)){exit("微信推送未开启");}
    $wx_name = $_POST['wx_name'];
    $url = parse_url($_POST['wx_url'])['host'];
    $wx_url = $url?$url:$_POST['wx_url'];
    $data = '
    {"wx_name":"'.$wx_name.'","wx_url":"'.$wx_url.'","web_name":"'.$web_title.'","plus_time":"'.$plus_time.'","wx_key":"'.$wx_key.'"}';
    exit(wxPlus($data));
}
?>