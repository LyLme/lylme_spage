<?php
require_once __DIR__ . '/functions.php';

/* ============ 逻辑区：条件渲染集中处理 ============ */
$background_url = background();
$theme_color    = theme_config('color', '#007AFF');
$link_cols      = theme_config('link_cols', 4);
$notice         = trim((string) theme_config('notice', ''));
$modules        = theme_config('modules', array('clock'));
if (!is_array($modules)) { $modules = array($modules); }

/* 风格扩展配置 */
$wall   = theme_lg_enum(theme_config('wall', 'aurora'), array('aurora', 'dusk', 'mint', 'graphite'), 'aurora');
$blur   = theme_lg_enum(theme_config('blur', 'medium'), array('soft', 'medium', 'strong'), 'medium');
$icons  = theme_lg_enum(theme_config('icons', 'gradient'), array('gradient', 'skew', 'flat', 'official'), 'gradient');
$scheme = theme_lg_enum(theme_config('scheme', 'auto'), array('auto', 'light', 'dark'), 'auto');
$sug_on  = theme_lg_enum(theme_config('suggestion', 'on'), array('on', 'off'), 'on');
$radius = theme_lg_enum(theme_config('icon_radius', '22'), array('16', '22', '28', '50'), '22');

$sou_list   = theme_sou();
$tag_list   = theme_tags();
$show_clock = in_array('clock', $modules);
$show_yan   = (in_array('yan', $modules) && $conf['yan'] == 'true');

/* 桌面组件显隐：时钟 / 一言 / 公告 / 记事本 / 待办，均由 config「modules」控制。
 * 向后兼容：旧配置（modules 中不含 note/notes/todo 任一键）视为全部开启，
 * 避免升级后已显示的组件突然消失；新保存的配置或全新安装走精确开关。 */
