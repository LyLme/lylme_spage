<?php

define('DEBUG', false);  //调试模式 true(开启) 或 false(关闭)
define('ADMIN_PATH', 'admin');  //后台目录 用于防火墙白名单放行（修改后台目录后需要修改）
define('IN_CRONLITE', true);
define('SYS_KEY', 'lylme_key');
define('SYSTEM_ROOT', dirname(__FILE__) . '/');
define('ROOT', dirname(SYSTEM_ROOT) . '/');
if (!(version_compare(phpversion(), '7.1.0', '>=') && version_compare(phpversion(), '8.0.0', '<'))) {
    exit('<h3>您的PHP版本过低或过高，请将PHP版本修改为PHP7.1及以上（不支持PHP8）</h3>');
}

require ROOT . 'config.php';
if (!defined('SQLITE') && (!$dbconfig['user'] || !$dbconfig['pwd'] || !$dbconfig['dbname'])) {
    @unlink(ROOT . 'install/install.lock');
    header("Location:");
    exit();
}
require SYSTEM_ROOT . "db.class.php";
$DB = new DB($dbconfig['host'], $dbconfig['user'], $dbconfig['pwd'], $dbconfig['dbname'], $dbconfig['port']);
require SYSTEM_ROOT . "site.php";

$web_config = $DB->query("SELECT * FROM `lylme_config`");
if (empty($web_config)) {
    //数据表不存在
    exit("<h3>LyLme Spage Error: MySQL config table is empty(code:404)<h3>");
}
while ($row = $DB->fetch($web_config)) {
    //网站配置
    $conf[$row['k']] = $row['v'];
}

require SYSTEM_ROOT . "include.php";
require SYSTEM_ROOT . "function.php";
require SYSTEM_ROOT . "lists.php";
require SYSTEM_ROOT . "member.php";
require SYSTEM_ROOT . "tj.php";
require SYSTEM_ROOT . "version.php";
require SYSTEM_ROOT . "updbase.php";
require SYSTEM_ROOT . 'lib/Form.php';
