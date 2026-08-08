<?php
$title = '未使用文件清理';
include './head.php';

$base_dir = ROOT . 'files';
$scan_dirs = array('download', 'upload');

// 删除处理
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_POST['files'])) {
    $files = (array) $_POST['files'];
    $del = 0;
    $fail = 0;
    foreach ($files as $rel) {
        $rel = trim((string) $rel);
        $rel = str_replace('\\', '/', $rel);
        // 安全校验：仅允许 download/ 或 upload/ 目录下的文件，防止路径穿越
       if (!preg_match('#^(download|upload)/[^/]+$#i', $rel) || preg_match('#(^|/)\.\.(/|$)#', $rel)) {
            $fail++;
            continue;
        }
        $ext = strtolower(pathinfo($rel, PATHINFO_EXTENSION));
        $full = $base_dir . '/' . $rel;
        if (is_file($full) && @unlink($full)) {
            $del++;
        } else {
            $fail++;
        }
    }
    $msg = '删除成功 ' . $del . ' 个文件' . ($fail ? '，失败 ' . $fail . ' 个' : '');
    echo '<script>alert("' . $msg . '");window.location.href="cleanimg.php";</script>';
    exit;
}

// 收集数据库中所有被引用的图标字段值
$icon_values = array();
$res = $DB->query("SELECT `group_icon` FROM `lylme_groups` WHERE `group_icon` IS NOT NULL AND `group_icon` != ''");
if ($res) {
    while ($row = $DB->fetch($res)) {
        if (isset($row['group_icon']) && $row['group_icon'] != '') {
            $icon_values[] = $row['group_icon'];
        }
    }
}
$res = $DB->query("SELECT `icon` FROM `lylme_links` WHERE `icon` IS NOT NULL AND `icon` != ''");
if ($res) {
    while ($row = $DB->fetch($res)) {
        if (isset($row['icon']) && $row['icon'] != '') {
            $icon_values[] = $row['icon'];
        }
    }
}

// 判断文件名是否被数据库引用（按文件名匹配，兼容子目录部署/完整URL）
function is_icon_used($filename, $icon_values)
{
    foreach ($icon_values as $v) {
        if (stripos($v, $filename) !== false) {
            return true;
        }
    }
    return false;
}

