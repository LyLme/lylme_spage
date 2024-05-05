<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title><?php echo $conf['title'] ?></title>
    <meta name="keywords" content="<?php echo $conf['keywords'] ?>">
    <meta name="description" content="<?php echo $conf['description'] ?>">
    <link rel="icon" href="<?php echo $conf['logo'] ?>" type="image/x-icon">
    <meta name="theme-color" content="#ffffff">
    <meta name="author" content="D.Young">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="full-screen" content="yes"><!--UC强制全屏-->
    <meta name="browsermode" content="application"><!--UC应用模式-->
    <meta name="x5-fullscreen" content="true"><!--QQ强制全屏-->
    <meta name="x5-page-mode" content="app"><!--QQ应用模式-->
    <meta name="lsvn" content="<?php echo base64_encode($conf['version']) ?>">
    <link href="<?php echo $templatepath; ?>/css/style.css?v=20240414" rel="stylesheet">
</head>
<?php if (!empty(background())) {
    echo '<body style="background: url(' . background() . ') no-repeat center/cover;">';
} else {
    echo '<body>';
} ?>
<div class="body ">
    <div id="menu"><i></i></div>
    <div class="list closed">
        <?php
        $rel = $conf["mode"] == 2 ? '' : 'rel="nofollow"';
        $html = array(
            'g1' => '<ul class="mylist row">', //分组开始标签
            'g2' => '<li class="title">{group_icon}<sapn>{group_name}</sapn></li>',  //分组内容
            'g3' => '</ul>',  //分组结束标签

            'l1' => '<li class="col-3 col-sm-3 col-md-3 col-lg-1">',  //链接开始标签
            'l2' => '<a ' . $rel . ' href="{link_url}" target="_blank">{link_icon}<span>{link_name}</span></a>',  //链接内容
            'l3' => '</li>',  //链接结束标签
        );
        lists($html);
        ?>
    </div>
    <div id="content">
        <div class="con">

            <div class="shlogo">
                
                <?php
                echo theme_config('home_title');
                if ($conf['yan'] == 'true') {
                    echo '<p class="yan">' . yan() . '</p>';
                }
                ?>
            </div>
            <div class="sou">
                <div class="lylme">
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

                    <input class="wd soinput" id="search-text" type="text" placeholder="" name="q" x-webkit-speech lang="zh-CN" autocomplete="off">
                    <button onclick="go('');"><svg class="icon" style=" width: 21px; height: 21px; opacity: 0.5;" aria-hidden="true">
                            <use xlink:href="#icon-sousuo"></use>
                        </svg></button>

                    <div id="word"></div>
                </div>

            </div>

            <div class="htmlcode">
         <?php
         if (theme_config('lytoday', 0) == 1) {
			echo theme_config('lytodaycode');
		}?>
            
            </div>
        </div>
        <footer>
            <div class="foot">


                <?php
                $i = 0;
                $tagslists = $site->getTags();
                while ($taglists = $DB->fetch($tagslists)) {
                    echo '<a class="nav-link" href="' . $taglists["tag_link"] . '"';
                    if ($taglists["tag_target"] == 1) {
                        echo ' target="_blank"';
                    }
                    echo '>' . $taglists["tag_name"] . '</a>';
                    if ($i < $DB->num_rows($tagslists) - 1) {
                        $i++;
                        echo ' | ';
                    }
                }
                ?>
                <!--备案信息-->
                <p>
                    <?php
                    if (!empty($conf['icp'])) {
                        echo '<a href="http://beian.miit.gov.cn/"  class="icp nav-link" target="_blank" _mstmutation="1" _istranslated="1">' . $conf['icp'] . '</a>';
                    }
                    if (!empty(theme_config('gonganbei', ""))) {
                        preg_match_all('/\d+/', theme_config('gonganbei'), $gab);
                        echo '<a class="icp" href="http://www.beian.gov.cn/portal/registerSystemInfo?recordcode=' . $gab[0][0] . '" target="_blank" rel="nofollow noopener"><img src="/assets/img/icp.png" alt="公安网备" width="16" height="16">' . theme_config('gonganbei') . ' </a>';
                    }
                    ?>
                </p>
                <!--版权信息-->
                <p><?php echo $conf['copyright']; ?></p>
                <!--自定义footer-->
                <?php if (!empty($conf['wztj'])) {
                    echo  $conf["wztj"];
                }

                ?>

            </div>
        </footer>
    </div>
</div>

</body>
<script src="<?php echo $cdnpublic ?>/assets/js/jquery.min.js"></script>
<script src="<?php echo $cdnpublic ?>/assets/js/svg.js"></script>
<script src="<?php echo $templatepath; ?>/js/sou.js?v=20240414"></script>
<script>
    function solist() {
        return <?php echo $json ?>;
    }
</script>
<!--
作者:D.Young
主页：https://blog.5iux.cn/
github：https://github.com/5iux/sou
日期：2020-11-23
版权所有，请勿删除
-->

</html>