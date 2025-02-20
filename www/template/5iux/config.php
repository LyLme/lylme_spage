<?php

/**
 * 主题自定义配置表单
 * @param array $theme_config 表单配置
 * 参数说明：
 * type：配置项表单类型，目前支持：[text：单行文本,textarea:多行文本,select：下拉菜单，checkbox:多选框,radio:单选，color:颜色]，建议使用radio(单选)替代switch(开关)
 * name：表单参数的键，在主题调用时传入，需唯一
 * title：配置项标题文字
 * description：配置项提示文字
 * value：默认值
 * enum：枚举值，下拉菜单和多选框，数组类型。需包含键值对,键为参数值，值为显示的文本
 * verify：表单验证方式，支持类型：[required：必填，url：网站，number：数字，email：邮箱等]
 */

$theme_config =  [
  [
    'type' => 'textarea',
    'name' => 'home_title',
    'title' => '首页提示',
    'description' => '显示在首页的提示内容，<code>支持HTML代码</code>',
    "value" => $GLOBALS['conf']['home-title'],
  ],
  [
    'type' => 'select',
    'name' => 'lytoday',
    'title' => '今日热榜',
    'description' => 'LyToday-JS插件显示位置，每日免费请求上限200次 <a href="https://doc.lylme.com/spage/#/lytoday-js" target="_blank">查看文档</a>',
    "value" => 0,
    'enum' => [
      0 => "关闭",
      1 => "开启",
    ],

  ],
  [
    'type' => 'textarea',
    'name' => 'lytodaycode',
    'title' => '今日热榜代码',
    'description' => 'LyToday-JS插件自定义代码，若不了解请勿修改 <a href="https://doc.lylme.com/spage/#/lytoday-js" target="_blank">查看文档</a>',
    'value' => '<div id="lytoday"></div><script src="https://lytoday.lylme.com/"></script>',
  ],
  [
    'type' => 'text',
    'name' => 'gonganbei',
    'title' => '公安网备案号',
    'description' => '公安网备案号，留空不显示',
    'value' => '',
    'placeholder' => "京公安网备xxxxxxxxxx号"

  ],

];
