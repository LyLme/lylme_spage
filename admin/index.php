<?php 
$title = '后台管理';
include './head.php';
$mysqlversion = mysqli_fetch_array(mysqli_query($con,"select VERSION()"))[0];
function tjsj($tjname){if($tjname==''){echo '0';}else{echo $tjname;}}
?>
   <!--页面主要内容-->
    <main class="lyear-layout-content">

      <div class="container-fluid">
<?php if(@$update = json_decode(file_get_contents('https://'.$update_host.'/lylmes_page/update.json'), true)){
if($update['switch'] == true){echo ' <div class="card"><div class="card-header"><h4>'.$update['title'].'</h4></div><ul class="list-group">';
if($update['msg']!='') {echo $update['msg'];}
if($update['version']!=$conf['version']){echo $update['update_msg'];}echo '</ul></div>';}} ?>
        <div class="row">
          <div class="col-sm-6 col-lg-3">
            <div class="card bg-primary">
              <div class="card-body clearfix">
                <div class="pull-right">
                  <p class="h6 text-white m-t-0">链接数量</p>
                  <p class="h3 text-white m-b-0 fa-1-5x"><?php tjsj($linksrows);?></p>
                </div>
                <div class="pull-left"> <span class="img-avatar img-avatar-48 bg-translucent"><i class="mdi mdi-currency-cny fa-1-5x"></i></span> </div>
              </div>
            </div>
          </div>
          
          <div class="col-sm-6 col-lg-3">
            <div class="card bg-danger">
              <div class="card-body clearfix">
                <div class="pull-right">
                  <p class="h6 text-white m-t-0">今日访客量</p>
                  <p class="h3 text-white m-b-0 fa-1-5x"><?php tjsj($tjtoday);?></p>
                </div>
                <div class="pull-left"> <span class="img-avatar img-avatar-48 bg-translucent"><i class="mdi mdi-account fa-1-5x"></i></span> </div>
              </div>
            </div>
          </div>
          
          <div class="col-sm-6 col-lg-3">
            <div class="card bg-success">
              <div class="card-body clearfix">
                <div class="pull-right">
                  <p class="h6 text-white m-t-0">昨日访客量</p>
                  <p class="h3 text-white m-b-0 fa-1-5x"><?php tjsj($tjyesterday); ?></p>
                </div>
                <div class="pull-left"> <span class="img-avatar img-avatar-48 bg-translucent"><i class="mdi mdi-arrow-down-bold fa-1-5x"></i></span> </div>
              </div>
            </div>
          </div>
          
          <div class="col-sm-6 col-lg-3">
            <div class="card bg-purple">
              <div class="card-body clearfix">
                <div class="pull-right">
                  <p class="h6 text-white m-t-0">累计访客量</p>
                  <p class="h3 text-white m-b-0 fa-1-5x"><?php tjsj($tjtotal); ?></p>
                </div>
                <div class="pull-left"> <span class="img-avatar img-avatar-48 bg-translucent"><i class="mdi mdi-comment-outline fa-1-5x"></i></span> </div>
              </div>
            </div>
          </div>
        </div>
        
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
			<?php if(ini_get('safe_mode')) { echo '线程安全'; } else { echo '非线程安全'; } ?>
		</li>
		<li class="list-group-item">
			<b>MySQL 版本：</b><?php echo $mysqlversion ?>
		</li>
		<li class="list-group-item">
			<b>服务器软件：</b><?php echo $_SERVER['SERVER_SOFTWARE'] ?>
		</li>
		<li class="list-group-item">
			<b>程序名称：</b>六零导航页(LyLme Spage)
		</li>
		<li class="list-group-item">
			<b>当前版本：</b><?php echo $conf['version']?>
		</li>
		<li class="list-group-item">
			<b>最新版本：</b> <a href="https://gitee.com/LyLme/lylme_spage/releases" target="_blant">检查更新</a>
		</li>
		<li class="list-group-item">
			<b>项目作者：</b>六零
		</li>
		<li class="list-group-item">
			<b>项目地址：</b>https://github.com/LyLme/lylme_spage
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
<script type="text/javascript" src="js/Chart.js"></script>
<script type="text/javascript">
$(document).ready(function(e) {
    var $dashChartBarsCnt  = jQuery( '.js-chartjs-bars' )[0].getContext( '2d' ),
        $dashChartLinesCnt = jQuery( '.js-chartjs-lines' )[0].getContext( '2d' );
    
    var $dashChartBarsData = {
		labels: ['今日访客', '昨日访客', '本月访客', '总访客', '链接数', '分组数'],
		datasets: [
			{
				label: '数量',
                borderWidth: 1,
                borderColor: 'rgba(0,0,0,0)',
				backgroundColor: 'rgba(51,202,185,0.5)',
                hoverBackgroundColor: "rgba(51,202,185,0.7)",
                hoverBorderColor: "rgba(0,0,0,0)",
				data: [<?php echo $tjtoday;?>, <?php echo $tjyesterday;?>, <?php echo $tjmonth;?>, <?php echo $tjtotal;?>, <?php echo $linksrows;?>, <?php echo $groupsrows;?>]
			}
		]
	};
    var $dashChartLinesData = {
		labels: ['今日访客', '昨日访客', '本月访客', '总访客', '链接数', '分组数'],
		datasets: [
			{
				label: '数量',
				data: [<?php echo $tjtoday;?>, <?php echo $tjyesterday;?>, <?php echo $tjmonth;?>, <?php echo $tjtotal;?>,<?php echo $linksrows;?>, <?php echo $groupsrows;?>],
				borderColor: '#358ed7',
				backgroundColor: 'rgba(53, 142, 215, 0.175)',
                borderWidth: 1,
                fill: false,
                lineTension: 0
			}
		]
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
