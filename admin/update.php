<?php 
$title = '检查更新';
include './head.php';
?>
   <!--页面主要内容-->
    <main class="lyear-layout-content">

      <div class="container-fluid">
<?php 
if(getver($update['version']) > getver($conf['version']) && !empty($update['version']) ){
    echo '<div class="alert alert-info" role="alert">发现新版本：'.$update['version'].'&nbsp;&nbsp;<a href="https://gitee.com/LyLme/lylme_spage/releases" target="_blant" class="alert-link">查看更新说明</a></div>
        <div class="card"><div class="card-header"><h4>版本更新</h4></div><ul class="list-group">
            <li class="list-group-item"><b>当前版本：</b>'.$conf['version'].'</li>
            <li class="list-group-item"><b>最新版本：</b>'.$update['version'].'</li>
            <li class="list-group-item"><b>更新说明：</b>点击<a href="https://gitee.com/LyLme/lylme_spage/releases" target="_blant" class="alert-link">这里</a> 下载更新包：<code>lylme_spage_update_'.$update['version'].'.zip</code> 上传到网站根目录后解压，点击更新数据库</li>
            <li class="list-group-item"><button id="update" class="btn btn-primary">更新数据库</button></li>';}
else if(!empty($update['version'])){
echo '<div class="alert alert-success" role="alert">当前已是最新版本！&nbsp;&nbsp;<a href="https://gitee.com/LyLme/lylme_spage/releases" target="_blant" class="alert-link">查看更新说明</a></div>
<div class="card"><div class="card-header"><h4>版本更新</h4></div><ul class="list-group">
            <li class="list-group-item"><b>当前版本：</b>'.$conf['version'].'</li>
            <li class="list-group-item"><b>最新版本：</b>'.$update['version'].'</li>';
}
else{
  echo '<div class="alert alert-danger" role="alert">检查更新失败！</div>
  <div class="card"><div class="card-header"><h4>版本更新</h4></div><ul class="list-group">
  <li class="list-group-item"><b>当前版本：</b>'.$conf['version'].'</li>
  <li class="list-group-item"><b>最新版本：</b>Unknown</li>
  <li class="list-group-item"><b>手动更新：</b><a href="https://gitee.com/LyLme/lylme_spage/releases" target="_blant" class="alert-link">下载更新包</a>解压到网站根目录后点击<a href="./update.php?set=update" class="alert-link">更新数据库</a></li>';      
}
 ?>
		</ul></div>

</div>
      
    </main>
    <!--End 页面主要内容-->
  </div>
</div>

<script type="text/javascript"> 
window.onload=function(){
 var update=document.getElementById("update");
 update.onclick=function(){
  if(confirm("注意：是否更新数据库？")){
   window.location.href='./update.php?set=update';
  }
  else{
   return false;
  }
 }
}
</script> 

<?php 
include './footer.php';
$set=isset($_GET['set'])?$_GET['set']:null;
if($set=='update')
{
    $vn=explode('.',str_replace('v','',$conf['version']));
    $vernum =  $vn[0].sprintf("%02d",$vn[1]).sprintf("%02d",$vn[2]);
if($vernum < 10101 ){
    $sqlpath='../install/update.sql';
    if(!file_exists($sqlpath)){exit("<script language='javascript'>alert('数据库更新文件：".$sqlpath." 不存在！\n请下载更新包解压到网站根目录');window.location.href='./';</script>");}
	$sql = file_get_contents($sqlpath);
	$version = 'v1.1.1';
	saveSetting('version',$version);
}
	
else{
    echo "<script language='javascript'>alert('你的网站已是最新版本！');window.location.href='./update.php';</script>";
}

	$sql=explode(';',$sql);
	$t=0; $e=0; $error='';
	for($i=0;$i<count($sql);$i++) {
		if (trim($sql[$i])=='')continue;
		if(mysqli_query($con, $sql[$i])) {
			++$t;
		} else {
			++$e;
			$error.=mysqli_error($con).'\n';
		}
	}
if($e!=0) {
exit('<script language="javascript">alert("数据库升级失败！\nSQL成功'.$t.'句/失败'.$e.'句\n错误信息：\n'.$error.'");window.location.href="./";</script>');
}
else{
echo "<script language='javascript'>alert('网站数据库升级完成！');window.location.href='./';</script>";
}
}
?>