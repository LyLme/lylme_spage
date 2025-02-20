<?php
header('Content-Type:application/json');
include_once("../include/common.php");


if (isset($islogin) == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");
$submit = isset($_GET['submit']) ? $_GET['submit'] : null;
$e = 0;
switch ($submit) {



	case 'add_tag':
		$name = $_POST['name'];
		$link = $_POST['link'];
		$sort = $_POST['sort'] ?: 10;
		if ($_POST['target'] == true) {
			$target = 1;
		} else {
			$target = 0;
		}
		if ($name == null or $link == null) {
			echo '保存错误,请确保带星号的都不为空！';
		} else {
			$sql = "INSERT INTO `lylme_tags` (`tag_id`, `tag_name`, `tag_link`, `tag_target`,`sort`) VALUES (NULL, '" . $name . "', '" . $link . "', '" . $target . "','" . $sort . "');";
			if ($DB->query($sql)) {
				echo '添加导航菜单 ' . $name . ' 成功！';
			} else {
				echo '添加导航菜单失败';
			}
		}
		break;

	case 'edit_tag':
		$id = $_GET['id'];
		$sort = $_POST['sort'] ?: 10;
		$rows2 = $DB->query("select * from lylme_tags where tag_id='$id' limit 1");
		$rows = $DB->fetch($rows2);
		if (!$rows) {
			exit('该条记录不存在！');
		}
		$name = $_POST['name'];
		$link = $_POST['link'];
		if ($_POST['target'] == true) {
			$target = 1;
		} else {
			$target = 0;
		}
		if ($name == null or $link == null) {
			echo '保存错误,请确保带星号的都不为空！';
		} else {
			$sql = "UPDATE `lylme_tags` SET `tag_name` = '" . $name . "', `tag_link` = '" . $link . "', `tag_target` = '" . $target . "', `sort` = '" . $sort . "'  WHERE `lylme_tags`.`tag_id` = " . $id . ";";
			if ($DB->query($sql)) {
				echo '修改导航菜单 ' . $name . ' 成功！';
			} else {
				echo '修改导航菜单失败！';
			}
		}
		break;

	case 'add_link':
		$color = $_POST['color'];
		$name = $_POST['name'];
		if (empty($color)) {
			$name1 = $name;
		} else {
			$name1 = '<font color="' . $color . '">' . $name . '</font>';
		}
		$url = $_POST['url'];
		$icon = $_POST['icon'];
		$group_id = $_POST['group_id'];
		$link_order = $linksrows + 1;
		if ($name == null or $url == null) {
			exit('保存错误,请确保带星号的都不为空！');
		} else {
			$sql = "INSERT INTO `lylme_links` (`id`, `name`, `group_id`, `url`, `icon`, `link_desc`,`link_order`) VALUES (NULL, '" . $name1 . "', '" . $group_id . "', '" . $url . "', '" . $icon . "', '" . $name . "', '" . $link_order . "');";
			if ($DB->query($sql)) {
				exit('添加链接 ' . $name . ' 成功！');
			} else {
				exit('添加链接失败！');
			}
		}
		break;

	case 'edit_link':
		$id = $_GET['id'];
		$rows2 = $DB->query("select * from lylme_links where id='$id' limit 1");
		$rows = $DB->fetch($rows2);
		if (!$rows) {
			exit('该条记录不存在！');
		}
		$color = $_POST['color'];
		$name = $_POST['name'];
		if (empty($color)) {
			$name1 = $name;
		} else {
			$name1 = '<font color="' . $color . '">' . $name . '</font>';
		}
		$url = $_POST['url'];
		$icon = $_POST['icon'];
		$link_pwd = $_POST['link_pwd'];
		$group_id = $_POST['group_id'];
		if ($name == null or $url == null) {
			echo '保存错误,请确保带星号的都不为空！';
		} else {
			$sql = "UPDATE `lylme_links` SET `name` = '" . $name1 . "', `url` = '" . $url . "', `icon` = '" . $icon . "', `group_id` = '" . $group_id . "', `link_pwd` = " . $link_pwd . " WHERE `lylme_links`.`id` = '" . $id . "';";
			//   exit($sql);
			if ($DB->query($sql)) {
				echo '修改链接 ' . $name . ' 成功！';
			} else {
				echo '修改链接失败！';
			}
		}
		break;

	case 'add_sou':
		$name = $_POST['name'];
		$alias = $_POST['alias'];
		$hint = $_POST['hint'];
		$link = $_POST['link'];
		$waplink = $_POST['waplink'];
		$color = $_POST['color'];
		$icon = $_POST['icon'];
		if ($_POST['st'] == true) {
			$st = 1;
		} else {
			$st = 0;
		}
		$sou_order = $sousrows + 1;
		if (empty($name) && empty($alias) && empty($hint) && empty($link) && empty($color) && empty($icon)) {
			echo '保存错误,请确保带星号的都不为空！';
		} else {
			$sql = "INSERT INTO `lylme_sou` (`sou_id`, `sou_alias`, `sou_name`, `sou_hint`, `sou_color`, `sou_link`, `sou_waplink`, `sou_icon`, `sou_st`, `sou_order`) VALUES
(NULL, '" . $alias . "', '" . $name . "', '" . $hint . "', '" . $color . "', '" . $link . "', '" . $waplink . "', '" . $icon . "', '" . $st . "', '" . $sou_order . "');
";
			if ($DB->query($sql)) {
				echo '添加搜索引擎 ' . $name . ' 成功！';
			} else {
				echo '添加搜索引擎失败！';
			}
		}
		break;

	case 'edit_sou':
		$id = $_GET['id'];
		$rows2 = $DB->query("select * from lylme_sou where sou_id='$id' limit 1");
		$rows = $DB->fetch($rows2);
		if (!$rows) {
			exit('该条记录不存在！');
		}
		$name = $_POST['name'];
		$alias = $_POST['alias'];
		$hint = $_POST['hint'];
		$link = $_POST['link'];
		$waplink = $_POST['waplink'];
		$color = $_POST['color'];
		$icon = $_POST['icon'];
		$order = $_POST['order'];
		if ($_POST['st'] == true) {
			$st = 1;
		} else {
			$st = 0;
		}

		if (empty($name) && empty($alias) && empty($hint) && empty($link) && empty($color) && empty($icon) && empty($order)) {
			echo '保存错误,请确保带星号的都不为空！';
		} else {
			$sql = "UPDATE `lylme_sou` SET `sou_alias` = '" . $alias . "', `sou_name` = '" . $name . "', `sou_hint` = '" . $hint . "', `sou_color` = '" . $color . "', `sou_link` = '" . $link . "', `sou_waplink` = '" . $waplink . "', `sou_icon` = '" . $icon . "', `sou_st` = '" . $st . "', `sou_order` = '" . $order . "' WHERE `lylme_sou`.`sou_id` = " . $id . ";";
			if ($DB->query($sql)) {
				echo '修改搜索引擎 ' . $name . ' 成功！';
			} else {
				echo '修改失败！';
			}
		}
		break;

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
