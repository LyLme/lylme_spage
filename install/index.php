<?php
error_reporting(0);
session_start();
@header('Content-Type: text/html; charset=UTF-8');
include '../include/version.php';
$do=isset($_GET['do'])?$_GET['do']:'0';
if(file_exists('install.lock')){
	exit('您已经安装过，如需重新安装请删除<font color=red> install/install.lock </font>文件后再安装！');
}

function checkfunc($f,$m = false) {
	if (function_exists($f)) {
		return '<font color="green">可用</font>';
	} else {
		if ($m == false) {
			return '<font color="black">不支持</font>';
		} else {
			return '<font color="red">不支持</font>';
		}
	}
}

function checkclass($f,$m = false) {
	if (class_exists($f)) {
		return '<font color="green">可用</font>';
	} else {
		if ($m == false) {
			return '<font color="black">不支持</font>';
		} else {
			return '<font color="red">不支持</font>';
		}
	}
}
function checkconnect($connect){
	if(function_exists($connect)){
		echo '<font color="green">可用</font>';}
	else {
		echo '<font color="red">不支持</font>';
	}
}
$ver = constant("VERSION");
?>
<html lang="zh-cn">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta name="viewport" content="initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0,user-scalable=no,minimal-ui">
<title>六零导航页 - 安装程序</title>
<link href="//lib.baomitu.com/twitter-bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet"/>
</head>
<body>
<nav class="navbar navbar-fixed-top navbar-default">
    <div class="container">
      <div class="navbar-header">
        <span class="navbar-brand">安装向导</span>
      </div>
    </div>
  </nav>
  <div class="container" style="padding-top:60px;">
    <div class="col-xs-12 col-sm-8 col-lg-6 center-block" style="float: none;">

