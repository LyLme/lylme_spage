<?php 
$title = '主题设置';
include './head.php';
$set=isset($_GET['set'])?$_GET['set']:null;
if(!empty($set)) {
	if(saveSetting('template',$set)) {
		exit('<script>alert("主题修改成功！");window.location.href="./theme.php";</script>');
	} else {
		exit('<script>alert("主题修改失败！");window.location.href="./theme.php";</script>');
	}
}
?>
    <main class="lyear-layout-content">
  <div class="container-fluid">
		        <div class="row">
		          <div class="col-lg-12">
  <div class="card">
        <div class="card-header"><h4>主题设置</h4></div>
        <div class="card-body">
               <div class="table-responsive">
                  <table class="table">
                    <thead>
                      <tr>
                        <th width="35%">截图</th>
                        <th>详情</th>
                        <th>选择</th>
                      </tr>
                    </thead>
                    <tbody>
                    <?php 
                    $theme_path = ROOT.'template/';
$themes = glob($theme_path."*", GLOB_ONLYDIR);
foreach($themes as $theme) {
	$theme =  str_replace($theme_path ,"" , $theme);
	echo'<tr>
                    	        <td><img src="https://cdn.lylme.com/lylme_spage/themes/'.$theme.'.jpg" alt="'.$theme.'" width="500px"></td>
                    	        <td><h3>'.$theme.'</h3>
                    	        <p>';
	echo @file_get_contents( $theme_path.$theme.'/theme.ini');
	echo '</p><a href="https://doc.lylme.com/spage/#/%E4%B8%BB%E9%A2%98?id='.$theme.'" target="_blank">查看主题说明</a></td>';
	if($conf['template'] == $theme) {
		echo '<td><p class="btn btn-default disabled">当前使用</p></td>';
	} else {
		echo '<td><a href="./theme.php?set='.$theme.'" class="btn btn-label btn-primary"><label><i class="mdi mdi-checkbox-marked-circle-outline"></i></label>使用</a></td>';
	}
	echo '</tr>';
}
?> 
                    </tbody>
                    </table>
                </div>
                </div>
      </div> 
 </div> 
</main>
<?php 
include './footer.php';
?>