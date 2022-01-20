<!DOCTYPE html>
<html lang="zh-CN" element::-webkit-scrollbar {display:none}>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1"
		/>
		<title>
			上网导航 - LyLme Start Page
		</title>
		<meta name="keywords" content="六零起始页,百度搜索,哔哩哔哩搜索,知乎搜索,六零导航,LyLme Start Page,六零,LyLme小窝,LyLme,叮当云,网站导航,上网导航">
		<meta name="description" content="六零起始页(LyLme Start Page)致力于简洁高效无广告的上网导航和搜索入口，沉淀最具价值链接，全站无商业推广，简约而不简单。">
		<link rel="icon" sizes="any" mask href="./assets/img/logo.png">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-touch-fullscreen" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="full-screen" content="yes">
		<link rel="stylesheet" href="./assets/css/style.css">
		<meta name="browsermode" content="application">
		<meta name="x5-fullscreen" content="true">
		<meta name="x5-page-mode" content="app">
		<script src="https://lf3-cdn-tos.bytecdntp.com/cdn/expire-2-M/jquery/3.5.1/jquery.min.js"
		type="application/javascript">
		</script>
		<link href="https://lf26-cdn-tos.bytecdntp.com/cdn/expire-1-M/bootstrap/4.5.3/css/bootstrap.min.css"
		type="text/css" rel="stylesheet">
		<link rel="stylesheet" id="font-awesome-css" href="./assets/css/fontawesome-free5.13.0.css"
		type="text/css" media="all">
		<script src="./assets/js/svg.js">
		</script>
		<script src="./assets/js/script.js">
		</script>
			<script>
		(function(a) {
			a.fn.scrollToTop = function(c) {
				var d = {
					speed: 800
				};
				c && a.extend(d, {
					speed: c
				});
				return this.each(function() {
					var b = a(this);
					a(window).scroll(function() {
						100 < a(this).scrollTop() ? b.fadeIn() : b.fadeOut()
					});
					b.click(function(b) {
						b.preventDefault();
						a("body, html").animate({
							scrollTop: 0
						},
						d.speed)
					})
				})
			}
		})(jQuery);
		$(function() {
			ahtml = '<a href="javascript:void(0)" title="回到顶部" id="toTop" style="display:none;position:fixed;bottom:66px;right:15px;width:50px;height:50px;border-radius:50%;overflow:hidden;background-image:url(\'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABcAAAAWBAMAAADZWBo2AAAALVBMVEUAAAB5eXl5eXl5eXl5eXl5eXl5eXl5eXl5eXl5eXl5eXl5eXl5eXl5eXl5eXl4rtNiAAAADnRSTlMARHe7Zu7dMxGIIqqZzHSj3DwAAAB/SURBVBjTYwADPgYk8OABgs2HLPUAjBA6+JAk4FJ8UJLqYLKxsQNTXhIDs/GWBoZCPcEFeop6CnyKvhMYGOQYGJIYmBL4BBgfgDjsrxi4nvMJsCSAOCChh3yHjjqAZV4wcDznO6TFANYTwsASwCfAAOFMFRCdAOd0v3vdAOIAANnHHKk0/kXuAAAAAElFTkSuQmCC\');background-repeat:no-repeat;background-position:center;z-index:999;cursor:pointer;border:1px solid #d8d8d8;box-sizing:border-box;opacity:0.9;"></a>';
			$("body").append(ahtml);
			$("#toTop").scrollToTop(300);
		});
	</script>
	</head>