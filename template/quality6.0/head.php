<!DOCTYPE html>
<html lang="zh" id="content">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo $conf['title'] ?></title>
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
    <link rel="stylesheet" href="<?php echo $templatepath; ?>/css/style.css">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var nightMode = localStorage.getItem('nightMode');
            if (nightMode === 'true') {
                toggleNightMode();
            }
        });
    </script>
</head>
<video id="bgVideo" autoplay loop muted style="display: none;"></video>

<body>
    <div class="container">
        <header>
            <div class="logo-nav">
                <a href="/" title="<?php echo $conf['title'] ?>">
                    <img src="<?php echo $conf['logo'] ?>" class="logo" alt="<?php echo $conf['title'] ?>">
                </a>
            </div>
            <div class="right-content">
                <div class="dropdown">
                    <img class="dropbtn more">
                    <div class="dropdown-content">
                        <div id="bg-selector">
                            <img class="bg-preview" src="<?php echo $templatepath; ?>/img/bg-img.png" id="bg-img" alt="图片背景" title="图片背景">
                            <?php if (!empty(theme_config('background_video'))) {
                                echo '<img class="bg-preview" src="'.$templatepath.'/img/bg-video.png" id="bg-video" alt="视频背景" title="视频背景">';
                            } ?>
                            <img class="bg-preview" src="<?php echo $templatepath; ?>/img/bg-none.png" id="bg-none" alt="清空背景" title="清空背景">
                        </div>
                        <?php
                        $tagslists = $DB->query("SELECT * FROM `lylme_tags`");
                        while ($taglist = $DB->fetch($tagslists)) {
                            echo '<a href="' . $taglist["tag_link"] . '"';
                            if ($taglist["tag_target"]) {
                                echo ' target="_blant"';
                            }
                            echo '><svg t="1710555002631" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="9773" width="32" height="32"><path d="M509.4 508.5m-469.5 0a469.5 469.5 0 1 0 939 0 469.5 469.5 0 1 0-939 0Z" fill="#242424" p-id="9774"></path><path d="M617.9 467.2c-0.3-0.6-0.5-1.2-0.8-1.8-0.1-0.1-0.1-0.2-0.2-0.4-7.2-14.5-22.7-23.9-39.9-22.6-22.5 1.8-39.4 21.5-37.6 44 0.5 5.8 2.1 11.1 4.6 15.9 11.4 25.7 6.4 57-14.6 78.1l-110 110.2c-27.3 27.3-71.7 27.3-99 0-27.3-27.3-27.3-71.7 0-99l41-41-0.3-0.3c9.5-8.2 15-20.7 14-34.1-1.8-22.5-21.5-39.4-44-37.6-10.8 0.8-20.2 5.8-27 13.2l-0.1-0.1-41.8 41.8c-59.4 59.4-59.4 155.6 0 215 59.4 59.4 155.6 59.4 215 0l110.3-110.3c46.2-46.3 56.2-114.8 30.4-171z" fill="#FFFFFF" p-id="9775"></path><path d="M762.4 257.4c-59.4-59.4-155.6-59.4-215 0L437.1 367.7c-46.2 46.2-56.2 114.7-30.5 170.9 0.3 0.6 0.5 1.2 0.8 1.8 0.1 0.1 0.1 0.2 0.2 0.4 7.2 14.5 22.7 23.9 39.9 22.6 22.5-1.8 39.4-21.5 37.6-44-0.5-5.8-2.1-11.1-4.6-15.9-11.4-25.7-6.4-57 14.6-78.1l110.1-110.1c27.3-27.3 71.7-27.3 99 0 27.3 27.3 27.3 71.7 0 99l-41 41 0.3 0.3c-9.5 8.2-15 20.7-14 34.1 1.8 22.5 21.5 39.4 44 37.6 10.8-0.8 20.2-5.8 27-13.2l0.1 0.1 41.8-41.8c59.3-59.4 59.3-155.7 0-215z" fill="#FFFFFF" p-id="9776"></path></svg> ' . $taglist["tag_name"] . '</a>
                            ' . "\n";
                        } ?>
                    </div>
                </div>
                <img class="night-mode-btn" src="<?php echo $templatepath; ?>/img/day_mode.svg" alt="切换模式" onclick="toggleNightMode()">
            </div>
        </header>
        <div class="clock-container">
            <div class="clock-face" id="time"></div>
            <div class="clock-details">
                <div class="date-line">
                    <div class="date-part" id="monthYear"></div>
                    <div class="date-part" id="date"></div>
                    <div class="date-part" id="weekday"></div>
                </div>
            </div>
        </div>
        <div id="reminder" class="reminder"></div>
        <?php
        if ($conf['yan'] == 'true') {
            echo '<p class="yiyan">' . yan() . '</p>';
        }
        ?>
        <div class="sou">
            <span class="tip-text">点击这里切换搜索引擎</span>
            <div class="search-container">
                <?php
                $soulists = $site->getSou();
                $json = array();
                while ($soulist = $DB->fetch($soulists)) {
                    echo '<div class="ss hide"><div class="lg">' . $soulist["sou_icon"] . '</div>
                    </div>';
                    if (checkmobile() && !empty($soulist["sou_waplink"])) {
                        $so = $soulist["sou_waplink"];
                    } else {
                        $so = $soulist["sou_link"];
                    }
                    array_push($json, array($soulist['sou_name'], $soulist['sou_hint'], $so));
                }
                $json = json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                ?>
                <input class="wd soinput" type="text" placeholder="" name="q" x-webkit-speech lang="zh-CN" autocomplete="off">
                <button onclick="go('');">
                    <svg class="icon" style=" width: 21px; height: 21px; opacity: 0.5;" aria-hidden="true">
                        <use xlink:href="#icon-sousuo"></use>
                    </svg>
                </button>
                <div id="word"></div>
            </div>
        </div>