$mod_has_new = array_intersect($modules, array('note', 'notes', 'todo'));
if (empty($mod_has_new)) {
    $show_note  = ($notice !== '');
    $show_notes = true;
    $show_todo  = true;
} else {
    $show_note  = (in_array('note', $modules) && $notice !== '');
    $show_notes = in_array('notes', $modules);
    $show_todo  = in_array('todo', $modules);
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><?php echo theme_e($conf['title']); ?></title>
    <meta name="keywords" content="<?php echo theme_e($conf['keywords']); ?>">
    <meta name="description" content="<?php echo theme_e($conf['description']); ?>">
    <meta name="theme-color" content="#e9e4ff">
    <link rel="icon" href="<?php echo theme_e($conf['logo']); ?>">
    <style>
        :root{
            --lg-accent: <?php echo theme_e($theme_color); ?>;
            --lg-cols: <?php echo (int) $link_cols; ?>;
            --lg-icon-radius: <?php echo (int) $radius; ?>%;
        }
    </style>
    <?php theme_css('css/style.css'); ?>
    <?php theme_js('js/script.js'); ?>
</head>
<body class="<?php echo checkmobile() ? 'is-mobile' : 'is-pc'; ?> lg-wall-<?php echo theme_e($wall); ?> lg-blur-<?php echo theme_e($blur); ?>"
      data-lg-icons="<?php echo theme_e($icons); ?>"
      data-lg-scheme="<?php echo theme_e($scheme); ?>">

<!-- ============ 壁纸层：柔和渐变 + 液态光斑 ============ -->
<div class="lg-wallpaper"<?php if ($background_url !== '') { echo ' style="background-image:url(\'' . theme_e($background_url) . '\')"'; } ?> aria-hidden="true">
    <span class="lg-blob lg-blob-1"></span>
    <span class="lg-blob lg-blob-2"></span>
    <span class="lg-blob lg-blob-3"></span>
    <span class="lg-grain"></span>
</div>

<!-- ============ macOS 菜单栏（PC 端） ============ -->
<header class="lg-menubar">
    <div class="lg-mb-left">
        <span class="lg-mb-apple">
            
            <?php
           if( theme_config('logo_type', 'logo') =='apple'){
                echo theme_lg_glyph('apple');
           }else{
               echo '<img src="' . $conf['logo'] . '" style="margin-right:10px"';
           }
            ?>
            </span>
        <strong class="lg-mb-app"><?php echo theme_e(explode("-", $conf['title'])[0]); ?></strong>
        <nav class="lg-mb-menu" aria-label="快捷导航">
            <?php foreach ($tag_list as $tag): ?>
            <a class="lg-mb-link" href="<?php echo theme_e($tag['link']); ?>"<?php echo $tag['blank'] ? ' target="_blank" rel="nofollow noopener"' : ''; ?>><?php echo theme_e($tag['name']); ?></a>
            <?php endforeach; ?>
        </nav>
    </div>
    <div class="lg-mb-right">
        <span class="lg-mb-item"><?php echo theme_lg_glyph('signal'); ?></span>
        <span class="lg-mb-item"><?php echo theme_lg_glyph('wifi'); ?></span>
        <span class="lg-mb-item"><?php echo theme_lg_battery(); ?></span>
        <button type="button" class="lg-mb-item lg-mb-btn" data-lg-action="spotlight" title="聚焦搜索" aria-label="聚焦搜索"><?php echo theme_lg_glyph('search'); ?></button>
        <button type="button" class="lg-mb-item lg-mb-btn" data-lg-action="control" title="控制中心" aria-label="控制中心"><?php echo theme_lg_glyph('sliders'); ?></button>
        <span class="lg-mb-item lg-mb-time" id="lg-clock-menu">--:--</span>
    </div>
</header>

<!-- ============ iOS 状态栏（移动端，默认隐藏，由设置开关控制） ============ -->
<div class="lg-statusbar" id="lg-statusbar">
    <span class="lg-sb-time" id="lg-clock-status">--:--</span>
    <span class="lg-sb-island" aria-hidden="true"></span>
    <span class="lg-sb-right">
        <span class="lg-sb-item"><?php echo theme_lg_glyph('signal'); ?></span>
        <span class="lg-sb-item"><?php echo theme_lg_glyph('wifi'); ?></span>
        <span class="lg-sb-item"><?php echo theme_lg_battery('lg-batt-sm'); ?></span>
        <button type="button" class="lg-sb-btn" data-lg-action="control" aria-label="控制中心"><?php echo theme_lg_glyph('sliders'); ?></button>
    </span>
</div>

<!-- ============ 快捷导航条（移动端置顶：theme_tags） ============ -->
<?php if (count($tag_list) > 0): ?>
<nav class="lg-quickrow" aria-label="快捷导航">
    <div class="lg-quickrow-inner">
        <?php foreach ($tag_list as $tag): ?>
        <a class="lg-quick" href="<?php echo theme_e($tag['link']); ?>"<?php echo $tag['blank'] ? ' target="_blank" rel="nofollow noopener"' : ''; ?>>
            <span class="lg-quick-ico"><?php echo theme_lg_glyph('bookmark'); ?></span>
            <span class="lg-quick-name"><?php echo theme_e($tag['name']); ?></span>
        </a>
        <?php endforeach; ?>
    </div>
</nav>
<?php endif; ?>

<!-- ============ 桌面小组件（PC 侧边 / 移动顶部） ============ -->
<aside class="lg-widgets" id="lg-widgets" aria-label="桌面组件">
    <?php if ($show_clock || $show_yan || $show_note): ?>
    <!-- 移动端合并卡片：时间 / 一言 / 公告（PC 端为 display:contents，不影响布局） -->
    <div class="lg-widget-combo" id="lg-combo">
    <?php endif; ?>

    <?php if ($show_clock): ?>
    <section class="lg-widget lg-widget-clock" id="lg-widget-clock">
        <div class="lg-w-titlebar">
            <button type="button" class="lg-w-light lg-w-light-r" data-wclose="lg-widget-clock" aria-label="关闭时钟"></button>
            <button type="button" class="lg-w-light lg-w-light-y" data-wmin="lg-widget-clock" aria-label="最小化时钟"></button>
            <button type="button" class="lg-w-light lg-w-light-g" data-wmax="lg-widget-clock" aria-label="展开时钟"></button>
            <span class="lg-w-title">今天</span>
        </div>
        <span class="lg-w-time" id="lg-clock-widget">--:--</span>
        <span class="lg-w-date" id="lg-date-widget">--月--日 星期-</span>
    </section>
    <?php endif; ?>

    <?php if ($show_yan): ?>
    <section class="lg-widget lg-widget-yan" id="lg-widget-yan">
        <div class="lg-w-titlebar">
            <button type="button" class="lg-w-light lg-w-light-r" data-wclose="lg-widget-yan" aria-label="关闭一言"></button>
            <button type="button" class="lg-w-light lg-w-light-y" data-wmin="lg-widget-yan" aria-label="最小化一言"></button>
            <button type="button" class="lg-w-light lg-w-light-g" data-wmax="lg-widget-yan" aria-label="展开一言"></button>
            <span class="lg-w-title">一言</span>
        </div>
        <p class="lg-w-yan"><?php echo yan(); ?></p>
    </section>
    <?php endif; ?>

    <?php if ($show_note): ?>
    <section class="lg-widget lg-widget-note" id="lg-widget-note">
        <div class="lg-w-titlebar">
            <button type="button" class="lg-w-light lg-w-light-r" data-wclose="lg-widget-note" aria-label="关闭公告"></button>
            <button type="button" class="lg-w-light lg-w-light-y" data-wmin="lg-widget-note" aria-label="最小化公告"></button>
            <button type="button" class="lg-w-light lg-w-light-g" data-wmax="lg-widget-note" aria-label="展开公告"></button>
            <span class="lg-w-title">公告</span>
        </div>
        <div class="lg-w-note"><?php echo $notice; ?></div>
    </section>
    <?php endif; ?>

    <?php if ($show_clock || $show_yan || $show_note): ?>
    </div>
    <?php endif; ?>

    <?php if ($show_notes): ?>
    <section class="lg-widget lg-widget-notes" id="lg-widget-notes">
        <div class="lg-w-titlebar">
            <button type="button" class="lg-w-light lg-w-light-r" data-wclose="lg-widget-notes" aria-label="关闭记事本"></button>
            <button type="button" class="lg-w-light lg-w-light-y" data-wmin="lg-widget-notes" aria-label="最小化记事本"></button>
            <button type="button" class="lg-w-light lg-w-light-g" data-wmax="lg-widget-notes" aria-label="展开记事本"></button>
            <span class="lg-w-title">记事本</span>
        </div>
        <textarea id="lg-notes" class="lg-notes-area" placeholder="随手记点什么…" rows="3" maxlength="2000" aria-label="记事本"></textarea>
    </section>
    <?php endif; ?>

    <?php if ($show_todo): ?>
    <section class="lg-widget lg-widget-todo" id="lg-widget-todo">
        <div class="lg-w-titlebar">
            <button type="button" class="lg-w-light lg-w-light-r" data-wclose="lg-widget-todo" aria-label="关闭待办"></button>
            <button type="button" class="lg-w-light lg-w-light-y" data-wmin="lg-widget-todo" aria-label="最小化待办"></button>
            <button type="button" class="lg-w-light lg-w-light-g" data-wmax="lg-widget-todo" aria-label="展开待办"></button>
            <span class="lg-w-title">代办事项</span>
        </div>
        <form class="lg-todo-add" id="lg-todo-form" autocomplete="off">
            <input type="text" id="lg-todo-input" class="lg-todo-input" placeholder="添加任务…" aria-label="添加任务">
            <button type="submit" aria-label="添加">+</button>
        </form>
        <ul class="lg-todo-list" id="lg-todo-list" aria-live="polite"></ul>
        <div class="lg-todo-meta" id="lg-todo-meta"></div>
    </section>
    <?php endif; ?>
</aside>

<!-- ============ 主窗口（PC） / 主屏（移动端） ============ -->
<main class="lg-shell" id="lg-shell">
    <div class="lg-titlebar">
        <span class="lg-lights" aria-hidden="true"><i class="lg-light lg-light-r"></i><i class="lg-light lg-light-y"></i><i class="lg-light lg-light-g"></i></span>
        <span class="lg-titlebar-name"><?php echo theme_e($conf['title']); ?></span>
        <span class="lg-titlebar-tools">
            <button type="button" class="lg-tb-btn" data-lg-action="prev" aria-label="上一分组"><?php echo theme_lg_glyph('back'); ?></button>
            <button type="button" class="lg-tb-btn" data-lg-action="next" aria-label="下一分组"><?php echo theme_lg_glyph('forward'); ?></button>
            <button type="button" class="lg-tb-btn" data-lg-action="settings" aria-label="设置"><?php echo theme_lg_glyph('gear'); ?></button>
        </span>
    </div>

    <!-- 搜索区（DOM 锚点：id="search-form" / id="search-input" / name="sou"） -->
    <div class="lg-toolbar">
        <?php if (count($sou_list) > 0): ?>
        <!-- PC：分段选择器（在搜索框上方）；移动端：隐藏，改用放大镜触发的下拉（列表由脚本从 radio 生成） -->
        <div class="lg-engines" id="lg-engines" role="radiogroup" aria-label="搜索引擎">
            <?php foreach ($sou_list as $index => $sou): ?>
            <label class="lg-engine" style="--lg-engine: <?php echo theme_e($sou['color']); ?>;">
                <input type="radio" name="sou"
                       value="<?php echo theme_e($sou['link']); ?>"
                       data-alias="<?php echo theme_e($sou['alias']); ?>"
                       data-hint="<?php echo theme_e($sou['hint']); ?>"
                       <?php echo $index === 0 ? 'checked' : ''; ?>>
                <span class="lg-engine-ico"><?php echo $sou['icon'] . "\n"; ?></span>
                <span class="lg-engine-name"><?php echo theme_e($sou['name']); ?></span>
            </label>
            <?php endforeach; ?>
        </div>
        <div class="lg-eng-menu" id="lg-eng-menu" role="listbox" aria-label="搜索引擎列表"></div>
        <?php endif; ?>

        <form id="search-form" class="lg-finder" action="#" autocomplete="off">
            <button type="button" class="lg-finder-glass" id="lg-eng-toggle" aria-haspopup="listbox" aria-expanded="false" aria-label="选择搜索引擎">
                <?php echo theme_lg_glyph('search'); ?>
                <span class="lg-eng-caret" aria-hidden="true"><?php echo theme_lg_glyph('chevron'); ?></span>
            </button>
            <input type="text" id="search-input" class="lg-finder-input" placeholder="聚焦搜索" autocomplete="off" aria-label="搜索">
            <span class="lg-caret" id="lg-caret" aria-hidden="true"></span>
            <button type="submit" class="lg-finder-go">搜索</button>
            <?php if ($sug_on === 'on'): ?>
            <div class="lg-sug" id="lg-sug" role="listbox" aria-label="搜索建议" hidden></div>
            <?php endif; ?>
        </form>
    </div>

    <div class="lg-body">
        <!-- 侧边栏：分组收藏（PC） -->
        <nav class="lg-rail" aria-label="分组收藏">
            <div class="lg-rail-cap">个人收藏</div>
            <ul class="lg-rail-list" id="lg-rail-list"></ul>
        </nav>

        <div class="lg-pager" id="lg-pager">
            <div class="lg-pills" id="lg-pills" role="tablist" aria-label="分组切换"></div>
            <div class="lg-track" id="lg-track">
<?php
/* ============ 链接列表：每个分组 = 一屏可横向滑动的页面 ============ */
$html = array(
    'g1' => '<section class="lg-page" data-lg-name="{group_name}"><div class="lg-grid">',
    'g2' => '<div class="lg-page-head"><span class="lg-page-ico">{group_icon}</span>'
          . '<h2 class="lg-page-name">{group_name}</h2><span class="lg-page-count"></span></div>',
    'g3' => '</div></section>',
    'l1' => '<a class="lg-tile" href="{link_url}" target="_blank" rel="nofollow noopener" title="{link_name_text}">',
    'l2' => '<span class="lg-tile-glyph">{link_icon}</span>'
          . '<span class="lg-tile-name">{link_name}</span>'
          . '<span class="lg-tile-desc">{link_desc}</span>',
    'l3' => '</a>',
);
lists($html);
?>
            </div>
            <div class="lg-pager-foot">
                <div class="lg-dots" id="lg-dots" aria-hidden="true"></div>
                <div class="lg-pager-hint"></div>
            </div>
        </div>
    </div>

    <!-- 移动端底部组件（记事本 / 代办），在窄屏随主屏卡片一起滚动展示；PC 端 display:none -->
    <div class="lg-bottomwidgets" id="lg-bottomwidgets" aria-label="便签与待办"></div>
</main>

<footer class="lg-foot">
    <span class="lg-foot-line"><?php echo $conf['copyright']; ?></span>
    <?php theme_icp(); ?>
    <?php theme_security_filing(); ?>
</footer>

<!-- ============ Dock（PC） / Tab Bar（移动端） ============ -->
<nav class="lg-dock" aria-label="快捷导航">
    <div class="lg-dock-inner" id="lg-dock-inner">
        <button type="button" class="lg-dock-item" data-lg-action="spotlight">
            <span class="lg-dock-ico"><?php echo theme_lg_glyph('search'); ?></span>
            <span class="lg-dock-name">聚焦搜索</span>
        </button>
        <button type="button" class="lg-dock-item" data-lg-action="control">
            <span class="lg-dock-ico"><?php echo theme_lg_glyph('sliders'); ?></span>
            <span class="lg-dock-name">控制中心</span>
        </button>
        <button type="button" class="lg-dock-item" data-lg-action="settings">
            <span class="lg-dock-ico"><?php echo theme_lg_glyph('gear'); ?></span>
            <span class="lg-dock-name">设置</span>
        </button>
    </div>
</nav>

<!-- ============ 聚焦搜索 Spotlight ============ -->
<div class="lg-spotlight" id="lg-spotlight" role="dialog" aria-modal="true" aria-label="聚焦搜索">
    <div class="lg-spot-scrim" data-lg-close="spotlight"></div>
    <div class="lg-spot-panel">
        <form class="lg-spot-bar" id="lg-spot-form" action="#" autocomplete="off">
            <span class="lg-finder-glass" aria-hidden="true"><?php echo theme_lg_glyph('search'); ?></span>
            <input type="text" id="lg-spot-input" class="lg-spot-input" placeholder="搜索应用与网站" autocomplete="off" aria-label="搜索应用与网站">
            <button type="button" class="lg-spot-clear" id="lg-spot-clear" aria-label="清除">清除</button>
        </form>
        <div class="lg-spot-results" id="lg-spot-results"></div>
        <div class="lg-spot-foot">
            <span>↑ ↓ 选择 · Enter 打开 · Esc 关闭</span>
            <span id="lg-spot-engine">搜索引擎</span>
        </div>
    </div>
</div>

<!-- ============ 控制中心 ============ -->
<div class="lg-control" id="lg-control" role="dialog" aria-label="控制中心">
    <div class="lg-cc-head">控制中心</div>
    <div class="lg-cc-quick" id="lg-cc-quick"></div>
    <div class="lg-cc-card">
        <div class="lg-row">
            <span class="lg-row-label">深色模式</span>
            <span class="lg-switch" data-lg-toggle="dark" role="switch" tabindex="0" aria-label="深色模式"><i></i></span>
        </div>
        <div class="lg-row lg-row-stack">
            <span class="lg-row-label">图标风格</span>
            <div class="lg-seg" data-lg-seg="icons">
                <button type="button" data-value="gradient">渐变</button>
                <button type="button" data-value="skew">拟物</button>
                <button type="button" data-value="flat">扁平</button>
                <button type="button" data-value="official">官方</button>
            </div>
        </div>
        <div class="lg-row lg-row-stack">
            <span class="lg-row-label">每行列数</span>
            <div class="lg-seg" data-lg-seg="cols">
                <button type="button" data-value="3">3</button>
                <button type="button" data-value="4">4</button>
                <button type="button" data-value="5">5</button>
                <button type="button" data-value="6">6</button>
            </div>
        </div>
        <div class="lg-row lg-row-stack">
            <span class="lg-row-label">玻璃模糊 <em id="lg-blur-val">20px</em></span>
            <input type="range" class="lg-range" id="lg-blur-range" min="6" max="40" step="2" value="20" aria-label="玻璃模糊">
        </div>
    </div>
</div>

<!-- ============ 设置（分组列表 Grouped Table View） ============ -->
<div class="lg-settings" id="lg-settings" role="dialog" aria-modal="true" aria-label="设置">
    <div class="lg-set-scrim" data-lg-close="settings"></div>
    <div class="lg-set-sheet">
        <div class="lg-set-head">
            <button type="button" data-lg-close="settings">完成</button>
            <h2>设置</h2>
            <span class="lg-set-head-pad"></span>
        </div>
        <div class="lg-set-scroll">
            <section class="lg-set-group">
                <h3 class="lg-set-cap">外观</h3>
                <div class="lg-set-card">
                    <div class="lg-row lg-row-stack">
                        <span class="lg-row-label">主题模式</span>
                        <div class="lg-seg" data-lg-seg="scheme">
                            <button type="button" data-value="auto">跟随系统</button>
                            <button type="button" data-value="light">浅色</button>
                            <button type="button" data-value="dark">深色</button>
                        </div>
                    </div>
                    <div class="lg-row lg-row-stack">
                        <span class="lg-row-label">图标风格</span>
                        <div class="lg-seg" data-lg-seg="icons">
                            <button type="button" data-value="gradient">渐变</button>
                            <button type="button" data-value="skew">拟物</button>
                            <button type="button" data-value="flat">扁平</button>
                            <button type="button" data-value="official">官方</button>
                        </div>
                    </div>
                    <div class="lg-row lg-row-stack">
                        <span class="lg-row-label">每行列数</span>
                        <div class="lg-seg" data-lg-seg="cols">
                            <button type="button" data-value="3">3</button>
                            <button type="button" data-value="4">4</button>
                            <button type="button" data-value="5">5</button>
                            <button type="button" data-value="6">6</button>
                        </div>
                    </div>
                    <div class="lg-row lg-row-stack">
                        <span class="lg-row-label">玻璃模糊 <em id="lg-blur-val-2">20px</em></span>
                        <input type="range" class="lg-range lg-range-settings" min="6" max="40" step="2" value="20" aria-label="玻璃模糊">
                    </div>
                    <div class="lg-row">
                        <span class="lg-row-label">减弱动效</span>
                        <span class="lg-switch" data-lg-toggle="motion" role="switch" tabindex="0" aria-label="减弱动效"><i></i></span>
                    </div>
                </div>
            </section>

            <section class="lg-set-group">
                <h3 class="lg-set-cap">主屏</h3>
                <div class="lg-set-card">
                    <div class="lg-row">
                        <span class="lg-row-label">显示时钟组件</span>
                        <span class="lg-switch" data-lg-toggle="clock" role="switch" tabindex="0" aria-label="显示时钟组件"><i></i></span>
                    </div>
                    <div class="lg-row">
                        <span class="lg-row-label">显示一言组件</span>
                        <span class="lg-switch" data-lg-toggle="yan" role="switch" tabindex="0" aria-label="显示一言组件"><i></i></span>
                    </div>
                    <div class="lg-row">
                        <span class="lg-row-label">显示公告组件</span>
                        <span class="lg-switch" data-lg-toggle="note" role="switch" tabindex="0" aria-label="显示公告组件"><i></i></span>
                    </div>
                    <div class="lg-row">
                        <span class="lg-row-label">显示记事本组件</span>
                        <span class="lg-switch" data-lg-toggle="notes" role="switch" tabindex="0" aria-label="显示记事本组件"><i></i></span>
                    </div>
                    <div class="lg-row">
                        <span class="lg-row-label">显示代办事项组件</span>
                        <span class="lg-switch" data-lg-toggle="todo" role="switch" tabindex="0" aria-label="显示代办事项组件"><i></i></span>
                    </div>
                    <div class="lg-row">
                        <span class="lg-row-label">显示状态栏（仅移动端）</span>
                        <span class="lg-switch" data-lg-toggle="statusbar" role="switch" tabindex="0" aria-label="显示状态栏"><i></i></span>
                    </div>
                </div>
            </section>

            <section class="lg-set-group">
                <h3 class="lg-set-cap">数据</h3>
                <div class="lg-set-card">
                    <div class="lg-row lg-row-link" id="lg-export-row">
                        <span class="lg-row-label">导出配置与数据</span>
                        <span class="lg-row-value">JSON <?php echo theme_lg_glyph('chevron'); ?></span>
                    </div>
                    <div class="lg-row lg-row-link" id="lg-import-row">
                        <span class="lg-row-label">导入配置与数据</span>
                        <span class="lg-row-value"><?php echo theme_lg_glyph('chevron'); ?></span>
                        <input type="file" id="lg-import-file" accept="application/json,.json" hidden>
                    </div>
                    <div class="lg-row lg-row-link" id="lg-reset-row">
                        <span class="lg-row-label">清除本机数据</span>
                        <span class="lg-row-value"><span class="lg-row-danger">不可恢复</span> <?php echo theme_lg_glyph('chevron'); ?></span>
                    </div>
                </div>
                <p class="lg-set-note">记事本 / 待办 / 隐藏图标 / 偏好均存在本机浏览器，可导出 JSON 备份或迁移到其它设备。</p>
            </section>

            <section class="lg-set-group">
                <h3 class="lg-set-cap">图标管理</h3>
                <div class="lg-set-card">
                    <div class="lg-row lg-row-link" id="lg-restore-row">
                        <span class="lg-row-label">恢复已隐藏的图标</span>
                        <span class="lg-row-value"><em id="lg-hidden-count">0</em> 个<span class="lg-chev"><?php echo theme_lg_glyph('chevron'); ?></span></span>
                    </div>
                    <div class="lg-row lg-row-link" id="lg-edit-row">
                        <span class="lg-row-label">进入编辑模式</span>
                        <span class="lg-row-value">长按可隐藏<span class="lg-chev"><?php echo theme_lg_glyph('chevron'); ?></span></span>
                    </div>
                </div>
                <p class="lg-set-note">长按任意图标可呼出毛玻璃上下文菜单，隐藏后的图标仅保存在本机，可随时恢复。</p>
            </section>

            <section class="lg-set-group">
                <h3 class="lg-set-cap">关于</h3>
                <div class="lg-set-card">
                    <div class="lg-row"><span class="lg-row-label">站点</span><span class="lg-row-value"><?php echo theme_e($conf['title']); ?></span></div>
                    <div class="lg-row"><span class="lg-row-label">主题</span><span class="lg-row-value">LiquidGlass <?php echo theme_e(theme_version()); ?></span></div>
                    <?php foreach ($tag_list as $tag): ?>
                    <div class="lg-row lg-row-link">
                        <a class="lg-row-a" href="<?php echo theme_e($tag['link']); ?>"<?php echo $tag['blank'] ? ' target="_blank" rel="nofollow noopener"' : ''; ?>>
                            <span class="lg-row-label"><?php echo theme_e($tag['name']); ?></span>
                            <span class="lg-chev"><?php echo theme_lg_glyph('chevron'); ?></span>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <p class="lg-set-foot">LiquidGlass · 六零导航页主题<br>通透、轻盈、有呼吸感</p>
        </div>
    </div>
</div>

<!-- ============ 上下文菜单（毛玻璃浮层） ============ -->
<div class="lg-ctx" id="lg-ctx" role="menu">
    <button type="button" class="lg-ctx-item" data-lg-ctx="open"><span><?php echo theme_lg_glyph('link'); ?></span>打开链接</button>
    <button type="button" class="lg-ctx-item" data-lg-ctx="copy"><span><?php echo theme_lg_glyph('bookmark'); ?></span>复制链接</button>
    <button type="button" class="lg-ctx-item lg-ctx-danger" data-lg-ctx="hide"><span><?php echo theme_lg_glyph('minus'); ?></span>隐藏图标</button>
    <button type="button" class="lg-ctx-item" data-lg-ctx="cancel">取消</button>
</div>

<script src="<?php echo $cdnpublic; ?>/assets/js/icon.js"></script>
<script src="<?php echo $cdnpublic; ?>/assets/js/svg.js"></script>
<?php echo $conf['wztj']; ?>
</body>
</html>
