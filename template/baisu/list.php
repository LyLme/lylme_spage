<?php

// 主题开发文档：https://doc.lylme.com/dev/theme
$rel = $conf["mode"] == 2 ? '' : 'rel="nofollow"'; 
$html= array(
    'g1' => '<div class="site-name" id="category-{group_id}">{group_icon}{group_name}</div>', //分组开始标签
    'g2' => '<div class="site-list">',  //分组内容
    'g3' => '<div class="list kongs"></div><div class="list kongs"></div><div class="list kongs"></div><div class="list kongs"></div></div>',  //分组结束标签
    
    'l1' => '<div class="list urllist" id="id_{link_id}" data-id="{link_id}" data-url="{link_url}">',  //链接开始标签
    'l2' => '<a '.$rel.' href="{link_url}" target="_blank">{link_icon}',  //链接内容
    'l3' => '<p class="name">{link_name}</p><p class="desc"></p></a></div>',  //链接结束标签
);
lists($html);
?>