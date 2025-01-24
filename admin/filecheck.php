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
        'mutable' => []
    ];
    if (empty($originalMd5s)) {
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
    '/admin/cache.php',
    '/assets/img/cron.php'

];

$currentMd5s = generateFileMd5s($targetDirectory, $whitelist);
$remoteJson = get_curl('https://cdn.lylme.com/lylme_spage/file_check/v2.0.0/file.json');
if ($remoteJson === false) {
    $remotemsg =  '<div class="alert alert-danger" role="alert">无法获取远程配置，请检查服务器是否支持外网访问</div>';
} else {
    $remotemsg = '<div class="alert alert-success" role="alert">
                            <p><b>当前文件版本：v' . VERSION . '</b>&nbsp;&nbsp;<a href="https://gitee.com/LyLme/lylme_spage" target="_blank" class="alert-link">[查看源代码]</a></p>
                            <p> <small class="help-block">该页面用于检查网站脚本文件是否被篡改器<br>该页面仅供参考，需注意“篡改”和"冗余"文件是否存在恶意代码并从上方链接对比替换</br>排除完成后建议修改后台账号密码和数据库密码</br>该功能需要服务器支持外网访问，仅适用于Linux内核的服务器，Windows服务器暂不支持</small></p>
                        </div>';
    $originalMd5s = json_decode($remoteJson, true);
    $comparisonResult = compareMd5s($originalMd5s, $currentMd5s, $whitelist);
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
                        default:
                            $statusy =  '正常';
                            break;
                    }
                    foreach ($value as $file) {
                        echo '<tr class="filecheck_' . $key . '"><td>' . $file . '</td><td>' . $statusy . '</td></tr>';
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
    tr.filecheck_missing {
        color: orange;
    }

    tr.filecheck_new {
        color: blue;
    }

    tr.filecheck_tampered {
        color: red;
    }

    tr.filecheck_normal {
        color: green;
    }
</style>
<?php
include './footer.php';
?>