<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <title><?php echo $conf['title']?></title>
  <meta name="keywords" content="<?php echo $conf['keywords']?>">
  <meta name="description" content="<?php echo $conf['description']?>">
  <link rel="icon" href="<?php echo $conf['logo']?>" type="image/x-icon">
  <meta http-equiv="Cache-Control" content="no-siteapp">
  <meta name="referrer" content="no-referrer" />
  <meta name="theme-color" content="#ffffff">
  <meta name="author" content="D.Young">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-touch-fullscreen" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <meta name="full-screen" content="yes"><!--UC强制全屏-->
  <meta name="browsermode" content="application"><!--UC应用模式-->
  <meta name="x5-fullscreen" content="true"><!--QQ强制全屏-->
  <meta name="x5-page-mode" content="app"><!--QQ应用模式-->
  <meta name="lsvn" content="<?php echo base64_encode($conf['version'])?>">
  <link href="<?php echo $templatepath;?>/css/style.css?v=20221210" rel="stylesheet">
  <link href="<?php echo $templatepath;?>/css/wea.css" rel="stylesheet">
  <script src="https://lf3-cdn-tos.bytecdntp.com/cdn/expire-2-M/jquery/3.5.1/jquery.min.js"></script>
</head>
<?php if(!empty(background())){
	echo '<body style="background: url('.background().') no-repeat center/cover;">';}
	else{ echo '<body>';}?>
    <div id="menu"><i></i></div>
    <div class="list closed">
<?php

$html= array(
    'g1' => '<ul class="mylist row">', //分组开始标签
    'g2' => '<li class="title">{group_icon}<sapn>{group_name}</sapn></li>',  //分组内容
    'g3' => '</ul>',  //分组结束标签
    
    'l1' => '<li class="col-3 col-sm-3 col-md-3 col-lg-1">',  //链接开始标签
    'l2' => '<a rel="nofollow" href="{link_url}" target="_blank">{link_icon}<span>{link_name}</span></a>',  //链接内容
    'l3' => '</li>',  //链接结束标签
);
lists($html);

echo '</div>';
if ($conf['tq']) {
    echo '<!--天气-->
    <div class="mywth">
        <div class="wea_hover">
            <div class="wea_in wea_top"></div>
            <div class="wea_in wea_con">
                <ul></ul>
            </div>
            <div class="wea_in wea_foot">
                <ul></ul>
            </div>
        </div>
        <!--天气插件，基于和风天气接口制作-->
        <script src="'.$cdnpublic.'/template/5iux/js/wea.js"></script>
        ';
}
?>
    </div>    
    <div id="content">
        <div class="con">
            <div class="shlogo"><?php echo $conf['home-title'] ?></div>
            <div class="sou">
               <div class="lylme">
                    <?php 
$soulists = $DB->query("SELECT * FROM `lylme_sou` WHERE `sou_st` = 1 ORDER BY `lylme_sou`.`sou_order` ASC");
$json = array();
while ($soulist = $DB->fetch($soulists)) {
		echo '<div class="ss hide"><div class="lg">' . $soulist["sou_icon"] . '</div>
        </div>';
        if (checkmobile()&& !empty($soulist["sou_waplink"])) {
        $so = $soulist["sou_waplink"];
        } else {
           $so = $soulist["sou_link"];
        }
         array_push($json,array($soulist['sou_name'],$soulist['sou_hint'],$so));
}
$json = json_encode($json,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)
?>

<input class="wd soinput" type="text" placeholder="" name="q" x-webkit-speech lang="zh-CN" autocomplete="off">
<button onclick="go('');"><svg class="icon" style=" width: 21px; height: 21px; opacity: 0.5;" aria-hidden="true"><use xlink:href="#icon-sousuo"></use></svg></button>
          
                <div id="word"></div>
            </div>
        </div>
        <div class="foot">
<?php
if ($conf['yan'] == 'true') {
	echo '<p class="content">' . yan().'</p>'; 
}
$i= 0;
$tagslists = $DB->query("SELECT * FROM `lylme_tags`");
while($taglists = $DB->fetch($tagslists)) {
	echo '<a class="nav-link" href="' . $taglists["tag_link"] . '"';
	if ($taglists["tag_target"] == 1) echo ' target="_blant"';
	echo '>' . $taglists["tag_name"] . '</a>';
	if($i<$DB->num_rows($tagslists)-1) {
		$i++;
		echo ' | ';
	}
}
?> 
  <!--网站统计-->
 <?php if(!empty($conf['wztj'])) {
	echo '<p>'.$conf["wztj"].'</p>';
}
?>
 <!--备案信息-->
   <?php if(!empty($conf['icp'])) {
	echo '<p><img src="./assets/img/icp.png" width="16px" height="16px" /><a href="http://beian.miit.gov.cn/"  class="icp nav-link" target="_blank" _mstmutation="1" _istranslated="1">'.$conf['icp'].'</a></p>';
}
?> 
  <!--版权信息-->
  <!-- <p> Theme by <a href="https://github.com/5iux/sou/" target="_blank">5iux</a> .<?php echo $conf['copyright'];
?></p>  -->
    </div>
<script src="<?php echo $cdnpublic ?>/assets/js/svg.js"></script>
<script src="<?php echo $templatepath;?>/js/sou.js?v=20221210"></script>

<script>
function solist(){
    return  <?php echo $json?>;
}
</script>
<!--
作者:D.Young
主页：https://blog.5iux.cn/
github：https://github.com/5iux/sou
日期：2020-11-23
版权所有，请勿删除
-->
</body>
</html>