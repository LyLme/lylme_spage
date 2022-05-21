<?php
include_once("../include/common.php");
if(isset($islogin)==1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");

    $page = isset($_GET['page'])? $_GET['page'] : 1;
    $groups = $DB->query("SELECT * FROM `lylme_groups` ORDER BY `group_order` ASC"); //获取分组
    $gpwd = $DB->fetch($DB->query("SELECT `group_id`, `group_pwd` FROM `lylme_groups` WHERE `group_id` = ".$page))["group_pwd"]; //分组加密状态
    $rs = $DB->query("SELECT * FROM `lylme_links` WHERE `group_id` = ".$page." ORDER BY `lylme_links`.`id` ASC");  //获取链接
    $grouprows=$DB->num_rows($rs);
    echo '<div class="alert alert-info">系统收录： <b>' . $linksrows . '</b> 个链接 / 当前分组： <b>'.$grouprows.'</b>个链接 
    <i class="mdi mdi-help-circle tips"></i>
    </div>
		<nav><ul class="pagination">';
while ($group = $DB->fetch($groups)) {
      echo '<li ';
      if($page ==$group["group_id"]){echo 'class="active"';}
      echo '><a href="?page='.$group["group_id"].'">'.$group["group_name"].'</a></li>';
      }
       echo '</ul>
                </nav>
        <!-- 功能按钮 S-->
          <div id="toolbar" class="toolbar-btn-action">
            <a  href="./link.php?set=add"  class="btn btn-primary btn-label">
              <label><i class="mdi mdi-plus" aria-hidden="true"></i></label>新增</a>
            <button id="btn_edit" type="button" class="btn btn-success btn-label" onclick="on_link()">
              <label><i class="mdi mdi-check" aria-hidden="true"></i></label>启用</button>
            <button id="btn_edit" type="button" class="btn btn-warning btn-label" onclick="off_link()">
              <label><i class="mdi mdi-block-helper" aria-hidden="true"></i></label>禁用 </button>
            <button id="btn_delete" type="button" class="btn btn-danger btn-label" onclick="del_link()">
              <label><i class="mdi mdi-window-close" aria-hidden="true"></i></label>删除</button>
            <button id="edit_group" type="button" class="btn btn-info btn-label" onclick="edit_group(mv_group)">
             <label><i class="mdi mdi-account-edit" aria-hidden="true"></i></label>移动</button>
             ';
             if(empty($gpwd)){
             echo '<button id="btn_delete" type="button" class="btn btn btn-pink btn-label" onclick="pwd_link(pwd_list)">
              <label><i class="mdi mdi-key-variant" aria-hidden="true"></i></label>加密</button>';
              }
              else{
                echo '<button id="btn_delete" type="button" class="btn btn btn-pink btn-label" onclick="pwd_links()">
              <label><i class="mdi mdi-key-variant" aria-hidden="true"></i></label>分组已加密</button>';
              }
              echo '
            <button class="btn btn-label btn btn-purple" id="save_order" style="display:none" onclick="save_order()">
            <label><i class="mdi mdi-checkbox-marked-circle-outline"></i></label> 保存排序</button>
          </div> 
          <!-- 功能按钮 E -->
		<div class="table-responsive">       
        <table class="table table-striped" id="classlisttbody">
          <thead><tr style="cursor: pointer">
          <th><input  type="checkbox" class="checkbox-parent" id="check_all" onclick="check_all()"></th>
          <th>排序</th><th>名称</th><th>链接</th><th>分组</th><th>启用</th><th>操作</th></tr></thead>
          <tbody id="link">';
    
    $rs = $DB->query("SELECT * FROM `lylme_links` WHERE `group_id` = ".$page." ORDER BY `lylme_links`.`link_order` ASC");
    while ($res = $DB->fetch($rs)) {

        echo '<tr><td><input type="checkbox" name="link-check" value="'.$res['id'].'"></td>
     <!-- 链接排序 S -->  
    <td><a class="btn btn-success btn-xs sort-goup" data-toggle="tooltip"  data-placement="top" title="移到顶部"><i class="mdi mdi-arrow-collapse-up"></i></a>
	<a class="btn btn-info btn-xs sort-godown" data-toggle="tooltip" data-placement="top" title="移到底部"><i class="mdi mdi-arrow-collapse-down"></i></a>
	<a class="btn btn-primary btn-xs sort-up" data-toggle="tooltip" data-placement="top" title="移到上一行"><i class="mdi mdi-arrow-up"></i></a>
	<a class="btn btn-cyan btn-xs sort-down" data-toggle="tooltip" data-placement="top" title="移到下一行"><i class="mdi mdi-arrow-down"></i></a></td>
	 <!-- 链接排序 E -->
        <td class="lylme">' . $res['name'] . '</td><td>';
        if(!empty($res['link_pwd'])||!empty($gpwd)){ echo '<font color="#f96197">'. $res['url'] .'</font>';}else{echo $res['url'];}
        echo 
        '</td><td>'. $DB->fetch($DB->query("SELECT * FROM `lylme_groups` WHERE `group_id` = " . $res['group_id'])) ["group_name"]. '</td>
        <td>';
        if($res['link_status']=="0"){ echo '<font color="red">禁用</font>';}else{echo '<font color="green">启用</font>';}
        $de_llink = "del_link('".$res['id']."')";
        echo'</td>
        <td><a href="./link.php?set=edit&id=' . $res['id'] . '" class="btn btn-info btn-primary">编辑</a>&nbsp;<button class="btn btn-primary btn-danger" onclick="'.$de_llink.'">删除</button></td></tr>';
        ;
    }
    echo '
      </tbody>
        </table>
		</div>
      </div>';
?>
   