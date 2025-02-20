<?php
/* 
 * @Description: 主题自定义设置
 * @Author: LyLme admin@lylme.com
 * @Date: 2024-04-12 14:30:34
 * @LastEditors: LyLme admin@lylme.com
 * @LastEditTime: 2024-04-14 05:49:36
 * @FilePath: /lylme_spage/admin/theme_setting.php
 * @Copyright (c) 2024 by LyLme, All Rights Reserved. 
 */
$title =  '主题自定义设置';
include './head.php';
?>
<link href="../assets/js/layui/css/layui.css" type="text/css" rel="stylesheet" />
<script src="../assets/js/layui/layui.js" type="application/javascript"></script>
<link rel="stylesheet" href="../assets/admin/css/theme_setting.css">
<main class="lyear-layout-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4><?php echo theme($conf['template'], 'theme_name') . '主题 - 自定义设置'; ?></h4>
                    </div>
                    <div class="card-body">
                        <div class="layui-form layuimini-form">
                            <?php
                            $theme_configs = ROOT . 'template/' . $conf['template'] . '/config.php';
                            if (file_exists($theme_configs) && ($result = @require $theme_configs) !== false && is_array($theme_config)) {

                                try {
                                    $theme_name = "theme_config_" . $conf['template'];
                                    if(isset($conf[$theme_name])){
                                        $form_data = json_decode($conf[$theme_name], true);

                                    }
                                    $form_data = !empty($form_data) ? $form_data : [];
                                    echo Form::getInstance()
                                        ->form_action('ajax_theme.php?set=save')

                                        ->form_method(Form::form_method_post)
                                        ->form_schema($theme_config)
                                        ->input_hidden("theme_path", $conf['template'])
                                        // ->switch('是否启用', '启用主题配置', 'status', true)
                                        ->input_submit('保存', 'class="layui-btn" lay-submit lay-filter="saveBtn"')
                                        // ->input_submit('确认保存', 'class="btn btn-primary m-r-5"')
                                        ->form_data($form_data)
                                        ->create();
                                } catch (Exception $e) {
                                    echo "主题配置载入失败";
                                    //$e->getMessage();
                                }
                            } else {
                                echo "<h3>当前主题未适配主题自定义设置</h3>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script src="/assets/admin/js/theme_setting.js"></script>
    <!--选色器-->
    <link rel="stylesheet" type="text/css" href="/assets/admin/css/coloris.min.css" />
    <script type="text/javascript" src="/assets/admin/js/coloris.min.js"></script>
    <script type="text/javascript">
        Coloris({
            el: '.coloris',
            swatches: ['#000000', '#555555', '#666666', '#264653', '#2a9d8f', '#f4a261', '#e76f51', '#ff0000', '#d62828', '#023e8a', '#0077b6', '#0096c7']
        });
    </script>
</main>
<?php
include './footer.php';
?>