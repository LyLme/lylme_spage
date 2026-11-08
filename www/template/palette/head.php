<?php
$sou_list = theme_sou();
$tags = theme_tags();
$title_parts = explode('-', $conf['title']);
$brand_text = isset($title_parts[0]) ? trim($title_parts[0]) : '';
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo theme_e($conf['title']); ?></title>
    <meta name="keywords" content="<?php echo theme_e($conf['keywords']); ?>" />
    <meta name="description" content="<?php echo theme_e($conf['description']); ?>" />
    <link rel="shortcut icon" href="<?php echo theme_e($conf['logo']); ?>">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="full-screen" content="yes">
    <meta name="browsermode" content="application">
    <meta name="x5-fullscreen" content="true">
    <meta name="x5-page-mode" content="app">
    <script>
        (function () {
            try {
                if (localStorage.getItem('palette_nightMode') === 'true') {
                    document.documentElement.classList.add('night-mode');
                }
                var color = localStorage.getItem('palette_color');
                // 只做通用的合法性校验，不维护颜色白名单：
                // 新增自定义色彩时只需改 style.css 与 script.js，无需同步此处。
                if (color && /^[a-z][a-z0-9_-]*$/i.test(color)) {
                    document.documentElement.setAttribute('data-theme', color);
                }
            } catch (e) {
                // 隐私模式下 localStorage 不可用，使用默认配色
            }
        })();
    </script>
    <?php theme_css('css/style.css'); ?>
    <?php theme_js('js/script.js'); ?>
</head>

<body>
    <!-- 动态背景层 -->
    <div class="bg-layer"></div>

    <div class="main-wrapper">
        <!-- 顶部栏 -->
        <header class="top-bar">
            <div class="top-bar-left">
                <a href="/" class="brand-link" title="<?php echo theme_e($conf['title']); ?>">
                    <img src="<?php echo theme_e($conf['logo']); ?>" class="brand-logo" alt="<?php echo theme_e($conf['title']); ?>">
                    <span class="brand-text"><?php echo theme_e($brand_text); ?></span>
                </a>
            </div>
            <div class="top-bar-right">
                <!-- 背景切换 -->
                <button type="button" class="icon-btn" id="bgBtn" title="切换背景" aria-label="切换背景">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                        <circle cx="8.5" cy="8.5" r="1.5"/>
                        <polyline points="21 15 16 10 5 21"/>
                    </svg>
                </button>
                <!-- 调色板 -->
                <div class="palette-dropdown">
                    <button type="button" class="icon-btn" id="paletteBtn" title="切换主题色彩" aria-label="切换主题色彩" aria-haspopup="true" aria-expanded="false">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 2a10 10 0 0 1 0 20"/>
                            <path d="M12 2a10 10 0 0 0 0 20"/>
                            <line x1="2" y1="12" x2="22" y2="12"/>
                        </svg>
                    </button>
                    <div class="palette-popover">
                        <div class="color-options">
                            <button type="button" class="color-opt active" data-color="purple" title="极光紫" aria-label="极光紫"></button>
                            <button type="button" class="color-opt" data-color="blue" title="板岩蓝" aria-label="板岩蓝"></button>
                            <button type="button" class="color-opt" data-color="green" title="森林绿" aria-label="森林绿"></button>
                            <button type="button" class="color-opt" data-color="pink" title="玫瑰粉" aria-label="玫瑰粉"></button>
                            <button type="button" class="color-opt" data-color="orange" title="日落橙" aria-label="日落橙"></button>
                            <button type="button" class="color-opt" data-color="teal" title="海洋青" aria-label="海洋青"></button>
                        </div>
                    </div>
                </div>
                <!-- 夜间模式 -->
                <button type="button" class="icon-btn" id="nightBtn" title="切换模式" aria-label="切换夜间模式" aria-pressed="false">
                    <svg class="icon-sun" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <circle cx="12" cy="12" r="5"/>
                        <line x1="12" y1="1" x2="12" y2="3"/>
                        <line x1="12" y1="21" x2="12" y2="23"/>
                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
                        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                        <line x1="1" y1="12" x2="3" y2="12"/>
                        <line x1="21" y1="12" x2="23" y2="12"/>
                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
                        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                    </svg>
                    <svg class="icon-moon" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" style="display:none" aria-hidden="true">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                    </svg>
                </button>
                <!-- 更多菜单：快捷导航为空时不渲染，避免弹出空白浮层 -->
                <?php if (!empty($tags)) : ?>
                <div class="dropdown">
                    <button type="button" class="icon-btn" title="更多" aria-label="更多导航" aria-haspopup="true" aria-expanded="false">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true">
                            <circle cx="12" cy="5" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="12" cy="19" r="2"/>
                        </svg>
                    </button>
                    <div class="dropdown-menu">
                        <?php foreach ($tags as $tag) : ?>
                            <a href="<?php echo theme_e($tag['link']); ?>"<?php echo $tag['blank'] ? ' target="_blank" rel="noopener"' : ''; ?> class="dropdown-link"><?php echo theme_e($tag['name']); ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </header>

        <!-- 中心内容区 -->
        <div class="hero-section">
            <!-- 时钟 -->
            <div class="clock-area">
                <div class="clock-time" id="clockTime">00:00:00</div>
                <div class="clock-date" id="clockDate"></div>
                <div class="clock-greeting" id="clockGreeting"></div>
            </div>

            <?php if ($conf['yan'] == 'true') : ?>
            <p class="yiyan-text"><?php echo theme_e(yan()); ?></p>
            <?php endif; ?>

            <!-- 搜索区 -->
            <div class="search-area">
                <form class="search-form" id="search-form" action="#">
                    <div class="search-wrapper">
                        <div class="search-engine-switcher" id="engineSwitcher" title="选择搜索引擎" role="button" tabindex="0" aria-label="选择搜索引擎" aria-expanded="false" aria-controls="engineOptions">
                            <span class="engine-icon" id="engineIcon"></span>
                            <svg class="engine-arrow" viewBox="0 0 24 24" width="12" height="12" fill="currentColor">
                                <path d="M7 10l5 5 5-5z"/>
                            </svg>
                        </div>
                        <input class="search-input" type="text" id="search-input" placeholder="请输入搜索内容" autocomplete="off" x-webkit-speech lang="zh-CN">
                        <button class="search-btn" id="searchBtn" type="submit" title="搜索">
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"/>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                        </button>
                    </div>
                    <div class="engine-options" id="engineOptions" role="radiogroup" aria-label="选择搜索引擎">
                        <?php foreach ($sou_list as $index => $sou) : ?>
                        <label class="engine-option" title="<?php echo theme_e($sou['name']); ?>">
                            <input type="radio" name="sou"
                                   value="<?php echo theme_e($sou['link']); ?>"
                                   data-alias="<?php echo theme_e($sou['alias']); ?>"
                                   data-hint="<?php echo theme_e($sou['hint']); ?>"
                                   <?php echo $index === 0 ? 'checked' : ''; ?>>
                            <span class="engine-option-icon"><?php echo $sou['icon']; ?></span>
                            <span class="engine-option-name"><?php echo theme_e($sou['name']); ?></span>
                            <svg class="engine-option-check" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.6" aria-hidden="true">
                                <path d="M20 6L9 17l-5-5"/>
                            </svg>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <!-- 搜索建议 -->
                    <ul class="search-suggestions" id="searchSuggestions"></ul>
                </form>
            </div>
        </div>

        <!-- 链接导航区 -->
        <div class="nav-section">