function fmt_size($bytes)
{
    if ($bytes >= 1048576) {
        return round($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return round($bytes / 1024, 2) . ' KB';
    }
    return $bytes . ' B';
}

// 分别扫描各目录，区分已使用/未使用
$dirs = array();
foreach ($scan_dirs as $d) {
    $dirs[$d] = array('total' => 0, 'used' => 0, 'unused' => 0, 'size' => 0, 'files' => array());
    $dir = $base_dir . '/' . $d;
    if (!is_dir($dir)) {
        continue;
    }
    $items = @scandir($dir);
    if ($items === false) {
        continue;
    }
    foreach ($items as $f) {
        if ($f == '.' || $f == '..') {
            continue;
        }
        $full = $dir . '/' . $f;
        if (!is_file($full)) {
            continue;
        }
        $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
        $dirs[$d]['total']++;
        $size = filesize($full);
        $rel = $d . '/' . $f;
        $is_img = in_array($ext, array('png', 'jpg', 'jpeg', 'gif', 'ico', 'webp'));
        $item = array('rel' => $rel, 'name' => $f, 'dir' => $d, 'size' => $size, 'url' => '/files/' . $rel, 'ext' => $ext, 'is_img' => $is_img);
        if (is_icon_used($f, $icon_values)) {
            $dirs[$d]['used']++;
        } else {
            $dirs[$d]['unused']++;
            $dirs[$d]['size'] += $size;
            $dirs[$d]['files'][] = $item;
        }
    }
}

// 汇总统计
$total_count = $dirs['download']['total'] + $dirs['upload']['total'];
$used_count = $dirs['download']['used'] + $dirs['upload']['used'];
$unused_count = $dirs['download']['unused'] + $dirs['upload']['unused'];
$total_size = $dirs['download']['size'] + $dirs['upload']['size'];

// 输出首页同款统计卡片
function stat_card($color, $icon, $label, $value)
{
    echo '<div class="col-sm-6 col-lg-3">';
    echo '<div class="card ' . $color . '">';
    echo '<div class="card-body clearfix">';
    echo '<div class="pull-right">';
    echo '<p class="h6 text-white m-t-0">' . $label . '</p>';
    echo '<p class="h3 text-white m-b-0 fa-1-5x">' . $value . '</p>';
    echo '</div>';
    echo '<div class="pull-left"><span class="img-avatar img-avatar-48 bg-translucent"><i class="mdi ' . $icon . ' fa-1-5x"></i></span></div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}
?>
<main class="lyear-layout-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4>未使用图标文件清理</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php
                            stat_card('bg-primary', 'mdi-file', '文件总数', $total_count);
                            stat_card('bg-success', 'mdi-check-circle', '已使用', $used_count);
                            stat_card('bg-warning', 'mdi-alert-circle', '未使用', $unused_count);
                            stat_card('bg-danger', 'mdi-database', '未使用占用空间', fmt_size($total_size));
                            ?>
                        </div>
                        <p>扫描 <code>files/download</code> 与 <code>files/upload</code> 目录中的全部文件，并与数据库中 <code>lylme_groups</code> 表的 分组图标</code>、和 <code>lylme_links</code> 表的 链接图标</code> 进行比对，找出未被使用的文件。删除后不可恢复，请谨慎操作！</p>
                    </div>


                    <div class="card-body">
                        <ul class="nav nav-tabs" id="dirTab" role="tablist">
                            <li class="active">
                                <a href="#tab-download" data-toggle="tab">抓取的图标(download )
                                    <?php if ($dirs['download']['unused'] > 0) { ?><span class="badge badge-danger"><?php echo $dirs['download']['unused']; ?></span><?php } ?>
                                </a>
                            </li>
                            <li>
                                <a href="#tab-upload" data-toggle="tab">上传的图标(upload)
                                    <?php if ($dirs['upload']['unused'] > 0) { ?><span class="badge badge-danger"><?php echo $dirs['upload']['unused']; ?></span><?php } ?>
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content p-t-15">
                            <?php foreach ($scan_dirs as $d) { ?>
                                <div class="tab-pane fade<?php echo $d == 'download' ? ' in active' : ''; ?>" id="tab-<?php echo $d; ?>">
                                    <?php if ($dirs[$d]['unused'] > 0) { ?>
                                        <form method="post" action="cleanimg.php?action=delete" onsubmit="return confirm('确定删除选中的文件吗？删除后不可恢复！');" id="delForm-<?php echo $d; ?>">
                                            <div class="m-b-10">

                                                <button type="button" class="btn btn-danger" onclick="delAll('<?php echo $d; ?>')">删除<?php echo $d; ?>全部未使用</button>
                                                <button type="submit" class="btn btn-warning">仅删除选中</button>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th width="40"><input type="checkbox" id="checkAll-<?php echo $d; ?>"></th>
                                                            <th width="70">预览</th>
                                                            <th>文件</th>
                                                            <th width="90">类型</th>
                                                            <th width="100">大小</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($dirs[$d]['files'] as $item) { ?>
                                                            <tr>
                                                                <td><input type="checkbox" name="files[]" value="<?php echo htmlspecialchars($item['rel']); ?>" class="file-check-<?php echo $d; ?>"></td>
                                                                <td>
                                                                    <?php if ($item['is_img']) { ?>
                                                                        <img src="<?php echo htmlspecialchars($item['url']); ?>" style="width:40px;height:40px;object-fit:cover;" onerror="this.style.visibility='hidden'">
                                                                    <?php } else { ?>
                                                                        <i class="mdi mdi-file" style="font-size:28px;color:#999;line-height:40px;"></i>
                                                                    <?php } ?>
                                                                </td>
                                                                <td><code><?php echo htmlspecialchars($item['rel']); ?></code></td>
                                                                <td><?php echo htmlspecialchars($item['ext'] ?: '未知'); ?></td>
                                                                <td><?php echo fmt_size($item['size']); ?></td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </form>
                                    <?php } else { ?>
                                        <div class="alert alert-success"><?php echo $d; ?> 目录未发现未使用的文件，所有文件均被数据库引用。</div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<script>
    (function() {
        var dirs = ['download', 'upload'];
        for (var i = 0; i < dirs.length; i++) {
            (function(dir) {
                var checkAll = document.getElementById('checkAll-' + dir);
                if (!checkAll) return;
                checkAll.addEventListener('change', function() {
                    var checks = document.querySelectorAll('.file-check-' + dir);
                    for (var j = 0; j < checks.length; j++) {
                        checks[j].checked = this.checked;
                    }
                });
            })(dirs[i]);
        }
    })();

    function delAll(dir) {
        var checks = document.querySelectorAll('.file-check-' + dir);
        if (!confirm('确定删除 ' + dir + ' 目录全部未使用文件吗？共 ' + checks.length + ' 个，删除后不可恢复！')) return;
        for (var i = 0; i < checks.length; i++) {
            checks[i].checked = true;
        }
        document.getElementById('delForm-' + dir).submit();
    }
</script>
<?php include './footer.php'; ?>