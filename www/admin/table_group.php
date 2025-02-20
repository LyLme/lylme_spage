<?php
/* 
 * @Description: 
 * @Author: LyLme admin@lylme.com
 * @Date: 2024-01-23 12:25:35
 * @LastEditors: LyLme admin@lylme.com
 * @LastEditTime: 2024-04-14 05:35:10
 * @FilePath: /lylme_spage/admin/table_group.php
 * @Copyright (c) 2024 by LyLme, All Rights Reserved. 
 */
include_once("../include/common.php");
if (isset($islogin) == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");

echo '<div class="alert alert-info">系统共有 <b>' . $groupsrows . '</b> 个分组<br/><a href="./group.php?set=add" class="btn btn-primary">新建分组</a></div>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead><tr><th>名称</th><th>排序</th><th>链接数</th><th>访问密码</th><th>状态</th><th>操作</th></tr></thead>
          <tbody>';

$rs = $DB->query("SELECT * FROM lylme_groups ORDER BY group_order ASC");
while ($res = $DB->fetch($rs)) {
  $pwd = null;
  if (isset($res['group_pwd']) && $res['group_pwd'] !== 0) {
   
    $pwd_row = $DB->get_row("SELECT `pwd_id`, `pwd_key` FROM `lylme_pwd` WHERE `pwd_id` = " . intval($res['group_pwd']));
    if ($pwd_row !== null) {
      $pwd = $pwd_row['pwd_key'];
    } 
  } 
  echo '<tr><td><input type="hidden" name="group_id" value="' . $res['group_id'] . '">' . $res['group_name'] . '</td><td>
        <button  class="btn btn-primary btn-xs sort-up">上移</button>&nbsp;<button class="btn btn-cyan btn-xs sort-down">下移</button></td>
        <td>' . $DB->num_rows($DB->query("SELECT `id` FROM `lylme_links` WHERE `group_id` =" . $res['group_id'])) . '</td>
        <td>';
  if ($pwd || $res['group_pwd']) {
    if (empty($pwd)) {
      echo '<font color="red">失效[请重新设置加密组]</font>';
    } else {
      echo '<font color="f96197">' . $pwd . '</font>';
    }
  } else {
    echo '<font color="green">未加密</font>';
  }
  echo ' </td><td>';

  if ($res['group_status']) {
    echo '<button  class="btn btn-pink btn-xs" onclick="off_group(' . $res['group_id'] . ')">禁用</button>';
  } else {
    echo '<button  class="btn btn-success btn-xs" onclick="on_group(' . $res['group_id'] . ')">启用</button>';
  }

  echo '</td><td>&nbsp;<a href="./group.php?set=edit&id=' . $res['group_id'] . '" class="btn btn-info btn-xs">编辑</a>&nbsp;<button class="btn btn-xs btn-danger" onclick="del_group(' . $res['group_id'] . ')">删除</button></td></tr>';
}
?>

</tbody>
</table>
</div>