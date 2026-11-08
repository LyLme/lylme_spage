<?php

/**
 * Palette 主题自定义设置
 *
 * 后台路径：主题设置 → 主题自定义设置
 * 读取方式：theme_config('配置名', '默认值')
 *
 * 支持的表单类型：text / textarea / select / checkbox / radio / color
 * 数组统一使用 array()，兼容 PHP 5.4+。
 */
$theme_config = array(
    array(
        'type' => 'text',
        'name' => 'background_video',
        'title' => '背景视频地址',
        'description' => '视频背景的 URL，留空则背景切换仅使用图片背景；填写后仅使用视频背景，顶部背景按钮在「视频 / 无背景」间切换。支持 MP4 格式',
        'value' => '',
        'placeholder' => 'https://example.com/video.mp4',
    ),
    array(
        'type' => 'text',
        'name' => 'gonganbei',
        'title' => '公安备案号',
        'description' => '显示在页脚，留空不显示。备案号中的数字会自动用于生成查询链接',
        'value' => '',
        'placeholder' => '京公安网备xxxxxxxxxx号',
    ),
    array(
        'type' => 'switch',
        'name' => 'go_enable',
        'title' => '外链安全提醒页',
        'description' => '开启后，点击站外链接会先经过倒计时确认页',
        'value' => 0,
    ),
);
