<?php
/* 
 * @Description: 主题设置操作
 * @Author: LyLme admin@lylme.com
 * @Date: 2024-04-12 22:39:49
 * @LastEditors: LyLme admin@lylme.com
 * @LastEditTime: 2024-04-13 17:22:28
 * @FilePath: /lylme_spage/admin/ajax_theme.php
 * @Copyright (c) 2024 by LyLme, All Rights Reserved. 
 */

include_once("../include/common.php");
if (isset($islogin) == 1) {
} else {
    exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
header('Content-Type:application/json');

$set = isset($_GET['set']) ? $_GET['set'] : null;
$data = isset($_POST) ? $_POST : null;
if ($set == "save" && !empty($data)) {
    $theme_name = "theme_config_" . $conf['template'];
    // $data['status'] = isset($data['status']) && $data['status'] == "on" ? true : false;
    unset($data['file']);
    $data = json_encode($data);
    if (saveSetting($theme_name, $data, theme($conf['template'], 'theme_name') . "主题自定义设置")) {
        exit('{"code": 200,"msg":"保存成功"}');
    } else {
        exit('{"code": 0,"msg":"保存失败"}');
    }
} else {
    exit('{"code": -1,"msg":"非法请求！"}');
}
