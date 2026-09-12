<!DOCTYPE html>
<html lang="zh" id="content">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title> <?php echo $url_name  ?> - <?php echo $conf['title']; ?> - <?php echo $group_name ?></title>
    <meta name="keywords" content="<?php echo $conf['keywords'] ?>" />
    <meta name="description" content="<?php echo $conf['description'] ?>" />
    <link rel="shortcut icon" href="<?php echo $conf['logo'] ?>">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="full-screen" content="yes">
    <meta name="browsermode" content="application">
    <meta name="x5-fullscreen" content="true">
    <meta name="x5-page-mode" content="app">
    <script src="<?php echo $cdnpublic ?>/assets/js/svg.js"></script>
    <script src="<?php echo $cdnpublic ?>/assets/js/icon.js"></script>
    <link rel="stylesheet" href="<?php echo $cdnpublic ?>/site/static/site.css">
    <script src="<?php echo $cdnpublic ?>/assets/js/jquery.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var nightMode = localStorage.getItem('nightMode');
            if (nightMode === 'true') {
                toggleNightMode();
            }
        });
    </script>
</head>

<body>
    <?php if (!empty(background())) {
        echo '<div class="background" style="background-image: url(' . background() . ');background-size: cover"></div>';
    } ?>

    <div class="container">
        <header>
            <div class="logo-nav">
                <a href="/" title="<?php echo $conf['title'] ?>">
                    <img src="<?php echo $conf['logo'] ?>" class="logo" alt="<?php echo $conf['title'] ?>">
                </a>
            </div>
            <div class="title-nav">
                <h1><?php echo explode("-", $conf['title'])[0]; ?></h1>
            </div>
            <div class="right-content">
                <div class="dropdown">
                    <span class="dropbtn more" onclick="toggleDropdown(event)"></span>
                    <div class="dropdown-content">

                        <?php
                        $tagslists = $DB->query("SELECT * FROM `lylme_tags`");
                        while ($taglist = $DB->fetch($tagslists)) {
                            echo '<a href="' . $taglist["tag_link"] . '"';
                            if ($taglist["tag_target"]) {
                                echo ' target="_blant"';
                            }
                            echo '><svg class="icon menu_icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="8195" width="200" height="200"><path d="M873.04 211.55l-60.38-60.39c-24.67-24.67-57.41-37.42-90.16-37.42s-65.49 12.76-90.15 37.42L512 271.52c-49.75 49.75-49.75 130.55 0 180.31l-59.96 60.39h-0.43c-25.09-25.09-57.41-37.42-90.16-37.42-32.32 0-65.07 12.33-90.15 37.42L150.96 632.56c-49.75 49.75-49.75 130.55 0 180.31l60.38 59.96c24.67 25.09 57.41 37.42 90.16 37.42s65.49-12.33 90.15-37.42L512 752.48c49.75-49.75 49.75-130.55 0-180.31l59.96-59.96c25.09 24.67 57.84 37.42 90.59 37.42 32.32 0 65.07-12.76 90.15-37.42l120.35-120.35c49.75-49.75 49.75-130.55-0.01-180.31z m-421 480.97L331.69 812.87c-11.06 10.63-23.82 12.33-30.19 12.33-6.38 0-19.14-1.7-30.19-12.33l-59.97-60.39c-11.06-10.63-12.33-23.39-12.33-29.77 0-6.8 1.27-19.14 12.33-30.19l120.35-120.35c10.63-10.63 23.39-12.33 29.77-12.33 6.81 0 19.56 1.7 30.19 12.33l-30.19 30.19c-16.58 16.59-16.58 43.38 0 59.96 8.5 8.51 19.56 12.76 30.19 12.76 11.06 0 21.69-4.25 30.2-12.76l30.2-29.77c16.57 16.6 16.57 43.39-0.01 59.97zM812.66 331.9L692.31 451.83c-10.63 11.06-23.39 12.76-29.77 12.76-6.81 0-19.56-1.7-30.19-12.76l30.19-29.77c16.58-16.59 16.58-43.8 0-60.39-8.5-8.08-19.56-12.33-30.19-12.33-11.06 0-21.69 4.25-30.2 12.33l-30.2 30.19c-16.58-16.59-16.58-43.38 0-59.96L692.3 211.55c11.06-11.05 23.82-12.76 30.19-12.76 6.38 0 19.14 1.7 30.19 12.76l59.97 59.96c16.59 16.59 16.59 43.81 0.01 60.39z" fill="#1A1A1A" p-id="8196"></path></svg> ' . $taglist["tag_name"] . '</a>
                            ' . "\n";
                        } ?>
                    </div>
                </div>
                <span class="night-mode-btn" onclick="toggleNightMode()" title="切换模式" role="button" aria-label="切换深色模式">
                    <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="4"></circle>
                        <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"></path>
                    </svg>
                    <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"></path>
                    </svg>
                </span>
            </div>
        </header>

        <!-- 极简时钟条 -->
        <div class="clock-container">
            <div class="clock-face" id="time"></div>
            <div class="clock-details">
                <div class="date-line">
                    <div class="date-part" id="date"></div>
                    <div class="date-part" id="weekday"></div>
                </div>
            </div>
        </div>



        <!-- 主卡片 -->
        <article class="card main-card">
            <!-- 面包屑 -->
            <nav class="crumb">
                <a href="/">首页</a>
                <span class="sep">/</span>

                <a href="/#group_<?php echo $group_id; ?>"><?php echo $group_name; ?></a>
                <span class="sep">/</span>
                <span class="cur"><?php echo $url_name; ?></span>
            </nav>
            <div class="site-head">
                <div class="site-logo"><?php echo $url_icon; ?></div>
                <div class="site-titles">
                    <h2 class="site-name"><?php echo $url_name; ?></h2>
                    <p class="site-desc"><?php echo $url_description; ?></p>
                </div>
            </div>

            <div class="site-actions">
                <a href="<?php echo $url_herf; ?>" rel="nofollow" target="_blank" class="btn btn-primary">立即访问 <i class="fa fa-paper-plane"></i></a>
                <div class="urls-tools-qr">
                    <a href="javascript:void(0);" class="btn btn-ghost">手机查看 <i class="fa fa-qrcode"></i></a>
                    <span id="code"><img width="120" height="120" src="/include/qrcode.php?text=<?php echo $url_herf ?>"></span>
                </div>
                <a href="javascript:void(0);" class="btn btn-ghost" onclick="copyLink()">复制链接 <i class="fa fa-link"></i></a>
            </div>
            <div class="site-keywords" id="site_keyword"><?php echo $url_keywords; ?></div>
        </article>

        <!-- 网站信息 -->
        <article class="card">
            <h3 class="card-title">网站信息</h3>
            <ul class="info-list">
                <li>
                    <span class="info-label"><i class="fa fa-link"></i>链接地址</span>
                    <span class="info-value" id="meta-url"><?php echo $url_herf; ?></span>
                </li>
                <li>
                    <span class="info-label"><i class="fa fa-folder-open"></i>所属分组</span>
                    <span class="info-value"><?php echo $group_name; ?></span>
                </li>
                <li>
                    <span class="info-label"><i class="fa fa-tags"></i>网站关键词</span>
                    <span class="info-value"><?php echo $url_keywords; ?></span>
                </li>
                <li>
                    <span class="info-label"><i class="fa fa-file-text-o"></i>网站描述</span>
                    <span class="info-value"><?php echo $url_description; ?></span>
                </li>
            </ul>
        </article>

        <?php
        if (!empty($conf['snapshot'])) {
        ?>
            <article class="card shot-card">
                <h3 class="card-title">网页快照</h3>
                <div class="view_img">
                    <img class="lazyload" data-src="<?php echo $conf['snapshot'] . $url_herf ?>" src="/site/static/default-image.webp" alt="<?php echo $url_name; ?>快照">
                </div>
            </article>
            <script>
                const viewer = new Viewer(document.querySelector(".view_img"), {
                    viewed() {},
                });
            </script>
        <?php
        } ?>
        <div class="footer-inner">
            <div class="footer-text">

                <?php if (!empty($conf['icp'])) {
                    echo '<a href="http://beian.miit.gov.cn/" class="icp" target="_blank" _mstmutation="1" _istranslated="1">' . $conf['icp'] . '</a>';
                } ?>
                <?php if (!empty($conf['wztj'])) {
                    echo '<p>' . $conf["wztj"] . '</p>';
                }
                ?>
                <p> <?php echo $conf['copyright']; ?> </p>



            </div>
        </div>
        <link href="<?php echo $cdnpublic ?>/assets/css/viewer.min.css" type="text/css" rel="stylesheet" />
        <script src="<?php echo $cdnpublic ?>/assets/js/viewer.min.js" type="application/javascript"></script>
        <script src="<?php echo $cdnpublic ?>/site/static/site.js"></script>

    </div>
</body>

</html>