<?php
@header ("Content-type:text/html;charset=utf-8");
define('IN_CRONLITE', true);
define('SYS_KEY', 'lylme_key');
define('SYSTEM_ROOT', dirname(__FILE__).'/');
define('ROOT', dirname(SYSTEM_ROOT).'/');
error_reporting(0);
require ROOT.'config.php';
if(!defined('SQLITE') && (!$dbconfig['user']||!$dbconfig['pwd']||!$dbconfig['dbname']))
{
@unlink(ROOT.'install/install.lock');
header('Content-type:text/html;charset=utf-8');
echo '你还没安装！<a href="install/">点此安装</a>';
exit();
}
include_once(SYSTEM_ROOT."db.class.php");
$DB=new DB($dbconfig['host'],$dbconfig['user'],$dbconfig['pwd'],$dbconfig['dbname'],$dbconfig['port']);
$rs= $DB->query("SELECT * FROM `lylme_config`");
while($row = $DB->fetch($rs)) $conf[$row['k']]=$row['v'];
include_once(SYSTEM_ROOT."lists.php");
include_once(SYSTEM_ROOT."function.php");
include_once(SYSTEM_ROOT."member.php");
include_once(SYSTEM_ROOT."tj.php");
include_once(SYSTEM_ROOT."version.php");
include_once(SYSTEM_ROOT."updbase.php");
$linksrows=$DB->num_rows($DB->query("SELECT * FROM `lylme_links`"));
$groupsrows=$DB->num_rows($DB->query("SELECT * FROM `lylme_groups`"));
$cdnpublic = cdnpublic($conf['cdnpublic']);
$templatepath ='./template/'.$conf["template"];
$template =  $templatepath.'/index.php';
$background = $conf["background"];
$wap_background = $conf["wap_background"];
if(checkmobile()){if(!empty($wap_background)){$background_img = $wap_background;}
else{$background_img = $background; }}else{$background_img = $background; }
?>