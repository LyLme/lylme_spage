<?php
/* 
 * @Description: 
 * @Author: LyLme admin@lylme.com
 * @Date: 2024-01-23 12:25:35
 * @LastEditors: LyLme admin@lylme.com
 * @LastEditTime: 2024-04-13 17:25:21
 * @FilePath: /lylme_spage/admin/ajax_link.php
 * @Copyright (c) 2024 by LyLme, All Rights Reserved. 
 */
header('Content-Type:application/json');
include_once("../include/common.php");


if (isset($islogin) == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");
$submit = isset($_GET['submit']) ? $_GET['submit'] : null;
$e = 0;
switch ($submit) {

		//修改分组
	case 'set_group':
		foreach ($_POST['links'] as $lk => $lv) {
			$sql = "UPDATE `lylme_links` SET `group_id` = '" . $_POST['group_id'] . "' WHERE `lylme_links`.`id` = " . $lv . ";";
			if (!$DB->query($sql)) {
				$e++;
			}
		}
		if ($e == 0) {
			exit('{"code": 200,"msg":"操作成功！"}');
		} else {
			exit('{"code": 100,"msg":"错误，失败' . $e . '条"}');
		}
		break;
	case 'allorder':
		//拖拽排序
		for ($i = 0; $i < count($_POST["link_array"]); $i++) {
			$sql = "UPDATE `lylme_links` SET `link_order` = '" . $i . "' WHERE `lylme_links`.`id` = " . $_POST["link_array"][$i] . ";";
			if (!$DB->query($sql)) {
				$e++;
			}
		}
		if ($e == 0) {
			exit('{"code": 200,"msg":"操作成功！"}');
		} else {
			exit('{"code": 100,"msg":"错误，失败' . $e . '条"}');
		}
		break;
	case 'pwd_link':
		//链接加密
		foreach ($_POST['links'] as $lk => $lv) {
			$sql = "UPDATE `lylme_links` SET `link_pwd` = '" . $_POST['pwd_id'] . "' WHERE `lylme_links`.`id` = " . $lv . ";";
			if (!$DB->query($sql)) {
				$e++;
			}
		}
		if ($e == 0) {
			exit('{"code": 200,"msg":"操作成功！"}');
		} else {
			exit('{"code": 100,"msg":"错误，失败' . $e . '条"}');
		}
		break;
	case 'on':
		//链接启用
		foreach ($_POST['links'] as $lk => $lv) {
			$sql = "UPDATE `lylme_links` SET `link_status` = '1' WHERE `lylme_links`.`id` = " . $lv . ";";
			if (!$DB->query($sql)) {
				$e++;
			}
		}
		if ($e == 0) {
			exit('{"code": 200,"msg":"操作成功！"}');
		} else {
			exit('{"code": 100,"msg":"错误，失败' . $e . '条"}');
		}
		break;
	case 'off':
		//链接禁用
		foreach ($_POST['links'] as $lk => $lv) {
			$sql = "UPDATE `lylme_links` SET `link_status` = '0' WHERE `lylme_links`.`id` = " . $lv . ";";
			if (!$DB->query($sql)) {
				$e++;
			}
		}
		if ($e == 0) {
			exit('{"code": 200,"msg":"操作成功！"}');
		} else {
			exit('{"code": 100,"msg":"错误，失败' . $e . '条"}');
		}
		break;
	case 'del':
		//链接删除
		foreach ($_POST['links'] as $lk => $lv) {
			$sql = "DELETE FROM `lylme_links` WHERE `lylme_links`.`id` = " . $lv . ";";
			if (!$DB->query($sql)) {
				$e++;
			}
		}
		if ($e == 0) {
			exit('{"code": 200,"msg":"操作成功！"}');
		} else {
			exit('{"code": 100,"msg":"错误，失败' . $e . '条"}');
		}
		break;

		//获取链接信息
	case 'geturl':
		$url = $_GET['url'];
		$head = get_head($url);
		if (empty($head['title']) && empty($head['icon'])) exit('Unable to access');
		exit(json_encode($head, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));  //输出json
		break;
		//检测更新
	case 'update':
		function zipExtract($src, $dest)
		{
			$zip = new ZipArchive();
			if ($zip->open($src) === true) {
				$zip->extractTo($dest);
				$zip->close();
				return true;
			}
			return false;
		}
		function deldir($dir)
		{
			if (!is_dir($dir)) return false;
			$dh = opendir($dir);
			while ($file = readdir($dh)) {
				if ($file != "." && $file != "..") {
					$fullpath = $dir . "/" . $file;
					if (!is_dir($fullpath)) {
						unlink($fullpath);
					} else {
						deldir($fullpath);
					}
				}
			}
			closedir($dh);
			if (rmdir($dir)) {
				return true;
			} else {
				return false;
			}
		}
		$scriptpath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
		$scriptpath = substr($scriptpath, 0, strrpos($scriptpath, '/'));
		$admin_path = substr($scriptpath, strrpos($scriptpath, '/') + 1);
		$update  = require('cache.php');
		if (!empty($update) && $update['switch']) {
			if (!$update['file'] == $_POST['file']) {
				exit('{"code": -1,"msg":"更新文件校验不通过！"}');
			}
		} else {
			exit('{"code": -99,"msg":"更新未经鉴权！"}');
		}
		$RemoteFile = $_POST['file'];
		$ZipFile = "lylme_spage-update.zip";
		copy($RemoteFile, $ZipFile) or die('{"code": 400,"msg":"无从更新服务器获取更新资源包！"}');
		if (zipExtract($ZipFile, ROOT)) {
			if ($admin_path != 'admin' && is_dir(ROOT . 'admin')) {
				//修改后台地址
				deldir(ROOT . $admin_path);
				rename(ROOT . 'admin', ROOT . $admin_path);
			}
			unlink($ZipFile);
			exit('{"code": 200,"msg":"更新成功"}');
		} else {
			unlink($ZipFile);
			exit('{"code": 10,"msg":"无法解压文件！请手动下载更新包解压"}');
		}
		break;
	default:
		exit('{"code": -2,"msg":"error"}');
		break;
}
