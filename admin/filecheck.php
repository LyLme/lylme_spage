<?php
$title = '文件完整性安全效验';
include './head.php';
$targetDirectory = ROOT;
function generateFileMd5s($directory, $whitelist = [])
{
    $md5s = [];
    if (!is_dir($directory)) {
        return $md5s;
    }
    $directory = rtrim(str_replace('\\', '/', $directory), '/') . '/';
    function traverseDirectory($currentDir, &$md5s, $directory, $whitelist)
    {
        $files = scandir($currentDir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $filePath = $currentDir . '/' . $file;
            $filePath = str_replace('\\', '/', $filePath);
            // 检查文件路径是否在允许范围内
            if (strpos($filePath, $directory) === 0) {
                if (is_file($filePath)) {
                    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                    if ($extension === 'php' || $extension === 'html') {
                        $relativePath = substr($filePath, strlen($directory));
                        if (!in_array($relativePath, $whitelist)) {
                            $md5s[$relativePath] = md5_file($filePath);
                        }
                    }
                } elseif (is_dir($filePath)) {

                    traverseDirectory($filePath, $md5s, $directory, $whitelist);
                }
            }
        }
    }
    traverseDirectory($directory, $md5s, $directory, $whitelist);
    return $md5s;
}
function compareMd5s($originalMd5s, $currentMd5s, $whitelist = [])
{
    $result = [
        'missing' => [],
        'tampered' => [],
        'new' => [],
        'normal' => [],
        'mutable' => [],
        'unknown' => []
    ];

    // 确保参数是数组类型
    if (!is_array($originalMd5s) || !is_array($currentMd5s)) {
        return $result;
    }

    // 官方版本文件为空/不可用，无法比对，本地文件统一标记为“未知”
    if (empty($originalMd5s)) {
        foreach (array_keys($currentMd5s) as $filePath) {
            $filePath = str_replace('\\', '/', $filePath);
            if (in_array($filePath, $whitelist)) {
                $result['mutable'][] = $filePath;
            } else {
                $result['unknown'][] = $filePath;
            }
        }
        return $result;
    }

    $allFilePaths = array_unique(array_merge(array_keys($originalMd5s), array_keys($currentMd5s)));
    foreach ($allFilePaths as $filePath) {
        $filePath = str_replace('\\', '/', $filePath);
        if (in_array($filePath, $whitelist)) {
            $result['mutable'][] = $filePath;
            continue;
        }
        $originalMd5 = isset($originalMd5s[$filePath]) ? $originalMd5s[$filePath] : null;
        $currentMd5 = isset($currentMd5s[$filePath]) ? $currentMd5s[$filePath] : null;
        if ($originalMd5 === null) {
            $result['new'][] = $filePath;
        } elseif ($currentMd5 === null) {
            $result['missing'][] = $filePath;
        } elseif ($originalMd5 !== $currentMd5) {
            $result['tampered'][] = $filePath;
        } else {
            $result['normal'][] = $filePath;
        }
    }
    return $result;
}
$whitelist = [
    '/config.php',
    '/' . ADMIN_PATH . '/cache.php',
    '/assets/img/cron.php'

];
// 若后台已修改系统设置（ADMIN_PATH 或 DEBUG），则不再校验 include/common.php，
// 避免其 MD5 与官方版本不一致被误判为"篡改"
if (isset($system_settings_modified) && $system_settings_modified === true) {
    $whitelist[] = '/include/common.php';
}

$currentMd5s = generateFileMd5s($targetDirectory, $whitelist);
$remoteJsonUrl = 'https://cdn.lylme.com/lylme_spage/file_check/?v=' . VERSION;

$remoteJson = str_replace('/admin/', '/' . ADMIN_PATH . '/', get_curl($remoteJsonUrl));
$comparisonResult = array();
if ($remoteJson === false) {
    // 情况1：无法连接远端（网络异常或服务器不支持外网访问）
    $remotemsg =  '<div class="alert alert-danger" role="alert"><i class="mdi mdi-cloud-alert mdi-alert-icon"></i>无法获取远程配置，请检查服务器是否支持外网访问。以下仅列出本地文件，状态为“未知”</div>';
    // 远端不可用时，仅展示本地文件列表，全部标记为“未知”
    $comparisonResult = compareMd5s(array(), $currentMd5s, $whitelist);
} else {
    $originalMd5s = json_decode($remoteJson, true);

    // 确保originalMd5s是数组
    if (!is_array($originalMd5s) || empty($originalMd5s)) {
        // 情况2：远端有响应，但返回内容无效或版本文件为空，无法进行比对
        $remotemsg = '<div class="alert alert-warning" role="alert"><i class="mdi mdi-cloud-alert mdi-alert-icon"></i>远程版本文件无效或为空，无法进行对比校验。以下仅列出本地文件，状态为“未知”</div>';
        $comparisonResult = compareMd5s(array(), $currentMd5s, $whitelist);
    } else {
        // 情况3：远端版本文件可用，正常进行完整性比对
        $remotemsg = '<div class="alert alert-success" role="alert">
                            <div class="alert-stat">
                              <div><i class="mdi mdi-shield-check mdi-alert-icon"></i><b>当前文件版本：v' . VERSION . '</b></div>
                              <a href="https://gitee.com/LyLme/lylme_spage" target="_blank" class="alert-link">[查看源代码]</a>
                            </div>
                            <span class="alert-help">该页面用于检查网站脚本文件是否被篡改器<br>该页面仅供参考，需注意"篡改"和"冗余"文件是否存在恶意代码并从上方链接对比替换<br>排除完成后建议修改后台账号密码和数据库密码<br>该功能需要服务器支持外网访问</span>
                        </div>';
        $comparisonResult = compareMd5s($originalMd5s, $currentMd5s, $whitelist);
    }
}
?>
<main class="lyear-layout-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <?php echo $remotemsg;
                        if (isset($comparisonResult)): ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>文件</th>
                                            <th>状态</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($comparisonResult as $key => $value) {
                                            switch ($key) {
                                                case 'tampered':
                                                    $statusy =   '篡改';
                                                    break;
                                                case 'new':
                                                    $statusy =   '冗余';
                                                    break;
                                                case 'missing':
                                                    $statusy =  '丢失';
                                                    break;
                                                case 'mutable':
                                                    $statusy =  '可变';
                                                    break;
                                                case 'unknown':
                                                    $statusy =  '未知';
                                                    break;
                                                default:
                                                    $statusy =  '正常';
                                                    break;
                                            }
                                            foreach ($value as $file) {
                                                $filename = str_replace('/' . ADMIN_PATH . '/', '/admin/', $file);
                                        ?>
                                                <tr class="filecheck_<?php echo $key; ?>">
                                                    <td><a rel="noopener noreferrer" href="https://gitee.com/lylme/lylme_spage/blob/master<?php echo $filename; ?>" target="_blank"><?php echo $file; ?></a></td>
                                                    <td><?php echo $statusy; ?></td>
                                                </tr>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<style>
    tr.filecheck_missing td {
        color: orange;
    }

    tr.filecheck_new td {
        color: blue;
    }

    tr.filecheck_tampered td {
        color: red;
    }

    tr.filecheck_normal td {
        color: green;
    }

    tr.filecheck_mutable td {
        color: #6c757d;
    }

    tr.filecheck_unknown td {
        color: #999;
    }
    td a {
        color: #4d5259 !important;
    }
</style>
<?php
include './footer.php';
?>