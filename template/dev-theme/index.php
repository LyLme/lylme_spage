<?php

/**
 * 开发包devTheme —— 六零导航页官方开发包脚手架主题
 *
 * 单文件入口，按注释分区：逻辑区 → head → 导航 → 搜索 → 列表 → 底部
 * 所有数据接口封装见 functions.php，建议本文件只负责输出，不写取数逻辑
 *
 * @version 1.0.0
 */

require_once __DIR__ . '/functions.php';

// =========================================================
// 一、主题逻辑区：条件渲染集中在这里处理，输出到变量
// =========================================================

// 背景图：background() 已按 checkmobile() 自动选择 PC / 手机背景
$background_url = background();

// 主题配置项（均带默认值，未配置也能正常显示）
$theme_color = theme_config('color', '#2f6fed');       // 主题主色
$link_cols   = theme_config('link_cols', 4);           // 列表列数
$notice      = trim((string) theme_config('notice', '')); // 首页公告（支持 HTML）
$modules     = theme_config('modules', array('clock')); // 首页模块（多选）
if (!is_array($modules)) {
    $modules = array($modules);
}

// 拼接 body 的行内样式：仅用于「数据驱动」的值（背景图、主题色、列数）
$style_parts = array();
if (!empty($background_url)) {
    $style_parts[] = 'background-image: url(' . theme_e($background_url) . ')';
}
if (!empty($theme_color)) {
    $style_parts[] = '--theme-color: ' . theme_e($theme_color);
}
$style_parts[] = '--link-cols: ' . (int) $link_cols;
$body_style = ' style="' . implode('; ', $style_parts) . '"';

?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo theme_e($conf['title']); ?></title>
    <meta name="keywords" content="<?php echo theme_e($conf['keywords']); ?>">
    <meta name="description" content="<?php echo theme_e($conf['description']); ?>">
    <link rel="icon" href="<?php echo theme_e($conf['logo']); ?>">
    <meta name="lsvn" content="<?php echo base64_encode($conf['version']) ?>">
    <?php theme_css('css/style.css'); ?>
    <?php theme_js('js/script.js'); ?>
</head>

<body class="<?php echo checkmobile() ? 'is-mobile' : 'is-pc'; ?> bg-fixed" <?php echo $body_style; ?>>

    <!-- ===================== 二、导航区 ===================== -->
    <header class="site-header">
        <a class="site-logo" href="/">
            <img src="<?php echo theme_e($conf['logo']); ?>" alt="<?php echo theme_e($conf['title']); ?>">
            <span><?php echo theme_e($conf['title']); ?></span>
        </a>

        <nav class="site-nav">
            <ul>
                <?php foreach (theme_tags() as $tag): ?>
                    <li>
                        <a href="<?php echo theme_e($tag['link']); ?>" <?php echo $tag['blank'] ? ' target="_blank" rel="noopener"' : ''; ?>>
                            <?php echo theme_e($tag['name']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </header>

    <main class="site-main">

        <!-- 首页模块：时间显示（可在后台「首页显示模块」中关闭） -->
        <?php if (in_array('clock', $modules)): ?>
            <div class="clock">
                <span class="clock-time" id="clock-time">--:--:--</span>
                <span class="clock-date" id="clock-date"></span>
            </div>
        <?php endif; ?>

        <!-- 随机一言：由后台「随机一言」开关控制 -->
        <?php if ($conf['yan'] == 'true'): ?>
            <p class="yan"><?php echo yan(); ?></p>
        <?php endif; ?>

        <!-- ===================== 三、搜索区（结构契约见 README） ===================== -->
        <section class="search">
            <form class="search-form" id="search-form" action="#">
                <input type="text" class="search-input" id="search-input"
                    placeholder="请输入搜索内容" autocomplete="off">
                <button type="submit" class="search-btn">搜索</button>
            </form>

            <div class="search-engines">
                <?php foreach (theme_sou() as $index => $sou): ?>
                    <label class="engine" style="--engine-color: <?php echo theme_e($sou['color']); ?>;">
                        <input type="radio" name="sou"
                            value="<?php echo theme_e($sou['link']); ?>"
                            data-alias="<?php echo theme_e($sou['alias']); ?>"
                            data-hint="<?php echo theme_e($sou['hint']); ?>"
                            <?php echo $index === 0 ? 'checked' : ''; ?>>
                        <?php echo $sou['icon'] . "\n"; ?>
                        <span class="engine-name"><?php echo theme_e($sou['name']); ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- 首页公告：后台主题配置填写，支持 HTML -->
        <?php if (!empty($notice)): ?>
            <div class="notice"><?php echo $notice; ?></div>
        <?php endif; ?>

        <!-- ===================== 四、分组和链接列表区：lists() ===================== -->
        <section class="links">

            <?php
            $html = array(
                'g1' => '<div class="link-group">', //分组开始标签
                'g2' => '<h2 class="group-title">{group_icon}<span>{group_name}</span></h2>',//分组内容
                'g3' => '</div>', //分组结束标签
                'l1' => '<a class="link-item" href="{link_url}" target="_blank" rel="nofollow noopener" title="{link_name_text}">',  //链接开始标签
                'l2' => '<span class="link-icon">{link_icon}</span><span class="link-name">{link_name}</span>',
                'l3' => '</a>', //链接结束标签
            );
            lists($html);
            ?>
        </section>

    </main>

    <!-- ===================== 五、底部区 ===================== -->
    <footer class="site-footer">
        <?php echo $conf['copyright']; ?> 

        <?php theme_icp(); /* ICP 备案号，留空不显示 */ ?>
        <?php theme_security_filing(); /* 公安备案号（主题配置），留空不显示 */ ?>
    </footer>

    <?php
    // 自定义统计代码：必须原样输出在最底部
    echo $conf['wztj'];
    ?>
    <!-- 图标雪碧图：数据库所有 #lyicon-* 图标的唯一来源，不可删除 -->
    <script src="<?php echo $cdnpublic; ?>/assets/js/icon.js"></script>

</body>

</html>