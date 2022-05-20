<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width">
<title>请登录...</title>
<style>
    *{margin:0;padding:0;box-sizing:border-box}
body{text-align:center;background:#fff;color:#7b8993}
.form-wrapper{padding-top:10%;border-radius:2px;margin:50px auto;position:relative;width:375px}
form{padding:30px 20px 0}
.form-item{margin-bottom:10px;width:100%}
.form-item input{border-radius:15px;border:1px solid #ccc;border-radius:15px;color:#000;font-size:1em;height:50px;padding:0 16px;transition:background 0.3s ease-in-out;width:100%}
.form-item input:focus{outline:none;border-color:#9ecaed;box-shadow:0 0 10px #9ecaed}
.button-panel{margin:20px 0 0;width:100%}
.button-panel .button{-webkit-appearance:none;background:#009dff;border:none;border-radius:15px;color:#fff;cursor:pointer;height:50px;font-family:'Open Sans',sans-serif;font-size:1.2em;letter-spacing:0.05em;text-align:center;text-transform:uppercase;transition:background 0.3s ease-in-out;width:100%}
.button:hover{background:#00c8ff}
@media only screen
and (max-width:320px){.form-wrapper{padding-top:10%;border-radius:2px;margin:50px auto;position:relative;width:320px}
}.top{background:#009dff;position:fixed;z-index:1031;top:0;left:0;height:4px;transition:all 1s;width:0;overflow:hidden}
.colors{width:100%;height:4px}
.top-banner{background-color:#333}
.nav{margin-bottom:30px}
.nav li.current a{background-color:#009DFF;color:#fff;padding:10px}
.nav a{margin:5px;color:#333;text-decoration:none}
</style>
</head>
<body>
<div class="form-wrapper">
	<div class="top"><div class="colors"></div></div>
	<div class="nav">
		<ul>
			<h1>Login</h1>
			
		</ul>
	</div><p>请输入密码显示加密链接</p>
	<?php 
	session_start(); //设置session
	if($_SESSION['pass'] != 1){?>
	<form name="form" action="../include/go.php" method="POST">
		<div class="form">

			<div class="form-item">
				<input type="password" autocomplete="new-password"  name="pass" required="required" value="123" placeholder="密码" autocomplete="off">
			</div>
			<div class="button-panel">
				<input type="submit" class="button" title="登录" value="登录">
			</div>
		</div>
	</form><?php }else{ ?>
	<form name="form" action="../include/go.php" method="POST">
		<div class="form">
			<div class="button-panel">
			    <p> 您已登录</p>
			    <div class="form-item">
				<input type="hidden" autocomplete="new-password"  name="exit" required="required" value="exit"  >
			</div>
				<input type="submit" class="button" title="注销登录" value="注销登录">
			</div>
		</div>
	</form>
	<?php }?>
</div>

</body>
</html>
