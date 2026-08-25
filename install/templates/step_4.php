<?php if (!defined('IN_INSTALL')) {
    exit('Request Error!');
} ?>
<?php
// 读取安装时自定义的管理员账号密码（保存在 session 中，由 index.php 写入，未自定义时使用默认值）
$install_admin_user = (isset($_SESSION['install_admin_user']) && $_SESSION['install_admin_user'] !== '') ? $_SESSION['install_admin_user'] : 'admin';
$install_admin_pwd = (isset($_SESSION['install_admin_pwd']) && $_SESSION['install_admin_pwd'] !== '') ? $_SESSION['install_admin_pwd'] : '123456';
?>


<?php



// 站点地址（兼容非 Web 环境下的 $_SERVER 键缺失）
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
$web_url = ($isHttps ? 'https://' : 'http://') . $host;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>六零导航页安装向导 - 安装成功</title>
    <link href="templates/style/install.css" type="text/css" rel="stylesheet" />
    <script type="text/javascript" src="templates/js/jquery.min.js"></script>
</head>

<body>
    <div class="header"></div>
    <div class="mainBody">
        <div class="note">
            <div class="complete"><strong>现在您可以：</strong><br />
                <div class="installed">
                    <a href="../" target="_blank" >访问首页</a><span>或</span><a class="admin" target="_blank" href="../admin">登录后台</a>
                </div>

            </div>
            <table class="install-table">
                <caption>六零导航页安装信息</caption>
                <tr>
                    <th>前台地址</th>
                    <td><a href="../"><?php echo $web_url; ?></a></td>
                </tr>
                <tr>
                    <th>后台地址</th>
                    <td><a href="../admin"><?php echo $web_url; ?>/admin</a></td>
                </tr>
                <tr>
                    <th>后台账号</th>
                    <td><?php echo $install_admin_user; ?></td>
                </tr>
                <tr>
                    <th>后台密码</th>
                    <td><?php echo $install_admin_pwd; ?></td>
                </tr>
            </table>


            <p>您可以访问 <a href="https://doc.lylme.com/spage/#/readme" target="_blank" class="link">帮助文档</a> 获取更多帮助</p>
        </div>
    </div>
    <div class="footer"><span class="step4"></span> <span class="copyright"><?php echo $cfg_copyright; ?></span></div>
</body>

</html>