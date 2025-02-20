<?php if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header("Location:/");
} ?>
<script src="<?php echo $cdnpublic ?>/assets/js/bootstrap.min.js" type="application/javascript"></script>
<script src="<?php echo $templatepath; ?>/js/script.js?v=20240414"></script>
<script src="<?php echo $cdnpublic ?>/assets/js/svg.js"></script>
<div style="display:none;" class="back-to" id="toolBackTop">
    <a title="返回顶部" onclick="window.scrollTo(0,0);return false;" href="#top" class="back-top"></a>
</div>
<div class="mt-5 mb-3 footer text-muted text-center">
    <!--备案信息-->
    <?php
    if (!empty(theme_config('gonganbei', ""))) {

        preg_match_all('/\d+/', theme_config('gonganbei'), $gab);

        echo '<a class="icp" href="http://www.beian.gov.cn/portal/registerSystemInfo?recordcode=' . $gab[0][0] . '" target="_blank" rel="nofollow noopener">
    <img src="/assets/img/icp.png" alt="公安网备" width="16" height="16">' . theme_config('gonganbei') . ' </a>';
    }
    ?>
    <?php if ($conf['icp'] != null) {
        echo '<a href="http://beian.miit.gov.cn/" class="icp" target="_blank" _mstmutation="1" _istranslated="1">' . $conf['icp'] . '</a>';
    } ?>
    <!--版权信息-->
    <p> <?php echo $conf['copyright']; ?></p>
    <!--网站统计-->
    <?php if ($conf['wztj'] != null) {
        echo $conf["wztj"];
    } ?>
</div>

</html>