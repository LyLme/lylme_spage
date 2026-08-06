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
    <link rel="stylesheet" href="/site/static/site.css">
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
                            echo '><svg class="menu_icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="9773" width="32" height="32"><path d="M509.4 508.5m-469.5 0a469.5 469.5 0 1 0 939 0 469.5 469.5 0 1 0-939 0Z" fill="#242424" p-id="9774"></path><path d="M617.9 467.2c-0.3-0.6-0.5-1.2-0.8-1.8-0.1-0.1-0.1-0.2-0.2-0.4-7.2-14.5-22.7-23.9-39.9-22.6-22.5 1.8-39.4 21.5-37.6 44 0.5 5.8 2.1 11.1 4.6 15.9 11.4 25.7 6.4 57-14.6 78.1l-110 110.2c-27.3 27.3-71.7 27.3-99 0-27.3-27.3-27.3-71.7 0-99l41-41-0.3-0.3c9.5-8.2 15-20.7 14-34.1-1.8-22.5-21.5-39.4-44-37.6-10.8 0.8-20.2 5.8-27 13.2l-0.1-0.1-41.8 41.8c-59.4 59.4-59.4 155.6 0 215 59.4 59.4 155.6 59.4 215 0l110.3-110.3c46.2-46.3 56.2-114.8 30.4-171z" fill="#FFFFFF" p-id="9775"></path><path d="M762.4 257.4c-59.4-59.4-155.6-59.4-215 0L437.1 367.7c-46.2 46.2-56.2 114.7-30.5 170.9 0.3 0.6 0.5 1.2 0.8 1.8 0.1 0.1 0.1 0.2 0.2 0.4 7.2 14.5 22.7 23.9 39.9 22.6 22.5-1.8 39.4-21.5 37.6-44-0.5-5.8-2.1-11.1-4.6-15.9-11.4-25.7-6.4-57 14.6-78.1l110.1-110.1c27.3-27.3 71.7-27.3 99 0 27.3 27.3 27.3 71.7 0 99l-41 41 0.3 0.3c-9.5 8.2-15 20.7-14 34.1 1.8 22.5 21.5 39.4 44 37.6 10.8-0.8 20.2-5.8 27-13.2l0.1 0.1 41.8-41.8c59.3-59.4 59.3-155.7 0-215z" fill="#FFFFFF" p-id="9776"></path></svg> ' . $taglist["tag_name"] . '</a>
                            ' . "\n";
                        } ?>
                    </div>
                </div>
                <span class="night-mode-btn" onclick="toggleNightMode()" title="切换模式" role="button" aria-label="切换深色模式">
                    <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"></circle><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"></path></svg>
                    <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"></path></svg>
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
                <a href="/"><?php echo $group_name; ?></a>
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
            <div class="site-keywords" id="site_keyword"><?php echo $url_keywords; ?></div>
            <div class="site-actions">
                <a href="<?php echo $url_herf; ?>" rel="nofollow" target="_blank" class="btn btn-primary">立即访问 <i class="fa fa-paper-plane"></i></a>
                <div class="urls-tools-qr">
                    <a href="javascript:void(0);" class="btn btn-ghost">手机查看 <i class="fa fa-qrcode"></i></a>
                    <span id="code"><img width="120" height="120" src="/include/qrcode.php?text=<?php echo $url_herf ?>"></span>
                </div>
                <a href="javascript:void(0);" class="btn btn-ghost" onclick="copyLink()">复制链接 <i class="fa fa-link"></i></a>
            </div>
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
        <link href="/assets/css/viewer.min.css" type="text/css" rel="stylesheet" />
        <script src="/assets/js/viewer.min.js" type="application/javascript"></script>
        <script src="/site/static/site.js"></script>

    </div>
</body>

</html>