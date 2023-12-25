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
	case 'pwd_link':
	    //链接加密
	     foreach($_POST['links'] as $lk=> $lv) {
    		$sql = "UPDATE `lylme_links` SET `link_pwd` = '".$_POST['pwd_id']."' WHERE `lylme_links`.`id` = ".$lv.";";
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
        $url = $_GET['url'];
    	$head = get_head($url);
    	if(empty($head['title'])&&empty($head['icon']))exit('Unable to access');
    	//download_img($url,head['icon']);
    	header('Content-Type:application/json');
    	exit(json_encode($head,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));  //输出json
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