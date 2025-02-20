        <div class="footer-inner">
            <div class="footer-text">
                <?php if (!empty($conf['wztj'])) {
                    echo '<p>' . $conf["wztj"] . '</p>';
                }
                ?>
                <p>本站内容源自互联网，如有内容侵犯了您的权益，请联系删除相关内容。</p>
                <p><?php echo $conf['copyright']; ?> <a href="https://beian.miit.gov.cn/"> <?php echo $conf['icp'] ?> </a> Theme By <a href="https://www.hbd0.cn/thread-1407-1-1.html">quality6.0</a></p>
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