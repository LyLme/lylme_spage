<?php
@header('Content-Type: text/html; charset=UTF-8');
if(!file_exists('install/install.lock')){
	exit('<title>六零导航页 - 安装</title>您还未安装，点击<a href="install"><font color="blue">这里</font></a>开始安装！');
}
include "include/head.php"; 
echo '<body onload="FocusOnInput()"><div class="banner-video">';
if ($conf['background']  == '') {
    if(!file_exists('./assets/img/background.jpg')){
        echo '<img src="./assets/img/cron.php" alt="Bing每日背景">';
    }
    else{
        echo '<img src="./assets/img/background.jpg" alt="本地背景">';
}
}
else{
       echo '<img src="'.$conf['background'].'" alt="自定义背景">';

}
?> 
			<div class="bottom-cover" style="background-image: linear-gradient(rgba(255, 255, 255, 0) 0%, rgb(244 248 251 / 0.6) 50%, rgb(244 248 251) 100%);">
			</div>
		</div>
		<!--topbar开始-->
		<nav class="navbar navbar-expand-lg navbar-light fixed-top" style="position: absolute; z-index: 10000;">
		<!--<a class="navbar-brand" href="/"><img src="./assets/img/logos.png" height="25"  title="六零起始页"></a>-->
			<button class="navbar-toggler collapsed" style="border: none; outline: none;"
			type="button" data-toggle="collapse" data-target="#navbarsExample05" aria-controls="navbarsExample05"
			aria-expanded="false" aria-label="Toggle navigation">
			<svg t="1636792421852" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="3801" width="200" height="200"><path d="M901.632 896H122.368c-30.72 0-55.808-25.088-55.808-55.808v-1.536c0-30.72 25.088-55.808 55.808-55.808h779.776c30.72 0 55.808 25.088 55.808 55.808v1.536c-0.512 30.72-25.6 55.808-56.32 55.808zM901.632 568.32H122.368c-30.72 0-55.808-25.088-55.808-55.808v-1.536c0-30.72 25.088-55.808 55.808-55.808h779.776c30.72 0 55.808 25.088 55.808 55.808v1.536c-0.512 30.72-25.6 55.808-56.32 55.808zM901.632 240.64H122.368c-30.72 0-55.808-25.088-55.808-55.808v-1.536c0-30.72 25.088-55.808 55.808-55.808h779.776c30.72 0 55.808 25.088 55.808 55.808v1.536c-0.512 30.72-25.6 55.808-56.32 55.808z" p-id="3802" fill="#ffffff"></path></svg>
				<span>
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" class="bi bi-x"
					fill="currentColor" id="x">
						<path fill-rule="evenodd" d="M11.854 4.146a.5.5 0 010 .708l-7 7a.5.5 0 01-.708-.708l7-7a.5.5 0 01.708 0z">
						</path>
						<path fill-rule="evenodd" d="M4.146 4.146a.5.5 0 000 .708l7 7a.5.5 0 00.708-.708l-7-7a.5.5 0 00-.708 0z">
						</path>
					</svg>
					<span>
			</button>
			<div class="collapse navbar-collapse" id="navbarsExample05">
				<ul class="navbar-nav mr-auto">
					<li class="nav-item">
						<a class="nav-link" href="https://www.lylme.com" target="_blant">主页</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="https://blog.lylme.com" target="_blant">博客</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="https://github.com/lylme" target="_blant">Github</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="https://blog.lylme.com/about.html" target="_blant">关于</a>
					</li>
				</ul>
				<div id="main">  
<div id="show_date"></div>  
<div id="show_time"></div>
 </div>	

				<div id="he-plugin-simple">
				</div>
				
				<script src="https://widget.qweather.net/simple/static/js/he-simple-common.js?v=2.0">
				</script>
			</div>
			
		</nav>
				<!--topbar结束-->
		<div class="container" style="margin-top:10vh; position: relative; z-index: 100;">
			<h2 class="title">上网，从这里开始!</h2>

 <?php

$filename = './assets/data/data.dat';  //随机一言文件目录

