<?php

$theme_config =  [
  [
    'type' => 'text',
    'name' => 'search',
    'title' => '自定义搜索引擎',
    'description' => '<code>%s</code>表示搜索词，例：百度搜索 <code>https://www.baidu.com/s?word=%s</code> 或 <code>https://www.baidu.com/s?wd=%s&ie=UTF-8</code><br>若搜索词在末尾可省略<code>%s</code>',
    'value' => 'https://cn.bing.com/search?q=%s',
    'placeholder' => "https://cn.bing.com/search?q="

  ],
  [
    'type' => 'select',
    'name' => 'lytoday',
    'title' => '今日热榜',
    'description' => 'LyToday-JS插件显示位置，每日免费请求上限200次 <a href="https://doc.lylme.com/spage/#/lytoday-js" target="_blank">查看文档</a>',
    "value" => 0,
    'enum' => [
      0 => "关闭",
      1 => "底部"
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
