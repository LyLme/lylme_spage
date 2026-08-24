<?php
$title = '检查更新';
include './head.php';
// @unlink('log.txt');
$s = isset($_GET['s']) ? $_GET['s'] : null;
if ($s=="refresh") {
    exit("<script language='javascript'>window.location.href='./';</script>");
}
?>
<!--页面主要内容-->
<main class="lyear-layout-content">
    <div class="container-fluid">
        <div id="update-loading" class="text-center" style="padding:60px 0;">
            <i class="mdi mdi-loading mdi-spin" style="font-size:40px;color:#4a6cf7"></i>
            <p style="margin-top:16px;font-size:15px;color:#666;">正在检查更新，请稍候...</p>
        </div>
        <div id="update-content" style="display:none;"></div>
    </div>
</main>
</div>
</div>
<?php
include './footer.php';
?>
<script src="/assets/admin/js/layer.min.js" type="application/javascript"></script>
<script>
// 页面加载后立即异步检查更新
$(function() {
    $.ajax({
        url: "ajax_link.php?submit=check_update",
        method: "GET",
        dataType: "json",
        timeout: 30000,
        success: function(data) {
            $('#update-loading').hide();
            if (data.code == 200 && data.version) {
                renderUpdateResult(data);
            } else {
                renderError();
            }
        },
        error: function() {
            $('#update-loading').hide();
            renderError();
        }
    });
});

// 版本号比较
function getver(ver) {
    return ver.replace(/[^\d.]/g, '').split('.').map(function(n) { return parseInt(n) || 0; }).join('.');
}

function compareVer(v1, v2) {
    var a1 = v1.split('.').map(Number);
    var a2 = v2.split('.').map(Number);
    for (var i = 0; i < Math.max(a1.length, a2.length); i++) {
        var n1 = a1[i] || 0, n2 = a2[i] || 0;
        if (n1 > n2) return 1;
        if (n1 < n2) return -1;
    }
    return 0;
}

// 渲染更新结果
function renderUpdateResult(data) {
    var html = '';
    if (compareVer(getver(data.version), getver(data.current_version)) > 0) {
        // 发现新版本
        html += '<div class="alert alert-info alert-stat" role="alert"><div><i class="mdi mdi-update mdi-alert-icon"></i>发现新版本：<b>' + data.version + '</b>&nbsp;&nbsp;当前版本：<b>' + data.current_version + '</b></div><a href="https://gitee.com/LyLme/lylme_spage/releases" target="_blank" class="alert-link">查看发行版</a></div>';
        html += '<div class="card"><div class="card-header"><h4>更新说明</h4></div><ul class="list-group">';
        html += data.update_log;
        html += '<li class="list-group-item"><button onclick="doUpdate(\'' + data.file + '\')" class="btn btn-primary">更新</button></li>';
    } else {
        // 已是最新版本
        html += '<div class="alert alert-success alert-stat" role="alert"><div><i class="mdi mdi-check-decagram mdi-alert-icon"></i>当前已是最新版本：<b>' + data.current_version + '</b></div><a href="https://gitee.com/LyLme/lylme_spage/releases" target="_blank" class="alert-link">查看发行版</a></div>';
        html += '<div class="card"><div class="card-header"><h4>版本更新</h4></div><ul class="list-group">';
        html += '<li class="list-group-item"><b>当前版本：</b>' + data.current_version + '</li>';
        html += '<li class="list-group-item"><b>最新版本：</b>' + data.version + '</li>';
    }
    html += '</ul></div>';
    $('#update-content').html(html).show();
}

// 渲染错误状态
function renderError() {
    var currentVersion = '<?php echo isset($conf["version"]) ? $conf["version"] : ""; ?>';
    var html = '<div class="alert alert-danger" role="alert"><i class="mdi mdi-alert-circle mdi-alert-icon"></i>检查更新失败！</div>';
    html += '<div class="card"><div class="card-header"><h4>版本更新</h4></div><ul class="list-group">';
    html += '<li class="list-group-item"><b>当前版本：</b>' + currentVersion + '</li>';
    html += '<li class="list-group-item"><b>手动更新：</b>请前往<a href="https://gitee.com/LyLme/lylme_spage/releases" target="_blank" class="alert-link">码云</a>下载最新版本<code>lylme_spage_update.zip</code>后缀的更新包上传到网站根目录解压即可，程序会自动更新数据库</li>';
    html += '</ul></div>';
    $('#update-content').html(html).show();
}

// 执行更新
function doUpdate(file) {
    $.confirm({
        title: '更新',
        content: '<p><b>注意：更新会替换大部分文件(不会删除原有文件)</b></p><p>若您修改了源码的内容，为防止丢失请先进行备份</p><p>另外：不建议通过修改源码的方式来修改内容，本程序预留了自定义JS的功能，建议通过JS的方式来修改内容，欢迎加群讨论获取</p>',
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
                            file: file
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
                                lightyear.notify(data.msg, 'danger', 3000);
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
