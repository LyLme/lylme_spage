<?php
@header ("Content-type:text/html;charset=utf-8");
define('IN_CRONLITE', true);
define('SYS_KEY', 'lylme_key');
define('SYSTEM_ROOT', dirname(__FILE__).'/');
define('ROOT', dirname(SYSTEM_ROOT).'/');
error_reporting(E_ALL ^ E_NOTICE);
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
while($row = mysqli_fetch_assoc($rs)) $conf[$row['k']]=$row['v'];
include_once(SYSTEM_ROOT."function.php");
include_once(SYSTEM_ROOT."member.php");
include_once(SYSTEM_ROOT."tj.php");
$linksrows=$DB->num_rows($DB->query("SELECT * FROM `lylme_links`"));
$groupsrows=$DB->num_rows($DB->query("SELECT * FROM `lylme_groups`"));
$cdnpublic = cdnpublic(isset($conf['cdnpublic']));
$template =  './template/'.$conf["template"].'/index.php';
?>