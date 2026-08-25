<?php
/**
 * 主题自定义配置表单
 */
$theme_config = [
    
  [
    'type' => 'select',
    'name' => 'lytoday',
    'title' => '今日热榜',
    'description' => 'LyToday-JS插件显示位置，每日免费请求上限200次 <a href="https://doc.lylme.com/spage/#/lytoday-js" target="_blank">查看文档</a>',
    "value" => 0,
    'enum' => [
      0 => "关闭",
      1 => "搜索栏下方",
      2 => "底部"
    ],

  ],
  [
    'type' => 'textarea',
    'name' => 'lytodaycode',
    'title' => '今日热榜代码',
    'description' => 'LyToday-JS插件自定义代码，若不了解请勿修改 <a href="https://doc.lylme.com/spage/#/lytoday-js" target="_blank">查看文档</a>',
    'value' => '<div id="lytoday"></div><script src="https://lytoday.lylme.com/"></script>',
  ]
   
];
