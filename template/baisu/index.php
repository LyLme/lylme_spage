<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta name="renderer" content="webkit|ie-comp|ie-stand">
		<meta http-equiv="X-UA-Compatible" content="IE=Edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
		<meta http-equiv="Cache-Control" content="no-transform">
		<meta name="applicable-device" content="pc,mobile">
		<meta name="MobileOptimized" content="width">
		<meta name="HandheldFriendly" content="true">
		<meta name="author" content="BaiSu" />
		<title><?php echo $conf['title']?></title>
		<meta name="keywords" content="<?php echo $conf['keywords']?>" />
		<meta name="description" content="<?php echo $conf['description']?>" />
		<link rel="icon" href="<?php echo $conf['logo']?>" type="image/x-icon">
		<link rel="stylesheet" type="text/css" href="<?php echo $templatepath;?>/css/style.css?v=20220512" />
		<link rel="stylesheet" href="<?php echo $cdnpublic ?>/assets/css/fontawesome-free5.13.0.css">
		<meta name="lsvn" content="<?php echo base64_encode($conf['version'])?>">
	</head>
	<body>
		<!--手机顶部 S-->
		<div class="m-header">
			<div class="logo">
				<a href="/"><?php echo explode("-", $conf['title'])[0];
?></a>
			</div>
			<div class="navbar">
				<i class="iconfont icon-caidan"></i>
			</div>
			<div class="m-navlist-w">
				<div class="m-navlist">
<?php
//输出导航菜单
$tagslists = $DB->query("SELECT * FROM `lylme_tags`");
while ($taglists = $DB->fetch($tagslists)) {
	echo '<a href="' . $taglists["tag_link"] . '" class="list catlist"';
	if ($taglists["tag_target"] == 1) echo ' target="_blant"';
	echo '><b>' . $taglists["tag_name"] . '</b></a>';
}
?>
				</div>
			</div>
		</div>
		<!--手机顶部 E-->
		<!--左侧分类栏 S-->
		<div class="index-nav">
			<div class="logo">
				<a href="/"><?php echo explode("-", $conf['title'])[0];
?></a>
			</div>
				

			<div class="type-list">
		<?php
$tagslists = $DB->query("SELECT * FROM `lylme_tags`");
while ($taglists = $DB->fetch($tagslists)) {
	echo '
<div class="list">
	<a href="' . $taglists["tag_link"] . '" class="list catlist"';
	if ($taglists["tag_target"] == 1) echo ' target="_blant"';
	echo '>' . $taglists["tag_name"] . '</a>	</div> ';
}
?>				
			<hr><p style="margin: 10px;color: #000;font-weight: bold;font-size:18px">分组</p>				
<?php
$groups = $DB->query("SELECT * FROM `lylme_groups` ORDER BY `group_order` ASC");
while ($group = $DB->fetch($groups)) {
	echo '<div class="list">
	<a href="#category-' . $group["group_id"] . '" class="list catlist">
							' . $group["group_icon"]  . $group["group_name"] . '</a>
							</div>';
}
?>
			</div>	
		</div>
		<!--左侧分类栏 E-->
		<!--中间主体 S-->
		<div class="index-main">
			<!--搜索 S-->
			<div class="search-main-w">
				<div class="date-main" 
				<?php if(background()){
				echo 'style="background-image: url('.background().')"';
				}?>>
					<time class="times" id="nowTime">00:00:00</time>
					<span class="dates" id="nowYmd">2022年01月01日</span>
					<div class="list">
						<span class="lunars" id="nowLunar">辛丑年十一月廿九
</span>
						<span class="weeks" id="nowWeek">星期六</span>
					</div>
				</div>
				<div class="weather-main" id="he-plugin-standard"></div>
			</div>
		
<div class="search-main">
					<div class="search-input">
						<input type="text" class="kw" name="search" id="search" value="" class="kw" placeholder="请输入搜索内容" autocomplete="off" />
						<!--<button class="search-bendi"><i class="iconfont icon-sousuo"></i></button>-->
					</div>
					<div class="search-btnlist">
