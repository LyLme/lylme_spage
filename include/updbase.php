<?php

if (empty(constant("VERSION"))) {
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
if ($sqlvn < $filevn) {
    //文件版本大于数据库版本执行更新
    $sql = '';
    if ($sqlvn < 10101) {
        $version = 'v1.1.1';
        $sql = $sql . file_get_contents(ROOT . 'install/data/update.sql');
    }
    if ($sqlvn < 10103) {
        $version = 'v1.1.3';
        @unlink(ROOT . 'include/head.php');
        @unlink(ROOT . 'include/home.php');
        @unlink(ROOT . 'include/apply.php');
        @unlink(ROOT . 'include/footer.php');
        $sql = $sql . file_get_contents(ROOT . 'install/data/update1.sql');
    }
    if ($sqlvn < 10104) {
        $version = 'v1.1.4';
    }
    if ($sqlvn < 10105) {
        $version = 'v1.1.5';
        $sql = $sql . file_get_contents(ROOT . 'install/data/update2.sql');
    }
    if ($sqlvn < 10106) {
        $version = 'v1.1.6';
        $sql = $sql . file_get_contents(ROOT . 'install/data/update3.sql');
    }
    if ($sqlvn < 10109) {
        $version = 'v1.1.9';
        $sql = $sql . file_get_contents(ROOT . 'install/data/update4.sql');
    }
    if ($sqlvn < 10200) {
        $version = 'v1.2.0';
        $sql = $sql . file_get_contents(ROOT . 'install/data/update5.sql');
    }
    if ($sqlvn < 10205) {
        $version = 'v1.2.5';
    }
    if ($sqlvn < 10300) {
        $version = 'v1.3.0';
    }
    if ($sqlvn < 10304) {
        $version = 'v1.3.4';
    }
    if ($sqlvn < 10500) {
        $version = 'v1.5.0';
    }
    if ($sqlvn < 10501) {
        $version = 'v1.5.1';
        $sql = $sql . file_get_contents(ROOT . 'install/data/update6.sql');
    }
    if ($sqlvn < 10600) {
        $version = 'v1.6.0';
        $sql = $sql . file_get_contents(ROOT . 'install/data/update7.sql');
    }
    if ($sqlvn < 10700) {
        $version = 'v1.7.0';
    }
    if ($sqlvn < 10800) {
        $version = 'v1.8.0';
        $sql = $sql . file_get_contents(ROOT . 'install/data/update8.sql');
        if (!isset($conf['md5pass'])) {
            //MD5加密密码
            $admin_pwd = md5('lylme' . $conf['admin_pwd']);
            $DB->query("INSERT INTO `lylme_config` (`k`, `v`, `description`) VALUES ('md5pass', '1', '启用md5加密密码');");
            saveSetting('admin_pwd', $admin_pwd);
        }
    }
    if ($sqlvn < 10805) {
        $version = 'v1.8.5';
        if (!isset($conf['about'])) {

            $DB->query("INSERT INTO `lylme_config` (`k`, `v`, `description`) VALUES ('about', '1', '新版关于页面');");
            $about_file = ROOT . 'about/about.txt';
            if (file_exists($about_file)) {
                $about_content = str_replace(PHP_EOL, '\r\n', daddslashes(file_get_contents($about_file)));
                $about =  " INSERT INTO `lylme_config` (`k`, `v`, `description`) VALUES ('about_content', '$about_content', '关于页面');";
                $DB->query($about);
                @unlink(ROOT . 'about/说明.txt');
            }
        }
    }
    if ($sqlvn < 10900) {
        $version = 'v1.9.0';
    }
    if ($sqlvn < 10905) {
        $version = 'v1.9.5';
    }
    $sql = explode(';', $sql);
    for ($i = 0; $i < count($sql); $i++) {
        if (trim($sql[$i]) == '') {
            continue;
        }
        if ($DB->query($sql[$i])) {
        }
    }
    saveSetting('version', $version);
}
