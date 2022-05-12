<?php 
include("../include/common.php");
if(!empty($url = isset($_GET['url']) ? $_GET['url'] : null)) {
	function get_head($url) {
        ini_set("user_agent","Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/101.0.4951.54 Safari/537.36 Edg/101.0.1210.39 Lylme/11.24");
		$opts = array(
		    'http'=>array( 
		    'method'=>"GET", 
		    'timeout'=>4
		    ) 
		);
		$contents = @file_get_contents("compress.zlib://".$url, false, stream_context_create($opts));
		preg_match('/<title>(.*?)<\/title>/is',$contents,$title);  // 获取网站标题
		preg_match('/<link rel=".*?icon" * href="(.*?)".*?>/is', $contents,$icon);  // 获取网站icon
		preg_match('/<meta.+?charset=[^\w]?([-\w]+)/i', $contents,$charset);  //获取网站编码
		$get_heads = array();
		$get_heads['charset']=$charset[1];
		$get_heads['title'] = str_replace("'","\"",preg_replace("/\s/","",$title[1]));
		$get_heads['icon'] = get_urlpath(preg_replace("/\s/","",$icon[1]),$url);
		if(strtolower($get_heads['charset'])!="uft-8"){
		    // 将非UTF-8编码转换
		    $get_heads['title']  = iconv($get_heads['charset'], "UTF-8",$get_heads['title']);
		    $get_heads['icon']  = iconv($get_heads['charset'], "UTF-8",$get_heads['icon']);
		}
		return $get_heads;
	}
	$head = get_head($_GET['url']);
	if(empty($head['title'])&&empty($head['icon']))exit('Unable to access');
	header('Content-Type:application/json');
	exit('{"title": "'.$head['title'].'", "icon": "'.$head['icon'].'","charset": "'.$head['charset'].'"}');
}
$grouplists =$DB->query("SELECT * FROM `lylme_groups`");
if(isset($_REQUEST['authcode'])) {
	session_start();
	if(strtolower($_REQUEST['authcode'])== $_SESSION['authcode']) {
		if(isset($_POST['name'])&& isset($_POST['url'])&& isset($_POST['icon'])&& isset($_POST['group_id'])) {
			$status = $conf["apply"];
			if($status==2) {
				exit('<script>alert("提交失败，网站已关闭申请收录功能！");window.location.href="./";</script>');
			}
			$name=strip_tags(daddslashes($_POST['name']));
			$url=strip_tags(daddslashes($_POST['url']));
			$icon=strip_tags(daddslashes($_POST['icon']));
			$group_id=strip_tags(daddslashes($_POST['group_id']));
			$userip=strip_tags(get_real_ip());
			$sw = 1;
			$date = date("Y-m-d H:i:s");
			if(empty($status)) {
				$status=0;
			}
		}
		function strlens($str) {
			if(strlen($str) > 255) {
				return true;
			} else {
				return false;
			}
		}
		if($sw == 1) {
			if(empty($name) || empty($url) || empty($group_id) || empty($userip) ) {
				//|| empty($icon)
				exit('<script>alert("提交失败，请确保所有选项都不为空！");history.go(-1);</script>');
			} else if(!preg_match('/^http*/i', $url)) {
				exit('<script>alert("提交失败，输入不符合要求！");history.go(-1);</script>');
			} else if(strlens($name)||strlens($url)||strlens($icon)||strlens($group_id)||strlens($userip)) {
				exit('<script>alert("非法参数！");history.go(-1);</script>');
			} else {
				if($DB->num_rows($DB->query("SELECT * FROM `lylme_apply` WHERE `apply_url` LIKE '".$url."';"))>0) {
					exit('<script>alert("链接已存在，请勿重复提交，如需修改请联系站长！");history.go(-1);</script>');
				}
				$sql = "INSERT INTO `lylme_apply` (`apply_id`, `apply_name`, `apply_url`, `apply_group`, `apply_icon`, `apply_mail`, `apply_time`, `apply_status`) VALUES (NULL, '".$name."', '".$url."', '".$group_id."', '".$icon."', '".$userip."', '".$date."', '".$status."');";
				if($DB->query($sql)) {
					switch ($status) {
						case 0:
							echo '<script>alert("提交成功！请等待管理员审核！");window.location.href="./";</script>';
						break;
						case 1:
							$link_order = $DB->count('select MAX(id) from `lylme_links`')+1;
						$sql1 = "INSERT INTO `lylme_links` (`id`, `name`, `group_id`, `url`, `icon`, `PS`,`link_order`) VALUES (NULL, '" . $name . "', '" . $group_id . "', '" . $url . "', '" . $icon . "', '" . $userip . "的提交 ', '" . $link_order . "');";
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
<title>申请收录 - <?php echo $conf['title']?></title>
<link rel="icon" href="<?php echo get_urlpath($conf['logo'],siteurl().'/apply');?>" type="image/ico">
<link href="https://cdn.lylme.com/admin/lyear/css/materialdesignicons.min.css" rel="stylesheet">
<link href="https://cdn.lylme.com/admin/lyear/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.lylme.com/admin/lyear/css/style.min.css" rel="stylesheet">
<style>
#loading{position:absolute;left:0;top:0;height:100vh;width:100vw;z-index:100;display:none;align-items:center;justify-content:center;background-color:rgba(0,0,0,0.5);color:#bbb;font-size:16px}
#loading>img{height:18px;width:18px}
.lylme-wrapper{position:relative}
.lylme-form{display:flex !important;min-height:100vh;align-items:center !important;justify-content:center !important}
.lylme-form:after{content:'';min-height:inherit;font-size:0}
.lylme-center{background:#fff;min-width:29.25rem;padding:30px;border-radius:20px;margin:2.85714em}
.lylme-header{margin-bottom:1.5rem !important}
.lylme-center .has-feedback.feedback-left .form-control{padding-left:38px;padding-right:12px}
.lylme-center .has-feedback.feedback-left .form-control-feedback{left:0;right:auto;width:38px;height:38px;line-height:38px;z-index:4;color:#dcdcdc}
.lylme-center .has-feedback.feedback-left.row .form-control-feedback{left:15px}
.code{height:38px}
.apply_gg{margin:20px 0;font-size:15px;line-height:2}
</style>
</head>
<body>
<div id="loading"><img src="https://cdn.lylme.com/admin/lyear/img/loading.gif"/>  &nbsp;正在获取....</div>
<?php
if(!empty($background = background())){
    $background = str_replace('./','../',$background);
	echo '<div class="row lylme-wrapper" style="background-image:  url('.$background.');background-size: cover;">';}
	else{ echo '<div class="row lylme-wrapper">';}
?>

<div class="lylme-form">
    <div class="lylme-center">
        <?php if($conf["apply"]==2) {
	exit('<div class="lylme-header text-center"><h2>网站已关闭申请收录</h2></div>'. $conf['apply_gg']. '</div>');
}
?>
    <div class="lylme-header text-center"><h2>申请收录</h2></div>
    <div class="apply_gg">
      <?php echo $conf['apply_gg']?>
    </div>
<form action="" method="POST" AUTOCOMPLETE="OFF">
    <div class="form-group">
<label>*URL链接地址:</label>
<div class="input-group">
<input type="text" class="form-control" name="url" placeholder="完整链接或域名" value="" onchange="gurl()"  required >
<span class="input-group-btn">
  <button class="btn btn-default" onclick="geturl()" type="button">自动获取</button>
</span>
</div></div>
<div class="form-group has-feedback feedback-left row">
    <div class="col-xs-12">
    <label>* 选择分组:</label>
    <select title="分组" class="form-control" name="group_id" required>
    <option value="">请选择</option>
    <?php
    while($grouplist = $DB->fetch($grouplists)) {
	echo '
	<option value="'.$grouplist["group_id"].'">'.$grouplist["group_name"].'</option>';
}
?>
    </select>
    <span class="mdi mdi-folder form-control-feedback" aria-hidden="true"></span>
    </div>
</div>
<div class="form-group has-feedback feedback-left row">
    <div class="col-xs-12">
    <label>* 网站名称:</label>
    <input type="text" class="form-control" id="title" name="name" value="" required placeholder="网站名称">
    <span class="mdi mdi-format-title form-control-feedback" aria-hidden="true"></span>
        <small class="help-block">填写网站名称</small>
    </div>
</div>
<div class="form-group has-feedback feedback-left row">
    <div class="col-xs-12">
    <label>网站图标:</label>
    <textarea type="text" id="icon" class="form-control" name="icon"  placeholder="填写图标的URL地址"></textarea>
    <span class="mdi mdi-emoticon form-control-feedback" aria-hidden="true"></span>
    <small class="help-block">填写图标的<code>URL</code>地址，如：<code>http://www.xxx.com/logo.png</code><br>
    部分网站无法自动获取，请手动填写</small>
</div>
</div>
    <label>* 验证码:</label>
<div class="form-group has-feedback feedback-left row">
    <div class="col-xs-8">
    <input type="text" name="authcode" class="form-control" placeholder="验证码">
    <span class="mdi mdi-check form-control-feedback" aria-hidden="true"></span>
    </div>
    <div class="col-xs-4">
        <img id="captcha_img" title="验证码" src='../include/validatecode.php' class="pull-right code"
        onclick="document.getElementById('captcha_img').src='../include/validatecode.php?r='+Math.random()"
        />
          </div>
    </div>
<div class="form-group">
<input type="submit" id="submit"class="btn btn-primary btn-block" value="提交"></form>
    </div>
    <center><?php echo $conf['copyright']?></center>
  </div>
</div>
</body>
<script src="https://lf3-cdn-tos.bytecdntp.com/cdn/expire-2-M/jquery/3.5.1/jquery.min.js" type="application/javascript"></script>
<script src="https://lf3-cdn-tos.bytecdntp.com/cdn/expire-1-M/layer/3.1.1/layer.min.js" type="application/javascript"></script>
<script>
function geturl(){
	var url = $("input[name=\'url\']").val();
	if(!url){
		layer.msg('链接地址不能为空');
		return false;
	}
	$('#loading').css("display","flex");
    if (!/^http[s]?:\/\/+/.test(url)&&url!="") {
		var url =  "http://"+url;
		$("input[name=\'url\']").val(url);
	}
	
    $.ajax({
        url:"index.php",
        type:"GET",
        dataType:"json",
        data:{url:url},
        success:function(data){
            var head = eval(data);
            $("input[name=\'name\']").val(head.title);
            if(!head.icon){
                layer.msg('未获取到网站图标');
            }
            $("textarea[name=\'icon\']").val(head.icon);
            $('#loading').css("display","none");
            return true;
        },
        error:function(data){
        	layer.msg('获取失败，网站无法访问或对方防火墙限制！');
        	$('#loading').css("display","none");
        	return false;
        }
    });
}  
function gurl(){
    var url = $("input[name=\'url\']").val();
    if (!/^http[s]?:\/\/+/.test(url)&&url!="") {
		var url =  "http://"+url;
		$("input[name=\'url\']").val(url);
		return false;
	}
	return true;
}
</script>
</html>