<?php
/* 
 * @Description: 加密页
 * @Author: LyLme admin@lylme.com
 * @Date: 2024-01-23 12:25:35
 * @LastEditors: LyLme admin@lylme.com
 * @LastEditTime: 2024-04-09 01:04:15
 * @FilePath: /lylme_spage/pwd/index.php
 * @Copyright (c) 2024 by LyLme, All Rights Reserved. 
 */
include("../include/common.php");
?>
<!DOCTYPE html>
<html lang="zh-cn">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width">
	<title>访问管理</title>
	<style>
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box
		}

		body {
			text-align: center;
			color: #7b8993
		}

		.form-wrapper {
			padding-top: 50px;
			border-radius: 15px;
			margin: 50px auto;
			position: relative;
			width: 375px;
			background: #fff;
		}

		form {
			padding: 30px 20px 0
		}

		.form-item {
			margin-bottom: 10px;
			width: 100%
		}

		.form-item input {
			border-radius: 15px;
			border: 1px solid #ccc;
			border-radius: 15px;
			color: #000;
			font-size: 1em;
			height: 50px;
			padding: 0 16px;
			transition: background 0.3s ease-in-out;
			width: 100%
		}

		.form-item input:focus {
			outline: none;
			border-color: #9ecaed;
			box-shadow: 0 0 10px #9ecaed
		}

		.button-panel {
			margin: 40px 0;
			width: 100%
		}

		.button-panel .button {
			padding: 10px 15px;
			background: #009dff;
			border: none;
			border-radius: 15px;
			color: #fff;
			cursor: pointer;
			height: 50px;
			font-family: 'Open Sans', sans-serif;
			font-size: 1.2em;
			letter-spacing: 0.05em;
			text-align: center;
			text-transform: uppercase;
			transition: background 0.3s ease-in-out;
			width: 100%
		}

		.button:hover {
			background: #00c8ff
		}

		@media only screen and (max-width:320px) {
			.form-wrapper {
				padding-top: 10%;
				border-radius: 2px;
				margin: 50px auto;
				position: relative;
				width: 320px
			}
		}

		.top {
			background: #009dff;
			position: fixed;
			z-index: 1031;
			top: 0;
			left: 0;
			height: 4px;
			transition: all 1s;
			width: 0;
			overflow: hidden
		}

		.colors {
			width: 100%;
			height: 4px
		}

		.top-banner {
			background-color: #333
		}

		.nav {
			margin-bottom: 30px
		}

		.nav li.current a {
			background-color: #009DFF;
			color: #fff;
			padding: 10px
		}

		.nav a {
			margin: 5px;
			color: #333;
			text-decoration: none
		}

		.home {
			text-decoration: none;
			color: #bbb;
			line-height: 4;
		}

		.body {
			background-size: cover;
			display: flex;
			height: 100vh;
			align-items: center;
			justify-content: center;
		}
	</style>
</head>

<body>
	<?php

	if (!empty($background = background())) {
		$background = str_replace('./', '../', $background);
		echo '<div class="body" style="background-image:  url(' . $background . ');">';
	}
	?>
	<div class="form-wrapper">

		<div class="nav">

			<?php
			if ($DB->num_rows($DB->query("SELECT * FROM `lylme_pwd`")) != 0) {
				echo '<h1>访问管理</h1>'; ?>

		</div>
		<?php
				session_start(); //设置session
				if (isset($_SESSION['pass']) != 1) { ?>
			<p>请输入密码登录</p>
			<form name="form" action="../include/go.php" method="POST">
				<div class="form">

					<div class="form-item">
						<input type="password" autocomplete="new-password" name="pass" required="required" value="" placeholder="密码" autocomplete="off">
					</div>
					<div class="button-panel">
						<input type="submit" class="button" title="登录" value="登录">
					</div>
				</div>
			</form><?php } else { ?>
			<form name="form" action="../include/go.php" method="POST">
				<div class="form">
					<div class="button-panel">
						<p> 欢迎回来，您已登录！<br><br>用户组:
							<?php foreach ($_SESSION['list'] as $list) {
								echo (' [' . $list . '] ');
							}
							?></p>
						<div class="form-item">
							<input type="hidden" autocomplete="new-password" name="exit" required="required" value="exit">
						</div>
						<input type="submit" class="button" title="注销登录" value="注销登录">
					</div>
				</div>

			</form>
		<?php
				}
			} else { ?>

		<h2>当前站点未启用链接加密</h2>
		<div class="button-panel">
			<a href="../" class="button">返回首页</a>
		</div>
	<?php } ?>
	</div>
	</div>
</body>

</html>