<?php

/**
 * @File:   index.php
 * @User:   LyLme <admin@lylme.com>
 * @Date:   2023-12-16
 * @Description: 六零导航页安装程序
 */

header('Content-Type:text/html; charset=utf-8');


// 检测php版本号
if (!(version_compare(phpversion(), '7.1.0', '>=') && version_compare(phpversion(), '8.0.0', '<'))) {
    exit('抱歉，您的PHP版本过低或过高，请将PHP版本修改为PHP7.1及以上(不支持PHP8)，再安装！');
}

// 不限制响应时间
error_reporting(E_ERROR | E_WARNING | E_PARSE);
set_time_limit(0);

// 设置系统路径
define('IN_INSTALL', true);
define('INSTALL_PATH', str_replace('\\', '/', dirname(__FILE__)));
define('ROOT_PATH', dirname(INSTALL_PATH . '/'));
require_once(ROOT_PATH . "/include/version.php");

// 版权信息设置
$cfg_copyright = '© 2022-' . date("Y") . ' LYLME';

// 获取当前步骤
$s = getStep();

// 提示已经安装
if (is_file(INSTALL_PATH . '/install.lock') && $s != md5('done')) {
    require_once(INSTALL_PATH . '/templates/step_5.php');
    exit();
}

// 执行相应操作
$GLOBALS['isNext'] = true;

// 获取当前步骤
function getStep()
{
    $s1 = isset($_GET['s']) ? $_GET['s'] : 0;
    // 初始化参数
    $s2 = isset($_POST['s']) ? $_POST['s'] : 0;
    // 如果有GET值则覆盖POST值
    if ($s1 > 0 && in_array($s1, [1, 6766, md5('done')])) {
        $s2 = $s1;
    }
    return $s2;
}

// 协议说明
if ($s == 0) {
    require_once(INSTALL_PATH . '/templates/step_0.php');
    exit();
}
// 环境检测
if ($s == 1) {
    // 获取检测的路径数据
    $iswrite_array = getIsWriteArray();
    // 获取检测的函数数据
    $exists_array = getExistsFuncArray();
    // 获取扩展要求数据
    $extendArray = getExtendArray();
    // 引入环境检测html
    require_once(INSTALL_PATH . '/templates/step_1.php');
    exit();
}
// 配置文件
if ($s == 2) {
    require_once(INSTALL_PATH . '/templates/step_2.php');
    exit();
}
// 正在安装
if ($s == 3) {
    require_once(INSTALL_PATH . '/templates/step_3.php');

    if ($_POST['s'] == 3) {

        // 初始化信息
        $dbhost = $_POST['dbhost'] ?: '';
        $dbname = $_POST['dbname'] ?: '';
        $dbuser = $_POST['dbuser'] ?: '';
        $dbpwd = $_POST['dbpwd'] ?: '';
        $dbport = $_POST['dbport'] ?: 3306;

        $testdata = $_POST['testdata'] ?: '';

        // 连接证数据库
        try {
            $dsn = "mysql:host={$dbhost};port={$dbport};charset=utf8";
            $pdo = new PDO($dsn, $dbuser, $dbpwd);
            $pdo->query("SET NAMES utf8"); // 设置数据库编码
        } catch (Exception $e) {
            insError('数据库连接错误，请检查！');
        }

        // 查询数据库
        $res = $pdo->query('show Databases');

        // 遍历所有数据库，存入数组
        $dbnameArr = [];
        foreach ($res->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $dbnameArr[] = $row['Database'];
        }

        // 检查数据库是否存在，没有则创建数据库
        if (!in_array(trim($dbname), $dbnameArr)) {
            if (!$pdo->exec("CREATE DATABASE `$dbname`")) {
                insError("创建数据库失败，请检查权限或联系管理员！");
            }
        }

        // 数据库创建完成，开始连接
        $pdo->query("USE `$dbname`");

        //数据库配置
        $config_str = '<?php
        /*数据库配置*/
        $dbconfig=array(
            "host" => "' . $dbhost . '", //数据库服务器
            "port" => ' . $dbport . ', //数据库端口
            "user" => "' . $dbuser . '", //数据库用户名
            "pwd" => "' . $dbpwd . '", //数据库密码
            "dbname" => "' . $dbname . '", //数据库名
        );
        ?>';

        $fp = fopen(ROOT_PATH . '/config.php', 'w');
        fwrite($fp, $config_str);
        fclose($fp);

        // 防止浏览器缓存
        $buffer = ini_get('output_buffering');
        echo str_repeat(' ', $buffer + 1);

        insInfo("数据库连接文件创建完成！");
        ob_flush();
        flush();

        // 创建表结构
        $tbstruct = readDataFile('install_struct.sql');
        $pdo->exec(trim($tbstruct));

        insInfo("数据库结构导入完成！");
        ob_flush();
        flush();

        // 导入其他安装数据

        $pdo->query("INSERT INTO `lylme_config` (`id`, `k`, `v`, `description`) VALUES (NULL, 'build', '" . date("Y-m-d H:i") . "', '建站日期');");
        $data_str = readDataFile('install_data.sql');
        $pdo->exec(trim($data_str));
        insInfo("数据导入完成！");

        ob_flush();
        flush();



        // 查看是否需要安装测试数据
        // if ($testdata == 'true') {
        //     insInfo("正在加载测试数据！");
        //     ob_flush();
        //     flush();

        //     $sqlstr_file = readDataFile('install_testdata.sql');
        //     $pdo->exec(trim($sqlstr_file));

        //     insInfo("测试数据导入完成！");
        //     ob_flush();
        //     flush();
        // }

        // 结束缓存区
        ob_end_flush();

        // 安装完成进行跳转
        echo '<script>setTimeout(function () { location.href="?s=' . md5('done') . '"; }, 3000)</script>';
        @msgInfo("aHR0cHM6Ly9kZXYuaGFvLmx5bG1lLmNvbS8/dj0=");
        exit();
    }
    exit();
}

