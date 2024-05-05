<!DOCTYPE html>

<html lang="zh-CN" element::-webkit-scrollbar {display:none}>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
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
	<script src="<?php echo $cdnpublic ?>/assets/js/jquery.min.js" type="application/javascript"></script>
	<link href="<?php echo $cdnpublic ?>/assets/css/bootstrap.min.css" type="text/css" rel="stylesheet">
	<link rel="stylesheet" href="<?php echo $templatepath; ?>/css/style.css?v=20240414" type="text/css">
</head>

<body>
	<div class="banner-video">

		<?php
		if (theme_config('background_position', 0) == 1) {
			echo '<style>.banner-video{position: fixed !important;}</style>';
		}
		if (!empty(background())) {
			echo '<img src="' . background() . '">';
		} ?>

		<div class="bottom-cover" style="background-image: linear-gradient(rgba(255, 255, 255, 0) 0%, rgb(244 248 251 / 0.6) 50%, rgb(244 248 251) 100%);">
		</div>
	</div>

	<div class="box">
		<div class="change-type">
			<div class="type-left" id="type-left">
				<ul>
					<li data-lylme="search"><a>搜索</a><span></span></li>
					<?php
					$groups = $site->getGroups(); // 获取分类
					while ($group = $DB->fetch($groups)) { //循环所有分组

						echo '<li data-lylme="group_' . $group["group_id"] . '"><a>' . $group["group_name"] . '</a><span></span></li>' . "\n";
					}
					?>
				</ul>
			</div>

		</div>
	</div>

	<!--topbar开始-->
	<nav class="navbar navbar-expand-lg navbar-light fixed-top" style="position: absolute; z-index: 10000;">
		<!--<a class="navbar-brand" href="/"><img src="./assets/img/logo.png" height="25"  title="LyLme_Spage"></a>-->
		<button class="navbar-toggler collapsed" style="border: none; outline: none;" type="button" data-toggle="collapse" data-target="#navbarsExample05" aria-controls="navbarsExample05" aria-expanded="false" aria-label="Toggle navigation">
			<svg class="icon" width="200" height="200">
				<use xlink:href="#icon-menus"></use>
			</svg>
		</button>

		<div class="type-right">
			<svg t="1711940240250" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="7074" width="200" height="200">
				<path d="M512 0c281.6 0 512 230.4 512 512s-230.4 512-512 512S0 793.6 0 512 230.4 0 512 0z m192.96 261.888l-259.904 78.784c-51.2 13.76-90.592 53.184-106.336 104.384L259.936 704.96c-5.888 15.776 0 33.472 11.808 45.312 7.872 9.824 21.696 13.76 33.472 13.76 3.936 0 9.856 0 13.792-1.984l259.904-78.784c51.2-13.76 90.592-53.184 106.336-104.384l78.784-259.904c5.888-15.776 0-33.472-11.808-45.312-13.792-13.76-31.52-17.728-47.296-11.808v-0.032z m-96.448 295.424a79.872 79.872 0 0 1-53.184 53.152l-200.864 59.072 61.056-202.816a79.904 79.904 0 0 1 53.184-53.184l200.864-59.072-61.056 202.848zM472.64 512a39.36 39.36 0 1 0 78.72 0 39.36 39.36 0 0 0-78.72 0z" fill="#dbdbdb" p-id="7075" data-spm-anchor-id="a313x.search_index.0.i6.29583a81RxO4Dj" class="selected"></path>
			</svg>
		</div>
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
			<div id="main">
				<div id="show_date"></div>
				<div id="show_time"></div>
			</div>
		</div>

	</nav>
	<!--topbar结束-->
	<div class="container" style="margin-top:10vh; position: relative; z-index: 100;">
		<?php
		echo theme_config('home_title');
		if ($conf['yan'] == 'true') {
			echo '<p class="content">' . yan() . '</p>';
		}
		?>
		<!--搜索开始-->
		<div id="search" class="s-search">
			<div id="search-list" class="hide-type-list">
				<div class="search-group group-a s-current" style=" margin-top: 50px;">
					<ul class="search-type">
						<?php
						$soulists = $site->getSou();
						while ($soulist = $DB->fetch($soulists)) {
							if ($soulist["sou_st"] == 1) {
								echo '	<li>
								<input hidden=""  checked="" type="radio" name="type" id="type-' . $soulist["sou_alias"] . '" value="';
								if (checkmobile() && !empty($soulist["sou_waplink"])) {
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
				</div>
			</div>
			<form action="https://www.bing.com/search?q=" method="get" target="_blank" id="super-search-fm">
				<input type="text" id="search-text" placeholder="搜索一下" style="outline:0" autocomplete="off">
				<button class="submit" type="submit">
					<svg style="width: 22px; height: 22px; margin: 0 20px 0 20px; color: #fff;" class="icon" aria-hidden="true" viewBox="0 0 1024 1024">
						<use xlink:href="#icon-sousuo">
						</use>
					</svg>
					<span>
				</button>

				<ul id="word" style="display: none;">
				</ul>
			</form>
			<div class="set-check hidden-xs">
				<input type="checkbox" id="set-search-blank" class="bubble-3" autocomplete="off">
			</div>
		</div>

		<?php
		if (theme_config('lytoday', 0) == 1) {
			echo theme_config('lytodaycode');
		}
		include "list.php";
		if (theme_config('lytoday', 0) == 2) {
			echo theme_config('lytodaycode');
		}
		include "footer.php";
		?>