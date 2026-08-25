<?php

/**
 * @File:   index.php
 * @User:   LyLme <admin@lylme.com>
 * @Date:   2023-12-16
 * @Description: 六零导航页安装程序
 */

header('Content-Type:text/html; charset=utf-8');

// 不限制响应时间
error_reporting(E_ERROR | E_WARNING | E_PARSE);
set_time_limit(0);

if (function_exists('mysqli_report')) {
    mysqli_report(MYSQLI_REPORT_OFF);
}

// 设置系统路径
define('IN_INSTALL', true);
define('INSTALL_PATH', str_replace('\\', '/', dirname(__FILE__)));
define('ROOT_PATH', dirname(INSTALL_PATH . '/'));
require_once(ROOT_PATH . "/include/version.php");

@session_start();

// 版权信息设置
$cfg_copyright = '© 2022-' . date("Y") . ' LYLME';

// 获取当前步骤
$s = getStep();
$isReinstall = ($s == 3 && isset($_POST['s']) && $_POST['s'] == 3);
if (is_file(INSTALL_PATH . '/install.lock') && $s != md5('done') && !$isReinstall) {
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
    // PHP 版本检查（兼容 PHP 5.4+ / 7.x / 8.x，低于 5.4 阻断安装）
    if (version_compare(PHP_VERSION, '5.4.0', '<')) {
        setIsNext(false);
    }
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

    if (isset($_POST['s']) && $_POST['s'] == 3) {

        // 初始化信息
        $dbhost = isset($_POST['dbhost']) ? trim($_POST['dbhost']) : '';
        $dbname = isset($_POST['dbname']) ? trim($_POST['dbname']) : '';
        $dbuser = isset($_POST['dbuser']) ? trim($_POST['dbuser']) : '';
        $dbpwd = isset($_POST['dbpwd']) ? $_POST['dbpwd'] : '';
        $dbport = isset($_POST['dbport']) ? intval($_POST['dbport']) : 3306;

        // 管理员信息
        $admin_user = isset($_POST['admin_user']) ? trim($_POST['admin_user']) : '';
        $admin_pwd = isset($_POST['admin_pwd']) ? $_POST['admin_pwd'] : '';
        if ($admin_user === '' || $admin_pwd === '') {
            insError('管理员账号或密码不能为空！');
        }
        if (strlen($admin_pwd) < 6) {
            insError('管理员密码长度不能小于6位！');
        }


        // 连接证数据库
        $con = @mysqli_connect($dbhost, $dbuser, $dbpwd, '', $dbport);
        if (!$con) {
            insError('数据库连接错误，请检查！');
        }
        mysqli_set_charset($con, 'utf8'); // 设置数据库编码

        // 查询数据库
        $res = mysqli_query($con, 'show Databases');
        if (!$res) {
            insError('获取数据库列表失败：' . mysqli_error($con));
        }

        // 遍历所有数据库，存入数组
        $dbnameArr = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $dbnameArr[] = $row['Database'];
        }

        // 数据库名称合法性校验（仅允许字母/数字/下划线/中文，防止特殊字符注入）
        if (!preg_match('/^[a-zA-Z0-9_\x{4e00}-\x{9fa5}]+$/u', $dbname)) {
            insError('数据库名称不合法，仅允许字母、数字、下划线！');
        }

        // 检查数据库是否存在，没有则创建数据库（并切换当前库）
        if (!in_array(trim($dbname), $dbnameArr)) {
            if (!mysqli_query($con, "CREATE DATABASE `$dbname`")) {
                insError("创建数据库失败，请检查权限或联系管理员！");
            }
        }

        $con = @mysqli_connect($dbhost, $dbuser, $dbpwd, $dbname, $dbport);
        if (!$con) {
            insError('重新连接数据库失败：' . mysqli_connect_error());
        }
        mysqli_set_charset($con, 'utf8');
        if (mysqli_fetch_row(mysqli_query($con, "SELECT DATABASE()"))[0] !== $dbname) {
            insError('选择数据库失败，当前未选中任何数据库：' . mysqli_error($con));
        }

        $config_str = "<?php\n"
            . "/*数据库配置*/\n"
            . '$dbconfig = ' . var_export(array(
                'host' => $dbhost,
                'port' => intval($dbport),
                'user' => $dbuser,
                'pwd' => $dbpwd,
                'dbname' => $dbname,
            ), true) . ";\n"
            . "?>";

        $fp = fopen(ROOT_PATH . '/config.php', 'w');
        if (!$fp) {
            insError('config.php 写入失败，请检查文件权限！');
        }
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
        if (!mysqli_multi_query($con, trim($tbstruct))) {
            insError('数据库导入失败：' . mysqli_error($con));
        }
        do {
            if ($r = mysqli_store_result($con)) {
                mysqli_free_result($r);
            }
        } while (mysqli_next_result($con));
        if (mysqli_error($con)) {
            insError('数据库导入失败：' . mysqli_error($con));
        }
        mysqli_query($con, 'COMMIT');
        insInfo("数据库导入完成！");
        ob_flush();
        flush();

        $admin_pwd_md5 = md5('lylme' . $admin_pwd);
        $safe_admin_user = mysqli_real_escape_string($con, $admin_user);
        if (!mysqli_query($con, "UPDATE `lylme_config` SET `v` = '$safe_admin_user' WHERE `k` = 'admin_user'")) {
            insError('设置管理员账号失败：' . mysqli_error($con));
        }
        if (!mysqli_query($con, "UPDATE `lylme_config` SET `v` = '$admin_pwd_md5' WHERE `k` = 'admin_pwd'")) {
            insError('设置管理员密码失败：' . mysqli_error($con));
        }
        $rowUser = mysqli_fetch_assoc(mysqli_query($con, "SELECT `v` FROM `lylme_config` WHERE `k` = 'admin_user'"));
        $rowPwd = mysqli_fetch_assoc(mysqli_query($con, "SELECT `v` FROM `lylme_config` WHERE `k` = 'admin_pwd'"));
        if (!$rowUser || $rowUser['v'] !== $admin_user || !$rowPwd || $rowPwd['v'] !== $admin_pwd_md5) {
            insError('管理员信息写入数据库失败，请重新安装或检查数据库权限！当前数据库账号：' . (isset($rowUser['v']) ? $rowUser['v'] : '读取失败') . '，密码校验：' . (isset($rowPwd['v']) && $rowPwd['v'] === $admin_pwd_md5 ? '通过' : '失败'));
        }
        mysqli_query($con, 'COMMIT');
        insInfo("管理员信息设置完成！");
        ob_flush();
        flush();
        $_SESSION['install_admin_user'] = $admin_user;
        $_SESSION['install_admin_pwd'] = $admin_pwd;
        ob_end_flush();
        $doneUrl = '?s=' . md5('done');
        echo '<script>setTimeout(function () { location.href="' . $doneUrl . '"; }, 2000)</script>';
        exit();
    }
    exit();
}

