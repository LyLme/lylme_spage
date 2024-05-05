<?php

include_once("../include/common.php");
if (isset($islogin) == 1) {
} else {
    exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
header('Content-Type:application/json');

$set = isset($_GET['set']) ? $_GET['set'] : null;
switch ($set) {
        //审核
    case 'status':
        $sw = $_POST['status'] == 1 ? 1 : 2;
        $ids = $_POST['id'];
        if (!is_array($ids)) {
            //转为数组
            $ids = array($ids);
        }
        $e = 0;
        foreach ($ids as $id) {
            if ($sw == 1) {
                //将通过的链接插入到数据库
                $applyres =  $DB->get_row("SELECT * FROM `lylme_apply` WHERE `apply_status` = 0  ANd `apply_id` = $id");
                if (!empty($applyres)) {
                    $name = strip_tags(daddslashes($applyres['apply_name']));
                    $url = strip_tags(daddslashes($applyres['apply_url']));
                    $icon = daddslashes($applyres['apply_icon']);
                    $group_id = strip_tags(daddslashes($applyres['apply_group']));
                    $link_order = $DB->count('select MAX(id) from `lylme_links`') + 1;
                    $DB->query("INSERT INTO `lylme_links` (`id`, `name`, `group_id`, `url`, `icon`, `link_desc`,`link_order`) VALUES (NULL, '$name', '$group_id', '$url', '$icon', '', '$link_order');");
                }
            }
            $sql = "UPDATE `lylme_apply` SET `apply_status` = '$sw' WHERE  `apply_status` = 0  ANd  `apply_id` =  $id;";
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
        //删除
    case 'delete':
        $ids = $_POST['id'];
        if (!is_array($ids)) {
            $ids = array($ids);
        }
        $e = 0;
        foreach ($ids as $id) {
            if (!$DB->query("DELETE FROM `lylme_apply` WHERE apply_id = $id")) {
                $e++;
            }
        }
        if ($e == 0) {
            exit('{"code": 200,"msg":"操作成功！"}');
        } else {
            exit('{"code": 100,"msg":"错误，失败' . $e . '条"}');
        }
        break;
    default:
        exit('error');
        break;
}
