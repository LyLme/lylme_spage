<?php
// +----------------------------------------------------------+
// | LyLme Spage                                              |
// +----------------------------------------------------------+
// | Copyright (c) 2022 LyLme                                 |
// +----------------------------------------------------------+
// | File: list.php                                           |
// +----------------------------------------------------------+
// | Authors: LyLme <admin@lylme.com>                         |
// | date: 2022-3-12                                          |
// +----------------------------------------------------------+
$groups = $DB->query("SELECT * FROM `lylme_groups` ORDER BY `group_order` ASC"); // 获取分类
$i = 0;
while ($group = $DB->fetch($groups)) { //循环所有分组
    if($group["group_status"]=='0'){continue;}
    if(!in_array($group['group_pwd'],$_SESSION['list'])&&!empty($group['group_pwd'])){
    	    //如果 分组加密未在认证列表 并且分组设置了密码(不显示分组)
    	    continue;
    	}
    $sql = "SELECT * FROM `lylme_links` WHERE `group_id` = " . $group['group_id']." ORDER BY `link_order` ASC;";
    $group_links = $DB->query($sql);
    $link_num = $DB->num_rows($group_links); // 获取返回字段条目数量
    echo '<ul class="mylist row"><li class="title">' . $group["group_icon"] . '<sapn>' . $group["group_name"] . '</sapn></li>'; //输出分组图标和标题
    if ($link_num == 0) {
        echo '</ul>' . "\n";
        $i = 0;
        continue;
    }
    while ($link = $DB->fetch($group_links)) { // 循环每个链接
        // 返回指定分组下的所有字段
        $lpwd = true;
        if ($link_num > $i) {
            $i = $i + 1;
			if(!empty($group['group_pwd'])&&!empty($link['link_pwd'])){
			   //分组和链接同时加密
			   //忽略链接加密正常显示分组
			}
    		else if(!in_array($link['link_pwd'],$_SESSION['list'])&&!empty($link['link_pwd'])){
    			    //当前链接加密
    	            $lpwd = false;
    	   }
            if($link["link_status"]!="0" && $lpwd ){
            echo "\n" . '<li class="lylme-3"><a rel="nofollow" href="' . $link["url"] . '" target="_blank">';
            if ($link["icon"] == '') {
                echo '<img src="/assets/img/default-icon.png" alt="默认' . $link["name"] . '" />';
            } else if (!preg_match("/^<svg*/", $link["icon"])) {
                echo '<img src="' . $link["icon"] . '" alt="' . $link["name"] . '" />';
            } else {
                echo $link["icon"];
            }
            echo '<span>' . $link["name"] . '</span></a></li>';
            }
            //输出图标和链接
        }
        if ($link_num == $i) {
            //判断当前分组链接循环完毕
            echo '</ul>' . "\n"; //输出分类结束标签
            $i = 0;
            break; //重置$i为0跳出当前循环
            
        }
    }
}
$DB->close();
?>
