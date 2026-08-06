<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title><?php echo $conf['title']; ?></title>
    <meta name="keywords" content="<?php echo $conf['keywords']; ?>">
    <meta name="description" content="<?php echo $conf['description']; ?>">
    <meta name="author" content="LyLme">
    <link rel="icon" href="<?php echo $conf['logo']; ?>" type="image/x-icon">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="full-screen" content="yes">
    <meta name="browsermode" content="application">
    <meta name="x5-fullscreen" content="true">
    <meta name="x5-page-mode" content="app">
    <meta name="lsvn" content="<?php echo isset($conf['version']) ? base64_encode($conf['version']) : ''; ?>">
    <link href="<?php echo $cdnpublic; ?>/assets/css/bootstrap.min.css" type="text/css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $templatepath; ?>/css/style.css?v=20250809" type="text/css">
</head>

<body>
    <?php
    if (!function_exists('ltabRenderIcon')) {
        function ltabRenderIcon($icon, $alt = '')
        {
            if (empty($icon)) {
                return '<img src="/assets/img/default-icon.png" alt="' . htmlspecialchars(strip_tags($alt), ENT_QUOTES, 'UTF-8') . '" loading="lazy">';
            }
            $icon = htmlspecialchars_decode($icon);
            $trimmed = trim($icon);
            if (preg_match('/^<svg/i', $trimmed)) {
                return $icon;
            }
            if (!preg_match('/</', $trimmed)) {
                return '<img src="' . htmlspecialchars($trimmed, ENT_QUOTES, 'UTF-8') . '" alt="' . htmlspecialchars(strip_tags($alt), ENT_QUOTES, 'UTF-8') . '" loading="lazy">';
            }
            return $icon;
        }
    }
    ?>

    <div class="ltab-bg bg-fixed">
        <?php
        echo '<img src="' . background() . '" alt="bg">';

        ?>
        <div class="bg-overlay"></div>
    </div>

    <div class="ltab-wrap">
        <!-- 左侧导航 -->
        <aside class="ltab-sidebar">
            <div class="sidebar-inner">

                <nav class="sidebar-nav">
                    <a href="#home" class="nav-item active" data-target="home">
                        <svg viewBox="0 0 1024 1024" width="20" height="20">
                            <path d="M946.5 505L534.6 93.1a31.93 31.93 0 0 0-45.2 0L77.5 505c-12 12-18.8 28.3-18.8 45.3 0 35.3 28.7 64 64 64h43.4V848c0 17.7 14.3 32 32 32h168c17.7 0 32-14.3 32-32V640h152v208c0 17.7 14.3 32 32 32h168c17.7 0 32-14.3 32-32V614.4h43.4c35.3 0 64-28.7 64-64 0-17-6.8-33.3-18.8-45.3z" />
                        </svg>
                        <span>首页</span>
                    </a>

                    

            <?php
                    $groups = $site->getGroups();
                    while ($group = $DB->fetch($groups)) {
                        $gid = 'group_' . $group['group_id'];
                        $gname = $group['group_name'];
                        $gicon = isset($group['group_icon']) ? $group['group_icon'] : '';
                        echo '<a href="#' . $gid . '" class="nav-item" data-target="' . $gid . '">';
                        if ($gicon) echo '<span class="nav-icon">' . ltabRenderIcon($gicon, $gname) . '</span>';
                        else echo '<svg viewBox="0 0 1024 1024" width="20" height="20"><path d="M832 64H192c-35.3 0-64 28.7-64 64v768c0 35.3 28.7 64 64 64h640c35.3 0 64-28.7 64-64V128c0-35.3-28.7-64-64-64zM640 640H384V384h256v256z"/></svg>';
                        echo '<span>' . htmlspecialchars($gname) . '</span></a>';
                    }
                    ?>

