<?php
include("../include/common.php");
if(isset($_POST['user']) && isset($_POST['pass'])){
	$user=daddslashes($_POST['user']);
	$pass=daddslashes($_POST['pass']);
	if($user==$conf['admin_user'] && $pass==$conf['admin_pwd']) {
		$session=md5($user.$pass);
		$token=authcode("{$user}\t{$session}", 'ENCODE', SYS_KEY);
		setcookie("admin_token", $token, time() + 604800);
		@header('Content-Type: text/html; charset=UTF-8');
		exit("<script language='javascript'>alert('登陆管理中心成功！');window.location.href='./';</script>");
	}elseif ($pass != $conf['admin_pwd']) {
		@header('Content-Type: text/html; charset=UTF-8');
		exit("<script language='javascript'>alert('用户名或密码不正确！');history.go(-1);</script>");
	}
}elseif(isset($_GET['logout'])){
	setcookie("admin_token", "", time() - 604800);
	@header('Content-Type: text/html; charset=UTF-8');
	exit("<script language='javascript'>alert('您已成功注销本次登陆！');window.location.href='./login.php';</script>");
}elseif(isset($islogin)==1){
	exit("<script language='javascript'>alert('您已登陆！');window.location.href='./';</script>");
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
<title>后台登录 - <?php echo explode("-", $conf['title'])[0];?></title>
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/materialdesignicons.min.css" rel="stylesheet">
<link href="css/style.min.css" rel="stylesheet">
<style>
.lyear-wrapper {
    position: relative;
}
.lyear-login {
    display: flex !important;
    min-height: 100vh;
    align-items: center !important;
    justify-content: center !important;
}
.lyear-login:after{
    content: '';
    min-height: inherit;
    font-size: 0;
}
.login-center {
    background: #fff;
    min-width: 29.25rem;
    padding: 2.14286em 3.57143em;
    border-radius: 3px;
    margin: 2.85714em;
}
.login-header {
    margin-bottom: 1.5rem !important;
}
.login-center .has-feedback.feedback-left .form-control {
    padding-left: 38px;
    padding-right: 12px;
}
.login-center .has-feedback.feedback-left .form-control-feedback {
    left: 0;
    right: auto;
    width: 38px;
    height: 38px;
    line-height: 38px;
    z-index: 4;
    color: #dcdcdc;
}
.login-center .has-feedback.feedback-left.row .form-control-feedback {
    left: 15px;
}
</style>
</head>
  
<body>
    <?php
if(!empty($background = background())){
    $background = str_replace('./','../',$background);
	echo '<div class="row lylme-wrapper" style="background-image:  url('.$background.');background-size: cover;">';}
?>
<div class="row lyear-wrapper">
  <div class="lyear-login">
    <div class="login-center">
      <div class="login-header text-center">
       <h2>后台登录</h2>
      </div>
      <form action="" method="post">
        <div class="form-group has-feedback feedback-left">
          <input type="text" placeholder="用户名" class="form-control" name="user" id="username" value="<?php echo @$_POST['user']?>"/>
          <span class="mdi mdi-account form-control-feedback" aria-hidden="true"></span>
        </div>
        <div class="form-group has-feedback feedback-left">
          <input type="password" placeholder="密码" class="form-control" id="password" name="pass" />
          <span class="mdi mdi-lock form-control-feedback" aria-hidden="true"></span>
        </div>

        
        <div class="form-group">
          <button class="btn btn-block btn-primary" type="submit" id="login">登录</button>
        </div>
      </form>
      <hr>
      <footer class="col-sm-12 text-center">
        <p class="m-b-0">Copyright ©2021 - <?php echo(date('Y'));  ?> <a href="/"><?php echo explode("-", $conf['title'])[0];?></a></p>
      </footer>
    </div>
  </div>
</div>
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script type="text/javascript">;</script>
</body>
</html>