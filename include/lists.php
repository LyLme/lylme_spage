<?php
// +----------------------------------------------------------+
// | LyLme Spage                                              |
// +----------------------------------------------------------+
// | Copyright (c) 2022 LyLme                                 |
// +----------------------------------------------------------+
// | File: lists.php                                          |
// +----------------------------------------------------------+
// | Authors: LyLme <admin@lylme.com>                         |
// | date: 2022-06-10                                         |
// +----------------------------------------------------------+
function lists($htmls) {
	global $DB;
	$groups = $DB->query("SELECT * FROM `lylme_groups` ORDER BY `group_order` ASC");
	// 获取分类
	$i = 0;
	//初始化循环次数
	while ($group = $DB->fetch($groups)) {
		//循环所有分组
		$html = rearr($group,$htmls);
		if($group["group_status"]=='0') {
			continue;
		}
		if(!in_array($group['group_pwd'],$_SESSION['list'])&&!empty($group['group_pwd'])) {
			//如果 分组加密未在认证列表 并且分组设置了密码(不显示分组)
			continue;
		}
		$sql = "SELECT * FROM `lylme_links` WHERE `group_id` = " . $group['group_id']." ORDER BY `link_order` ASC;";
		$group_links = $DB->query($sql);
		$link_num = $DB->num_rows($group_links);
		// 获取返回字段条目数量
		echo $html['g1'].$html['g2'];
		//输出分组图标和标题
		if ($link_num == 0) {
			echo $html['g3'] . "\n\n";
			$i = 0;
			continue;
		}
		while ($link = $DB->fetch($group_links)) {
			// 循环每个链接
			$html = rearr($link,$htmls);
			// 返回指定分组下的所有字段
			$lpwd = true;
			if ($link_num > $i) {
				$i = $i + 1;
				if(!empty($group['group_pwd'])&&!empty($link['link_pwd'])) {
					//分组和链接同时加密
					//忽略链接加密正常显示分组
				} else if(!in_array($link['link_pwd'],$_SESSION['list'])&&!empty($link['link_pwd'])) {
					//当前链接加密
					$lpwd = false;
				}
				if($link['link_status'] && $lpwd ) {
					echo "\n" .$html['l1'].$html['l2'].$html['l3'];
				}
				//输出图标和链接
			}
			if ($link_num == $i) {
				//判断当前分组链接循环完毕
				echo $html['g3'] . "\n\n";
				//输出分类结束标签
				$i = 0;
				break;
				//重置$i为0跳出当前循环
			}
		}
	}
}
?>