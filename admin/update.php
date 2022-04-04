<?php 
$title = '检查更新';
include './head.php';
?>
   <!--页面主要内容-->
    <main class="lyear-layout-content">
      <div class="container-fluid">
<?php 
if(getver($update['version']) > getver($conf['version']) && !empty($update['version']) ) {
	echo '<script type="text/javascript"> 
window.onload=function() {
	var update=document.getElementById("update");
	update.onclick=function() {if(confirm("注意：更新将会替换部分文件，是否继续？")) {window.location.href="./update.php?set=update";}
		else {return false;}}}</script> ';
	echo '<div class="alert alert-info" role="alert">当前版本：</b>'.$conf['version'].'，发现新版本：'.$update['version'].'&nbsp;&nbsp;<a href="https://gitee.com/LyLme/lylme_spage/releases" target="_blant" class="alert-link">查看发行版</a></div>
        <div class="card"><div class="card-header"><h4>更新说明</h4></div><ul class="list-group">
            '.$update['update_log'].'
            <li class="list-group-item"><button id="update" class="btn btn-primary">更新</button></li>';
} else if(!empty($update['version'])) {
	echo '<div class="alert alert-success" role="alert">当前已是最新版本！&nbsp;&nbsp;<a href="https://gitee.com/LyLme/lylme_spage/releases" target="_blant" class="alert-link">查看发行版</a></div>
<div class="card"><div class="card-header"><h4>版本更新</h4></div><ul class="list-group">
            <li class="list-group-item"><b>当前版本：</b>'.$conf['version'].'</li>
            <li class="list-group-item"><b>最新版本：</b>'.$update['version'].'</li>';
} else {
	echo '<div class="alert alert-danger" role="alert">检查更新失败！</div>
  <div class="card"><div class="card-header"><h4>版本更新</h4></div><ul class="list-group">
  <li class="list-group-item"><b>当前版本：</b>'.$conf['version'].'</li>
  <li class="list-group-item"><b>最新版本：</b>Unknown</li>
  <li class="list-group-item"><b>手动更新：</b>点击<a href="'.$update['file'].'" target="_blant">这里</a>或前往码云<a href="https://gitee.com/LyLme/lylme_spage/releases" target="_blant" class="alert-link">下载<code>update.zip</code>后缀的更新包</a>解压到网站根目录后点击<a href="./update.php?set=updatesql" class="alert-link">更新数据库</a></li>';
}
?>
		</ul></div>
</div>
    </main>
    <!--End 页面主要内容-->
  </div>
</div>
<?php 
$set=isset($_GET['set'])?$_GET['set']:null;
if($set=='update') {
	function zipExtract ($src, $dest) {
		$zip = new ZipArchive();
		if ($zip->open($src)===true) {
			$zip->extractTo($dest);
			$zip->close();
			return true;
		}
		return false;
	}
	function deldir($dir) {
		if(!is_dir($dir))return false;
		$dh=opendir($dir);
		while ($file=readdir($dh)) {
			if($file!="." && $file!="..") {
				$fullpath=$dir."/".$file;
				if(!is_dir($fullpath)) {
					unlink($fullpath);
				} else {
					deldir($fullpath);
				}
			}
		}
		closedir($dh);
		if(rmdir($dir)) {
			return true;
		} else {
			return false;
		}
	}
	$scriptpath=str_replace('\\','/',$_SERVER['SCRIPT_NAME']);
	$scriptpath = substr($scriptpath, 0, strrpos($scriptpath, '/'));
	$admin_path = substr($scriptpath, strrpos($scriptpath, '/')+1);
	$RemoteFile = $update['file'];
	$ZipFile = "lylme_spage_update.zip";
	copy($RemoteFile,$ZipFile) or die("无法下载更新包文件！".'<a href="update.php">返回上级</a>');
	if (zipExtract($ZipFile,ROOT)) {
		if($admin_path!='admin' && is_dir(ROOT.'admin')) {
			//修改后台地址
			deldir(ROOT.$admin_path);
			rename(ROOT.'admin',ROOT.$admin_path);
		}
		if(function_exists("opcache_reset"))@opcache_reset();
		$upsql=true;
		unlink($ZipFile);
	} else {
		unlink($ZipFile);
		echo('<script language="javascript">alert("无法解压文件！");window.location.href="./update.php";</script>');
	}
}
if($set=='updatesql' || $upsql==true) {
	$vn=explode('.',str_replace('v','',$conf['version']));
	$vernum =  $vn[0].sprintf("%02d",$vn[1]).sprintf("%02d",$vn[2]);
	if($vernum < 10100) {
		exit("<script language='javascript'>alert(''网站程序版本太旧，不支持直接升级！');window.location.href='./';</script>");
	}
	if($vernum < 10101) {
		$sql = file_get_contents(ROOT.'install/update.sql');
		$version = 'v1.1.1';
	}
	if($vernum < 10103) {
		@unlink(ROOT.'include/head.php');
		@unlink(ROOT.'include/home.php');
		@unlink(ROOT.'include/apply.php');
		@unlink(ROOT.'include/footer.php');
		$sql = $sql.file_get_contents(ROOT.'install/update1.sql');
		$version = 'v1.1.3';
	} 
	if($vernum < 10104) {
		$version = 'v1.1.4';
	}else {
		exit("<script language='javascript'>alert('你的网站已是最新版本！');window.location.href='./update.php';</script>");
	}
	saveSetting('version',$version);
	$sql=explode(';',$sql);
	$t=0;
	$e=0;
	$error='';
	for ($i=0;$i<count($sql);$i++) {
		if (trim($sql[$i])=='')continue;
		if($DB->query($sql[$i])) {
			++$t;
		} else {
			++$e;
			$error.=$DB->error().'\n';
		}
	}
	
	echo('<script language="javascript">alert("网站升级完成！");window.location.href="./update.php";</script>');
}
include './footer.php';
?>