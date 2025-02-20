<!DOCTYPE html>

<html>

<head>
	<meta charset="utf-8" />
	<meta name="renderer" content="webkit|ie-comp|ie-stand">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
	<meta name="applicable-device" content="pc,mobile">
	<meta name="author" content="BaiSu" />
	<title><?php echo $conf['title'] ?></title>
	<meta name="keywords" content="<?php echo $conf['keywords'] ?>" />
	<meta name="description" content="<?php echo $conf['description'] ?>" />
	<link rel="icon" href="<?php echo $conf['logo'] ?>" type="image/x-icon">
	<link rel="stylesheet" type="text/css" href="<?php echo $templatepath; ?>/css/style.css?v=20240414" />
	<meta name="lsvn" content="<?php echo base64_encode($conf['version']) ?>">
	<style>
		<?php
		if (!empty(theme_config('group_font_weight', ""))) {
			echo '.site-main .site-name{font-weight: '.theme_config('group_font_weight', "normal").'}';
		}
		if (!empty(theme_config('url_font_weight', ""))) {
			echo '.site-main .site-list .list p.name{font-weight: '.theme_config('url_font_weight', "normal").'}';
		}
		?>
	</style>
</head>

<body>
	<!--手机顶部 S-->
	<div class="m-header">
		<div class="logo">
			<a href="/" class="logo-link text-base">
				<?php
				if (theme_config('logo_show', "1") == '1') {
					echo '<img src="' . $conf['logo'] . '" class="hide-mb-sm">';
				}

				echo explode("-", $conf['title'])[0]; ?> </a>

		</div>
		<div class="navbar">
			<i class="iconfont icon-caidan"></i>
		</div>
		<div class="m-navlist-w">
			<div class="m-navlist">
				<?php
				//输出导航菜单
				$tagslists = $site->getTags();
				while ($taglists = $DB->fetch($tagslists)) {
					echo '<a href="' . $taglists["tag_link"] . '" class="list catlist"';
					if ($taglists["tag_target"] == 1) {
						echo ' target="_blank"';
					}
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
			<a href="/" class="logo-link text-base">
				<?php
				if (theme_config('logo_show', "1") == '1') {
					echo '<img src="' . $conf['logo'] . '" class="hide-mb-sm">';
				}

				echo explode("-", $conf['title'])[0]; ?> </a>
		</div>
		<div class="type-list">
			<?php
			$tagslists = $site->getTags();
			while ($taglists = $DB->fetch($tagslists)) {
				echo '
<div class="list">
	<a href="' . $taglists["tag_link"] . '" class="list catlist"';
				if ($taglists["tag_target"] == 1) {
					echo ' target="_blank"';
				}
				echo '><i class="iconfont icon-charulianjie"></i>' . $taglists["tag_name"] . '</a>	</div> ';
			}
			?>
			<p style="margin: 10px;color: #000;font-weight: bold;font-size:16px">分组</p>
			<?php

			$groups = $site->getGroups();
			while ($group = $DB->fetch($groups)) {
				echo '<div class="list">
	<a href="#category-' . $group["group_id"] . '" class="list catlist">
							'  . $group["group_name"] . '</a>
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
			<div class="date-main" <?php if (background()) {
										echo 'style="background-image: url(' . background() . ')"';
									} ?>>
				<time class="times" id="nowTime"></time>
				<span class="dates" id="nowYmd"></span>
				<div class="list">
					<span class="lunars" id="nowLunar">
					</span>
					<span class="weeks" id="nowWeek"></span>
				</div>
			</div>
		</div>

		<div class="search-main">
			<div class="search-input">
				<input type="text" class="kw" name="search" id="search" value="" class="kw" placeholder="请输入搜索内容" autocomplete="off" />
				<!--<button class="search-bendi"><i class="iconfont icon-sousuo"></i></button>-->
			</div>
			<div class="search-btnlist">
				<?php
				$soulists = $site->getSou();
				while ($soulist = $DB->fetch($soulists)) {
					if ($soulist["sou_st"] == 1) {
						if (!$fso) {
							echo '<button class="search-btn" data-url="';
							if (checkmobile() && $soulist["sou_waplink"] != null) {
								echo $soulist["sou_waplink"];
							} else {
								echo $soulist["sou_link"];
							}
							echo '">' . $soulist["sou_icon"] . $soulist["sou_name"] . '</button>
						<button class="search-change"><i class="iconfont icon-xiangxia"></i></button>
						<div class="search-lists hide">  
						 ';
							$fso = true;
						}
						echo '	<div class="list" data-url="';
						if (checkmobile() && $soulist["sou_waplink"] != null) {
							echo $soulist["sou_waplink"];
						} else {
							echo $soulist["sou_link"];
						}
						echo '">' . $soulist["sou_icon"] . $soulist["sou_name"] . '
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
			<input type="text" name="search" id="search" value="" class="kw" placeholder="请输入搜索内容" autocomplete="off" />
			<button><i class="iconfont icon-sousuo"></i></button>
		</div>
	</div>
	<!--搜索 E-->


	<div class="site-main">
		<?php

		if ($conf['yan'] == 'true') {
			echo '<p class="content">' . yan() . '</p>';
		}

		if (theme_config('lytoday', 0) == 1) {
			echo theme_config('lytodaycode');
		}
		include "list.php";
		if (theme_config('lytoday', 0) == 2) {
			echo theme_config('lytodaycode');
		}
		?>
	</div>

	</div>
	</div>
	<!--中间主体 E-->
	<!--底部版权 S-->
	<footer>
		<!--网站统计-->
		<?php if (!empty($conf['wztj'])) {
			echo '<p>' . $conf["wztj"] . '</p>';
		}
		?>
		<!--备案信息-->
		<?php
		if (!empty(theme_config('gonganbei', ""))) {

			preg_match_all('/\d+/', theme_config('gonganbei'), $gab);

			echo '<a class="icp" href="http://www.beian.gov.cn/portal/registerSystemInfo?recordcode=' . $gab[0][0] . '" target="_blank" rel="nofollow noopener">
		<img src="/assets/img/icp.png" alt="公安网备" width="11" height="11">&nbsp;' . theme_config('gonganbei') . ' </a>';
		}
		if (!empty($conf['icp'])) {
			echo '<a href="http://beian.miit.gov.cn/" rel="nofollow" class="icp nav-link" target="_blank" _mstmutation="1" _istranslated="1">' . $conf['icp'] . '</a>';
		}
		?>
		<p>Theme By <a href="https://gitee.com/baisucode/baisu-two" target="_blank">BaiSu</a>. <?php echo $conf['copyright'] ?></p>
	</footer>
	<!--底部版权 E-->
	<!--返回顶部 S-->
	<div class="tool-list">
		<div class="scroll_top list">
			<i class="iconfont icon-top"></i>
		</div>
	</div>
	<!--返回顶部 E-->

	<!--iconfont-->
	<link rel="stylesheet" type="text/css" href="//at.alicdn.com/t/font_3000268_oov6h4vru0h.css" />
	<script src="//at.alicdn.com/t/font_3000268_oov6h4vru0h.js" type="text/javascript" charset="utf-8"></script>
	<!--JS-->
	<script src="<?php echo $cdnpublic ?>/assets/js/jquery.min.js"></script>
	<script src="<?php echo $templatepath; ?>/js/holmes.js" type="text/javascript" charset="utf-8"></script>
	<script src="<?php echo $templatepath; ?>/js/lunar.js" type="text/javascript" charset="utf-8"></script>
	<script src="<?php echo $templatepath; ?>/js/common.js?v=20240414" type="text/javascript" charset="utf-8"></script>
	<script src="<?php echo $cdnpublic ?>/assets/js/svg.js"></script>
</body>

</html>
<!--二开说明：-->
<!--1. 当前主题使用基于baisuTwo主题开发，作者：baisu-->
<!--2. 原项目地址https://gitee.com/baisucode/baisu-two-->
<!--3. 二开作者：六零-->
<!--4. 修改了适配LyLme Spage，修改了部分CSS，删除不适用与本项目的代码-->