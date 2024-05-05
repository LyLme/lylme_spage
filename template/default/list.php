<?php
// 主题开发文档：https://doc.lylme.com/dev/theme
$rel = $conf["mode"] == 2 ? '' : 'rel="nofollow"'; 
$html= array(
    'g1' => '<ul class="mylist row">', //分组开始标签
    'g2' => '<li id="group_{group_id}" class="title">{group_icon}<sapn>{group_name}</sapn></li>',  //分组内容
    'g3' => '</ul>',  //分组结束标签
    
    'l1' => '<li class="col-3 col-sm-3 col-md-3 col-lg-1">',  //链接开始标签
    'l2' => '<a '.$rel.' href="{link_url}" target="_blank">{link_icon}<span>{link_name}</span></a>',  //链接内容
    'l3' => '</li>',  //链接结束标签
);
lists($html);
?>