<?php
/* 
 * @Description: 后台首页
 * @Author: LyLme admin@lylme.com
 * @Date: 2024-01-23 12:25:35
 * @LastEditors: LyLme admin@lylme.com
 * @LastEditTime: 2024-04-09 02:09:02
 * @FilePath: /lylme_spage/admin/index.php
 * @Copyright (c) 2024 by LyLme, All Rights Reserved. 
 */
$title = '后台管理';
include './head.php';
$last = date("Ym");
if (@file_get_contents('log.txt') != $last || !file_exists('cache.php')) {
    $update = update();
    file_put_contents('log.txt', $last);
    var_export($update, true);
    $content = "<?php\nreturn " . var_export($update, true) . "\n?>";
    file_put_contents('cache.php', $content);
}
function tjsj($tjname)
{
    if ($tjname == '') {
        echo '0';
    } else {
        echo $tjname;
    }
}
?>
<!--页面主要内容-->
<main class="lyear-layout-content">
	<div class="container-fluid">
		<?php
        $update  = require('cache.php');
if (!empty($update)) {
    if ($update['switch']) {
        if ($update['msg_switch'] && !empty($update['msg'])) {
            echo '<div class="card"><div class="card-header"><h4>' . $update['title'] . '</h4></div><ul class="list-group">' . $update['msg'] . '</ul></div>';
        }
        if (getver($update['version']) > getver($conf['version'])) {
            echo '<div class="card"><div class="card-header"><h4>更新提示</h4></div><ul class="list-group">' . $update['update_msg'] . '</ul></div>';
        }
    }
}
?>
		<div class="row">
			<div class="col-sm-6 col-lg-3">
				<div class="card bg-primary">
					<div class="card-body clearfix">
						<div class="pull-right">
							<p class="h6 text-white m-t-0">链接数量</p>
							<p class="h3 text-white m-b-0 fa-1-5x"><?php tjsj($linksrows);
?></p>
						</div>
						<div class="pull-left"> <span class="img-avatar img-avatar-48 bg-translucent"><i class="mdi mdi-web fa-1-5x"></i></span> </div>
					</div>
				</div>
			</div>
			<div class="col-sm-6 col-lg-3">
				<div class="card bg-danger">
					<div class="card-body clearfix">
						<div class="pull-right">
							<p class="h6 text-white m-t-0">今日浏览量</p>
							<p class="h3 text-white m-b-0 fa-1-5x"><?php tjsj($tjtoday);
?></p>
						</div>
						<div class="pull-left"> <span class="img-avatar img-avatar-48 bg-translucent"><i class="mdi mdi-account fa-1-5x"></i></span> </div>
					</div>
				</div>
			</div>
			<div class="col-sm-6 col-lg-3">
				<div class="card bg-success">
					<div class="card-body clearfix">
						<div class="pull-right">
							<p class="h6 text-white m-t-0">昨日浏览量</p>
							<p class="h3 text-white m-b-0 fa-1-5x"><?php tjsj($tjyesterday);
?></p>
						</div>
						<div class="pull-left"> <span class="img-avatar img-avatar-48 bg-translucent"><i class="mdi mdi-account-convert fa-1-5x"></i></span> </div>
					</div>
				</div>
			</div>
			<div class="col-sm-6 col-lg-3">
				<div class="card bg-purple">
					<div class="card-body clearfix">
						<div class="pull-right">
							<p class="h6 text-white m-t-0">累计浏览量</p>
							<p class="h3 text-white m-b-0 fa-1-5x"><?php tjsj($tjtotal);
?></p>
						</div>
						<div class="pull-left"> <span class="img-avatar img-avatar-48 bg-translucent"><i class="mdi mdi-account-multiple fa-1-5x"></i></span> </div>
					</div>
				</div>
			</div>
		</div>
		<?php
        if ($applyrows > 0) {
            echo '
        <div class="row">   
        <div class="col-sm-6 col-lg-12">
            <div class="card bg-info">
              <div class="card-body clearfix">
              <a href="./apply.php">  <div class="pull-right">
                  <p class="h6 text-white m-t-0">待审核链接</p>
                  <p class="h3 text-white m-b-0 fa-1-5x">' . $applyrows . '</p>
                </div></a>
                <div class="pull-left"> <span class="img-avatar img-avatar-48 bg-translucent"><i class="mdi mdi-link fa-1-5x"></i></span> </div>
              </div>
            </div>
          </div>
          </div>';
        }