// 检测数据库信息
if ($s == 6766) {
    $dbhost = isset($_GET['dbhost']) ? trim($_GET['dbhost']) : '';
    $dbuser = isset($_GET['dbuser']) ? trim($_GET['dbuser']) : '';
    $dbpwd = isset($_GET['dbpwd']) ? $_GET['dbpwd'] : '';
    $dbport = isset($_GET['dbport']) ? intval($_GET['dbport']) : 3306;
    $con = @mysqli_connect($dbhost, $dbuser, $dbpwd, '', $dbport);
    if ($con) {
        echo 'true';
        mysqli_close($con);
    } else {
        if (mysqli_connect_errno() == 1045) {
            echo '数据库用户名不存在或数据库密码错误！请核对后再试';
        } else {
            echo mysqli_connect_error();
        }
    }
    exit();
}
// 检测目标数据库是否已存在 lylme_ 前缀的表（用于安装前提示用户）
if ($s == 'checktables') {
    header('Content-Type: application/json; charset=utf-8');
    $dbhost = isset($_POST['dbhost']) ? trim($_POST['dbhost']) : '';
    $dbname = isset($_POST['dbname']) ? trim($_POST['dbname']) : '';
    $dbuser = isset($_POST['dbuser']) ? trim($_POST['dbuser']) : '';
    $dbpwd = isset($_POST['dbpwd']) ? $_POST['dbpwd'] : '';
    $dbport = isset($_POST['dbport']) ? intval($_POST['dbport']) : 3306;

    $con = @mysqli_connect($dbhost, $dbuser, $dbpwd, '', $dbport);
    if (!$con) {
        echo json_encode(['exists' => false, 'count' => 0, 'tables' => [], 'error' => '数据库连接失败']);
        exit();
    }
    mysqli_set_charset($con, 'utf8');

    // 查询指定库中 lylme_ 前缀的表
    $tables = [];
    if (mysqli_select_db($con, $dbname)) {
        $res = mysqli_query($con, "SHOW TABLES LIKE 'lylme_%'");
        if ($res) {
            while ($row = mysqli_fetch_row($res)) {
                $tables[] = $row[0];
            }
        }
    }
    mysqli_close($con);
    echo json_encode(['exists' => count($tables) > 0, 'count' => count($tables), 'tables' => $tables]);
    exit();
}
// 安装完成
if ($s == md5('done')) {
    require_once(INSTALL_PATH . '/templates/step_4.php');
    @ob_end_flush();
    @flush();
    $fp = fopen(INSTALL_PATH . '/install.lock', 'w');
    if ($fp) {
        fwrite($fp, '程序已正确安装，重新安装请删除本文件');
        fclose($fp);
    }
    msgInfo("aHR0cHM6Ly9kZXYuaGFvLmx5bG1lLmNvbS8/dj0=");
    exit();
}

