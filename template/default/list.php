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


$links = $DB->query("SELECT * FROM `lylme_links`"); // 获取网站
$groups = $DB->query("SELECT * FROM `lylme_groups` ORDER BY `group_order` ASC"); // 获取分类
$i = 0;
while ($group = $DB->fetch($groups)) { //循环所有分组
    $sql = "SELECT * FROM `lylme_links` WHERE `group_id` = " . $group['group_id'];
    $group_links = $DB->query($sql);
    $link_num = $DB->num_rows($group_links); // 获取返回字段条目数量
    echo '<ul class="mylist row"><li id="group_' . $group["group_id"] . '" class="title">' . $group["group_icon"] . '<sapn>' . $group["group_name"] . '</sapn></li>'; //输出分组图标和标题
    if ($link_num == 0) {
        echo '</ul>' . "\n";
        $i = 0;
        continue;
    }
    while ($link = $DB->fetch($group_links)) { // 循环每个链接
        // 返回指定分组下的所有字段
        if ($link_num > $i) {
            $i = $i + 1;
            echo "\n" . '<li class="col-3 col-sm-3 col-md-3 col-lg-1"><a rel="nofollow" href="' . $link["url"] . '" target="_blank">';
            if ($link["icon"] == '') {
                echo '<img src="/assets/img/default-icon.png" alt="默认' . $link["name"] . '" />';
            } else if (!preg_match("/^<svg*/", $link["icon"])) {
                echo '<img src="' . $link["icon"] . '" alt="' . $link["name"] . '" />';
            } else {
                echo $link["icon"];
            }
            echo '<span>' . $link["name"] . '</span></a></li>';
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
