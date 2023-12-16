<?php

@header("Content-type:text/html;charset=utf-8");
define('IN_CRONLITE', true);
define('SYS_KEY', 'lylme_key');
define('SYSTEM_ROOT', dirname(__FILE__) . '/');
define('ROOT', dirname(SYSTEM_ROOT) . '/');
//error_reporting(0);
require ROOT . 'config.php';
if(!defined('SQLITE') && (!$dbconfig['user'] || !$dbconfig['pwd'] || !$dbconfig['dbname'])) {
    @unlink(ROOT . 'install/install.lock');
    header("Location:");
    exit();
}
require SYSTEM_ROOT . "db.class.php";
$DB = new DB($dbconfig['host'], $dbconfig['user'], $dbconfig['pwd'], $dbconfig['dbname'], $dbconfig['port']);
$web_config = $DB->query("SELECT * FROM `lylme_config`");
while($row = $DB->fetch($web_config)) {
    $conf[$row['k']] = $row['v'];
}
require SYSTEM_ROOT . "lists.php";
require SYSTEM_ROOT . "function.php";
require SYSTEM_ROOT . "member.php";
require SYSTEM_ROOT . "tj.php";
require SYSTEM_ROOT . "version.php";
require SYSTEM_ROOT . "updbase.php";
require SYSTEM_ROOT . "site.php";

$cdnpublic = cdnpublic($conf['cdnpublic']);
$templatepath = './template/' . $conf["template"];
$template =  $templatepath . '/index.php';
$background = $conf["background"];
$wap_background = $conf["wap_background"];
if(checkmobile()) {
    if(!empty($wap_background)) {
        $background_img = $wap_background;
    } else {
        $background_img = $background;
    }
} else {
    $background_img = $background;
}
