<?php if (!defined('IN_INSTALL')) {
    exit('Request Error!');
} ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>六零导航页安装向导 - 检测安装环境</title>
    <link href="templates/style/install.css" type="text/css" rel="stylesheet"/>
    <script type="text/javascript" src="templates/js/jquery.min.js"></script>
</head>
<body>
<div class="header"></div>
<div class="mainBody">
    <div class="forms">
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr align="left" class="head">
                <td width="30%" height="36">项目
                </th>
                <td width="30%">所需配置
                </th>
                <td width="15%">推荐配置
                </th>
                <td width="25%" align="right">当前服务器
                </th>
            </tr>
            <tr>
                <td height="26" class="firstCol">操作系统</td>
                <td>不限制</td>
                <td>Linux</td>
                <td class="endCol"><?php echo PHP_OS; ?></td>
            </tr>
            <tr>
                <td height="26" class="firstCol">PHP 版本</td>
                <td>&ge;7.1 && &lt;8.0</td>
                <td>7.4</td>
                <td class="endCol"><?php echo PHP_VERSION; ?></td>
            </tr>
            <tr>
                <td height="26" class="firstCol">附件上传</td>
                <td>2M</td>
                <td>2M</td>
                <td class="endCol"><?php echo get_cfg_var("upload_max_filesize") ? get_cfg_var("upload_max_filesize") : '不允许上传附件'; ?></td>
            </tr>
            <tr>
                <td height="26" class="firstCol">GD 库</td>
                <td>2.0</td>
                <td>2.1</td>
                <td class="endCol"><?php
                    $tmp = function_exists('gd_info') ? gd_info() : array();
@$env_items[$key]['current'] = empty($tmp['GD Version']) ? 'noext' : $tmp['GD Version'];
echo @$env_items[$key]['current'];
unset($tmp);
?></td>
            </tr>
            <tr>
                <td height="26" class="firstCol">磁盘空间</td>
                <td>10M</td>
                <td>不限制</td>
                <td class="endCol">
                    <?php
if (function_exists('disk_free_space')) {
    @$env_items[$key]['current'] = floor(disk_free_space('../') / (1024 * 1024)) . 'M';
} else {
    $env_items[$key]['current'] = 'unknow';
}
echo @$env_items[$key]['current'];
?>
                </td>
            </tr>
        </table>
        <div class="hr_10"></div>
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr align="left" class="head">
                <td width="60%" height="36">扩展要求
                </th>
                <td width="25%">检查结果
                </th>
                <td width="15%" align="right">建议
                </th>
            </tr>
            <?php foreach ($extendArray as $item): ?>
                <tr>
                    <td height="26" class="firstCol"><?= $item['name'] ?></td>
                    <td><?= $item['status'] ? '<font color="green">支持</font>' : '<font color="red">不支持</font>' ?></td>
                    <td class="endCol">
                        <span class="<?= $item['status'] ? '' : 'col-red' ?>"><?= $item['status'] ? '无' : '需安装' ?></span>
                    </td>
                </tr>
            <?php endforeach ?>
        </table>
        <div class="hr_10"></div>
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr align="left" class="head">
                <td width="60%" height="36">函数名称
                </th>
                <td width="25%">检查结果
                </th>
                <td width="15%" align="right">建议
                </th>
            </tr>
            <?php foreach ($exists_array as $v): ?>
                <tr>
                    <td height="26" class="firstCol"><?php echo $v; ?>()</td>
                    <td><?= isFunExists($v) ? '<font color="green">支持</font>' : '<font color="red">不支持</font>' ?></td>
                    <td class="endCol"><?= isFunExistsTxt($v) ?></td>
                </tr>
            <?php endforeach ?>
        </table>
        <div class="hr_10"></div>
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr align="left" class="head">
                <td width="60%" height="36">文件权限检测
                </th>
                <td width="25%">所需状态
                </th>
                <td width="15%" align="right">当前状态
                </th>
            </tr>
            <?php
            foreach ($iswrite_array as $v) {
                ?>
                <tr align="left">
                    <td height="26" class="firstCol"><?php echo $v; ?></td>
                    <td>可写</td>
                    <td class="endCol"><?php isWrite($v); ?></td>
                </tr>
                <?php
            }
?>
        </table>
    </div>
</div>
<div class="footer">
    <span class="step2"></span>
    <span class="copyright"><?= $cfg_copyright; ?></span>
    <span class="formSubBtn">
        <form class="j-form" method="post" action="index.php">
            <a href="javascript:void(0);" onclick="history.go(-1);return false;" class="back">返 回</a>
            <a href="javascript:void(0);" class="j-submit submit">下一步</a>
            <input type="hidden" name="s" id="s" value="2">
        </form>
	</span>
</div>
<script>
    $(function () {
        // 环境检测是否通过
        var isPass = <?= $GLOBALS['isNext'] ? 'true' : 'false' ?>;
        console.log(isPass)
        // 表单提交
        $('.j-submit').click(function () {
            if (isPass) {
                $('.j-form').submit();
            } else {
                alert('环境检测不通过，请先修复');
            }
        })
    })
</script>
</body>
</html>