<?php
/* 
 * @Description: 渲染页面
 * @Author: LyLme admin@lylme.com
 * @Date: 2024-01-23 12:25:35
 * @LastEditors: LyLme admin@lylme.com
 * @LastEditTime: 2024-04-14 05:43:14
 * @FilePath: /lylme_spage/include/lists.php
 * @Copyright (c) 2024 by LyLme, All Rights Reserved. 
 */
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
function listjson()
{
    global $DB;
    $groups = $DB->query("SELECT * FROM `lylme_groups` ORDER BY `group_order` ASC");
    // 获取分类
    $i = 0;
    $g = 0;
    $arr = array();
    //初始化循环次数
    while ($group = $DB->fetch($groups)) {
        //循环所有分组
        if($group["group_status"] == '0') {
            continue;
        }
        if(!in_array($group['group_pwd'], $_SESSION['list']) && !empty($group['group_pwd'])) {
            //如果 分组加密未在认证列表 并且分组设置了密码(不显示分组)
            continue;
        }
        $sql = "SELECT * FROM `lylme_links` WHERE `group_id` = " . $group['group_id'] . " ORDER BY `link_order` ASC;";
        $group_links = $DB->query($sql);
        $link_num = $DB->num_rows($group_links);
        // 获取返回字段条目数量
        $arr[$g] = array("id" => $group['group_id'],"title" => $group['group_name'],"icon" => $group['group_icon'],);
        //输出分组图标和标题

        while ($link = $DB->fetch($group_links)) {
            // 循环每个链接
            $arr[$g]['items'][] = array("id" => $link['id'],
            "title" => $link['name'],
            "alias" => 'link'.$link['id'],
            "keyword" => $link['name'],
            "category_id" => $group['group_id'],
            "icon" => $link['icon'],
            "url" =>$link['url'],
            "out" => true);
            // 返回指定分组下的所有字段
            $lpwd = true;
            if ($link_num > $i) {
                $i = $i + 1;
                if(!empty($group['group_pwd']) && !empty($link['link_pwd'])) {
                    //分组和链接同时加密
                    //忽略链接加密正常显示分组
                } elseif(!in_array($link['link_pwd'], $_SESSION['list']) && !empty($link['link_pwd'])) {
                    //当前链接加密
                    $lpwd = false;
                }
                if($link['link_status'] && $lpwd) {
                    
                }
                //输出图标和链接
            }
        }
        $g++;
    }
    return $arr;
}
function strexists($string, $find)
{
    return !(strpos($string, $find) === false);
}
function dstrpos($string, $arr)
{
    if (empty($string)) {
        return false;
    }
    foreach ($arr as $v) {
        if (strpos($string, $v) !== false) {
            return true;
        }
    }
    return false;
}
//判断移动端
function checkmobile()
{
    $useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
    $ualist = array('android', 'midp', 'nokia', 'mobile', 'iphone', 'ipod', 'blackberry', 'windows phone');
    if ((dstrpos($useragent, $ualist) || strexists($_SERVER['HTTP_ACCEPT'], "VND.WAP") || strexists(isset($_SERVER['HTTP_VIA']), "wap"))) {
        return true;
    } else {
        return false;
    }
}
//CDN
function cdnpublic($cdnpublic)
{
    if (empty($cdnpublic)) {
        return '.';
    } else {
        return $cdnpublic . $GLOBALS['version'];
    }
}

$cdnpublic = cdnpublic($conf['cdnpublic']);
$templatepath = './template/' . $conf["template"];
$template =  $templatepath . '/index.php';
$background = $conf["background"];
$wap_background = $conf["wap_background"];
if (checkmobile()) {
    if (!empty($wap_background)) {
        $background_img = $wap_background;
    } else {
        $background_img = $background;
    }
} else {
    $background_img = $background;
}
?>