<?php
$soulists = $DB->query("SELECT * FROM `lylme_sou` ORDER BY `lylme_sou`.`sou_order` ASC");
while ($soulist = $DB->fetch($soulists)) {
	if ($soulist["sou_st"] == 1) {
		if(!$fso) {
			echo '<button class="search-btn" data-url="';
			if (checkmobile()&& $soulist["sou_waplink"] != NULL) {
				echo $soulist["sou_waplink"];
			} else {
				echo $soulist["sou_link"];
			}
			echo '">'. $soulist["sou_icon"] . $soulist["sou_name"] . '</button>
						<button class="search-change"><i class="iconfont icon-xiangxia"></i></button>
						<div class="search-lists hide">   ';
			$fso = true;
		}
		echo '	<div class="list" data-url="';
		if (checkmobile()&& $soulist["sou_waplink"] != NULL) {
			echo $soulist["sou_waplink"];
		} else {
			echo $soulist["sou_link"];
		}
		echo '">'. $soulist["sou_icon"] . $soulist["sou_name"] . '
							</div>';
	}
}
?>	

							<div class="list kongs"></div>
						</div>
					</div>
						<ul id="word" style="display:none">
					</ul>
				</div>
			<div class="search">
				<div class="list">
					<input type="text" name="search" id="search" value="" class="kw" placeholder="输入关键词进行搜索，回车键百度搜索" autocomplete="off" />
					<button><i class="iconfont icon-sousuo"></i></button>
				</div>
			</div>
			<!--搜索 E-->


		<div class="site-main">
		    <?php
			
if ($conf['yan'] == 'true') {
	echo '<p class="content">' . yan().'</p>'; 
}


include'list.php';?>
        </div>

			</div>
		</div>
		<!--中间主体 E-->
		<!--底部版权 S-->
		<footer>
		      <!--网站统计-->
 <?php if(!empty($conf['wztj'])) {
	echo '<p>'.$conf["wztj"].'</p>';
}
?>
 <!--备案信息-->
   <?php if(!empty($conf['icp'])) {
	echo '<p><img src="./assets/img/icp.png" width="16px" height="16px" /><a href="http://beian.miit.gov.cn/" rel="nofollow" class="icp nav-link" target="_blank" _mstmutation="1" _istranslated="1">'.$conf['icp'].'</a></p>';
}
?>
		 <p>Theme By <a href="https://gitee.com/baisucode/baisu-two" target="_blank">BaiSu</a>. <?php echo $conf['copyright']?></p>
		</footer>
		<!--底部版权 E-->
		<!--返回顶部 S-->
		<div class="tool-list">
				<div class="scroll_top list">
					<i class="iconfont icon-top"></i>
				</div>
		</div>
		<!--返回顶部 E-->
		<?php if ($conf['tq']=='true'){?>
		<!--天气代码替换处 S-->
		<script type="text/javascript">
			WIDGET = {
	            "CONFIG": {
		            "layout": "1",
			    	"width": "240",
				    "height": "180",
				    "background": "1",
			    	"dataColor": "FFFFFF",
				    "borderRadius": "6",
				    "modules": "10",
				    "key": "7423b182d5cb48239f19df9e25cdf320" 
				    //和风天气秘钥申请地址：https://widget.qweather.com/create-standard/
            	}
            }
        </script>
		<script src="https://widget.qweather.net/standard/static/js/he-standard-common.js?v=2.0"></script>
		<!--天气代码替换处 E-->
		<?php }else{echo '<style>.search-main-w {display: none;} @media only screen and (max-width: 1200px){.search-main {padding-top:70px !important;}}</style>';}?>
		<!--iconfont-->
		<link rel="stylesheet" type="text/css" href="//at.alicdn.com/t/font_3000268_oov6h4vru0h.css" />
		<script src="//at.alicdn.com/t/font_3000268_oov6h4vru0h.js" type="text/javascript" charset="utf-8"></script>
		<!--JS-->
		<script src="https://lf26-cdn-tos.bytecdntp.com/cdn/expire-2-M/jquery/3.5.1/jquery.min.js" charset="utf-8"></script>
		<script src="https://lf26-cdn-tos.bytecdntp.com/cdn/expire-1-M/layer/3.5.1/layer.js" type="application/javascript"></script>
		<script src="<?php echo $templatepath;?>/js/holmes.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $templatepath;?>/js/lunar.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $templatepath;?>/js/common.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $cdnpublic ?>/assets/js/svg.js"></script>
    </body>
</html>
<!--二开说明：-->
<!--1. 当前主题使用基于baisuTwo主题开发，作者：baisu-->
<!--2. 原项目地址https://gitee.com/baisucode/baisu-two-->
<!--3. 二开作者：六零
<!--4. 修改了适配LyLme Spage，修改了部分CSS，删除不适用与本项目的代码-->