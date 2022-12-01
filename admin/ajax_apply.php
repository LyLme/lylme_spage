<?php
include_once("../include/common.php");
if(isset($islogin)==1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");
header('Content-Type:application/json');

$set= isset($_GET['set']) ? $_GET['set'] : null;
switch($set) {
	//修改分组
	case 'status':
        $id = $_POST['id'];
    	$sw = $_POST['status'];
    	$sql = "UPDATE `lylme_apply` SET `apply_status` = '".$sw."' WHERE `lylme_apply`.`apply_id` = ".$id.";";
    	if($sw==1) {
    		if ($DB->query($sql)) {
    			$applyres =  $DB->get_row("SELECT * FROM `lylme_apply` WHERE `apply_id` = ".$id);
    			$name=strip_tags(daddslashes($applyres['apply_name']));
    			$url=strip_tags(daddslashes($applyres['apply_url']));
    			$icon=daddslashes($applyres['apply_icon']);
    			$group_id=strip_tags(daddslashes($applyres['apply_group']));
    			$mail=strip_tags(daddslashes($applyres['apply_mail']));
    			$link_order = $DB->count('select MAX(id) from `lylme_links`')+1;
    			$sql1 = "INSERT INTO `lylme_links` (`id`, `name`, `group_id`, `url`, `icon`, `PS`,`link_order`) VALUES (NULL, '" . $name . "', '" . $group_id . "', '" . $url . "', '" . $icon . "', '" . $mail . "的提交 ', '" . $link_order . "');";
    			if($DB->query($sql1)) {
    			    exit('{"code": "200","msg":"成功！网站已成功收录！"}');
    			} else {
    			    exit('{"code": "-1","操作失败！原因：\n'.$DB->error().'"}');
    			}
    		}
    	} else if($sw==2){
    		if ($DB->query($sql)) {
    			exit('{"code": "200","msg":"操作成功！"}');
    		}
    		else{
    		   exit('{"code": "-1","操作失败！原因：\n'.$DB->error().'"}');
    		}
    	}
    	else{
    	    exit('{"code": "-2","操作失败！-2"}');
    	}
	break;
	case 'delete':
    	$id = $_POST['id'];
    	$delsql = 'DELETE FROM `lylme_apply` WHERE apply_id =' . $id;
    	if ($DB->query($delsql)) {
    	    	exit('{"code": "200","msg":"操作成功！"}');
    	}else{
    	    exit('{"code": "-2","操作失败！-2"}');
    	}
	break;
	default:
	    	exit('error');
	break;
}