<?php if($do=='0'){
$_SESSION['checksession']=1;
?>
<div class="panel panel-primary">
	<div class="panel-heading" style="background: #15A638;">
		<h3 class="panel-title" align="center">用户使用协议阅读</h3>
	</div>
	<div class="panel-body">
		<p><iframe src="readme.html" style="width:100%;height:465px;"></iframe></p>
		<p align="center"><a id="agreebtn" class="btn btn-primary" href="index.php?do=1">同意协议并开始安装</a></p>
	</div>
</div>
<?php }elseif($do=='1'){?>
<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title" align="center">环境检查</h3>
	</div>
<div class="progress progress-striped">
  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 10%">
	<span class="sr-only">10%</span>
  </div>
</div>
<table class="table table-striped">
	<thead>
		<tr>
			<th style="width:20%">函数检测</th>
			<th style="width:15%">需求</th>
			<th style="width:15%">当前</th>
			<th style="width:50%">用途</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>PHP 5.4及以上</td>
			<td>必须</td>
			<td><?php echo version_compare(PHP_VERSION, '5.4.0', '>')?'<font color="green">'.PHP_VERSION.'</font>':'<font color="red">'.PHP_VERSION.'</font>'; ?></td>
			<td>PHP版本支持</td>
		</tr>
		<tr>
			<td>PDO</td>
			<td>必须</td>
			<td><?php echo checkclass('PDO',true); ?></td>
			<td>数据库连接</td>
		</tr>
		<tr>
			<td>file_get_contents()</td>
			<td>必须</td>
			<td><?php echo checkfunc('file_get_contents',true); ?></td>
			<td>读取文件</td>
		</tr>
		<tr>
			<td>session</td>
			<td>必须</td>
			<td><?php echo $_SESSION['checksession']==1?'<font color="green">可用</font>':'<font color="red">不支持</font>'; ?></td>
			<td>PHP必备功能</td>
		</tr>

	</tbody>
</table>
<p><span><a class="btn btn-primary" href="index.php?do=0"><<上一步</a></span>
<span style="float:right"><a class="btn btn-primary" href="index.php?do=2" align="right">下一步>></a></span></p>
</div>

<?php }elseif($do=='2'){?>
<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title" align="center">数据库配置</h3>
	</div>
<div class="progress progress-striped">
  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 30%">
	<span class="sr-only">30%</span>
  </div>
</div>
	<div class="panel-body">
	<?php
if(!defined("SAE_ACCESSKEY"))
echo <<<HTML
		<form action="?do=3" class="form-sign" method="post">
		<label for="name">数据库地址:</label>
		<input type="text" class="form-control" name="db_host" value="localhost">
		<label for="name">数据库端口:</label>
		<input type="text" class="form-control" name="db_port" value="3306">
		<label for="name">数据库用户名:</label>
		<input type="text" class="form-control" name="db_user">
		<label for="name">数据库密码:</label>
		<input type="text" class="form-control" name="db_pwd">
		<label for="name">数据库名:</label>
		<input type="text" class="form-control" name="db_name">
		<br><input type="submit" class="btn btn-primary btn-block" name="submit" value="保存配置">
		</form><br/>
		（如果已事先填写好config.php相关数据库配置，请 <a href="?do=3&jump=1">点击此处</a> 跳过这一步！）
HTML;
?>
	</div>
</div>

<?php }elseif($do=='3'){
?>
<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title" align="center">保存数据库</h3>
	</div>
<div class="progress progress-striped">
  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 50%">
	<span class="sr-only">50%</span>
  </div>
</div>
	<div class="panel-body">
<?php
if(defined("SAE_ACCESSKEY") || $_GET['jump']==1){
	include_once '../config.php';
	if(!$dbconfig['user']||!$dbconfig['pwd']||!$dbconfig['dbname']) {
		echo '<div class="alert alert-danger">请先填写好数据库并保存后再安装！<hr/><a href="javascript:history.back(-1)"><< 返回上一页</a></div>';
	} else {
		if(!$con=mysqli_connect($dbconfig['host'],$dbconfig['user'],$dbconfig['pwd'],$dbconfig['dbname'],$dbconfig['port'])){
			if(mysqli_connect_errno()==2002)
				echo '<div class="alert alert-warning">连接数据库失败，数据库地址填写错误！</div>';
			elseif(mysqli_connect_errno()==1045)
				echo '<div class="alert alert-warning">连接数据库失败，数据库用户名或密码填写错误！</div>';
			elseif(mysqli_connect_errno()==1049)
				echo '<div class="alert alert-warning">连接数据库失败，数据库名不存在！</div>';
			else
				echo '<div class="alert alert-warning">连接数据库失败，['.mysqli_connect_errno().']'.mysqli_connect_error().'</div>';
		}elseif(version_compare(mysqli_get_server_info($con), '5.5.3', '<')){
			echo '<div class="alert alert-warning">MySQL数据库版本太低，需要MySQL 5.6或以上版本！</div>';
		}else{
			echo '<div class="alert alert-success">数据库配置文件保存成功！</div>';
			if(mysqli_query($con, "select * from lylme_config where 1")==FALSE)
				echo '<p align="right"><a class="btn btn-primary btn-block" href="?do=4">创建数据表>></a></p>';
			else
				echo '<div class="list-group-item list-group-item-info">系统检测到你已安装过六零导航页</div>
				<div class="list-group-item">
					<a href="?do=6" class="btn btn-block btn-info">跳过安装</a>
				</div>
				<div class="list-group-item">
					<a href="?do=4" onclick="if(!confirm(\'全新安装将会清空所有数据，是否继续？\')){return false;}" class="btn btn-block btn-warning">强制全新安装</a>
				</div>';
		}
	}
}else{
	$db_host=isset($_POST['db_host'])?$_POST['db_host']:NULL;
	$db_port=isset($_POST['db_port'])?$_POST['db_port']:NULL;
	$db_user=isset($_POST['db_user'])?$_POST['db_user']:NULL;
	$db_pwd=isset($_POST['db_pwd'])?$_POST['db_pwd']:NULL;
	$db_name=isset($_POST['db_name'])?$_POST['db_name']:NULL;

	if($db_host==null || $db_port==null || $db_user==null || $db_pwd==null || $db_name==null){
		echo '<div class="alert alert-danger">保存错误,请确保每项都不为空<hr/><a href="javascript:history.back(-1)"><< 返回上一页</a></div>';
	} else {
		$config='<?php
/*数据库配置*/
$dbconfig=array(
	"host" => "'.$db_host.'", //数据库服务器
	"port" => '.$db_port.', //数据库端口
	"user" => "'.$db_user.'", //数据库用户名
	"pwd" => "'.$db_pwd.'", //数据库密码
	"dbname" => "'.$db_name.'", //数据库名
);
?>';
		if(!$con=mysqli_connect($db_host,$db_user,$db_pwd,$db_name,$db_port)){
			if(mysqli_connect_errno()==2002)
				echo '<div class="alert alert-warning">连接数据库失败，数据库地址填写错误！</div>';
			elseif(mysqli_connect_errno()==1045)
				echo '<div class="alert alert-warning">连接数据库失败，数据库用户名或密码填写错误！</div>';
			elseif(mysqli_connect_errno()==1044)
				echo '<div class="alert alert-warning">连接数据库失败，数据库名填写错误！</div>';
			elseif(mysqli_connect_errno()==1049)
				echo '<div class="alert alert-warning">连接数据库失败，数据库名不存在！</div>';
			else
				echo '<div class="alert alert-warning">连接数据库失败，['.mysqli_connect_errno().']'.mysqli_connect_error().'</div>';
		}elseif(version_compare(mysqli_get_server_info($con), '5.5.3', '<')){
			echo '<div class="alert alert-warning">MySQL数据库版本太低，需要MySQL 5.6或以上版本！</div>';
		}elseif(file_put_contents('../config.php',$config)){
			if(function_exists("opcache_reset"))@opcache_reset();
			echo '<div class="alert alert-success">数据库配置文件保存成功！</div>';
			if(mysqli_query($con, "select * from lylme_config where 1")==FALSE)
				echo '<p align="right"><a class="btn btn-primary btn-block" href="?do=4">创建数据表>></a></p>';
			else
				echo '<div class="list-group-item list-group-item-info">系统检测到你已安装过六零导航页</div>
				<div class="list-group-item">
					<a href="?do=6" class="btn btn-block btn-info">跳过安装</a>
				</div>
				<div class="list-group-item">
					<a href="?do=4" onclick="if(!confirm(\'全新安装将会清空所有数据，是否继续？\')){return false;}" class="btn btn-block btn-warning">强制全新安装</a>
				</div>';
		}else
			echo '<div class="alert alert-danger">保存失败，请确保网站根目录有写入权限<hr/><a href="javascript:history.back(-1)"><< 返回上一页</a></div>';
	}
}
?>
	</div>
</div>
<?php }elseif($do=='4'){?>
<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title" align="center">创建数据表</h3>
	</div>
<div class="progress progress-striped">
  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 70%">
	<span class="sr-only">70%</span>
  </div>
</div>
	<div class="panel-body">
<?php
include_once '../config.php';
if(!$dbconfig['user']||!$dbconfig['pwd']||!$dbconfig['dbname']) {
	echo '<div class="alert alert-danger">请先填写好数据库并保存后再安装！<hr/><a href="javascript:history.back(-1)"><< 返回上一页</a></div>';
} else {
	date_default_timezone_set("PRC");
	$date = date("Y-m-d");
	$sql=file_get_contents("install.sql");
	$sql=explode(';',$sql);
	$sql[] = "INSERT INTO `lylme_config` VALUES ('build', '".$date."', '建站日期')";
	$cn = mysqli_connect($dbconfig['host'],$dbconfig['user'],$dbconfig['pwd'],$dbconfig['dbname'],$dbconfig['port']);
	if (!$cn) die('链接数据库失败:['.mysqli_connect_errno().']'.mysqli_connect_error());
	mysqli_query($cn, "set sql_mode = ''");
	mysqli_query($cn, "set names utf8");
	$t=0; $e=0; $error='';
	for($i=0;$i<count($sql);$i++) {
		if (trim($sql[$i])=='')continue;
		if(mysqli_query($cn, $sql[$i])) {
			++$t;
		} else {
			++$e;
			$error.=mysqli_error($cn).'<br/>';
		}
	}
}
if($e==0) {
	echo '<div class="alert alert-success">安装成功！<br/>SQL成功'.$t.'句/失败'.$e.'句</div><p align="right"><a class="btn btn-block btn-primary" href="index.php?do=5">下一步>></a></p>';
} else {
	echo '<div class="alert alert-danger">安装失败<br/>SQL成功'.$t.'句/失败'.$e.'句<br/>错误信息：'.$error.'</div><p align="right"><a class="btn btn-block btn-primary" href="index.php?do=4">点此进行重试</a></p>';
}
?>
	</div>
</div>

<?php }elseif($do=='5'){ ?>
<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title" align="center">安装完成</h3>
	</div>
<div class="progress progress-striped">
  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
	<span class="sr-only">100%</span>
  </div>
</div>
	<div class="panel-body">
<?php
	$domian=array('lylme','https',$ver);
	@file_get_contents($domian[1].'://dev.hao.'.$domian[0].'.com/installs?v='.$domian[2].'&date='.date('Y-m-d H:i').'&url='.$_SERVER['HTTP_HOST'], false, stream_context_create(array('http'=>array('method'=>"GET",'timeout'=>10))));
	@file_put_contents("install.lock",'安装锁');
	echo '<div class="alert alert-info"><font color="green">安装完成！管理账号和密码是:admin/123456</font><br/><br/><a href="../" target="_blank">>>网站首页</a>｜<a href="../admin/" target="_blank">>>后台管理</a><hr/>更多设置选项请登录后台管理进行修改。<br/><br/><font color="#FF0033">如果你的空间不支持本地文件读写，请自行在install/ 目录建立 install.lock 文件！</font></div></div>';
?>
	</div>
</div>

<?php }elseif($do=='6'){ ?>

<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title" align="center">安装完成</h3>
	</div>
<div class="progress progress-striped">
  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
	<span class="sr-only">100%</span>
  </div>
</div>
	<div class="panel-body">
<?php
	@file_put_contents("install.lock",'安装锁');
	echo '<div class="alert alert-info"><font color="green">安装完成！管理账号和密码是:admin/123456</font><br/><br/><a href="../" target="_blank">>>网站首页</a>｜<a href="../admin/" target="_blank">>>后台管理</a><hr/>更多设置选项请登录后台管理进行修改。<br/><br/><font color="#FF0033">如果你的空间不支持本地文件读写，请自行在install/ 目录建立 install.lock 文件！</font></div>';
?>
	</div>
</div>

<?php }?>

</div>
</body>
</html>