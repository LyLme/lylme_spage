<?php
header('Content-Type:application/json');
include_once("../include/common.php");


if (!isset($islogin) || $islogin !== 1) {
	exit("<script>window.location.href='./login.php';</script>");
}
$submit = isset($_GET['submit']) ? $_GET['submit'] : null;
$e = 0;

// 统一 JSON 响应输出
function json_response($code, $msg, $extra = array())
{
	$res = array('code' => $code, 'msg' => $msg);
	if (!empty($extra)) $res = array_merge($res, $extra);
	exit(json_encode($res, JSON_UNESCAPED_UNICODE));
}

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
			json_response(100, '保存错误,请确保带星号的都不为空！');
		} else {
			$sql = "INSERT INTO `lylme_tags` (`tag_id`, `tag_name`, `tag_link`, `tag_target`,`sort`) VALUES (NULL, '" . $name . "', '" . $link . "', '" . $target . "','" . $sort . "');";
			if ($DB->query($sql)) {
				json_response(200, '添加导航菜单 ' . $name . ' 成功！');
			} else {
				json_response(500, '添加导航菜单失败');
			}
		}
		break;

	case 'edit_tag':
		$id = intval($_GET['id']);
		$sort = intval($_POST['sort'] ?: 10);
		$rows2 = $DB->query("select * from lylme_tags where tag_id='$id' limit 1");
		$rows = $DB->fetch($rows2);
		if (!$rows) {
			json_response(404, '该条记录不存在！');
		}
		$name = daddslashes($_POST['name']);
		$link = daddslashes($_POST['link']);
		if ($_POST['target'] == true) {
			$target = 1;
		} else {
			$target = 0;
		}
		if ($name == null or $link == null) {
			json_response(100, '保存错误,请确保带星号的都不为空！');
		} else {
			$sql = "UPDATE `lylme_tags` SET `tag_name` = '" . $name . "', `tag_link` = '" . $link . "', `tag_target` = '" . $target . "', `sort` = '" . $sort . "'  WHERE `lylme_tags`.`tag_id` = " . $id . ";";
			if ($DB->query($sql)) {
				json_response(200, '修改导航菜单 ' . $name . ' 成功！');
			} else {
				json_response(500, '修改导航菜单失败！');
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
			json_response(100, '保存错误,请确保带星号的都不为空！');
		} else {
			// 链接查重：已存在则跳过（忽略末尾斜杠差异），防止重复入库
			$url_check = rtrim($url, '/');
			$exists = $DB->get_row("SELECT `id` FROM `lylme_links` WHERE `url` = '$url_check' OR `url` = '$url_check/' LIMIT 1");

			if ($exists) {
				$data = array('data' => array(
					'id' => $exists['id'],
					'name' => $name,
					'url' => $url,
					'color' => $color,
					'icon' => $icon,
					'group_id' => $group_id,
					'link_desc' => $link_desc,
					'link_keywords' => $link_keywords
				));
				json_response(201, '链接已存在，跳过！ID=' . $exists['id'], $data);
			}
			$sql = "INSERT INTO `lylme_links` (`id`, `name`, `group_id`, `url`, `icon`, `link_desc`, `link_keywords`, `link_order`) VALUES (NULL, '" . $name1 . "', '" . $group_id . "', '" . $url . "', '" . $icon . "', '" . $link_desc . "', '" . $link_keywords . "', '" . $link_order . "');";
			if ($DB->query($sql)) {
				$newid = $DB->insert_id();
				json_response(200, '添加链接 ' . $name . ' 成功！', array('id' => $newid));
			} else {
				json_response(500, '添加链接失败！');
			}
		}
		break;

	case 'edit_link':
		$id = intval($_GET['id']);
		$rows2 = $DB->query("select * from lylme_links where id='$id' limit 1");
		$rows = $DB->fetch($rows2);
		if (!$rows) {
			json_response(404, '该条记录不存在！');
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
			json_response(100, '保存错误,请确保带星号的都不为空！');
		} else {
			$sql = "UPDATE `lylme_links` SET `name` = '" . $name1 . "', `link_desc` = '" . $link_desc . "', `link_keywords` = '" . $link_keywords . "', `url` = '" . $url . "', `icon` = '" . $icon . "', `group_id` = '" . $group_id . "', `link_pwd` = " . $link_pwd . " WHERE `lylme_links`.`id` = '" . $id . "';";
			if ($DB->query($sql)) {
				json_response(200, '修改链接 ' . $name . ' 成功！');
			} else {
				json_response(500, '修改链接失败！');
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
			json_response(100, '保存错误,请确保带星号的都不为空！');
		} else {
			$sql = "INSERT INTO `lylme_sou` (`sou_id`, `sou_alias`, `sou_name`, `sou_hint`, `sou_color`, `sou_link`, `sou_waplink`, `sou_icon`, `sou_st`, `sou_order`) VALUES
(NULL, '" . $alias . "', '" . $name . "', '" . $hint . "', '" . $color . "', '" . $link . "', '" . $waplink . "', '" . $icon . "', '" . $st . "', '" . $sou_order . "');
";
			if ($DB->query($sql)) {
				json_response(200, '添加搜索引擎 ' . $name . ' 成功！');
			} else {
				json_response(500, '添加搜索引擎失败！');
			}
		}
		break;

	case 'edit_sou':
		$id = intval($_GET['id']);
		$rows2 = $DB->query("select * from lylme_sou where sou_id='$id' limit 1");
		$rows = $DB->fetch($rows2);
		if (!$rows) {
			json_response(404, '该条记录不存在！');
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
			json_response(100, '保存错误,请确保带星号的都不为空！');
		} else {
			$sql = "UPDATE `lylme_sou` SET `sou_alias` = '" . $alias . "', `sou_name` = '" . $name . "', `sou_hint` = '" . $hint . "', `sou_color` = '" . $color . "', `sou_link` = '" . $link . "', `sou_waplink` = '" . $waplink . "', `sou_icon` = '" . $icon . "', `sou_st` = '" . $st . "', `sou_order` = '" . $order . "' WHERE `lylme_sou`.`sou_id` = " . $id . ";";
			if ($DB->query($sql)) {
				json_response(200, '修改搜索引擎 ' . $name . ' 成功！');
			} else {
				json_response(500, '修改失败！');
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
		$order = $_POST['link_array'] ?? [];
		//拖拽排序
		$e = 0;
		for ($i = 0; $i < count($_POST["link_array"]); $i++) {
			$link_id = intval($_POST["link_array"][$i]);
			$sql = "UPDATE `lylme_links` SET `link_order` = '" . $i . "' WHERE `lylme_links`.`id` = " . $link_id . ";";
			if (!$DB->query($sql)) {
				$e++;
			}
		}
		if ($e == 0) {
			exit('{"code": 200,"msg":"操作成功！","order":"' . implode(',', $order) . '"}');
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
			exit('{"code": 200,"msg":"启用成功！"}');
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
			exit('{"code": 200,"msg":"禁用成功！"}');
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
			exit('{"code": 200,"msg":"链接删除成功！"}');
		} else {
			exit('{"code": 100,"msg":"错误，失败' . $e . '条"}');
		}
		break;

	//获取链接信息
	case 'geturl':
		$url = $_GET['url'];
		$head = get_head($url);
		if (empty($head['title']) && empty($head['icon'])) json_response(400, 'Unable to access');
		exit(json_encode($head, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		break;
	//链接编辑表单（iframe 弹窗用，输出精简 HTML）
	case 'edit_form':
		header('Content-Type:text/html; charset=utf-8');
		$id = intval($_GET['id']);
		$row2 = $DB->query("select * from lylme_links where id='$id' limit 1");
		$row = $DB->fetch($row2);
		if (!$row) json_response(404, '该条记录不存在！');

		$grouplists = array();
		$pwd_lists  = array();
		$gq = $DB->query("SELECT * FROM `lylme_groups`");
		while ($g = $DB->fetch($gq)) $grouplists[] = $g;
		$pq = $DB->query("SELECT * FROM `lylme_pwd`");
		while ($p = $DB->fetch($pq)) $pwd_lists[] = $p;

		preg_match_all('/<font color=[\"|\']+(.*?)[\"|\']>/i', $row['name'], $color);
		$link_color = isset($color[1][0]) ? $color[1][0] : '';
?>
		<!DOCTYPE html>
		<html lang="zh-CN">

		<head>
			<meta charset="utf-8">
			<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
			<title>编辑链接</title>
			<link href="/assets/admin/css/bootstrap.min.css" rel="stylesheet">
			<link href="/assets/admin/css/materialdesignicons.min.css" rel="stylesheet">
			<link href="/assets/admin/css/style.min.css" rel="stylesheet">
			<link href="/assets/admin/css/coloris.min.css" rel="stylesheet">
			<style>
				body {
					background: #fff;
					padding: 16px 18px;
					font-size: 14px
				}

				.form-group {
					margin-bottom: 14px
				}

				.form-group label {
					font-weight: 600
				}

				.clr-alpha {
					display: none !important
				}

				#loading {
					display: none;
					position: fixed;
					top: 0;
					left: 0;
					right: 0;
					bottom: 0;
					z-index: 9999;
					align-items: center;
					justify-content: center;
					background: rgba(255, 255, 255, .6)
				}
			</style>
		</head>

		<body>
			<h4 style="margin:0 0 14px"><i class="mdi mdi-pencil"></i> 修改链接信息</h4>
			<form id="editLinkForm" action="./ajax_link.php?submit=edit_link&id=<?php echo $id; ?>" method="POST">
				<div class="form-group">
					<label for="edit_url">*URL链接地址:</label>
					<div class="input-group">
						<input type="text" class="form-control" id="edit_url" name="url" placeholder="链接" value="<?php echo htmlspecialchars($row['url']); ?>" required>
						<span class="input-group-btn">
							<button class="btn btn-default" onclick="geturl()" type="button"><i class="mdi mdi-magnify"></i> 获取</button>
						</span>
					</div>
				</div>

				<div class="form-group">
					<label for="urlname">*网站名称:</label>
					<input type="text" class="form-control" id="urlname" name="name" value="<?php echo htmlspecialchars(strip_tags($row['name'])); ?>" required>
				</div>

				<div class="form-group">
					<label for="edit_color">链接颜色(留空默认):</label>
					<input type="text" class="coloris form-control" id="edit_color" onchange="select_color()" name="color" value="<?php echo htmlspecialchars($link_color); ?>" placeholder="点击选择颜色">
				</div>

				<div class="form-group">
					<label for="edit_icon">链接图标:</label>
					<div class="input-group">
						<textarea class="form-control" id="edit_icon" name="icon" placeholder="网站图标"><?php echo htmlspecialchars($row['icon']); ?></textarea>
						<span class="input-group-btn">
							<input type="file" id="file" onchange="uploadimg()" accept="image/png, image/jpeg,image/gif,image/x-icon" style="display:none">
							<button class="btn btn-default" id="uploadImage" onclick="$('#file').click();" type="button">选择</button>
						</span>
					</div>
					<small class="help-block"><b>可选方案：</b><br>
						1. 填写图标的<code>URL</code>地址，如<code>/img/logo.png</code>或<code>http://www.xxx.com/img/logo.png</code><br>
						2. 粘贴图标的<code>SVG</code>代码，<a href="./help.php?doc=icon" target="_blank">查看教程</a><br>
						3. 留空使用默认图标<br>
						4. 从本地上传</small>
				</div>

				<div class="form-group">
					<label for="edit_group">*分组:</label>
					<select class="form-control" id="edit_group" name="group_id">
						<?php foreach ($grouplists as $grouplist): ?>
							<option value="<?php echo $grouplist['group_id']; ?>" <?php echo $grouplist['group_id'] == $row['group_id'] ? ' selected' : ''; ?>><?php echo $grouplist['group_id']; ?> - <?php echo $grouplist['group_name']; ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="form-group">
					<label for="edit_pwd">链接加密:</label>
					<select class="form-control" id="edit_pwd" name="link_pwd" required>
						<?php foreach ($pwd_lists as $pwd_list): ?>
							<option value="<?php echo $pwd_list['pwd_id']; ?>" <?php echo $row['link_pwd'] == $pwd_list['pwd_id'] ? ' selected' : ''; ?>><?php echo $pwd_list['pwd_id']; ?> - <?php echo $pwd_list['pwd_name']; ?> | 密码[<?php echo $pwd_list['pwd_key']; ?>]</option>
						<?php endforeach; ?>
						<option value="0" <?php echo empty($row['link_pwd']) ? ' selected' : ''; ?>>0 - 不加密</option>
					</select>
					<small class="help-block"><code>注意：对链接所在的分组加密后，单独设置的链接加密将会失效</code><br>
						加密后只能通过输入密码访问，使用该功能先配置加密组
						<a href="./pwd.php" target="_blank">管理加密组</a></small>
				</div>

				<div class="form-group">
					<label for="edit_desc">链接描述:</label>
					<textarea rows="2" class="form-control" maxlength="255" id="edit_desc" name="link_desc" placeholder="链接描述"><?php echo htmlspecialchars($row['link_desc']); ?></textarea>
					<small class="help-block">链接描述仅部分主题支持显示和详情页SEO，访问详情页时若为空将自动采集写入，采集失败写入"无"</small>
				</div>

				<div class="form-group">
					<label for="edit_keywords">链接关键词:</label>
					<input type="text" class="form-control" maxlength="512" id="edit_keywords" name="link_keywords" maxlength="512" placeholder="多个关键词用逗号分隔" value="<?php echo htmlspecialchars($row['link_keywords']); ?>">
					<small class="help-block">关键词用于详情页 SEO，为空时访问详情页将自动采集写入</small>
				</div>

				<div class="form-group" style="display:flex;gap:8px;margin-bottom:0">
					<button type="submit" class="btn btn-primary" style="flex:1"><i class="mdi mdi-content-save"></i> 保存修改</button>
					<button type="button" class="btn btn-default" onclick="closeEditFrame()" style="flex:1"><i class="mdi mdi-close"></i> 关闭</button>
				</div>
				<div style="height:18px"></div>
			</form>

			<div id="loading"><i class="mdi mdi-loading mdi-spin" style="font-size:40px;color:#4a6cf7"></i></div>

			<script type="text/javascript" src="/assets/admin/js/jquery.min.js"></script>
			<script type="text/javascript" src="/assets/admin/js/layer.min.js"></script>
			<script type="text/javascript" src="/assets/admin/js/coloris.min.js"></script>
			<script type="text/javascript" src="/assets/admin/js/bootstrap-notify.min.js"></script>
			<script type="text/javascript" src="/assets/admin/js/lightyear.js"></script>
			<script type="text/javascript">
				Coloris({
					el: '.coloris',
					swatches: ['#000000', '#555555', '#666666', '#264653', '#2a9d8f', '#f4a261', '#e76f51', '#ff0000', '#d62828', '#023e8a', '#0077b6', '#0096c7']
				});

				function select_color() {
					var fontcolor = $('input[name="color"]').val();
					$('#urlname').css("color", fontcolor);
				}
				select_color();

				// 关闭弹窗
				function closeEditFrame() {
					if (window.parent && window.parent.layer) {
						var index = window.parent.layer.getFrameIndex(window.name);
						if (index) window.parent.layer.close(index);
					}
				}
				function truncateText(s, max) {
					if (s == null) return '';
					s = String(s);
					var chars = Array.from(s);
					if (chars.length <= max) return s;
					return chars.slice(0, Math.max(0, max - 3)).join('') + '...';
				}

				// 获取网站标题/图标
				function geturl() {
					var url = $("input[name=\'url\']").val();
					if (!url) {
						layer.msg('链接地址不能为空');
						return false;
					}
					$('#loading').css("display", "flex");
					if (!/^http[s]?:\/\/+/.test(url) && url != "") {
						var url = "http://" + url;
						$("input[name=\'url\']").val(url);
					}
					$.ajax({
						url: "ajax_link.php?submit=geturl",
						type: "GET",
						dataType: "json",
						data: {
							url: url
						},
						success: function(data) {
							$("input[name='name']").val(truncateText(data.title || '', 255));
							$("textarea[name='link_desc']").val(truncateText(data.description || '', 255));
							$("input[name='link_keywords']").val(truncateText(data.keywords || '', 512));
							if (!data.title && !data.icon) {
								layer.msg('获取失败，请手动填写');
							} else if (!data.icon) {
								layer.msg('未获取到网站图标');
							}
							layer.msg('正则抓取目标网站图标...');
							downloadimg(data.icon, url);
							$('#loading').css("display", "none");
							return true;
						},
						error: function(data) {
							layer.msg('获取失败，目标网站无法访问或防火墙限制！');
							$('#loading').css("display", "none");
							return false;
						}
					});
				}

				// 抓取网站图标
				function downloadimg(url, referer) {
					if (!url) return false;
					$.ajax({
						url: "/include/file.php",
						type: "POST",
						dataType: "json",
						data: {
							url: url,
							referer: referer
						},
						success: function(data) {
							if (data.code == '200') {
								$("textarea[name='icon']").val(data.url);
							} else {
								layer.msg(data.msg);
							}
							return true;
						},
						error: function() {
							layer.msg('服务器错误');
							return false;
						}
					});
				}

				// 上传图标
				function uploadimg() {
					var file = $("#file")[0];
					if (!file || !file.files || !file.files[0]) return false;
					var formData = new FormData();
					formData.append("file", file.files[0]);
					$.ajax({
						method: 'POST',
						url: '/include/file.php',
						data: formData,
						timeout: 20000,
						cache: false,
						processData: false,
						contentType: false,
						dataType: "JSON",
						success: function(data) {
							if (data.code == '200') {
								layer.msg(data.msg);
								$("textarea[name='icon']").val(data.url);
							} else {
								layer.msg(data.msg);
							}
							return true;
						},
						error: function() {
							layer.msg('服务器错误');
							return false;
						}
					});
				}

				// 表单 AJAX 提交：成功后通知父页面（检测弹窗）刷新并保留结果
				document.getElementById('editLinkForm').addEventListener('submit', function(event) {
					event.preventDefault();
					var form = this;
					var xhr = new XMLHttpRequest();
					xhr.open('POST', form.action, true);
					xhr.onreadystatechange = function() {
						if (xhr.readyState === 4 && xhr.status === 200) {
							var text = xhr.responseText;
							var resp = null;
							try {
								resp = JSON.parse(text);
							} catch (e) {}
							if ((resp && resp.code == 200) || text.indexOf('成功') >= 0) {
								var msg = resp ? resp.msg : text;
								if (window.parent && window.parent.editLinkSaved) {
									window.parent.editLinkSaved(<?php echo $id; ?>, msg);
								} else {
									lightyear.notify(msg, 'success', 1200);
								}
							} else {
								var msg = resp ? resp.msg : text;
								lightyear.notify(msg, 'danger', 3000);
							}
						}
					};
					xhr.send(new FormData(form));
				});
			</script>
		</body>

		</html>
<?php
		break;
	//检查更新（异步）
	case 'check_update':
		$update = update();
		file_put_contents('log.txt', date("Ym"));
		$content = "<?php\nreturn " . var_export($update, true) . "\n?>";
		file_put_contents('cache.php', $content);
		$current_version = isset($conf['version']) ? $conf['version'] : (isset($GLOBALS['conf']['version']) ? $GLOBALS['conf']['version'] : '');
		$result = array(
			'code' => 200,
			'version' => isset($update['version']) ? $update['version'] : '',
			'current_version' => $current_version,
			'update_log' => isset($update['update_log']) ? $update['update_log'] : '',
			'file' => isset($update['file']) ? $update['file'] : ''
		);
		exit(json_encode($result, JSON_UNESCAPED_UNICODE));
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