<hr class="navhr">

	<?php
				$tagslists = $site->getTags();
				while ($taglists = $DB->fetch($tagslists)) {
			

                    echo '  <a href="' . $taglists["tag_link"] . '" class="nav-item "';
					if ($taglists["tag_target"] == 1) {
						echo ' target="_blank"';
					}echo ' ><span class="nav-icon">
                    <svg t="1653451571762" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="5447" width="200" height="200"><path d="M333.824 986.112H178.176c-78.848 0-143.36-64.512-143.36-143.36V175.616c0-78.848 64.512-143.36 143.36-143.36h667.136c78.848 0 143.36 64.512 143.36 143.36v155.136c0 22.528-18.432 40.96-40.96 40.96s-40.96-18.432-40.96-40.96V175.616c0-33.792-27.648-61.44-61.44-61.44H178.176c-33.792 0-61.44 27.648-61.44 61.44v667.136c0 33.792 27.648 61.44 61.44 61.44h155.136c22.528 0 40.96 18.432 40.96 40.96s-17.92 40.96-40.448 40.96zM494.08 976.384c-10.24 0-20.992-4.096-29.184-11.776-15.872-15.872-15.872-41.984 0-57.856L916.48 454.656c15.872-15.872 41.984-15.872 57.856 0 15.872 15.872 15.872 41.984 0 57.856l-451.584 451.584c-7.68 8.192-18.432 12.288-28.672 12.288z" fill="#1e8bf6" p-id="5448"></path><path d="M782.336 340.992H241.664c-22.528 0-40.96-18.432-40.96-40.96s18.432-40.96 40.96-40.96h541.184c22.528 0 40.96 18.432 40.96 40.96s-18.432 40.96-41.472 40.96zM557.056 619.008H241.664c-22.528 0-40.96-18.432-40.96-40.96s18.432-40.96 40.96-40.96h315.904c22.528 0 40.96 18.432 40.96 40.96s-18.432 40.96-41.472 40.96z" fill="#1e8bf6" p-id="5449"></path></svg>
                 </span> <span>' . $taglists["tag_name"] . '</span>
                    </a>	    ';
				}
				
                    ?>

                </nav>
            </div>
        </aside>

        <!-- 主内容 -->
        <main class="ltab-main">
            <!-- 顶部时钟与搜索 -->
            <div class="ltab-header" id="home">

                <div class="ltab-clock">
                    <div class="clock-time" id="clock-time">--:--</div>
                    <div class="clock-date" id="clock-date">--月--日 星期-</div>
                </div>


                <div class="ltab-search">
                    <div class="search-box">
                        <div class="search-engine" id="search-engine" onclick="toggleEngine()">
                            <span id="engine-icon">
                                <svg viewBox="0 0 1024 1024" width="18" height="18">
                                    <path d="M512 64C264.6 64 64 264.6 64 512s200.6 448 448 448 448-200.6 448-448S759.4 64 512 64zm0 820c-205.4 0-372-166.6-372-372s166.6-372 372-372 372 166.6 372 372-166.6 372-372 372zm159.6-585.8l-59.3-59.3-204.2 204.2-89.1-89.1L271 490.9l148.3 148.4 252.3-252.2z" />
                                </svg>
                            </span>
                            <svg class="engine-arrow" viewBox="0 0 1024 1024" width="12" height="12">
                                <path d="M884 256h-75c-5.1 0-9.9 2.5-12.9 6.6L512 654.2 227.9 262.6c-3-4.1-7.8-6.6-12.9-6.6h-75c-6.5 0-10.3 7.4-6.5 12.7l352.6 486.1c12.8 17.6 39 17.6 51.7 0l352.6-486.1c3.9-5.3.1-12.7-6.4-12.7z" />
                            </svg>
                        </div>
                        <div class="search-engine-dropdown" id="engine-dropdown" style="display:none;">
                            <?php
                            $soulists = $site->getSou();
                            while ($soulist = $DB->fetch($soulists)) {
                                if ($soulist["sou_st"] == 1) {
                                    $souLink = (checkmobile() && !empty($soulist["sou_waplink"])) ? $soulist["sou_waplink"] : $soulist["sou_link"];
                                    echo '<div class="engine-item" data-id="' . $soulist["sou_id"] . '" data-link="' . $souLink . '" data-placeholder="' . $soulist["sou_hint"] . '" onclick="selectEngine(this)">';
                                    echo $soulist["sou_icon"] . ' <span>' . $soulist["sou_name"] . '</span>';
                                    echo '</div>';
                                }
                            }
                            ?>
                        </div>
                        <form action="" method="get" target="_blank" id="search-form" onsubmit="return doSearch();">
                            <input type="text" id="search-input" placeholder="输入搜索内容" autocomplete="off">
                            <button type="submit" class="search-btn">
                                <svg viewBox="0 0 1024 1024" width="18" height="18">
                                    <path d="M909.6 854.5L649.5 594.4C690.9 542 714 475.8 714 405.1 714 217.7 562.3 66 375 66S36 217.7 36 405.1 187.7 744.1 375 744.1c70.7 0 136.9-23.1 189.3-64.4l260.1 260.1c9.2 9.2 24.1 9.2 33.3 0l28.3-28.3c9.2-9.2 9.2-24.1-.1-33.3zM375 666c-143.9 0-261-117.1-261-261s117.1-261 261-261 261 117.1 261 261-117.1 261-261 261z" />
                                </svg>
                            </button>
                        </form>
                        <ul id="search-suggest" style="display:none;"></ul>
                    </div>
                </div>

                <?php if ($conf['yan'] == 'true') { ?>
                    <p class="ltab-yan"><?php echo yan(); ?></p>
                <?php } ?>
            </div>
            <?php
            if (theme_config('lytoday', 0) == 1) {
                echo theme_config('lytodaycode');
            } ?>
            <!-- 链接列表 -->
            <div class="ltab-content">
                <?php include "list.php"; ?>
            </div>
            <?php
            if (theme_config('lytoday', 0) == 2) {
                echo theme_config('lytodaycode');
            } ?>
            <!-- 底部 -->
            <footer class="ltab-footer">
                <p><?php echo $conf['copyright']; ?></p>
                <?php

                if (!empty($conf['icp'])) {
                    echo '<p class="icp"><a href="https://beian.miit.gov.cn/" target="_blank">' . $conf['icp'] . '</a></p>';
                }
                ?>
                <?php if ($conf['wztj'] != null) {
                    echo $conf["wztj"];
                } ?>
            </footer>
        </main>
    </div>

    <script src="<?php echo $cdnpublic ?>/assets/js/svg.js"></script>
    <script src="<?php echo $cdnpublic; ?>/assets/js/jquery.min.js"></script>
    <script src="<?php echo $cdnpublic; ?>/assets/js/bootstrap.min.js"></script>
    <script src="<?php echo $templatepath; ?>/js/main.js?v=20250809"></script>

</body>

</html>