if (!file_exists($filename)) {
    die($filename . ' Error');
}
$data = file_get_contents($filename);
$data = explode(PHP_EOL, $data);
$result = $data[array_rand($data)];
$result = str_replace(array("\r", "\n", "\r\n"), '', $result);
echo '<p class="content"><b>随机一言:</b>'.$result;
?>
		</p>
			<!--搜索开始-->
			<div id="search" class="s-search">
				<div id="search-list" class="hide-type-list">
					<div class="search-group group-a s-current" style=" margin-top: 50px;">
						<ul class="search-type">
							<li>
								<input checked="" hidden="" type="radio" name="type" id="type-baidu" value="<?php if($ua == "pc"){echo 'https://www.baidu.com/s?tn=simple&word=';}else{echo 'https://m.baidu.com/s?tn=simple&word=';}?>"
								data-placeholder="百度一下，你就知道">
								<label for="type-baidu">
									<svg class="icon" aria-hidden="true">
										<use xlink:href="#icon-icon_baidulogo">
										</use>
									</svg>
									<span style="color:#0c498c;font-weight:600">
										百度一下
									</span>
								</label>
							</li>
						
							<li>
								<input hidden="" type="radio" name="type" id="type-sogou" value="https://www.sogou.com/web?query="
								data-placeholder="上网从搜狗开始">
								<label for="type-sogou" style="font-weight:600">
									<svg class="icon" aria-hidden="true">
										<use xlink:href="#icon-sougou">
										</use>
									</svg>
									<span style="color:#696a6d">
										搜狗搜索
									</span>
								</label>
							</li>
							
								<li>
								<input hidden="" type="radio" name="type" id="type-bing" value="https://cn.bing.com/search?q="
								data-placeholder="微软必应搜索引擎">
								<label for="type-bing" style="font-weight:600">
									<svg class="icon" aria-hidden="true">
										<use xlink:href="#icon-bing">
										</use>
									</svg>
									<span style="color:#696a6d">
										Bing必应
									</span>
								</label>
							</li>
							<li>
								<input hidden="" type="radio" name="type" id="type-zhihu" value="https://www.zhihu.com/search?q="
								data-placeholder="有问题，上知乎">
								<label for="type-zhihu">
									<svg class="icon" aria-hidden="true">
										<use xlink:href="#icon-zhihu">
										</use>
									</svg>
									<span style="color:#06f;font-weight:600">
										知乎搜索
									</span>
								</label>
							</li>
							<li>
								<input hidden="" type="radio" name="type" id="type-bilibili" value="https://search.bilibili.com/all?keyword="
								data-placeholder=" (゜-゜)つロ 干杯">
								<label for="type-bilibili">
							<svg class="icon" aria-hidden="true">
										<use xlink:href="#icon-bili">
										</use>
									</svg>
									<span style="color:#00aeec;font-weight:600">
										哔哩哔哩
									</span>
								</label>
							</li>
							<li>
								<input hidden="" type="radio" name="type" id="type-weibo" value="https://s.weibo.com/weibo/"
								data-placeholder="随时随地发现新鲜事">
								<label for="type-weibo">
									<svg class="icon" aria-hidden="true">
										<use xlink:href="#icon-weibo">
										</use>
									</svg>
									<span style="color:#ff5722;font-weight:600">
										微博搜索
									</span>
								</label>
							</li>
							
							<li>
								<input hidden="" type="radio" name="type" id="type-google" value="https://www.google.com.hk/search?hl=zh-CN&q="
								data-placeholder="值得信任的搜索引擎">
								<label for="type-google" style="font-weight:600">
								<svg class="icon" aria-hidden="true">
										<use xlink:href="#icon-google00">
										</use>
									</svg>
									<span style="color:#3B83FA">
										谷歌搜索
									</span>
								</label>
							</li>
								<li>
								<input hidden="" type="radio" name="type" id="type-fanyi" value="https://translate.google.cn/?hl=zh-CN&sl=auto&tl=zh-CN&text="
								data-placeholder="输入翻译内容（自动检测语言）后回车">
								<label for="type-fanyi">
									<svg class="icon" aria-hidden="true">
										<use xlink:href="#icon-fanyi">
										</use>
									</svg>
									<span style="color:#06f;font-weight:600">
										在线翻译
									</span>
								</label>
							</li>
						</ul>
					</div>
				</div>
				<form action="https://www.baidu.com/s?wd=" method="get" target="_blank"
				id="super-search-fm">
					<input type="text" id="search-text" placeholder="百度一下" style="outline:0"
					autocomplete="off">
					<button class="submit" type="submit">
						<svg style="width: 22px; height: 22px; margin: 0 20px 0 20px; color: #fff;"
						class="icon" aria-hidden="true">
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
    include "include/list.php";
	include "include/footer.php";
?>