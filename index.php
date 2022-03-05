<?php
@header('Content-Type: text/html; charset=UTF-8');
if(!file_exists('install/install.lock')){
	exit('<title>六零导航页 - 安装程序</title>您还未安装，点击<a href="install"><font color="blue">这里</font></a>开始安装！');
}
include "include/head.php"; 
echo '<body onload="FocusOnInput()"><div class="banner-video">';
if ($conf['background']  == '') {
    if(!file_exists('./assets/img/background.jpg')){echo '<img src="./assets/img/cron.php">';}
    else{echo '<img src="./assets/img/background.jpg">';}}
    else{echo '<img src="'.$conf['background'].'">';}
?> 
			<div class="bottom-cover" style="background-image: linear-gradient(rgba(255, 255, 255, 0) 0%, rgb(244 248 251 / 0.6) 50%, rgb(244 248 251) 100%);">
			</div>
		</div>
		<!--topbar开始-->
		<nav class="navbar navbar-expand-lg navbar-light fixed-top" style="position: absolute; z-index: 10000;">
		<!--<a class="navbar-brand" href="/"><img src="./assets/img/logo.png" height="25"  title="六零起始页"></a>-->
		<button class="navbar-toggler collapsed" style="border: none; outline: none;"type="button" data-toggle="collapse" data-target="#navbarsExample05" aria-controls="navbarsExample05" aria-expanded="false" aria-label="Toggle navigation">
			    
		<svg class="icon" width="200" height="200"><use xlink:href="#icon-menus"></use></svg>
		<span><svg class="bi bi-x" 	fill="currentColor" id="x"><use xlink:href="#icon-closes"></use></svg><span>
			</button>
			<div class="collapse navbar-collapse" id="navbarsExample05">
				<ul class="navbar-nav mr-auto">
			<?php 
				$tagslists = mysqli_query($con, "SELECT * FROM `lylme_tags`"); 
				while($taglists = mysqli_fetch_assoc($tagslists)) { 
				    echo '<li class="nav-item"><a class="nav-link" href="'.$taglists["tag_link"].'"';
				    if($taglists["tag_target"]==1)echo ' target="_blant"';
				    echo '>'.$taglists["tag_name"].'</a></li>
				    ';
			}?>
				</ul>
				<div id="main">  
<div id="show_date"></div>  
<div id="show_time"></div>
 </div>	

<?php
if($conf['tq'] != 'false'){
    echo '<div id="he-plugin-simple"></div>
		<script src="https://widget.qweather.net/simple/static/js/he-simple-common.js?v=2.0"></script>';}
?>
			</div>
			
		</nav>
				<!--topbar结束-->
		<div class="container" style="margin-top:10vh; position: relative; z-index: 100;">
			<?php echo $conf['home-title']?>

<?php
if($conf['yan']!='false'){
$filename = './assets/data/data.dat';  //随机一言文件路径
if (file_exists($filename)) {
$data = explode(PHP_EOL, file_get_contents($filename));
$result = str_replace(array("\r", "\n", "\r\n"), '', $data[array_rand($data)]);
echo '<p class="content"><b>随机一言:</b>'.$result;
}
}
?>
		</p>
			<!--搜索开始-->
			<div id="search" class="s-search">
				<div id="search-list" class="hide-type-list">
					<div class="search-group group-a s-current" style=" margin-top: 50px;">
						<ul class="search-type">
						<?php 
						    $soulists = mysqli_query($con, "SELECT * FROM `lylme_sou` ORDER BY `lylme_sou`.`sou_order` ASC"); 
							while($soulist = mysqli_fetch_assoc($soulists)) { 
							    if($soulist["sou_st"]==1){
							        echo '	<li>
								<input hidden=""  checked="" type="radio" name="type" id="type-'.$soulist["sou_alias"].'" value="';
								if($ua=='wap'&&$soulist["sou_waplink"]!=NULL){echo $soulist["sou_waplink"];}else{echo $soulist["sou_link"];}
								echo '"data-placeholder="'.$soulist["sou_hint"].'">
								<label for="type-'.$soulist["sou_alias"].'" style="font-weight:600">
								'.$soulist["sou_icon"].'
									<span style="color:'.$soulist["sou_color"].'">
										'.$soulist["sou_name"].'
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
				<form action="https://www.baidu.com/s?wd=" method="get" target="_blank"
				id="super-search-fm">
					<input type="text" id="search-text" placeholder="百度一下，你就知道" style="outline:0"
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