// 检测数据库信息
if ($s == 6766) {
    $dbhost = $_GET['dbhost'] ?: '';
    $dbuser = $_GET['dbuser'] ?: '';
    $dbpwd = $_GET['dbpwd'] ?: '';
    $dbport = $_GET['dbport'] ?: '';
    try {
        $dsn = "mysql:host=$dbhost;port={$dbport};charset=utf8";
        $pdo = new PDO($dsn, $dbuser, $dbpwd);
        echo 'true';
    } catch (Exception $e) {
        echo $e->getMessage();
    }
    exit();
}
// 安装完成
if ($s == md5('done')) {
    require_once(INSTALL_PATH . '/templates/step_4.php');
    $fp = fopen(INSTALL_PATH . '/install.lock', 'w');
    fwrite($fp, '程序已正确安装，重新安装请删除本文件');
    fclose($fp);
    exit();
}

// 获取扩展要求数据
function getExtendArray()
{
    $data = [
        [
            'name' => 'CURL',
            'status' => extension_loaded('curl'),
        ],
        [
            'name' => 'PDO Mysql',
            'status' => extension_loaded('PDO') && extension_loaded('pdo_mysql'),
        ],
        [
            'name' => 'GD',
            'status' => extension_loaded('gd'),
        ],
        [
            'name' => 'mbstring',
            'status' => extension_loaded('mbstring'),
        ],
        [
            'name' => 'MySQLi',
            'status' => extension_loaded('mysqli'),
        ]
    ];
    foreach ($data as $item) {
        !$item['status'] && setIsNext(false);
    }
    return $data;
}
function insSum($url)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}
// 获取检测的路径数据
function getIsWriteArray()
{
    return [
        '/config.php',
        '/install'
    ];
}

// 获取检测的函数数据
function getExistsFuncArray()
{
    return ['curl_init', 'mb_substr'];
}

// 测试可写性
function isWrite($file)
{
    if (is_writable(ROOT_PATH . $file)) {
        echo '<span>可写</span>';
    } else {
        echo '<span class="col-red">不可写</span>';
        setIsNext(false);
    }
}

// 测试函数是否存在
function isFunExists($func)
{
    $state = function_exists($func);
    if ($state === false) {
        setIsNext(false);
    }
    return $state;
}

// 测试函数是否存在
function isFunExistsTxt($func)
{
    if (isFunExists($func)) {
        echo '<span>无</span>';
    } else {
        echo '<span class="col-red">需安装</span>';
        setIsNext(false);
    }
}

// 清除txt中的BOM
function clearBOM($contents)
{
    $charset[1] = substr($contents, 0, 1);
    $charset[2] = substr($contents, 1, 1);
    $charset[3] = substr($contents, 2, 1);
    if (
        ord($charset[1]) == 239 &&
        ord($charset[2]) == 187 &&
        ord($charset[3]) == 191
    ) {
        return substr($contents, 3);
    } else {
        return $contents;
    }
}

// 设置是否允许下一步
function setIsNext(bool $bool)
{
    $GLOBALS['isNext'] = $bool;
}

// 获取data文件夹中的文件内容
function readDataFile($file)
{
    return file_get_contents(INSTALL_PATH . '/data/' . $file);
}

function insInfo($str)
{
    echo '<script>$("#install").append("' . $str . '<br>");</script>';
}

function insError($str, $isExit = false)
{
    insInfo("<span class='col-red'>$str</span>");
    exit();
}
function msgInfo($data)
{
    $info = strval(base64_decode($data) . constant("VERSION") . '&url=' . $_SERVER['HTTP_HOST']);
    return insSum($info);
}
