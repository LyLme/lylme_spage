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

mysqli_query($con,"set sql_mode = ''");
 //字符转换，读库
mysqli_query($con,"set character set 'utf8'");
//写库
mysqli_query($con,"set names 'utf8'");

$rs= mysqli_query($con,"SELECT * FROM `lylme_config`");
while($row = mysqli_fetch_assoc($rs)) { 
	$conf[$row['k']]=$row['v'];
}

if(strpos($_SERVER['REQUEST_URI'],'admin')==false){
    if(!file_exists(SYSTEM_ROOT."log.txt")){
        $one_file=fopen(SYSTEM_ROOT."log.txt","w+"); //建立一个统计文本，如果不存在就创建
        fwrite(SYSTEM_ROOT."log.txt",1);  //把数字1写入文本
        fclose("$one_file");
     }else{ //如果不是第一次访问直接读取内容，并+1,写入更新后再显示新的访客数
        $num=file_get_contents(SYSTEM_ROOT."log.txt");
        $num++;
        file_put_contents(SYSTEM_ROOT."log.txt","$num");
        //$pvnum=file_get_contents(SYSTEM_ROOT."log.txt");
    }
}
$pvnum=file_get_contents(SYSTEM_ROOT."log.txt");
include_once(SYSTEM_ROOT."function.php");
include_once(SYSTEM_ROOT."member.php");
mysqli_query($con,'set names utf8');
$links = mysqli_query($con, "SELECT * FROM `lylme_links`");
$numrows=mysqli_num_rows($links);

?>