?>
		<div class="row">
			<div class="col-lg-6">
				<div class="card">
					<div class="card-header">
						<h4>仪表盘柱状统计图</h4>
					</div>
					<div class="card-body">
						<canvas class="js-chartjs-bars"></canvas>
					</div>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="card">
					<div class="card-header">
						<h4>仪表盘折线统计图</h4>
					</div>
					<div class="card-body">
						<canvas class="js-chartjs-lines"></canvas>
					</div>
				</div>
			</div>
		</div>
		<div class="card">
			<div class="card-header">
				<h4>服务器信息</h4>
			</div>
			<ul class="list-group">
				<li class="list-group-item">
					<b>PHP 版本：</b><?php echo phpversion() ?>
					<?php if (ini_get('safe_mode')) {
					    echo '线程安全';
					} else {
					    echo '非线程安全';
					}
?>
				</li>
				<li class="list-group-item">
					<b>MySQL 版本：</b><?php echo $DB->count("select VERSION()") ?>
				</li>
				<li class="list-group-item">
					<b>服务器软件：</b><?php echo $_SERVER['SERVER_SOFTWARE'] ?>
				</li>
				<li class="list-group-item">
					<b>程序名称：</b>六零导航页(LyLme Spage)
				</li>
				<li class="list-group-item">
					<b>授权状态：</b>
					<a href="https://www.lylme.com/spage/" target="_blank">查询</a>
				</li>
				<li class="list-group-item">
					<b>建站日期：</b><?php echo $conf['build'] ?>
				</li>
				<li class="list-group-item">
					<b>当前版本：</b><?php echo $conf['version'] ?> <a href="./update.php" target="_blank">检查更新</a>
				</li>
				<li class="list-group-item">
					<b>最新版本：</b> <?php echo $update['version'] ?> <a href="https://doc.lylme.com/spage/#/logs target=" _blank">更新日志</a>
				</li>
				<li class="list-group-item">
					<b>项目作者：</b>六零 <a href="https://www.lylme.com/support/" target="_blank">捐赠作者</a>
				</li>
				<li class="list-group-item">
					<b>项目地址：</b><a href="https://github.com/LyLme/lylme_spage" target="_blank">https://github.com/LyLme/lylme_spage</a>
				</li>
			</ul>
		</div>
	</div>
</main>
<!--End 页面主要内容-->
</div>
</div>
<?php
include './footer.php';
?>
<!--图表插件-->
<script type="text/javascript" src="/assets/admin/js/Chart.js"></script>
<script type="text/javascript">
	$(document).ready(function(e) {
		var $dashChartBarsCnt = jQuery('.js-chartjs-bars')[0].getContext('2d'),
			$dashChartLinesCnt = jQuery('.js-chartjs-lines')[0].getContext('2d');
		var $dashChartBarsData = {
			labels: ['今日浏览', '昨日浏览', '本月浏览', '总浏览'],
			datasets: [{
				label: '数量',
				borderWidth: 1,
				borderColor: 'rgba(0,0,0,0)',
				backgroundColor: 'rgba(51,202,185,0.5)',
				hoverBackgroundColor: "rgba(51,202,185,0.7)",
				hoverBorderColor: "rgba(0,0,0,0)",
				data: [<?php echo $tjtoday;
?>, <?php echo $tjyesterday;
?>, <?php echo $tjmonth;
?>, <?php echo $tjtotal;
?>]
			}]
		};
		var $dashChartLinesData = {
			labels: ['今日浏览', '昨日浏览', '本月浏览', '总浏览'],
			datasets: [{
				label: '数量',
				data: [<?php echo $tjtoday;
?>, <?php echo $tjyesterday;
?>, <?php echo $tjmonth;
?>, <?php echo $tjtotal;
?>],
				borderColor: '#358ed7',
				backgroundColor: 'rgba(53, 142, 215, 0.175)',
				borderWidth: 1,
				fill: false,
				lineTension: 0
			}]
		};
		new Chart($dashChartBarsCnt, {
			type: 'bar',
			data: $dashChartBarsData
		});
		var myLineChart = new Chart($dashChartLinesCnt, {
			type: 'line',
			data: $dashChartLinesData,
		});
	});
</script>