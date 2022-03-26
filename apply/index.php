<?php 
include("../include/common.php");
$grouplists =$DB->query("SELECT * FROM `lylme_groups`");
if(isset($_REQUEST['authcode'])) {
	session_start();
	if(strtolower($_REQUEST['authcode'])== $_SESSION['authcode']) {
		if(isset($_POST['name'])&& isset($_POST['url'])&& isset($_POST['icon'])&& isset($_POST['group_id'])&& isset($_POST['mail'])!=NULL) {
			$status = $conf["apply"];
			if($status==2) {
				exit('<script>alert("提交失败，网站已关闭申请收录功能！");window.location.href="./";</script>');
			}
			$name=strip_tags(daddslashes($_POST['name']));
			$url=strip_tags(daddslashes($_POST['url']));
			$icon=strip_tags(daddslashes($_POST['icon']));
			$group_id=strip_tags(daddslashes($_POST['group_id']));
			$mail=strip_tags(daddslashes($_POST['mail']));
			$sw = 1;
			$date = date("Y-m-d H:i:s");
			if(empty($status)) {
				$status=0;
			}
		}
		if($sw == 1) {
			if(empty($name) || empty($url) || empty($icon) || empty($group_id) || empty($mail) ) {
				exit('<script>alert("提交失败,请确保所有选项都不为空！");history.go(-1);</script>');
			} else if(!preg_match('{^http[s]?://([\w-]+\.)+[\w]+(/[\w-./%&=]*)\.(jpg|png|ico)$}i', $icon) 
						|| !preg_match('{^http[s]?://([\w-]+\.)+[\w-]+(/[\w-./?%&#=]*)?$}i', $url)) {
				exit('<script>alert("提交失败！输入不符合要求");history.go(-1);</script>');
			} else {
				if($DB->num_rows($DB->query("SELECT * FROM `lylme_apply` WHERE `apply_url` LIKE '".$url."';"))>0) {
					exit('<script>alert("链接已存在，请勿重复提交！");history.go(-1);</script>');
				}
				$sql = "INSERT INTO `lylme_apply` (`apply_id`, `apply_name`, `apply_url`, `apply_group`, `apply_icon`, `apply_mail`, `apply_time`, `apply_status`) VALUES (NULL, '".$name."', '".$url."', '".$group_id."', '".$icon."', '".$mail."', '".$date."', '".$status."');";
				if($DB->query($sql)) {
					switch ($status) {
						case 0:
							echo '<script>alert("提交成功！请等待管理员审核！");window.location.href="./";</script>';
						break;
						case 1:
							$link_order = $DB->count('select MAX(id) from `lylme_links`')+1;
						$sql1 = "INSERT INTO `lylme_links` (`id`, `name`, `group_id`, `url`, `icon`, `PS`,`link_order`) VALUES (NULL, '" . $name . "', '" . $group_id . "', '" . $url . "', '" . $icon . "', '" . $mail . "的提交 ', '" . $link_order . "');";
						if($DB->query($sql1)) {
							echo '<script>alert("提交成功！网站已成功收录！");window.location.href="./";</script>';
						} else {
							echo '<script>alert("提交成功！请等待管理员审核！");</script>';
						}
						break;
					}
				} else {
					echo '<script>alert("提交失败！请联系网站管理员！");history.go(-1);</script>';
				}
			}
		}
	} else {
		echo '<script>alert("验证码错误！");history.go(-1);</script>';
	}
	exit();
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
<title>申请收录 - <?php echo $conf['title'];
?></title>
<link rel="icon" href="/assets/img/logo.png" type="image/ico">
<meta name="author" content="LyLme">
<link href="../admin/css/materialdesignicons.min.css" rel="stylesheet">
<link href="../admin/css/bootstrap.min.css" rel="stylesheet">
<link href="../admin/css/style.min.css" rel="stylesheet">
<style>
.lylme-wrapper {
	position: relative;
}
.lylme-form {
	display: flex !important;
	min-height: 100vh;
	align-items: center !important;
	justify-content: center !important;
}
.lylme-form:after {
	content: '';
	min-height: inherit;
	font-size: 0;
}
.lylme-center {
	background: #fff;
	min-width: 29.25rem;
	padding: 2.14286em 3.57143em;
	border-radius: 20px;
	margin: 2.85714em;
}
.lylme-header {
	margin-bottom: 1.5rem !important;
}
.lylme-center .has-feedback.feedback-left .form-control {
	padding-left: 38px;
	padding-right: 12px;
}
.lylme-center .has-feedback.feedback-left .form-control-feedback {
	left: 0;
	right: auto;
	width: 38px;
	height: 38px;
	line-height: 38px;
	z-index: 4;
	color: #dcdcdc;
}
.lylme-center .has-feedback.feedback-left.row .form-control-feedback {
	left: 15px;
}
.code {
	padding-left:0px;
	padding-right:0px;
	height: 38px;
}
</style>
</head>
<body>
<div class="row lylme-wrapper" style="background-image: url(<?php echo background()?>);background-size: cover;">
<div class="lylme-form">
    <div class="lylme-center">
        <?php if($conf["apply"]==2) {
	exit('<div class="lylme-header text-center"><h2>网站已关闭申请收录</h2></div> </div>');
}
?>
    <div class="lylme-header text-center"><h2>申请收录</h2></div>
<form action="" method="POST">
<div class="form-group has-feedback feedback-left row">
    <div class="col-xs-12">
    <label>* 网站名称:</label>
    <input type="text" class="form-control" name="name" value="" required placeholder="网站名称">
    <span class="mdi mdi-format-title form-control-feedback" aria-hidden="true"></span>
    </div>
</div>
<div class="form-group has-feedback feedback-left row">
    <div class="col-xs-12">
    <label>* 网站链接:</label>
    <input type="text" class="form-control" name="url" value="" required placeholder="http://或https://开头">
    <span class="mdi mdi-link-variant form-control-feedback" aria-hidden="true"></span>
    </div>
</div>
<div class="form-group has-feedback feedback-left row">
    <div class="col-xs-12">
    <label>* 选择分组:</label>
    <select class="form-control" name="group_id">
    <?php
    while($grouplist = $DB->fetch($grouplists)) {
	if($grouplist["group_id"]==$row['group_id']) {
		$select='selected="selected"';
	} else {
		$select='';
	}
	echo '<option  value="'.$grouplist["group_id"].'">'.$grouplist["group_id"].'.  '.$grouplist["group_name"].'</option>';
}
?>
    </select>
    <span class="mdi mdi-folder form-control-feedback" aria-hidden="true"></span>
    </div>
</div>
<div class="form-group has-feedback feedback-left row">
    <div class="col-xs-12">
    <label>* 网站图标:</label>
    <textarea type="text" id="icon" class="form-control" name="icon" required placeholder="如：https://hao.lylme.com/assets/img/logo.png"></textarea>
    <span class="mdi mdi-emoticon form-control-feedback" aria-hidden="true"></span>
    <small class="help-block">1.填写图标的<code>URL</code>地址，如<code>http://www.xxx.com/img/logo.png</code><br>
2. 链接使用<code>http</code>或用<code>https</code>协议<br>
    3. 仅支持<code>.ico .png .jpg .gif</code>的格式</small>
    </div>
</div>
<div class="form-group has-feedback feedback-left row">
    <div class="col-xs-12">
    <label>* 联系邮箱:</label>
    <input type="text" class="form-control" name="mail" value="" autocomplete="off" required placeholder="填写邮箱">
    <span class="mdi mdi-email form-control-feedback" aria-hidden="true"></span>
    </div>
</div>
    <label>* 验证码:</label>
<div class="form-group has-feedback feedback-left row">
    <div class="col-xs-9">
    <input type="text" name="authcode" class="form-control" placeholder="验证码">
    <span class="mdi mdi-check form-control-feedback" aria-hidden="true"></span>
    </div>
    <div class="col-xs-3">
        <img id="captcha_img" src='../include/validatecode.php?r=echo rand(); ?>' class="pull-right code"
        onclick="document.getElementById('captcha_img').src='../include/validatecode.php?r='+Math.random()"
        />
          </div>
    </div>
<div class="form-group">
<input type="submit" id="submit"class="btn btn-primary btn-block" value="提交申请"></form>
    </div>
  </div>
</div>
</body>
<script>
      window.onload = function() {
	var inputInt = document.getElementById('icon');
	var submit = document.getElementById("submit");
	function sw_on() {
		inputInt.style.borderColor = "#ebebeb";
		submit.disabled = false;
		submit.value = "提交";
	}
	function sw_off() {
		inputInt.style.borderColor = "#ff0000";
		submit.disabled = true;
		submit.value = "输入不符合要求";
	}
	inputInt.oninput  = function() {
		var re =/^http[s]?:\/\/([\w-]+\.)+[\w]+(\/[\w-./%&=]*)\.(jpg|png|ico|gif)$/
		        if (!re.test(this.value)) {
			sw_off();
		} else {
			sw_on();
		}
	}
	;
	sw_on();
}
</script>
</html>