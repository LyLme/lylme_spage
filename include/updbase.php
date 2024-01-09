<?php

if(empty(constant("VERSION"))) {
    return 0;
}
function get_vernum($version)
{
    $vn = explode('.', str_replace('v', '', $version));
    $vernum =  $vn[0] . sprintf("%02d", $vn[1]) . sprintf("%02d", $vn[2]);
    return $vernum;
}
$sqlvn = get_vernum($conf['version']);  //数据库版本
$filevn = get_vernum(constant("VERSION"));  // 文件版本
if($sqlvn < $filevn) {
    //文件版本大于数据库版本执行更新
    $sql = '';
    if($sqlvn < 10101) {
        $version = 'v1.1.1';
        $sql = $sql . file_get_contents(ROOT . 'install/data/update.sql');
    }
    if($sqlvn < 10103) {
        $version = 'v1.1.3';
        @unlink(ROOT . 'include/head.php');
        @unlink(ROOT . 'include/home.php');
        @unlink(ROOT . 'include/apply.php');
        @unlink(ROOT . 'include/footer.php');
        $sql = $sql . file_get_contents(ROOT . 'install/data/update1.sql');
    }
    if($sqlvn < 10104) {
        $version = 'v1.1.4';
    }
    if($sqlvn < 10105) {
        $version = 'v1.1.5';
        $sql = $sql . file_get_contents(ROOT . 'install/data/update2.sql');
    }
    if($sqlvn < 10106) {
        $version = 'v1.1.6';
        $sql = $sql . file_get_contents(ROOT . 'install/data/update3.sql');
    }
    if($sqlvn < 10109) {
        $version = 'v1.1.9';
        $sql = $sql . file_get_contents(ROOT . 'install/data/update4.sql');
    }
    if($sqlvn < 10200) {
        $version = 'v1.2.0';
        $sql = $sql . file_get_contents(ROOT . 'install/data/update5.sql');
    }
    if($sqlvn < 10205) {
        $version = 'v1.2.5';
    }
    if($sqlvn < 10300) {
        $version = 'v1.3.0';
    }
    if($sqlvn < 10304) {
        $version = 'v1.3.4';
    }
    if($sqlvn < 10500) {
        $version = 'v1.5.0';
    }
    if($sqlvn < 10501) {
        $version = 'v1.5.1';
        $sql = $sql . file_get_contents(ROOT . 'install/data/update6.sql');
    }
    if($sqlvn < 10600) {
        $version = 'v1.6.0';
        $sql = $sql . file_get_contents(ROOT . 'install/data/update7.sql');
    }
    if($sqlvn < 10700) {
        $version = 'v1.7.0';
    }
    if($sqlvn < 10800) {
        $version = 'v1.8.0';
        $sql = $sql . file_get_contents(ROOT . 'install/data/update8.sql');
        if (!isset($conf['md5pass'])) {
            //MD5加密密码
            $admin_pwd = md5('lylme' . $conf['admin_pwd']);
            $DB->query("INSERT INTO `lylme_config` (`k`, `v`, `description`) VALUES ('md5pass', '1', '启用md5加密密码');");
            saveSetting('admin_pwd', $admin_pwd);
        }

    }
    if($sqlvn < 10805) {
        $version = 'v1.8.5';
        if (!isset($conf['about'])) {
            $about_file = ROOT . 'about/about.txt';
            if(file_exists($about_file)) {
                $about_content = daddslashes(file_get_contents($about_file));
                $about =  "INSERT INTO `lylme_config` (`id`, `k`, `v`, `description`) VALUES (NULL, 'about', '" . $about_content . "', NULL)";
            } else {
                $about =  "INSERT INTO `lylme_config` (`id`, `k`, `v`, `description`) VALUES (NULL, 'about', '<h3>关于本站</h3>\r\n<p>感谢来访，本站致力于简洁高效的上网导航和搜索入口，安全快捷。</p>\r\n<p>如果您喜欢我们的网站，请将本站添加到收藏夹（快捷键<code>Ctrl+D</code>），并<a href=\"https://jingyan.baidu.com/article/4dc40848868eba89d946f1c0.html\" target=\"_blank\">设为浏览器主页</a>，方便您的下次访问，感谢支持。<p>\r\n<hr>\r\n<h3>本站承诺</h3>\r\n<p><strong>绝对不会收集用户的隐私信息</strong><p>\r\n<p>区别于部分导航网站，本站链接直接跳转目标，不会对链接处理再后跳转，不会收集用户的隐藏信息，包括但不限于点击记录，访问记录和搜索记录，请放心使用</p>\r\n<hr>\r\n<h3>申请收录</h3>\r\n<p>请点<a href=\"../apply\" target=\"_blank\">这里</a></p>\r\n<hr>\r\n<h3>联系我们</h3>\r\n<p>若您在使用本站时遇到了包括但不限于以下问题：</p>\r\n<li>图标缺失</li>\r\n<li>目标网站无法打开</li>\r\n<li>描述错误</li>\r\n<li>网站违规</li>\r\n<li>收录加急处理</li>\r\n<li>链接删除</li>\r\n<p><strong>请发邮件与我们联系</strong></p>\r\n<h5>联系邮箱</h5>\r\n<p><a href=\"mailto:无\">无</a></p>\r\n<h5>联系说明</h5>\r\n<p>为了您的问题能快速被处理，建议在邮件主题添加【反馈】【投诉】【推荐】【友链】</p>', NULL)";

            }
            $DB->query(daddslashes($about));
            @unlink($about_file);
            @unlink(ROOT . 'about/说明.txt');
        }


    }
    $sql = explode(';', $sql);
    for ($i = 0;$i < count($sql);$i++) {
        if (trim($sql[$i]) == '') {
            continue;
        }
        if($DB->query($sql[$i])) {
        }
    }
    saveSetting('version', $version);
}