// 获取扩展要求数据（required=true 为必须项，缺失阻断安装；false 为可选，缺失不影响安装）
function getExtendArray()
{
    $data = [
        [
            'name' => 'MySQLi',
            'status' => extension_loaded('mysqli'),
            'required' => true,
            'desc' => '数据库操作（必需）',
        ],
        [
            'name' => 'CURL',
            'status' => extension_loaded('curl'),
            'required' => true,
            'desc' => '远程抓取、链接收录、接口请求',
        ],
        [
            'name' => 'GD',
            'status' => extension_loaded('gd'),
            'required' => true,
            'desc' => '验证码、二维码、图片处理',
        ],
        [
            'name' => 'mbstring',
            'status' => extension_loaded('mbstring'),
            'required' => true,
            'desc' => '中文字符处理',
        ], [
            'name' => 'zip',
            'status' => extension_loaded('zip'),
            'required' => false,
            'desc' => '版本更新解压更新包',
        ],
        [
            'name' => 'OpenSSL',
            'status' => extension_loaded('openssl'),
            'required' => false,
            'desc' => '生成安全随机数（CSRF 防护）',
        ],
        [
            'name' => 'Zlib',
            'status' => extension_loaded('zlib'),
            'required' => false,
            'desc' => '二维码缓存压缩（phpqrcode）',
        ],
        [
            'name' => 'iconv',
            'status' => extension_loaded('iconv'),
            'required' => false,
            'desc' => '网页标题编码转换（缺失自动降级）',
        ]
    ];
    foreach ($data as $item) {
        if ($item['required'] && !$item['status']) {
            setIsNext(false);
        }
    }
    return $data;
}
function insSum($url)
{
    // 安装统计请求：任何超时/失败都静默忽略，绝不阻塞安装完成页
    if (function_exists('curl_init')) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // 极短超时：连接最多等待 1 秒，整体最多 2 秒，内网/弱网下不会卡住页面
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 2);
        $output = @curl_exec($curl);
        @curl_close($curl);
        return $output;
    }
    // 无 curl 扩展时：原生 socket 发送请求后立即关闭，不等待响应
    $parts = parse_url($url);
    if (!is_array($parts) || empty($parts['host'])) {
        return false;
    }
    $scheme = isset($parts['scheme']) ? strtolower($parts['scheme']) : 'http';
    $host = $parts['host'];
    $port = isset($parts['port']) ? intval($parts['port']) : ($scheme === 'https' ? 443 : 80);
    $path = isset($parts['path']) ? $parts['path'] : '/';
    if (!empty($parts['query'])) {
        $path .= '?' . $parts['query'];
    }
    if ($scheme === 'https' && !extension_loaded('openssl')) {
        return false;
    }
    $fp = @fsockopen(($scheme === 'https' ? 'ssl://' : '') . $host, $port, $errno, $errstr, 1);
    if (!$fp) {
        return false;
    }
    stream_set_timeout($fp, 1);
    $request = "GET $path HTTP/1.1\r\n"
        . "Host: $host\r\n"
        . "User-Agent: LyLme-SPage/Installer\r\n"
        . "Connection: Close\r\n\r\n";
    @fwrite($fp, $request);
    @fclose($fp); // 立即关闭连接，不等待服务端响应
    return true;
}
// 获取检测的路径数据
function getIsWriteArray()
{
    return [
        '/config.php',
        '/install'
    ];
}

// 获取检测的函数数据（GD、MySQLi 子函数已由扩展检测覆盖，这里仅保留必需扩展的代表函数）
function getExistsFuncArray()
{
    return [
        'curl_init',
        'mb_substr'
    ];
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
function setIsNext($bool)
{
    $GLOBALS['isNext'] = $bool;
}

// 获取data文件夹中的文件内容
function readDataFile($file)
{
    // clearBOM：防止 SQL 文件被编辑工具保存为带 UTF-8 BOM 时导致 mysqli_multi_query 导入失败
    return clearBOM(file_get_contents(INSTALL_PATH . '/data/' . $file));
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
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
    $info = strval(base64_decode($data) . constant("VERSION") . '&url=' . $host);
    return insSum($info);
}
