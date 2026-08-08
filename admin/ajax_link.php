<?php
header('Content-Type:application/json');
include_once("../include/common.php");


if (!isset($islogin) || $islogin !== 1) {
    exit("<script>window.location.href='./login.php';</script>");
}
$submit = isset($_GET['submit']) ? $_GET['submit'] : null;
$e = 0;

// 按字符数截断文本并追加省略号，防止超出数据库字段长度
function truncate_text($str, $max)
{
    $str = (string)$str;
    $max = max(1, (int)$max);
    if (function_exists('mb_strlen')) {
        if (mb_strlen($str, 'UTF-8') > $max) {
            return mb_substr($str, 0, max(0, $max - 3), 'UTF-8') . '...';
        }
    } elseif (strlen($str) > $max) {
        return substr($str, 0, max(0, $max - 3)) . '...';
    }
    return $str;
}

switch ($submit) {



	case 'add_tag':
		$name = daddslashes($_POST['name']);
		$link = daddslashes($_POST['link']);
		$sort = intval($_POST['sort'] ?: 10);
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
		$id = intval($_GET['id']);
		$sort = intval($_POST['sort'] ?: 10);
		$rows2 = $DB->query("select * from lylme_tags where tag_id='$id' limit 1");
		$rows = $DB->fetch($rows2);
		if (!$rows) {
			exit('该条记录不存在！');
		}
		$name = daddslashes($_POST['name']);
		$link = daddslashes($_POST['link']);
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
		$color = daddslashes(truncate_text(isset($_POST['color']) ? $_POST['color'] : '', 32));
		$name = daddslashes(truncate_text(isset($_POST['name']) ? $_POST['name'] : '', 255));
		if (empty($color)) {
			$name1 = $name;
		} else {
			$name1 = '<font color="' . $color . '">' . $name . '</font>';
		}
		$url = daddslashes(truncate_text(isset($_POST['url']) ? $_POST['url'] : '', 255));
		$icon = daddslashes(isset($_POST['icon']) ? $_POST['icon'] : '');
		$group_id = intval($_POST['group_id']);
		$link_order = $linksrows + 1;
		$link_desc = isset($_POST['link_desc']) ? daddslashes(truncate_text($_POST['link_desc'], 255)) : '';
		$link_keywords = isset($_POST['link_keywords']) ? daddslashes(truncate_text($_POST['link_keywords'], 512)) : '';
		if ($name == null or $url == null) {
			exit('保存错误,请确保带星号的都不为空！');
		} else {
			// 链接查重：已存在则跳过（忽略末尾斜杠差异），防止重复入库
			$url_check = rtrim($url, '/');
			$exists = $DB->get_row("SELECT `id` FROM `lylme_links` WHERE `url` = '$url_check' OR `url` = '$url_check/' LIMIT 1");
			if ($exists) {
				exit('链接已存在，跳过！ID=' . $exists['id']);
			}
			$sql = "INSERT INTO `lylme_links` (`id`, `name`, `group_id`, `url`, `icon`, `link_desc`, `link_keywords`, `link_order`) VALUES (NULL, '" . $name1 . "', '" . $group_id . "', '" . $url . "', '" . $icon . "', '" . $link_desc . "', '" . $link_keywords . "', '" . $link_order . "');";
			if ($DB->query($sql)) {
				$newid = $DB->insert_id();
				exit('添加链接 ' . $name . ' 成功！ID=' . $newid);
			} else {
				exit('添加链接失败！');
			}
		}
		break;

	case 'edit_link':
		$id = intval($_GET['id']);
		$rows2 = $DB->query("select * from lylme_links where id='$id' limit 1");
		$rows = $DB->fetch($rows2);
		if (!$rows) {
			exit('该条记录不存在！');
		}
		$color = daddslashes(truncate_text(isset($_POST['color']) ? $_POST['color'] : '', 32));
		$name = daddslashes(truncate_text(isset($_POST['name']) ? $_POST['name'] : '', 255));
		if (empty($color)) {
			$name1 = $name;
		} else {
			$name1 = '<font color="' . $color . '">' . $name . '</font>';
		}
		$url = daddslashes(truncate_text(isset($_POST['url']) ? $_POST['url'] : '', 255));
		$icon = daddslashes(isset($_POST['icon']) ? $_POST['icon'] : '');
		$link_desc = isset($_POST['link_desc']) ? daddslashes(truncate_text($_POST['link_desc'], 255)) : '';
		$link_keywords = isset($_POST['link_keywords']) ? daddslashes(truncate_text($_POST['link_keywords'], 512)) : '';
		$link_pwd = intval($_POST['link_pwd']);
		$group_id = intval($_POST['group_id']);
		if ($name == null or $url == null) {
			echo '保存错误,请确保带星号的都不为空！';
		} else {
			$sql = "UPDATE `lylme_links` SET `name` = '" . $name1 . "', `link_desc` = '" . $link_desc . "', `link_keywords` = '" . $link_keywords . "', `url` = '" . $url . "', `icon` = '" . $icon . "', `group_id` = '" . $group_id . "', `link_pwd` = " . $link_pwd . " WHERE `lylme_links`.`id` = '" . $id . "';";
			//   exit($sql);
			if ($DB->query($sql)) {
				echo '修改链接 ' . $name . ' 成功！';
			} else {
				echo '修改链接失败！';
			}
		}
		break;

	case 'add_sou':
		$name = daddslashes($_POST['name']);
		$alias = daddslashes($_POST['alias']);
		$hint = daddslashes($_POST['hint']);
		$link = daddslashes($_POST['link']);
		$waplink = daddslashes($_POST['waplink']);
		$color = daddslashes($_POST['color']);
		$icon = daddslashes($_POST['icon']);
		if ($_POST['st'] == true) {
			$st = 1;
		} else {
			$st = 0;
		}
	$sou_order = isset($sousrows) ? (int)$sousrows + 1 : 1;
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
		$id = intval($_GET['id']);
		$rows2 = $DB->query("select * from lylme_sou where sou_id='$id' limit 1");
		$rows = $DB->fetch($rows2);
		if (!$rows) {
			exit('该条记录不存在！');
		}
		$name = daddslashes($_POST['name']);
		$alias = daddslashes($_POST['alias']);
		$hint = daddslashes($_POST['hint']);
		$link = daddslashes($_POST['link']);
		$waplink = daddslashes($_POST['waplink']);
		$color = daddslashes($_POST['color']);
		$icon = daddslashes($_POST['icon']);
		$order = intval($_POST['order']);
		if (isset($_POST['st']) && $_POST['st'] == true) {
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
		$group_id = intval($_POST['group_id']);
		foreach ($_POST['links'] as $lk => $lv) {
			$lv = intval($lv);
			$sql = "UPDATE `lylme_links` SET `group_id` = '" . $group_id . "' WHERE `lylme_links`.`id` = " . $lv . ";";
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
			$link_id = intval($_POST["link_array"][$i]);
			$sql = "UPDATE `lylme_links` SET `link_order` = '" . $i . "' WHERE `lylme_links`.`id` = " . $link_id . ";";
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
		$pwd_id = intval($_POST['pwd_id']);
		foreach ($_POST['links'] as $lk => $lv) {
			$lv = intval($lv);
			$sql = "UPDATE `lylme_links` SET `link_pwd` = '" . $pwd_id . "' WHERE `lylme_links`.`id` = " . $lv . ";";
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
			$lv = intval($lv);
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
			$lv = intval($lv);
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
			$lv = intval($lv);
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
				// 防护Zip Slip漏洞：遍历检查所有条目防止路径遍历
				$dest = rtrim(str_replace('\\', '/', realpath($dest) ?: $dest), '/') . '/';
				for ($i = 0; $i < $zip->numFiles; $i++) {
					$entry = $zip->getNameIndex($i);
					if ($entry === false) {
						continue;
					}
					$filepath = $dest . str_replace('\\', '/', $entry);
					$realpath = str_replace('\\', '/', realpath(dirname($filepath)) ?: dirname($filepath));
					// 确保解压路径在目标目录内
					if (strpos($realpath . '/', $dest) !== 0) {
						$zip->close();
						return false;
					}
				}
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
		// 安全验证：admin_path不能为空、不能包含路径遍历字符，且只允许安全的目录名字符
		if (empty($admin_path) || !preg_match('/^[A-Za-z0-9_-]+$/', $admin_path)) {
			exit('{"code": -2,"msg":"无效的管理员路径！"}');
		}
		$update  = require('cache.php');
		// 
		if (empty($update) || !$update['switch']) {
			exit('{"code": -99,"msg":"更新未经鉴权！"}');
		}
		// 修复逻辑错误导致的任意文件上传rce
		$RemoteFile = $_POST['file'];
		$host = parse_url($RemoteFile, PHP_URL_HOST);
		$allowed_hosts = ['cdn.lylme.com'];  // 使用域名白名单校验
		if (!$host || !in_array($host, $allowed_hosts, true)) {
			exit('{"code": -1,"msg":"更新文件校验不通过！"}');
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
			// 更新完成后，将 include/common.php 中的 ADMIN_PATH 常量同步为实际的后台目录
			$commonFile = ROOT . 'include/common.php';
			$commonContent = @file_get_contents($commonFile);
			if ($commonContent === false) {
				unlink($ZipFile);
				exit('{"code": 10,"msg":"更新成功，但无法读取 include/common.php，请手动确认后台目录配置！"}');
			}
			$newDefine = "define('ADMIN_PATH', '" . $admin_path . "')";
			$newContent = preg_replace("/define\(['\"]ADMIN_PATH['\"]\s*,\s*['\"][^'\"]*['\"]\s*\)/", $newDefine, $commonContent, 1);
			if ($newContent === null) {
				unlink($ZipFile);
				exit('{"code": 10,"msg":"更新成功，但无法解析 include/common.php 配置，请手动确认后台目录配置！"}');
			}
			if ($newContent !== $commonContent && @file_put_contents($commonFile, $newContent) === false) {
				unlink($ZipFile);
				exit('{"code": 10,"msg":"更新成功，但无法写入 include/common.php 后台目录配置，请手动修改 ADMIN_PATH 常量！"}');
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
