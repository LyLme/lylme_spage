<?php
require_once __DIR__ . '/functions.php';

// 逻辑区（条件渲染集中处理，输出到变量）
$background_url = background();
$theme_color = theme_config('color', '#2f6fed');
$link_cols   = theme_config('link_cols', 4);
$notice      = trim((string) theme_config('notice', ''));
$modules     = theme_config('modules', array('clock'));
if (!is_array($modules)) { $modules = array($modules); }

// 赛博朋克扩展配置
$accent   = theme_config('accent', '#ff2e97');
$scanline = theme_config('scanline', 'on');
$glow     = theme_config('glow', 'mid');
$sou_list = theme_sou();
$cw_tags  = theme_tags();
$glow_cls = in_array($glow, array('low', 'mid', 'high')) ? 'cw-glow-' . $glow : 'cw-glow-mid';
$scan_cls = ($scanline === 'off') ? 'cw-scan-off' : 'cw-scan-on';
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
    <?php theme_css('css/style.css'); ?>
    <style>
        :root{
            --cy-cyan: <?php echo theme_e($theme_color); ?>;
            --cy-magenta: <?php echo theme_e($accent); ?>;
            --cy-cols: <?php echo (int)$link_cols; ?>;
        }
    </style>
    <?php theme_js('js/script.js'); ?>
</head>
<body class="<?php echo checkmobile() ? 'is-mobile' : 'is-pc'; ?> <?php echo $glow_cls; ?> <?php echo $scan_cls; ?>">

    <?php if ($background_url !== ''): ?>
    <div class="cw-bg" style="background-image:url('<?php echo theme_e($background_url); ?>');" aria-hidden="true"></div>
    <?php endif; ?>
    <div class="cw-grid" aria-hidden="true"></div>
    <div class="cw-scan" aria-hidden="true"></div>

    <div class="cw-shell">

        <aside class="cw-rail">
            <div class="cw-brand">
                <span class="cw-brand-mark" aria-hidden="true">&#9672;</span>
                <span class="cw-brand-name" data-text="<?php echo theme_e($conf['title']); ?>"><?php echo theme_e($conf['title']); ?></span>
                <span class="cw-brand-sub">// NAVIGATION GRID</span>
            </div>

            <?php if (count($cw_tags) > 0): ?>
            <nav class="cw-rail-nav" aria-label="分类导航">
                <?php foreach ($cw_tags as $tag): ?>
                <a class="cw-rail-link" href="<?php echo theme_e($tag['link']); ?>"<?php echo $tag['blank'] ? ' target="_blank" rel="nofollow noopener"' : ''; ?>>
                    <span class="cw-rail-tick" aria-hidden="true">&#9656;</span>
                    <span class="cw-rail-text"><?php echo theme_e($tag['name']); ?></span>
                </a>
                <?php endforeach; ?>
            </nav>
            <?php endif; ?>

            <div class="cw-console">
                <?php if (in_array('clock', $modules)): ?>
                <div class="cw-time" id="cw-time">--:--:--</div>
                <div class="cw-date" id="cw-date">----/--/--</div>
                <?php endif; ?>

                <?php if ($conf['yan'] == 'true'): ?>
                <p class="cw-yan">&gt; <?php echo yan(); ?></p>
                <?php endif; ?>

                <?php if ($notice !== ''): ?>
                <div class="cw-notice"><?php echo $notice; ?></div>
                <?php endif; ?>
            </div>
        </aside>

        <div class="cw-stage">
            <section class="cw-hero">
                <h1 class="cw-hero-title" data-text="SYSTEM ONLINE">SYSTEM ONLINE</h1>
                <form id="search-form" class="cw-finder" action="#">
                    <span class="cw-finder-prompt" aria-hidden="true">root@grid:~$</span>
                    <input type="text" id="search-input" class="cw-finder-input" placeholder="请输入搜索内容" autocomplete="off">
                    <button type="submit" class="cw-finder-go">搜索</button>
                </form>
                <?php if (count($sou_list) > 0): ?>
                <div class="cw-engines" role="radiogroup" aria-label="搜索引擎">
                    <?php foreach ($sou_list as $index => $sou): ?>
                    <label class="cw-engine" style="--cy-edge: <?php echo theme_e($sou['color']); ?>;">
                        <input type="radio" name="sou" value="<?php echo theme_e($sou['link']); ?>"
                               data-alias="<?php echo theme_e($sou['alias']); ?>" data-hint="<?php echo theme_e($sou['hint']); ?>"
                               <?php echo $index === 0 ? 'checked' : ''; ?>>
                        <span class="cw-engine-icon"><?php echo $sou['icon'] . "\n"; ?></span>
                        <span class="cw-engine-label"><?php echo theme_e($sou['name']); ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </section>

            <main class="cw-board">
                <?php
                $html = array(
                    'g1' => '<section class="cw-zone">',
                    'g2' => '<header class="cw-zone-head"><span class="cw-zone-ico">{group_icon}</span><h2 class="cw-zone-name">{group_name}</h2><span class="cw-zone-id">{group_id}</span></header>',
                    'g3' => '</section>',
                    'l1' => '<a class="cw-node" href="{link_url}" target="_blank" rel="nofollow noopener" title="{link_name_text}">',
                    'l2' => '<span class="cw-node-glyph">{link_icon}</span><span class="cw-node-body"><span class="cw-node-label">{link_name}</span><span class="cw-node-desc">{link_desc}</span></span>',
                    'l3' => '</a>',
                );
                lists($html);
                ?>
            </main>
        </div>

    </div>

    <footer class="cw-foot">
        <span class="cw-foot-line"><?php echo $conf['copyright']; ?></span>
        <?php theme_icp(); ?>
        <?php theme_security_filing(); ?>
    </footer>

    <?php echo $conf['wztj']; ?>
    <script src="<?php echo $cdnpublic; ?>/assets/js/icon.js"></script>
    <script src="<?php echo $cdnpublic; ?>/assets/js/svg.js"></script>
</body>
</html>
