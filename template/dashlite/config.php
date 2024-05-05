<?php

$theme_config =  [
  [
    'type' => 'text',
    'name' => 'search',
    'title' => '自定义搜索引擎',
    'description' => '<code>%s</code>表示搜索词，例：百度搜索 <code>https://www.baidu.com/s?word=%s</code> 或 <code>https://www.baidu.com/s?wd=%s&ie=UTF-8</code><br>若搜索词在末尾可省略<code>%s</code>',
    'value' => 'https://cn.bing.com/search?q=%s',
    'placeholder' => "https://cn.bing.com/search?q="

  ]
];
