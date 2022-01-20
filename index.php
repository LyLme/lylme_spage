<?php
@header('Content-Type: text/html; charset=UTF-8');
if(!file_exists('install/install.lock')){
	exit('<title>六零导航页 - 安装</title>您还未安装，点击<a href="install"><font color="blue">这里</font></a>开始安装！');
}
include "include/head.php"; ?>

	<body onload="FocusOnInput()">

		<div class="banner-video">

<?php

if(file_exists('./assets/img/background.jpg')){
     echo 	'<img src="./assets/img/background.jpg" alt="本地背景">';
}
else {
    echo 	'<img src="./assets/img/bing.php" alt="Bing每日背景">';
}
?>
			<div class="bottom-cover">
			</div>
		</div>
		<!--topbar开始-->
		<nav class="navbar navbar-expand-lg navbar-light fixed-top">
		<!--<a class="navbar-brand" href="/"><img src="../assets/img/logos.png" height="25"  title="六零起始页"></a>-->
			<button class="navbar-toggler collapsed" style="border: none; outline: none;"
			type="button" data-toggle="collapse" data-target="#navbarsExample05" aria-controls="navbarsExample05"
			aria-expanded="false" aria-label="Toggle navigation">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" class="bi bi-list-nested"
				fill="currentColor" id="list-nested">
					<path fill-rule="evenodd" d="M4.5 11.5A.5.5 0 015 11h10a.5.5 0 010 1H5a.5.5 0 01-.5-.5zm-2-4A.5.5 0 013 7h10a.5.5 0 010 1H3a.5.5 0 01-.5-.5zm-2-4A.5.5 0 011 3h10a.5.5 0 010 1H1a.5.5 0 01-.5-.5z">
					</path>
				</svg>
				<span>
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" class="bi bi-x"
					fill="currentColor" id="x">
						<path fill-rule="evenodd" d="M11.854 4.146a.5.5 0 010 .708l-7 7a.5.5 0 01-.708-.708l7-7a.5.5 0 01.708 0z">
						</path>
						<path fill-rule="evenodd" d="M4.146 4.146a.5.5 0 000 .708l7 7a.5.5 0 00.708-.708l-7-7a.5.5 0 00-.708 0z">
						</path>
					</svg>
					</span>
			</button>
			<div class="collapse navbar-collapse" id="navbarsExample05">
				<ul class="navbar-nav mr-auto">
					<li class="nav-item">
						<a class="nav-link" href="https://www.lylme.com" target="_blant">
							主页
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="https://blog.lylme.com" target="_blant">
							博客
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="https://github.com/lylme" target="_blant">
							Github
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="https://blog.lylme.com/about.html" target="_blant">
							关于
						</a>
					</li>
				</ul>
				<div id="main">  
<div id="show_date"> </div>  
 </div>	
<div id="show_time"></div>
				<div id="he-plugin-simple">
				</div>
				
				<script src="https://widget.qweather.net/simple/static/js/he-simple-common.js?v=2.0">
				</script>
			</div>
			
		</nav>
				<!--topbar结束-->
		<div class="container" style="margin-top:10vh; position: relative; z-index: 100;">
		   
	
			<h2 class="title">
			上网，从这里开始!
			</h2>

			 


			<p class="content"><b>随机一言:</b>
			 
 <?php
// 存储数据的文件
$filename = './assets/data/data.dat'; 
// 指定页面编码
// if (!file_exists($filename)) {
//     die($filename . ' Error');
// }
// 读取整个数据文件
$data = file_get_contents($filename);
// 按换行符分割成数组
$data = explode(PHP_EOL, $data);
// 随机获取一行索引
$result = $data[array_rand($data)];
// 去除多余的换行符
$result = str_replace(array("\r", "\n", "\r\n"), '', $result);
echo $result;?>
		</p>
			<!--搜索开始-->
			<div id="search" class="s-search">
				<div id="search-list" class="hide-type-list">
					<div class="search-group group-a s-current" style=" margin-top: 50px;">
						<ul class="search-type">
							<li>
								<input checked="" hidden="" type="radio" name="type" id="type-baidu" value="https://www.baidu.com/?tn=simple#&wd="
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
								<input hidden="" type="radio" name="type" id="type-fanyi" value="https://translate.google.cn/?hl=zh-CN&sl=auto&tl=zh-CN&text="
								data-placeholder="输入翻译内容，自动检测语言">
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