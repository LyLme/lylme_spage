<?php 
include("../include/common.php");
$grouplists =$DB->query("SELECT * FROM `lylme_groups`");
if(isset($_REQUEST['authcode'])){
	session_start();
	if(strtolower($_REQUEST['authcode'])== $_SESSION['authcode']){
		if(isset($_POST['name'])&& isset($_POST['url'])&& isset($_POST['icon'])&& isset($_POST['group_id'])&& isset($_POST['mail'])!=NULL){
			$name=daddslashes($_POST['name']);
			$url=daddslashes($_POST['url']);
			$icon=daddslashes($_POST['icon']);
			$group_id=daddslashes($_POST['group_id']);
			$mail=daddslashes($_POST['mail']);
			$sw = 1;
			$date = date("Y-m-d H:i:s");
			$status = $conf["apply"];
			if($status==2) {
				exit('<script>alert("提交失败，网站已关闭申请收录功能！");window.location.href="./";</script>');
			}
			if(empty($status)){
				$status=0;
			}
		}
		if($sw == 1){
			if(empty($name) || empty($url) || empty($icon) || empty($group_id) || empty($mail) ){
				exit('<script>alert("提交失败,请确保所有选项都不为空！");history.go(-1);</script>');
			} else if(strpos($icon, 'http') !== 0 && strpos($icon, '<svg') !== 0 ||strpos($url, 'http') !== 0) {
				exit('<script>alert("提交失败，请按要求填写！");history.go(-1);</script>');
			} else{
				$sql = "INSERT INTO `lylme_apply` (`apply_id`, `apply_name`, `apply_url`, `apply_group`, `apply_icon`, `apply_mail`, `apply_time`, `apply_status`) VALUES (NULL, '".$name."', '".$url."', '".$group_id."', '".$icon."', '".$mail."', '".$date."', '".$status."');";
				if($DB->query($sql)){
					switch ($status) {
						case 0:
						    echo '<script>alert("提交成功，请等待管理员审核！");window.location.href="./";</script>';
						break;
						case 1:
						    echo '<script>alert("提交成功，网站已成功收录！");window.location.href="./";</script>';
						break;
					}
				} else{
					echo '<script>alert("提交失败,请联系网站管理员！");history.go(-1);</script>';
				}
			}
		}
	} else{
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
<title>申请收录 - <?php echo $conf['title'];?></title>
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
.lylme-form:after{
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
.code{
    padding-left:0px;
    padding-right:0px;
    height: 38px;
}
</style>
</head>
  
<body>
<div class="row lylme-wrapper" style="background-image: url(../assets/img/background.jpg);background-size: cover;">
<div class="lylme-form">
    <div class="lylme-center">
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
    if($grouplist["group_id"]==$row['group_id']){$select='selected="selected"';}else {$select='';}
    echo '<option  value="'.$grouplist["group_id"].'">'.$grouplist["group_id"].'.  '.$grouplist["group_name"].'</option>';
    }?>
    </select>
    <span class="mdi mdi-folder form-control-feedback" aria-hidden="true"></span>
    </div>
</div>

<div class="form-group has-feedback feedback-left row">
    <div class="col-xs-12">
    <label>* 网站图标:</label>
    <textarea type="text" class="form-control" name="icon" required placeholder="<svg 或 http://"></textarea>
    <span class="mdi mdi-emoticon form-control-feedback" aria-hidden="true"></span>
    <small class="help-block">方式1：填写图标的<code>URL</code>地址，如<code>http://www.xxx.com/img/logo.png</code><br>
方式2：粘贴图标的<code>SVG</code>代码(建议)，<a href="https://blog.lylme.com/archives/lylme_spage-svg.html" target="_blank">查看教程</a></small>
    </div>
</div>

<div class="form-group has-feedback feedback-left row">
    <div class="col-xs-12">
    <label>* 联系邮箱:</label>
    <input type="text" class="form-control" name="mail" value="" required placeholder="填写邮箱">
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
<input type="submit" class="btn btn-primary btn-block" value="提交"></form>
    </div>
  </div>
</div>
</body>
</html>