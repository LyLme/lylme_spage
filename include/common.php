<?php
include_once 'config.php';
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
mysqli_query($con, "set names utf8");
}
?>

