    <?php
    // 各读一次，避免 theme_config() 在未保存主题设置时重复 include config.php
    global $templatepath;
    $palette_gonganbei = trim((string) theme_config('gonganbei', ''));
    $palette_background_video = (string) theme_config('background_video', '');
    ?>
    <!-- 底部信息 -->
    <footer class="site-footer">
        <div class="footer-content">
            <?php if ($palette_gonganbei !== '') : ?>
                <span class="footer-item"><?php theme_security_filing('gonganbei', $palette_gonganbei); ?></span>
            <?php endif; ?>
            <?php if (!empty($conf['icp'])) : ?>
                <span class="footer-item"><?php theme_icp(); ?></span>
            <?php endif; ?>
            <span class="footer-item"><?php echo isset($conf['copyright']) ? $conf['copyright'] : ''; ?></span>
        </div>
    </footer>

    <script>
        var background_url = <?php echo json_encode((string) background(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
        var background_video_url = <?php echo json_encode($palette_background_video, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
        var palette_go_enabled = <?php echo json_encode(theme_go_enabled()); ?>;
        var palette_go_url = <?php echo json_encode(rtrim($templatepath, '/') . '/go.php'); ?>;
    </script>
    <?php theme_icon_scripts(); ?>
    <?php echo isset($conf['wztj']) ? $conf['wztj'] : ''; ?>
</body>

</html>
