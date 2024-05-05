<?php
include("../include/common.php");
$id = daddslashes($_GET['id']);

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
$info = get_head($site["id"], true);
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
$url_keywords = isset($info['keywords']) ? $info['keywords'] : "无"; //网站关键词(在线获取)
$url_description = isset($tmp_description) ? $tmp_description : "暂无网站描述"; //网站描述(优先本地)在线获取
