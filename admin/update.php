<?php
$title = '检查更新';
include './head.php';
@unlink('log.txt');
$update = update();
$s = isset($_GET['s']) ? $_GET['s'] : null;
if ($s=="refresh") {
    exit("<script language='javascript'>window.location.href='./';</script>");
}
?>
<!--页面主要内容-->
<main class="lyear-layout-content">
    <div class="container-fluid">
        <?php if (getver($update['version']) > getver($conf['version']) && !empty($update['version'])): ?>
<div class="alert alert-info alert-stat" role="alert"><div><i class="mdi mdi-update mdi-alert-icon"></i>发现新版本：<b><?php echo $update['version']; ?></b>&nbsp;&nbsp;当前版本：<b><?php echo $conf['version']; ?></b></div><a href="https://gitee.com/LyLme/lylme_spage/releases" target="_blank" class="alert-link">查看发行版</a></div>
<div class="card"><div class="card-header"><h4>更新说明</h4></div><ul class="list-group">
<?php echo $update['update_log']; ?>
<li class="list-group-item"><button onclick="update()" class="btn btn-primary">更新</button></li>
        <?php elseif (!empty($update['version'])): ?>
<div class="alert alert-success alert-stat" role="alert"><div><i class="mdi mdi-check-decagram mdi-alert-icon"></i>当前已是最新版本：<b><?php echo $conf['version']; ?></b></div><a href="https://gitee.com/LyLme/lylme_spage/releases" target="_blank" class="alert-link">查看发行版</a></div>
<div class="card"><div class="card-header"><h4>版本更新</h4></div><ul class="list-group">
<li class="list-group-item"><b>当前版本：</b><?php echo $conf['version']; ?></li>
<li class="list-group-item"><b>最新版本：</b><?php echo $update['version']; ?></li>
        <?php else: ?>
<div class="alert alert-danger" role="alert"><i class="mdi mdi-alert-circle mdi-alert-icon"></i>检查更新失败！</div>
<div class="card"><div class="card-header"><h4>版本更新</h4></div><ul class="list-group">
<li class="list-group-item"><b>当前版本：</b><?php echo $conf['version']; ?></li>
<li class="list-group-item"><b>手动更新：</b>请前往<a href="https://gitee.com/LyLme/lylme_spage/releases" target="_blank" class="alert-link">码云</a>下载最新版本<code>lylme_spage_update.zip</code>后缀的更新包上传到网站根目录解压即可，程序会自动更新数据库</li>
        <?php endif; ?>
        </ul>
    </div>
    </div>
</main>
</div>
</div>
<?php
include './footer.php';
?>
<script src="/assets/admin/js/layer.min.js" type="application/javascript"></script>
<script type="text/javascript" src="/assets/admin/js/lightyear.js"></script>
<script src="/assets/admin/js/bootstrap-notify.min.js"></script>
<link href="/assets/admin/js/jquery-confirm.min.css" type="text/css" rel="stylesheet" />
<script src="/assets/admin/js/jquery-confirm.min.js" type="application/javascript"></script>
<script>
    function update() {
        $.confirm({
            title: '更新',
            content: '<p><b>注意：更新会替换大部分文件(不会删除原有文件)</b></p><p>若您修改了源码的内容，为防止丢失请先进行备份</p><p>另外：不建议通过修改源码的方式来修改内容，本程序预留了自定义JS的功能，建议通过JS的方式来修改内容，欢迎加群讨论获取',
            type: 'orange',
            buttons: {
                omg: {
                    text: '更新',
                    btnClass: 'btn-orange',
                    action: function() {
                        lightyear.loading('show');
                        $.ajax({
                            url: "ajax_link.php?submit=update",
                            method: "POST",
                            data: {
                                file: "<?php echo $update['file'] ?>"
                            },
                            dataType: "json",
                            success: function(data) {
                                if (data.code == 200) {
                                    lightyear.loading('hide');
                                    lightyear.notify(data.msg, 'success', 1000);
                                    window.location.replace("./");
                                    return true;
                                } else {
                                    lightyear.loading('hide');
                                    lightyear.notify(data.msg, 'danger', 2000);
                                    return false;
                                }
                            },
                            error: function(data) {
                                layer.msg('服务器错误');
                                lightyear.loading('hide');
                                return false;
                            }
                        });
                    }
                },
                close: {
                    text: '取消',
                }
            }
        });
    }
</script>