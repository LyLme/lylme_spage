<?php
include_once("../include/common.php");
if(isset($islogin)==1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");
$submit = isset($_GET['submit']) ? $_GET['submit'] : null;
switch($submit) {
   
	//修改分组
	case 'set_group':
	    foreach($_POST['links'] as $lk=> $lv) {
    		$sql = "UPDATE `lylme_links` SET `group_id` = '".$_POST['group_id']."' WHERE `lylme_links`.`id` = ".$lv.";";
    		$DB->query($sql);
	    }
	break;
	case 'allorder':
	    //拖拽排序
	    for ($i=0; $i<count($_POST["link_array"]); $i++) {
		$sql = "UPDATE `lylme_links` SET `link_order` = '".$i."' WHERE `lylme_links`.`id` = ".$_POST["link_array"][$i].";";
		$DB->query($sql);
	}
	break;
	case 'on':
	    //链接启用
	foreach($_POST['links'] as $lk=> $lv) {
		$sql = "UPDATE `lylme_links` SET `link_status` = '1' WHERE `lylme_links`.`id` = ".$lv.";";
		$DB->query($sql);
	}
	break;
	case 'off':
	    //链接禁用
	foreach($_POST['links'] as $lk=> $lv) {
		$sql = "UPDATE `lylme_links` SET `link_status` = '0' WHERE `lylme_links`.`id` = ".$lv.";";
		$DB->query($sql);
	}
	break;
	case 'del':
	    //链接删除
	foreach($_POST['links'] as $lk=> $lv) {
		$sql = "DELETE FROM `lylme_links` WHERE `lylme_links`.`id` = ".$lv.";";
		$DB->query($sql);
	}
	break;
	
	 //获取链接信息
    case 'geturl':
    	function get_head($url) {
        ini_set("user_agent","Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/101.0.4951.54 Safari/537.36 Edg/101.0.1210.39 Lylme/11.24");
		$opts = array(
		    'http'=>array( 
		    'method'=>"GET", 
		    'timeout'=>4
		    ) 
		);
		$contents = @file_get_contents("compress.zlib://".$url, false, stream_context_create($opts));
		preg_match('/<title>(.*?)<\/title>/is',$contents,$title);  // 获取网站标题
		preg_match('/<link rel=".*?icon" * href="(.*?)".*?>/is', $contents,$icon);  // 获取网站icon
		preg_match('/<meta.+?charset=[^\w]?([-\w]+)/i', $contents,$charset);  //获取网站编码
		$get_heads = array();
		$get_heads['charset']=$charset[1];
		$get_heads['title'] = str_replace("'","\"",preg_replace("/\s/","",$title[1]));
		$get_heads['icon'] = get_urlpath(preg_replace("/\s/","",$icon[1]),$url);
		if(strtolower($get_heads['charset'])!="uft-8"){
		    // 将非UTF-8编码转换
		    $get_heads['title']  = iconv($get_heads['charset'], "UTF-8",$get_heads['title']);
		    $get_heads['icon']  = iconv($get_heads['charset'], "UTF-8",$get_heads['icon']);
		}
		return $get_heads;
	}
	$head = get_head($_POST['url']);
	if(empty($head['title'])&&empty($head['icon']))exit('Unable to access');
	header('Content-Type:application/json');
	exit('{"title": "'.$head['title'].'", "icon": "'.$head['icon'].'","charset": "'.$head['charset'].'"}');
	break;
	//检测更新
	case 'update':
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
    	$RemoteFile = $_POST['file'];
    	$ZipFile = "lylme_spage-update.zip";
    	copy($RemoteFile,$ZipFile) or die("无从更新服务器下载更新包文件！");
    	if (zipExtract($ZipFile,ROOT)) {
    		if($admin_path!='admin' && is_dir(ROOT.'admin')) {
    			//修改后台地址
    			deldir(ROOT.$admin_path);
    			rename(ROOT.'admin',ROOT.$admin_path);
    		}
    		unlink($ZipFile);
    		exit ('success');
    	}
    	else {
    		unlink($ZipFile);
    		exit('无法解压文件！请手动下载更新包解压');
    	}
    break;
	default:
	    	exit('error');
	break;
}