<?php
header ("Content-type:text/html;charset=utf-8");

define('SYS_KEY', 'lylme_key');
define('SYSTEM_ROOT', dirname(__FILE__).'/');
define('ROOT', dirname(SYSTEM_ROOT).'/');

include ROOT.'config.php';
if(!$con =mysqli_connect($dbconfig['host'],$dbconfig['user'],$dbconfig['pwd'],$dbconfig['dbname'],$dbconfig['port'])) {
	if(mysqli_connect_errno()==2002) {
		echo '连接数据库失败，数据库地址填写错误！';
	} elseif(mysqli_connect_errno()==1045) {
		echo '连接数据库失败，数据库用户名或密码填写错误！';
	} elseif(mysqli_connect_errno()==1049) {
		echo '连接数据库失败，数据库名不存在！';
	} else {
		echo '连接数据库失败，['.mysqli_connect_errno().']'.mysqli_connect_error();
	}
}
$GLOBALS['con'] = $con;
mysqli_query($con,"set sql_mode = ''");
 //字符转换，读库
mysqli_query($con,"set character set 'utf8'");
//写库
mysqli_query($con,"set names 'utf8'");

$rs= mysqli_query($con,"SELECT * FROM `lylme_config`");
while($row = mysqli_fetch_assoc($rs)) { 
	$conf[$row['k']]=$row['v'];
}


include_once(SYSTEM_ROOT."function.php");
include_once(SYSTEM_ROOT."member.php");
include_once(SYSTEM_ROOT."tj.php");
mysqli_query($con,'set names utf8');
$linksrows=mysqli_num_rows(mysqli_query($con, "SELECT * FROM `lylme_links`"));
$groupsrows=mysqli_num_rows(mysqli_query($con, "SELECT * FROM `lylme_groups`"));

?>

