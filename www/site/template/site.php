<?php
/*
 * @Description:
 * @Author: LyLme admin@lylme.com
 * @Date: 2024-01-23 12:25:35
 * @LastEditors: LyLme admin@lylme.com
 * @LastEditTime: 2024-04-13 22:51:33
 * @FilePath: /lylme_spage/site/template/site.php
 * @Copyright (c) 2024 by LyLme, All Rights Reserved.
 */

?>
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
    <link rel="stylesheet" href="/site/static/font-awesome.min.css">
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
                    <img class="dropbtn more">
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
                <img class="night-mode-btn" src="/site/static/light_mode.svg" alt="切换模式" onclick="toggleNightMode()">
            </div>
        </header>

        <div class="clock-container">
            <div class="clock-face" id="time"></div>
            <div class="clock-details">
                <div class="date-line">
                    <div class="date-part" id="date"></div>
                    <div class="date-part" id="weekday"></div>
                </div>
            </div>
        </div>
        <div class="site-container">

            <div class="site-left-column">
                <div class="site-right-info site-info">
                    <div class="site-right-nav nav_path">
                        <!-- <i class="fa fa-chrome"></i> -->
                        <a class="path_tag" href="/">首页</a>
                        <?php
echo '<i class="fa fa-angle-right pathi"></i> <a class="path_tag">' . $group_name . '</a>';
echo '<i class="fa fa-angle-right pathi"></i> <span class="path_tag">'  . $url_name . '</span>';
?>


                    </div>
                    <div class="site_info_warp">
                        <div class="site-head-info">
                            <div class="site-right-logo"> <?php echo $url_icon; ?></div>
                            <!-- <div class="site-right-title">
                           
                        </div> -->
                            <div class="site-right-title">


                                <h2 class="web_name"><?php echo  $url_name; ?></h2>
                                <p class='url_description'><?php echo  $url_description  ?></p>


                            </div>
                        </div>
                        <p class='xinxi-text-2 url_keywords' id='site_keyword'> <?php echo  $url_keywords; ?></p>
                        <div class="site-right-url">
                            <div class="urls-tools-item">
                                <a href="<?php echo $url_herf; ?>" rel="nofollow" target="_blank" class="urls-tools-btn btn-arrow">立即访问<i class="fa fa-paper-plane"></i></a>
                            </div>
                            <div class="urls-tools-item urls-tools-qr">
                                <a href="javascript:void(0);" class="urls-tools-btn btn-wap">手机查看 <i class="fa fa-qrcode"></i></a>
                                <span id="code"><img width="100" height="100" src="/include/qrcode.php?text=<?php echo $url_herf ?>"></span>
                            </div>
                        </div>
                        <?php
if (!empty($conf['snapshot'])) {
    ?>
                            <div class="site-right-column">
                                <h4>网页快照</h4>
                                <div class="site-left-info site-info view_img">
                                    <img class="lazyload" data-src="<?php echo $conf['snapshot'] . $url_herf ?>" src="/site/static/default-image.webp" style="width: 100%;height:100%;border-radius: 5px;vertical-align: middle;">
                                </div>
                            </div>
                            <script>
                                const viewer = new Viewer(document.querySelector(".view_img"), {
                                    viewed() {},
                                });
                            </script>
                        <?php
} ?>

                    </div>



                </div>
            </div>

        </div>
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