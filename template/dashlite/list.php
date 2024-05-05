<?php

$rel = $conf["mode"] == 2 ? '' : 'rel="nofollow"'; 

// 主题开发文档：https://doc.lylme.com/dev/theme
$html = array(
    'g1' => '<div class="card card-preview category-card" data-category-id="{group_id}">', //分组开始标签
    'g2' => '<div class="card-inner mt-3"><div class="nya-title nk-ibx-action-item progress-rating"><em class="icon ni ni-setting"></em><span class="nk-menu-text font-weight-bold">{group_name}</span></div><div class="row g-2">',  //分组内容
    'g3' => '</div></div></div>',  //分组结束标签

    'l1' => '<div class="col-lg-3 col-md-4 col-6">',  //链接开始标签
    'l2' => '<a '.$rel.' href="{link_url}" target="_blank" data-id="73" class="btn btn-wider btn-block btn-xl btn-outline-light tool-link">{link_name}</a>',  //链接内容
    'l3' => '</div>',  //链接结束标签
);
lists($html);