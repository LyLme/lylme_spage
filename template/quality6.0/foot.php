        <div class="footer-inner">
            <div class="footer-text">

             <!--备案信息-->
    <?php
    if (!empty(theme_config('gonganbei', ""))) {

        preg_match_all('/\d+/', theme_config('gonganbei'), $gab);

        echo '<p><a class="gab" href="http://www.beian.gov.cn/portal/registerSystemInfo?recordcode=' . $gab[0][0] . '" target="_blank" rel="nofollow noopener">
    <img src="/assets/img/icp.png" alt="公安网备" width="16" height="16">' . theme_config('gonganbei') . ' </a></p>';
    }
    ?>
    <?php if ($conf['icp'] != null) {
        echo '<p><a href="http://beian.miit.gov.cn/" class="icp" target="_blank" _mstmutation="1" _istranslated="1">' . $conf['icp'] . '</a></p>';
    } ?>
    <!--版权信息-->
    <p> <?php echo $conf['copyright']; ?></p>
    <!--网站统计-->
    <?php if ($conf['wztj'] != null) {
        echo $conf["wztj"];
    } ?>


          </div>
        </div>
        <script>
            var backgroundimg = "url(<?php echo background(); ?>)";

            var background_video = "<?php echo theme_config('background_video'); ?>";
        </script>
        <script src="<?php echo $cdnpublic ?>/assets/js/jquery.min.js"></script>
        <script src="<?php echo $templatepath; ?>/js/script.js"></script>
        <script src="<?php echo $templatepath; ?>/js/tool.js"></script>
        <script>
            function solist() {
                return <?php echo $json ?>;
            }
        </script>
        </div>
        </body>

        </html>