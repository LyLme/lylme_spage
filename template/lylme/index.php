<!DOCTYPE html>
<html lang="zh-CN" element::-webkit-scrollbar {display:none}>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $conf['title'] ?></title>
	<meta name="keywords" content="<?php echo $conf['keywords'] ?>">
	<meta name="description" content="<?php echo $conf['description'] ?>">
	<meta name="author" content="LyLme">
	<link rel="icon" href="<?php echo $conf['logo'] ?>" type="image/x-icon">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-touch-fullscreen" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="full-screen" content="yes">
	<meta name="browsermode" content="application">
	<meta name="x5-fullscreen" content="true">
	<meta name="x5-page-mode" content="app">
	<meta name="lsvn" content="<?php echo base64_encode($conf['version']) ?>">

	<link href="<?php echo $cdnpublic ?>/assets/css/bootstrap.min.css" type="text/css" rel="stylesheet">
	<script src="<?php echo $cdnpublic ?>/assets/js/jquery.min.js" type="application/javascript"></script>
	<script src="<?php echo $cdnpublic ?>/assets/js/bootstrap.min.js" type="application/javascript"></script>
	<link rel="stylesheet" href="<?php echo $templatepath; ?>/css/style.css?v=20240409" type="text/css">
</head>
<?php if (!empty(background())) {
	echo '<body onload="FocusOnInput()" style="background-image: url(' . background() . ');background-size: cover;">';
} else {
	echo '<body onload="FocusOnInput()">';
} ?>

<nav class="navbar navbar-expand-lg navbar-light fixed-top" style="position: absolute; z-index: 10000;">
	<button class="navbar-toggler collapsed" style="border: none; outline: none;" type="button" data-toggle="collapse" data-target="#navbarsExample05" aria-controls="navbarsExample05" aria-expanded="false" aria-label="Toggle navigation">

		<svg class="icon" width="200" height="200">
			<use xlink:href="#icon-menus"></use>
		</svg>
		<span><svg class="bi bi-x" fill="currentColor" id="x">
				<use xlink:href="#icon-closes"></use>
			</svg><span>
	</button>
	<div class="collapse navbar-collapse" id="navbarsExample05">
		<ul class="navbar-nav mr-auto">
			<?php
			$tagslists = $site->getTags();
			while ($taglists = $DB->fetch($tagslists)) {
				echo '<li class="nav-item"><a class="nav-link" href="' . $taglists["tag_link"] . '"';
				if ($taglists["tag_target"] == 1) {
					echo ' target="_blank"';
				}
				echo '>' . $taglists["tag_name"] . '</a></li>
				    ';
			}
			?>
		</ul>

	</div>
</nav>
<!--topbar结束-->
<div class="container" style="margin-top:10vh; position: relative; z-index: 100;">

	<div id="main">
		<div id="show_time"></div>
		<div id="show_date"></div>

	</div>
	<?php
	if ($conf['yan'] == 'true') {
		echo '<p class="content">' . yan() . '</p>';
	}
	?>
	</p>
	<!--搜索开始-->
	<div id="search" class="s-search">
		<div id="search-list" class="hide-type-list">
			<div class="search-group group-a s-current">



				<div class="search-box">
					<div id="search-lylme">
						<form action="https://www.bing.com/search?q=" method="get" target="_blank" id="super-search-fm">
							<div id="checke-so" onclick="lylme()">
								<svg class="lylme" viewBox="0 0 1024 1024" aria-hidden="true">
									<use xlink:href="#icon-icon_baidulogo"></use>
								</svg>
								<svg class="sw" id="lylme-up" style="display:inline" aria-hidden="true">
									<use xlink:href="#icon-up"></use>
								</svg>
								<svg class="sw" id="lylme-down" style="display:none" aria-hidden="true">
									<use xlink:href="#icon-down"></use>
								</svg>
							</div>


							<input type="text" id="search-text" placeholder="百度一下，你就知道" style="outline:0" autocomplete="off">
							<button class="submit" id="search-submit" type="submit">
								<svg style="width: 22px; height: 22px; margin: 0 20px 0 20px; color: #fff;" class="icon" aria-hidden="true">
									<use xlink:href="#icon-sousuo">
									</use>
								</svg>
								<span>
							</button>
					</div>

					</form>
				</div>
			</div>




		</div>
		<ul class="search-type" id="chso">
			<?php
			$soulists = $site->getSou();
			while ($soulist = $DB->fetch($soulists)) {
				if ($soulist["sou_st"] == 1) {
					echo '	<li>
								<input hidden=""  checked="" type="radio" name="type" id="type-' . $soulist["sou_alias"] . '" value="';
					if (checkmobile() && $soulist["sou_waplink"] != null) {
						echo $soulist["sou_waplink"];
					} else {
						echo $soulist["sou_link"];
					}
					echo '"data-placeholder="' . $soulist["sou_hint"] . '">
								<label for="type-' . $soulist["sou_alias"] . '" style="font-weight:600">
								' . $soulist["sou_icon"] . '
									<span style="color:' . $soulist["sou_color"] . '">
										' . $soulist["sou_name"] . '
									</span>
								</label>
							</li>
							';
				}
			}
			?>
		</ul>
		<div class="set-check hidden-xs">
			<input type="checkbox" id="set-search-blank" class="bubble-3" autocomplete="off">
		</div>
		<ul id="word" style="display: none;">
		</ul>
	</div>


	<?php
	include "list.php";
	include "footer.php";
	?>