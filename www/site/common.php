<?php
include("../include/common.php");
$id = intval($_GET['id']);

session_start();
$pwd_list = $_SESSION['list'];
if (!empty($pwd_list)) {
    $whereClause = '';

    foreach ($pwd_list as $index => $pwd_id) {
        if ($index === 0) {
            // 如果是第一个ID，直接开始构建WHERE子句  
            $whereClause .= "(`link_pwd` = 0 OR  `link_pwd` = " . $pwd_id;
        } else {
            // 对于后续的ID，使用OR连接  
            $whereClause .= " OR `link_pwd` = " . $pwd_id;
        }
    }
    if (!empty($whereClause)) {
        $whereClause .= ") AND";
    }
} else {
    $whereClause = "(`link_pwd` = 0 ) AND";
}
$sites = $DB->query("select * from lylme_links where $whereClause `id` = $id limit 1;");
if (!$sites || $DB->num_rows($sites) === 0) {
    include(theme_file('404.php')); //页面不存在
    exit();
}

$site = $DB->fetch($sites);

$group_id = $site['group_id'];
$site_groups = $DB->query("SELECT * FROM `lylme_groups` WHERE `group_id` = $group_id  LIMIT 1");
$group_pwd = $DB->fetch($site_groups)['group_pwd'];
array_unshift($pwd_list, 0);
if (!in_array($group_pwd, $pwd_list)) {
    include(theme_file('404.php')); //页面不存在
    exit();
}
if (!empty($site['link_desc']) && !empty($site['link_keywords'])) {
    // 描述与关键词已采集过（成功值或"无"），直接使用数据库数据，避免每次访问重复采集
    $info = array(
        'title' => $site['name'],
        'charset' => 'UTF-8',
        'icon' => '',
        'description' => $site['link_desc'],
        'keywords' => $site['link_keywords'],
        'url' => $site['url']
    );
} else {
    $info = get_head($site['url'], true);
    // 采集链接描述/关键词并写入数据库，采集失败写入"无"，下次访问不再采集
    $save_desc = !empty($info['description']) ? trim(strip_tags($info['description'])) : '无';
    $save_kw = !empty($info['keywords']) ? trim(strip_tags($info['keywords'])) : '无';
    // 截断到字段长度，避免超出 varchar 限制
    if (function_exists('mb_substr')) {
        $save_desc = mb_substr($save_desc, 0, 255);
        $save_kw = mb_substr($save_kw, 0, 512);
    } else {
        $save_desc = substr($save_desc, 0, 255);
        $save_kw = substr($save_kw, 0, 512);
    }
    $save_kw = str_replace(['、', '，', ' '], ',', $save_kw);
    $save_kw = trim(preg_replace('/,+/', ',', $save_kw));

    $sets = array();
    if (empty($site['link_desc'])) {
        $sets[] = "`link_desc` = '" . daddslashes($save_desc) . "'";
        $site['link_desc'] = $save_desc;
    }
    if (empty($site['link_keywords'])) {
        $sets[] = "`link_keywords` = '" . daddslashes($save_kw) . "'";
        $site['link_keywords'] = $save_kw;
    }
    if (!empty($sets)) {
        $DB->query("UPDATE `lylme_links` SET " . implode(', ', $sets) . " WHERE `id` = " . $id);
    }
}
if (empty($site["icon"])) {
    $site["icon"] =  '<img src="/assets/img/default-icon.png" alt="' . strip_tags($site["name"]) . '" />';
} else if (!preg_match("/^<svg*/", $site["icon"])) {
    $site["icon"] = '<img src="' . $site["icon"] . '" alt="' . strip_tags($site["name"]) . '" />';
} else {
    $site["icon"] = $site["icon"];
}

$group = $DB->fetch($DB->query("select `group_name`,`group_icon` from lylme_groups where group_id=" . $site['group_id'] . " limit 1"));

$tmp_description = !empty($site['link_desc']) ? $site['link_desc'] : $info['description'];


$group_name = $group['group_name']; //所在分组名称
$group_icon = $group['group_icon']; //所在分组图标
$url_id  =  $site['id']; //链接ID
$url_name = strip_tags($site['name']); //链接名称
$url_herf = $site['url']; //链接地址
$url_icon = $site['icon']; //链接图标
$url_title = strip_tags($info['title']); //网站标题(在线获取)
$url_keywords = !empty($site['link_keywords']) ? $site['link_keywords'] : (isset($info['keywords']) ? $info['keywords'] : ""); //网站关键词(数据库优先)
$url_description = isset($tmp_description) ? $tmp_description : "暂无网站描述"; //网站描述(优先本地)在线获取
