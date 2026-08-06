<?php
// ltab 风格链接列表渲染（default 风格：使用 lists() 模板数组）
$rel = $conf["mode"] == 2 ? '' : 'rel="nofollow"';

$html = array(
    // 分组开始标签
    'g1' => '<div class="ltab-group" id="group_{group_id}">',
    // 分组内容（标题 + 图标 + 网格开始）
    'g2' => '<div class="group-title"><span class="group-icon">{group_icon}</span><span>{group_name}</span></div><div class="ltab-grid">',
    // 分组结束标签
    'g3' => '</div></div>',

    // 链接开始标签
    'l1' => '<a ' . $rel . ' href="{link_url}" target="_blank" class="ltab-card" title="{link_name_text}">',
    // 链接内容
    'l2' => '<div class="card-icon">{link_icon}</div><div class="card-name">{link_name_text}</div>',
    // 链接结束标签
    'l3' => '</a>',
);
lists($html);
