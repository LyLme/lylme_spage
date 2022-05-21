<?php
$t=strtolower(urlencode($_GET["t"])); //搜索引擎
$q=urlencode($_POST["q"]); //搜索词
if (!empty($q)) {
	if($soulist = $DB->fetch($DB->query("SELECT * FROM `lylme_sou` WHERE `sou_alias` LIKE '".$t."'"))) {
		if (checkmobile()&& !empty($soulist["sou_waplink"])) {
			echo'<script>window.location.href="'.$soulist["sou_waplink"].$q.'";</script>';
		} else {
			echo'<script>window.location.href="'.$soulist["sou_link"].$q.'";</script>';
		}
	} else {
		echo'<script>window.location.href="https://www.baidu.com/s?word='.$q.'";</script>';
	}
}
?>
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
  <link href="<?php echo $templatepath;?>/css/style.css" rel="stylesheet">
  <link href="<?php echo $templatepath;?>/css/wea.css" rel="stylesheet">
  <script src="https://lf3-cdn-tos.bytecdntp.com/cdn/expire-2-M/jquery/3.5.1/jquery.min.js"></script>
</head>
<?php if(!empty(background())){
	echo '<body style="background: url('.background().') no-repeat center/cover;">';}
	else{ echo '<body>';}?>
    <div id="menu"><i></i></div>
    <div class="list closed">
<?php        
session_start(); //设置session
$groups = $DB->query("SELECT * FROM `lylme_groups` ORDER BY `group_order` ASC");
// 获取分类
$i = 0;
while ($group = $DB->fetch($groups)) {
	//循环所有分组
	if($group["group_status"]=='0'){continue;}
	if(!in_array($group['group_pwd'],$_SESSION['list'])&&!empty($group['group_pwd'])){
	    //如果 分组加密未在认证列表 并且分组设置了密码(不显示分组)
	    continue;
	}
	$sql = "SELECT * FROM `lylme_links` WHERE `group_id` = " . $group['group_id']." ORDER BY `link_order` ASC;";
	$group_links = $DB->query($sql);
	$link_num = $DB->num_rows($group_links);
	// 获取返回字段条目数量
	echo '<ul class="mylist row"><li class="title">' . $group["group_icon"] . '<sapn>' . $group["group_name"] . '</sapn></li>';
	//输出分组图标和标题
	if ($link_num == 0) {
		echo '</ul>' . "\n";
		$i = 0;
		continue;
	}
	while ($link = $DB->fetch($group_links)) {
		// 循环每个链接
		// 返回指定分组下的所有字段
		$lpwd = true;
		if ($link_num > $i) {
			$i = $i + 1;
			if(!empty($group['group_pwd'])&&!empty($link['link_pwd'])){
			   //分组和链接同时加密
			   //忽略链接加密正常显示分组
			}
    		else if(!in_array($link['link_pwd'],$_SESSION['list'])&&!empty($link['link_pwd'])){
    			    //当前链接加密
    	            $lpwd = false;
    	   }
    		if($link["link_status"]!="0" && $lpwd ){
			echo "\n" . '<li class="col-3 col-sm-3 col-md-3 col-lg-1"><a rel="nofollow" href="' . $link["url"] . '" target="_blank">';
			if ($link["icon"] == '') {
				echo '<img src="/assets/img/default-icon.png" alt="默认' . $link["name"] . '" />';
			} else if (!preg_match("/^<svg*/", $link["icon"])) {
				echo '<img src="' . $link["icon"] . '" alt="' . $link["name"] . '" />';
			} else {
				echo $link["icon"];
			}
			echo '<span>' . $link["name"] . '</span></a></li>';
			//输出图标和链接
			}
		}
		if ($link_num == $i) {
			//判断当前分组链接循环完毕
			echo '</ul>' . "\n";
			//输出分类结束标签
			$i = 0;
			break;
			//重置$i为0跳出当前循环
		}
	}
}
?>  
    </div>
<?php if ($conf['tq'] != 'false') {
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
                <form action="" method="post"  target="_blank">
                    <?php 
$soulists = $DB->query("SELECT * FROM `lylme_sou` ORDER BY `lylme_sou`.`sou_order` DESC");
$i = 0;
while ($soulist = $DB->fetch($soulists)) {
	if ($soulist["sou_st"] == 1 && $soulist["sou_alias"] == $t) {
		if(empty($soun)) {
			$alias = $DB->fetch($DB->query("SELECT * FROM `lylme_sou` WHERE `sou_alias` NOT LIKE '".$soulist["sou_alias"]."' ORDER BY `sou_order` ASC LIMIT 1"));
			$soun = $alias["sou_alias"];
		}
		echo '<div class="lg" onclick="window.location.href=\'?t='.$soun.'\';">' . $soulist["sou_icon"] . '</div>
        <input class="wd" type="text" placeholder="' . $soulist["sou_hint"] . '" name="q" x-webkit-speech lang="zh-CN" autocomplete="off">';
		$sousw = 1;
	}
	if ($soulist["sou_st"] == 1) {
		$soun = $soulist["sou_alias"];
	}
	if(empty($soun))break;
}
if(empty($sousw)||empty($soun)) {
	$alias = $DB->fetch($DB->query("SELECT * FROM `lylme_sou` WHERE `sou_alias` NOT LIKE 'baidu' ORDER BY `sou_id` DESC LIMIT 1"));
	echo '<div class="lg" onclick="window.location.href=\'?t='.$alias["sou_alias"].'\';"><svg class="icon" aria-hidden="true"><use xlink:href="#icon-icon_baidulogo"></use></svg></div>
     <input class="wd" type="text" placeholder="百度一下，你就知道" name="q" x-webkit-speech lang="zh-CN" autocomplete="off">
    ';
}
?>
                    <button><svg class="icon" style=" width: 21px; height: 21px; opacity: 0.5;" aria-hidden="true"><use xlink:href="#icon-sousuo"></use></svg></button>
                </form>
                <div id="word"></div>
            </div>
        </div>
        <div class="foot">
<?php
if ($conf['yan'] != 'false') {
	$filename = ROOT.'assets/data/data.dat';
	//随机一言文件路径
	if (file_exists($filename)) {
		$data = explode(PHP_EOL, file_get_contents($filename));
		$result = str_replace(array(
		            "\r",
		            "\n",
		            "\r\n"
		        ) , '', $data[array_rand($data) ]);
		echo '<p class="content">[ ' . $result.' ]</p>';
	}
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
  <p> Theme by <a href="https://github.com/5iux/sou/" target="_blank">5iux</a> .<?php echo $conf['copyright'];
?></p>
    </div>
<script src="<?php echo $cdnpublic ?>/assets/js/svg.js"></script>
<script src="<?php echo $templatepath;?>/js/sou.js"></script>

<?php
if(empty($t)) {echo '<script>var sou = "?t="+localStorage.getItem("sou");window.location.href=sou;</script>';}
echo '<script>localStorage.setItem("sou", "'.$t.'");</script>';
?>
<!--
作者:D.Young
主页：https://blog.5iux.cn/
github：https://github.com/5iux/sou
日期：2020-11-23
版权所有，请勿删除
-->
</body>
</html>