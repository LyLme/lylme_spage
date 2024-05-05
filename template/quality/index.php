<!DOCTYPE html>

<!--
模板：quality
作者：六零
二开：七夏 QQ：8641340
说明：https://www.52qxwl.cn/about/#%E5%BC%80%E5%8F%91%E6%97%A5%E5%BF%97
-->
<html lang="zh-CN" element::-webkit-scrollbar {display:none}>
	<head>
        <meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?php echo $conf['title']?></title>
		<meta name="keywords" content="<?php echo $conf['keywords']?>">
		<meta name="description" content="<?php echo $conf['description']?>">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
		<meta name="author" content="LyLme_pro">
		<link rel="icon" href="<?php echo $conf['logo']?>" type="image/x-icon">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-touch-fullscreen" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="full-screen" content="yes">
		<meta name="browsermode" content="application">
		<meta name="x5-fullscreen" content="true">
		<meta name="x5-page-mode" content="app">
		<meta name="lsvn" content="<?php echo base64_encode($conf['version'])?>">
		<script src="<?php echo $cdnpublic ?>/assets/js/jquery.min.js" type="application/javascript"></script>
		<link href="<?php echo $cdnpublic ?>/assets/css/bootstrap.min.css" type="text/css" rel="stylesheet">
		<script src="<?php echo $cdnpublic ?>/assets/js/bootstrap.min.js" type="application/javascript"></script>
		<link rel="stylesheet" href="<?php echo $templatepath;?>/css/daohang.css" type="text/css">
		<link rel="stylesheet" href="<?php echo $templatepath;?>/css/style.css?v=20240409" type="text/css">
	</head>
	<?php if(!empty(background())) {
	    echo '<body onload="FocusOnInput()" style="background-image: url(' . background() . ');background-size: cover;/**/margin: 0;padding: 0;background: linear-gradient(#3c65e1, #195bc1, #6011d5, #5d21a8, #53097d);min-height: 100vh;display: flex;justify-content: center;align-items: center;background-attachment: fixed;">';
	} else {
	    echo '<body onload="FocusOnInput()">';
	}?>
<!---左侧导航开始-->
<script>
    $(function(){
        $('.type-left').click(function(){
            $('.iconnav').toggleClass('iconnavblock')
        });
    })
</script>
<div class="type-left">
<svg t="1655358398870" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="98214" width="200" height="200"><path d="M516.266667 951.466667c-8.533333 0-17.066667-4.266667-21.333334-12.8l-59.733333-132.266667C256 768 128 618.666667 128 443.733333c0-204.8 170.666667-366.933333 384-366.933333s384 166.4 384 366.933333c0 174.933333-128 324.266667-302.933333 358.4l-55.466667 132.266667c0 8.533333-12.8 17.066667-21.333333 17.066667zM512 123.733333c-183.466667 0-332.8 145.066667-332.8 320 0 157.866667 115.2 290.133333 277.333333 315.733334 8.533333 0 17.066667 8.533333 17.066667 12.8l42.666667 89.6 38.4-89.6c4.266667-8.533333 8.533333-12.8 17.066666-12.8 157.866667-25.6 273.066667-157.866667 273.066667-311.466667 0-179.2-149.333333-324.266667-332.8-324.266667z" fill="#6A3906" p-id="98215"></path><path d="M780.8 452.266667c0-149.333333-132.266667-256-268.8-256-149.333333 0-268.8 115.2-268.8 256 0 128 98.133333 234.666667 226.133333 256 17.066667 0 29.866667 0 46.933334 4.266666 145.066667-4.266667 264.533333-119.466667 264.533333-260.266666z" fill="#F5CB2B" p-id="98216"></path><path d="M512 558.933333c-59.733333 0-110.933333-51.2-110.933333-110.933333S452.266667 341.333333 512 341.333333c59.733333 0 110.933333 51.2 110.933333 110.933334s-51.2 106.666667-110.933333 106.666666z m0-170.666666c-34.133333 0-59.733333 25.6-59.733333 59.733333S477.866667 512 512 512s59.733333-25.6 59.733333-59.733333-25.6-64-59.733333-64z" fill="#6A3906" p-id="98217"></path></svg>
</div>

<ul class="iconnav">
  <li><a href="#main"><svg class="icon" aria-hidden="true"  width="200" height="200"><use xlink:href="#icon-sousuo"></use></svg><span>搜索</span></a></li>  
    
<?php
$groups = $site->getGroups();
		while ($group = $DB->fetch($groups)) {
		    echo '<li><a href="#category-' . $group["group_id"] . '">' . $group["group_icon"] . '<span>' . $group["group_name"] . '</span></a></li>';
		}
		?>
