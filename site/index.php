<?php 
include("../include/common.php"); 
$id = daddslashes($_GET['id']);
$sites = $DB->query("select * from lylme_links where id='$id' limit 1");
$site = $DB->fetch($sites); 
$info = get_head($site["url"]);
if (empty($site["icon"])) {
	$site["icon"] =  '<img src="/assets/img/default-icon.png" alt="' . $site["name"] . '" />';
} else if (!preg_match("/^<svg*/", $site["icon"])) {
    $site["icon"] = '<img src="' . $site["icon"] . '" alt="' . $site["name"] . '" />';
} else {
    $site["icon"] = $site["icon"];
}
if(empty($DB->num_rows($sites)) || $site['link_pwd']!=0){
    include(theme_file('404.php')); //页面不存在
    exit();
}
else {
    $groups = $DB->query("select `group_name`,`group_icon` from lylme_groups where group_id=".$site['group_id']." limit 1");
    $group = $DB->fetch($groups);
    include(theme_file('site.php'));
    
    // $pv =$site['link_pv']+1;
    // $DB->query("UPDATE `lylme_links` SET `link_pv` = '".$pv."' WHERE `lylme_links`.`id` = $id;");
    exit();
}

/** 
 * 变量说明
 * $groups['group_name']    所在分组名称
 * $groups['group_icon']    所在分组图标
 * $site['id']              链接ID
 * $site['name']            链接名称
 * $site['url']             链接地址
 * $site['PS']              链接描述
 * $info['title']           网站标题
 * $info['description']     网站描述
 * $info['keywords']        网站关键词
 **/
?>


