<?php
/**
 * 开发包devTheme 主题自定义配置
 * 
 * 
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


$theme_config = array(

    // ---------- 外观设置 ----------
    array(
        'type'  => 'color',
        'name'  => 'color',
        'title' => '主题主色',
        'description' => '用于搜索按钮、链接悬停等主色调，留空则使用默认蓝色',
        'value' => '#2f6fed',
    ),
    array(
        'type'  => 'select',
        'name'  => 'link_cols',
        'title' => '列表列数',
        'description' => '每行显示的链接数量，手机端会自动变为 2 列',
        'value' => 4,
        'enum'  => array(
            3 => '3 列',
            4 => '4 列',
            5 => '5 列',
            6 => '6 列',
        ),
    ),


    // ---------- 首页设置 ----------
    array(
        'type'  => 'checkbox',
        'name'  => 'modules',
        'title' => '首页显示模块',
        'description' => '可多选，取消勾选后对应模块不再显示',
        'value' => array('clock'),
        'enum'  => array(
            'clock' => '时间显示',
        ),
    ),
    array(
        'type'  => 'textarea',
        'name'  => 'notice',
        'title' => '首页公告',
        'description' => '显示在搜索框下方，<code>支持 HTML 代码</code>，留空不显示',
        'value' => '',
    ),

    // ---------- 备案信息 ----------
    array(
        'type'  => 'text',
        'name'  => 'gonganbei',
        'title' => '公安备案号',
        'description' => '公安备案号，留空不显示',
        'value' => '',
        'placeholder' => '京公安网备xxxxxxxxxx号',
    ),

);