<hr>
<li><a target="_blank" href="/apply"><svg t="1655349272190" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="6087" width="200" height="200"><path d="M511.984 64C264.976 64 64 264.96 64 512.016 64 759.024 264.976 960 511.984 960 759.056 960 960 759.024 960 512.016 960 264.944 759.024 64 511.984 64z" fill="#FFBD27" p-id="6088"></path><path d="M695.76 552.16h-143.616v143.536A40.224 40.224 0 0 1 512 735.936a40.256 40.256 0 0 1-40.128-40.24v-143.52h-143.632a40.208 40.208 0 1 1 0-80.4h143.632v-143.584a40.16 40.16 0 1 1 80.288 0v143.568h143.616a40.208 40.208 0 1 1 0 80.416z" fill="#333333" p-id="6089"></path></svg><span>申请收录</span></a></li>
<li><a target="_blank" href="/about"><svg t="1655350264547" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="12046" width="200" height="200"><path d="M512 162c47.3 0 93.1 9.2 136.2 27.5 41.7 17.6 79.1 42.9 111.3 75 32.2 32.2 57.4 69.6 75 111.3C852.8 418.9 862 464.7 862 512s-9.2 93.1-27.5 136.2c-17.6 41.7-42.9 79.1-75 111.3-32.2 32.2-69.6 57.4-111.3 75C605.1 852.8 559.3 862 512 862s-93.1-9.2-136.2-27.5c-41.7-17.6-79.1-42.9-111.3-75-32.2-32.2-57.4-69.6-75-111.3C171.2 605.1 162 559.3 162 512s9.2-93.1 27.5-136.2c17.6-41.7 42.9-79.1 75-111.3 32.2-32.2 69.6-57.4 111.3-75C418.9 171.2 464.7 162 512 162m0-80C274.5 82 82 274.5 82 512s192.5 430 430 430 430-192.5 430-430S749.5 82 512 82z" fill="#4a5fe2" p-id="12047"></path><path d="M612 687.6h-60V405.4c0-20.7-17-37.7-37.7-37.7H508.6c-6.8-0.2-13.7 1.4-20 5L415.4 415c-18 10.4-24.2 33.6-13.8 51.5l2.3 4c10.4 18 33.6 24.2 51.5 13.8l16.6-9.6v213h-60c-22 0-40 18-40 40s18 40 40 40h200c22 0 40-18 40-40 0-22.1-18-40.1-40-40.1z" fill="#7c44e2" p-id="12048"></path><path d="M512 306.4m-50 0a50 50 0 1 0 100 0 50 50 0 1 0-100 0Z" fill="#7c44e2" p-id="12049"></path></svg><span>关于本站</span></a></li>
<!--<li><a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=8641340&site=qq&menu=yes"><svg t="1655350319930" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="15564" width="200" height="200"><path d="M512.4096 77.7728c-232.0896 0-420.864 188.8256-420.864 420.864 0 232.0896 188.8256 420.864 420.864 420.864 232.0896 0 420.864-188.8256 420.864-420.864s-188.7744-420.864-420.864-420.864z m0 800.8192c-209.5104 0-379.904-170.4448-379.904-379.904 0-209.5104 170.4448-379.904 379.904-379.904 209.5104 0 379.904 170.4448 379.904 379.904 0 209.4592-170.3936 379.904-379.904 379.904z" fill="#00A0E9" p-id="15565"></path><path d="M683.9296 485.376c-1.8944-2.048-3.1232-5.4272-3.2768-8.2432-0.4096-8.448 0.2048-16.9472-0.256-25.3952-0.5632-10.1376-2.7136-20.6336-10.0352-27.5456-6.144-5.7856-6.8608-11.9296-7.168-19.0464-0.1024-2.56-0.3584-5.0688-0.6144-7.6288-3.9424-34.3552-13.0048-66.9184-36.4032-93.44-36.4544-41.3184-83.1488-57.1392-136.9088-49.2032-47.0528 6.912-84.1216 30.464-107.1616 73.4208-13.5168 25.1904-19.0464 52.5312-20.736 80.7424-0.3072 5.4272-0.8192 10.0352-5.888 13.6704-2.7136 1.9456-4.352 5.6832-5.8368 8.9088-6.5024 13.9264-6.656 28.7744-5.376 43.6736 0.3584 4.5056-0.8192 7.8336-4.0448 11.3152-24.6784 26.4704-42.1888 56.9344-48.384 92.9792-2.5088 14.5408-1.2288 29.2864 4.608 43.1104 3.3792 7.9872 9.216 9.9328 15.616 4.3008 7.2704-6.3488 13.4144-13.9776 19.7632-21.3504 2.9696-3.4816 5.1712-7.5776 7.3216-10.8032 11.5712 21.0944 23.0912 42.0352 34.2528 62.3104-7.2704 5.376-16.2304 10.5472-23.296 17.5616-15.36 15.1552-20.8384 45.6704 10.8544 60.1088 2.8672 1.3312 5.8368 2.56 8.9088 3.4304 29.2864 8.3968 58.5728 7.9872 87.9616 0.2048 15.5136-4.096 30.5152-9.0624 42.1888-20.8896 5.8368-5.9392 18.688-5.3248 25.1904-0.1024 6.5024 5.2736 13.4144 10.6496 21.0432 13.8752 22.3232 9.5232 45.824 13.9776 70.144 12.9536 16.7936-0.7168 33.4848-2.4064 48.896-10.0352 15.2064-7.5264 22.6816-19.2 21.5552-33.5872-1.024-13.3632-7.5264-23.808-17.9712-31.6416-5.888-4.4032-12.3904-7.936-17.6128-11.264 11.4688-20.8896 22.9888-41.8304 34.56-62.8736 2.1504 3.2256 4.4032 7.3216 7.3216 10.8544 5.7856 6.9632 11.4176 14.336 18.176 20.2752 7.9872 7.0144 14.1312 4.9664 17.9712-5.0688 4.9152-12.9536 6.2976-26.7776 4.0448-40.1408-6.3488-36.9664-24.3712-68.096-49.408-95.4368z" fill="#00A0E9" p-id="15566"></path></svg><span>联系客服</span></a></li>-->
</ul>
<!---左侧导航结束-->
	
		<nav class="navbar navbar-expand-lg navbar-light fixed-top" style="position: absolute; z-index: 10000;">
		<button class="navbar-toggler collapsed" style="border: none; outline: none;"type="button" data-toggle="collapse" data-target="#navbarsExample05" aria-controls="navbarsExample05" aria-expanded="false" aria-label="Toggle navigation">
			    
		<svg class="icon" width="200" height="200"><use xlink:href="#icon-menus"></use></svg>
		<!---<span><svg class="bi bi-x" 	fill="currentColor" id="x"><use xlink:href="#icon-closes"></use></svg><span>-->
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
<?php if ($conf['tq'] != 'false') {
		    echo '<div id="he-plugin-simple"></div>
<script src="https://widget.qweather.net/simple/static/js/he-simple-common.js?v=2.0"></script>';
		}?>
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
		//调用随机一言
		if ($conf['yan'] == 'true') {
		    echo '<p class="content">' . yan() . '</p>';
		}
		?>
		<!--搜索开始-->
			<div id="search" class="s-search">
				<div id="search-list" class="hide-type-list">
					<div class="search-group group-a s-current">
					
					
				
						<div class="search-box">
				<div id="search-lylme">
				<form action="https://www.baidu.com/s?wd=" method="get" target="_blank"
				id="super-search-fm">
				    <div id="checke-so" onclick="lylme()">
				    <svg class="lylme" aria-hidden="true"><use xlink:href="#icon-icon_baidulogo"></use></svg>
				    <svg class="sw" id="lylme-up" style="display:inline" aria-hidden="true"><use xlink:href="#icon-up"></use></svg>
				    <svg class="sw" id="lylme-down" style="display:none" aria-hidden="true"><use xlink:href="#icon-down"></use></svg>
				    </div>
					
		
				<input type="text" id="search-text" placeholder="百度一下，你就知道" style="outline:0"
					autocomplete="off">
					<button class="submit" id="search-submit" type="submit">
						<svg style="width: 22px; height: 22px; margin: 0 20px 0 20px; color: #fff;"
						class="icon" aria-hidden="true">
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
		    if($soulist["sou_st"] == 1) {
		        echo '	<li>
								<input hidden=""  checked="" type="radio" name="type" id="type-' . $soulist["sou_alias"] . '" value="';
		        if(checkmobile() && $soulist["sou_waplink"] != null) {
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

<style>

</style>

<?php
$rel = $conf["mode"] == 2 ? '' : 'rel="nofollow"'; 
$html = array(
    'g1' => '<ul class="mylist row">', //分组开始标签
    'g2' => '<li id="category-{group_id}" class="title">{group_icon}<sapn>{group_name}</sapn></li>',  //分组内容
    'g3' => '</ul>',  //分组结束标签

    'l1' => '<li class="lylme-3">',  //链接开始标签
    'l2' => '<a '.$rel.' href="{link_url}" target="_blank">{link_icon}<span>{link_name}</span></a>',  //链接内容
    'l3' => '</li>',  //链接结束标签
);
		lists($html);
		?>
<script src="<?php echo  $templatepath;?>/js/script.js?v=20220518"></script>
<script src="<?php echo $cdnpublic ?>/assets/js/svg.js"></script>
<div style="display:none;" class="back-to" id="toolBackTop"> 
<a title="返回顶部" onclick="window.scrollTo(0,0);return false;" href="#top" class="back-top"></a> 
</div> 
<div class="mt-5 mb-3 footer text-muted text-center"> 
  <!--备案信息-->
  <?php if($conf['icp'] != null) {
      echo '<a href="http://beian.miit.gov.cn/" class="icp" target="_blank" _mstmutation="1" _istranslated="1">' . $conf['icp'] . '</a>';
  } ?> 
  <!--版权信息-->
  <p> <?php echo $conf['copyright']; ?></p>
  <!--网站统计-->
 <?php if($conf['wztj'] != null) {
     echo $conf["wztj"];
 }?>
  </div>  
    <script>

		</script>
 </body>
</html>
