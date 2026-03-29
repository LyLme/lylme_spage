<?php

/**
 * 公共函数库
 * 
 * @Description: 公共函数
 * @Copyright (c) 2024 by LyLme, All Rights Reserved.
 */

// 定义全局变量占位符，防止未定义错误
if (!isset($conf)) {
    $conf = [];
}

if (!isset($GLOBALS['conf'])) {
    $GLOBALS['conf'] = &$conf;
}

if (!defined('ENCRYPT_KEY')) {
    define('ENCRYPT_KEY', SYS_KEY);
}

/**
 * 判断是否为爬虫/蜘蛛
 * @return bool
 */
function is_spider()
{
    // CLI访问或没有User-Agent的情况
    if (php_sapi_name() === 'cli' || !isset($_SERVER['HTTP_USER_AGENT'])) {
        return false;
    }

    $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
    $spiders = [
        'Googlebot',
        'Baiduspider',
        'Bingbot',
        'Yahoo! Slurp',
        'YodaoBot',
        'msnbot',
        '360Spider',
        'Sogou web spider',
        'Sosospider',
        'YoudaoBot',
        'JikeSpider',
        'spider',
        'Spider',
        'bot',
        'Bot',
        'crawler',
        'Crawler'
    ];

    foreach ($spiders as $spider) {
        $spiderLower = strtolower($spider);
        if (strpos($userAgent, $spiderLower) !== false) {
            return true;
        }
    }
    return false;
}

/**
 * 递归添加斜杠 (增强PHP 8.2兼容性)
 * @param mixed $string 输入数据
 * @return mixed
 */
function daddslashes($string)
{
    if (is_array($string)) {
        foreach ($string as $key => $val) {
            $string[$key] = daddslashes($val);
        }
        return $string;
    } elseif (is_string($string)) {
        // PHP 8.1+ 兼容性：addslashes对null返回空字符串
        return addslashes($string);
    } elseif (is_null($string)) {
        return '';
    } elseif (is_numeric($string) || is_bool($string)) {
        return $string;
    } else {
        // 其他类型转换为字符串处理
        return addslashes((string) $string);
    }
}

/**
 * 字符串加密/解密函数
 * @param string $string 原文或密文
 * @param string $operation DECODE解密|ENCODE加密
 * @param string $key 密钥
 * @param int $expiry 密文有效期（秒）
 * @return string
 */
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0)
{
    // Debug
    if (isset($_GET['debug_authcode'])) {
        echo "<!-- authcode: string=$string, operation=$operation, key=$key, expiry=$expiry, ENCRYPT_KEY=" . ENCRYPT_KEY . " -->";
    }

    if (!is_string($string) || empty($string)) {
        return '';
    }

    $operation = strtoupper($operation);
    if (!in_array($operation, ['DECODE', 'ENCODE'])) {
        return '';
    }

    $ckey_length = 4;
    $key = md5($key ? $key : ENCRYPT_KEY);
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));

    if ($operation == 'DECODE') {
        if (strlen($string) < $ckey_length) {
            return '';
        }
        $keyc = substr($string, 0, $ckey_length);
        $string = substr($string, $ckey_length);
    } else {
        $keyc = substr(md5(microtime()), -$ckey_length);
    }

    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);

    if ($operation == 'DECODE') {
        $string = base64_decode($string);
        if ($string === false) {
            return '';
        }
    } else {
        $string = sprintf('%010d', $expiry ? $expiry + time() : 0)
            . substr(md5($string . $keyb), 0, 16)
            . $string;
    }

    $string_length = strlen($string);
    $result = '';
    $box = range(0, 255);
    $rndkey = [];

    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }

    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }

    if ($operation == 'DECODE') {
        if (strlen($result) < 26) {
            return '';
        }

        $timestamp = substr($result, 0, 10);
        $md5_check = substr($result, 10, 16);
        $data = substr($result, 26);

        // 确保 $timestamp 是数字
        if (!is_numeric($timestamp)) {
            return '';
        }

        if (
            ($timestamp == 0 || (int) $timestamp - time() > 0)
            && $md5_check == substr(md5($data . $keyb), 0, 16)
        ) {
            return $data;
        }
        return '';
    } else {
        // ENCODE 操作：返回密文
        return $keyc . base64_encode($result);
    }
}

/**
 * 获取背景图片
 * @return string
 */
function background()
{
    global $background_img;

    if (isset($background_img) && !empty($background_img)) {
        return $background_img;
    }

    // 回退到全局配置
    if (isset($GLOBALS['conf']['background']) && !empty($GLOBALS['conf']['background'])) {
        return $GLOBALS['conf']['background'];
    }

    return '';
}

/**
 * 标准化版本号
 * @param string $ver 版本号字符串
 * @return string
 */
function getver($ver)
{
    if (!is_string($ver) || empty($ver)) {
        return '00000';
    }

    $ver = str_replace('v', '', $ver);
    $vn = explode('.', $ver);

    // 确保至少有3个部分
    while (count($vn) < 3) {
        $vn[] = '0';
    }

    // 限制每个部分最大99
    $vn[0] = min(intval($vn[0]), 99);
    $vn[1] = min(intval($vn[1]), 99);
    $vn[2] = min(intval($vn[2]), 99);

    return sprintf("%02d", $vn[0]) . sprintf("%02d", $vn[1]) . sprintf("%02d", $vn[2]);
}

/**
 * 保存设置到数据库
 * @param string $k 键名
 * @param mixed $v 值
 * @param string $desc 描述
 * @return bool
 */
function saveSetting($k, $v, $desc = '')
{
    global $DB;

    if (!isset($DB) || !($DB instanceof DB)) {
        error_log('Database connection not available in saveSetting');
        return false;
    }

    if (!is_string($k) || empty($k)) {
        error_log('Invalid key in saveSetting: ' . $k);
        return false;
    }

    $k = daddslashes($k);
    $v = daddslashes($v);
    $desc = daddslashes($desc);

    // 使用参数化查询防止SQL注入
    $query = "INSERT INTO `lylme_config` (`k`, `v`, `description`) 
              VALUES (?, ?, ?) 
              ON DUPLICATE KEY UPDATE `v` = ?, `description` = ?";

    try {
        // 假设DB类有prepare方法
        if (method_exists($DB, 'prepare')) {
            $stmt = $DB->prepare($query);
            if ($stmt) {
                $stmt->bind_param('sssss', $k, $v, $desc, $v, $desc);
                return $stmt->execute();
            }
        }

        // 回退到传统方法
        $query = "INSERT INTO `lylme_config` (`k`, `v`, `description`) 
                  VALUES ('$k', '$v', '$desc') 
                  ON DUPLICATE KEY UPDATE `v` = '$v', `description` = '$desc'";
        return $DB->query($query) !== false;
    } catch (Exception $e) {
        error_log('saveSetting error: ' . $e->getMessage());
        return false;
    }
}

/**
 * 获取相对路径的完整URL
 * @param string $srcurl 源URL
 * @param string $baseurl 基础URL
 * @return string
 */
function get_urlpath($srcurl, $baseurl)
{
    if (empty($srcurl)) {
        return '';
    }

    if (substr($srcurl, 0, 2) == "//") {
        $scheme = parse_url($baseurl, PHP_URL_SCHEME);
        if ($scheme) {
            return $scheme . ':' . $srcurl;
        }
        return 'http:' . $srcurl; // 默认协议
    }

    $srcinfo = parse_url($srcurl);
    if (isset($srcinfo['scheme'])) {
        return $srcurl;
    }

    $baseinfo = parse_url($baseurl);
    if (!isset($baseinfo['scheme']) || !isset($baseinfo['host'])) {
        return $srcurl; // 基础URL无效
    }

    $url = $baseinfo['scheme'] . '://' . $baseinfo['host'];
    if (isset($baseinfo['port'])) {
        $url .= ':' . $baseinfo['port'];
    }

    if (substr(isset($srcinfo['path']) ? $srcinfo['path'] : '', 0, 1) == '/') {
        $path = $srcinfo['path'];
    } else {
        $basepath = isset($baseinfo['path']) ? $baseinfo['path'] : '/';
        $path = dirname($basepath) . '/' . (isset($srcinfo['path']) ? $srcinfo['path'] : '');
    }

    $rst = [];
    $path_array = explode('/', $path);

    if (!$path_array[0]) {
        $rst[] = '';
    }

    foreach ($path_array as $dir) {
        if ($dir == '..') {
            if (empty($rst) || end($rst) == '..') {
                $rst[] = '..';
            } else {
                array_pop($rst);
            }
        } elseif ($dir && $dir != '.') {
            $rst[] = $dir;
        }
    }

    if (!end($path_array)) {
        $rst[] = '';
    }

    $url .= implode('/', $rst);

    if (isset($srcinfo['query'])) {
        $url .= '?' . $srcinfo['query'];
    }

    if (isset($srcinfo['fragment'])) {
        $url .= '#' . $srcinfo['fragment'];
    }

    return str_replace('\\', '/', $url);
}

/**
 * 获取客户端真实IP (增强PHP 8.2兼容性)
 * @return string
 */
function get_real_ip()
{
    $headers = [
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'HTTP_CLIENT_IP',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_X_REAL_IP',
        'REMOTE_ADDR'
    ];

    foreach ($headers as $header) {
        // PHP 8.2兼容性：使用 ?? 操作符替代 isset() + 直接访问
        $server_value = isset($_SERVER[$header]) ? $_SERVER[$header] : '';
        if (!empty($server_value)) {
            $ips = explode(',', $server_value);
            $ip = trim($ips[0]);

            // 验证IP格式 - PHP 8.0+ FILTER_FLAG_NO_PRIV_RANGE 需要参数
            $filter_opts = FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE;
            if (version_compare(PHP_VERSION, '8.0.0', '>=')) {
                if (filter_var($ip, FILTER_VALIDATE_IP, $filter_opts)) {
                    return $ip;
                }
            } else {
                // PHP 7.x 兼容
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
    }

    // 返回REMOTE_ADDR或默认值
    $remoteAddr = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
    return filter_var($remoteAddr, FILTER_VALIDATE_IP) ? $remoteAddr : '0.0.0.0';
}

/**
 * 获取一言
 * @return string
 */
function yan()
{
    $filename = defined('ROOT') ? ROOT . '/assets/data/data.dat' : __DIR__ . '/../../assets/data/data.dat';

    if (!file_exists($filename) || !is_readable($filename)) {
        return '生活不止眼前的苟且，还有诗和远方的田野。';
    }

    $data = @file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($data === false || empty($data)) {
        return '生活不止眼前的苟且，还有诗和远方的田野。';
    }

    $result = $data[array_rand($data)];
    return str_replace(["\r", "\n", "\r\n"], '', $result);
}

/**
 * 替换模板变量
 * @param array $data 数据数组
 * @param array|string $arr 模板数组或字符串
 * @return array|string
 */
function rearr($data, $arr)
{
    if (!is_array($data)) {
        $data = [];
    }
    $replacements = [
        '{group_id}' => isset($data['group_id']) ? $data['group_id'] : '',
        '{group_name}' => isset($data['group_name']) ? $data['group_name'] : '',
        '{group_icon}' => isset($data['group_icon']) ? $data['group_icon'] : '',
        '{link_id}' => isset($data['id']) ? $data['id'] : (isset($data['link_id']) ? $data['link_id'] : ''),
        '{link_name}' => isset($data['name']) ? $data['name'] : '',
        '{link_name_text}' => isset($data['name']) ? strip_tags($data['name']) : '',
        '{link_desc}' => isset($data['link_desc']) ? $data['link_desc'] : '',
    ];

    // 处理URL
    $url = '';
    if (isset($data['url'])) {
        $mode = isset($GLOBALS['conf']['mode']) ? $GLOBALS['conf']['mode'] : 1;
        $url = ($mode == 2 && isset($data['id'])) ? "/site-" . $data['id'] . ".html" : $data['url'];
    }
    $replacements['{link_url}'] = $url;

    // 处理图标
    $alt = isset($data['name']) ? $data['name'] : (isset($data['group_name']) ? $data['group_name'] : '');
    $icon = '';

    $icon = isset($data['icon']) ? $data['icon'] : '';
    if (empty($icon)) {
        $icon = '<img src="/assets/img/default-icon.png" alt="' . htmlspecialchars(strip_tags($alt), ENT_QUOTES, 'UTF-8') . '" />';
    } elseif (isset($data['icon']) && !preg_match("/^<svg*/", $data['icon'])) {
        $icon = '<img src="' . htmlspecialchars($data['icon'], ENT_QUOTES, 'UTF-8') . '" alt="' . htmlspecialchars(strip_tags($alt), ENT_QUOTES, 'UTF-8') . '" />';
    } else {
        $icon = isset($data['icon']) ? $data['icon'] : '';
    }
    $replacements['{link_icon}'] = $icon;

    if (is_array($arr)) {
        $result = [];
        foreach ($arr as $key => $value) {
            $result[$key] = str_replace(array_keys($replacements), array_values($replacements), $value);
        }
        return $result;
    } elseif (is_string($arr)) {
        return str_replace(array_keys($replacements), array_values($replacements), $arr);
    }

    return $arr;
}

/**
 * 获取网站head信息
 * @param string|int $url URL或链接ID
 * @param bool $cache 是否使用缓存
 * @return array
 */
function get_head($url, $cache = false)
{
    $id = null;

    if ($cache && is_numeric($url)) {
        $id = (int) $url;
        global $DB;

        if (isset($DB) && $DB instanceof DB) {
            $site_head = $DB->get_row("SELECT * FROM `lylme_links` WHERE `id` = $id AND `link_pwd` = 0");
            if ($site_head && isset($site_head['url'])) {
                $url = $site_head['url'];

                $cache_path = defined('ROOT') ? ROOT . "cache/" : __DIR__ . "/../../cache/";
                $cache_file = $cache_path . md5($url) . ".txt";

                if (file_exists($cache_file) && is_readable($cache_file)) {
                    $file_mtime = filemtime($cache_file);
                    if ((time() - $file_mtime) < 7 * 24 * 60 * 60) {
                        $cached = @file_get_contents($cache_file);
                        if ($cached !== false) {
                            $data = json_decode($cached, true);
                            if (is_array($data)) {
                                return $data;
                            }
                        }
                    }
                }
            }
        }
    }

    if (!is_string($url) || empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
        return [
            'title' => '',
            'charset' => 'UTF-8',
            'icon' => '',
            'description' => '',
            'keywords' => '',
            'url' => $url
        ];
    }

    $data = get_curl($url);
    if ($data === 404 || empty($data)) {
        return [
            'title' => '',
            'charset' => 'UTF-8',
            'icon' => '',
            'description' => '',
            'keywords' => '',
            'url' => $url
        ];
    }

    // 获取网站title - 修复PHP 8.2不支持 ${var} 语法
    $title = '';
    if (preg_match('/<title.*?>(?<title>.*?)<\/title>/sim', $data, $title_matches)) {
        $title = isset($title_matches['title']) ? $title_matches['title'] : '';
    }

    // 检测编码
    $encode = 'UTF-8';
    if (!empty($title)) {
        $detect_order = 'UTF-8, GB2312, GBK, BIG5, ASCII';
        if (function_exists('mb_detect_encoding')) {
            $encode = mb_detect_encoding($title, $detect_order, true);
        }
        if (!$encode) {
            $encode = 'UTF-8';
        }
    }

    // 转换编码
    if ($encode != 'UTF-8' && $encode != 'ASCII' && function_exists('iconv')) {
        $title = @iconv($encode, 'UTF-8//IGNORE', $title);
        $data = @iconv($encode, 'UTF-8//IGNORE', $data);
    }

    // 获取网站icon
    $icon = '';
    preg_match('/<link[^>]*rel=("|\')?(icon|shortcut icon|apple-touch-icon)("|\')?[^>]*>/i', $data, $icon_matches);
    if (!empty($icon_matches[0])) {
        preg_match('/href=("|\')?([^"\'>]+)("|\')?/i', $icon_matches[0], $href_matches);
        $icon = isset($href_matches[2]) ? $href_matches[2] : '';
    }

    if (!empty($icon)) {
        $icon = get_urlpath($icon, $url);
    } else {
        $parse = parse_url($url);
        if (isset($parse['scheme']) && isset($parse['host'])) {
            $port = isset($parse['port']) && $parse['port'] != 80 ? ':' . $parse['port'] : '';
            $iconurl = $parse['scheme'] . '://' . $parse['host'] . $port . '/favicon.ico';
            $icon_check = get_curl($iconurl);
            if ($icon_check !== 404 && !empty($icon_check)) {
                $icon = $iconurl;
            }
        }
    }

    // 获取description
    $description = '';
    preg_match('/<meta[^>]*name=("|\')?description("|\')?[^>]*content=("|\')?([^"\'>]+)("|\')?[^>]*>/i', $data, $desc_matches);
    $description = isset($desc_matches[4]) ? $desc_matches[4] : '';

    // 获取keywords
    $keywords = '';
    preg_match('/<meta[^>]*name=("|\')?keywords("|\')?[^>]*content=("|\')?([^"\'>]+)("|\')?[^>]*>/i', $data, $keyword_matches);
    $keywords = isset($keyword_matches[4]) ? $keyword_matches[4] : '';

    $result = [
        'title' => trim($title),
        'charset' => $encode,
        'icon' => trim($icon),
        'description' => trim($description),
        'keywords' => trim($keywords),
        'url' => $url
    ];

    // 缓存结果
    if ($cache && $id !== null) {
        $cache_path = defined('ROOT') ? ROOT . "cache/" : __DIR__ . "/../../cache/";
        if (!file_exists($cache_path)) {
            @mkdir($cache_path, 0755, true);
        }
        if (is_dir($cache_path) && is_writable($cache_path)) {
            $cache_file = $cache_path . md5($url) . ".txt";
            @file_put_contents($cache_file, json_encode($result));
        }
    }

    return $result;
}

/**
 * 模拟GET请求
 * @param string $url 请求URL
 * @return string|int
 */
function get_curl($url)
{
    if (!function_exists('curl_init')) {
        return @file_get_contents($url) ?: 404;
    }

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 5,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/101.0.4951.54 Safari/537.36',
        CURLOPT_ENCODING => '',
        CURLOPT_HTTPHEADER => [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language: zh-CN,zh;q=0.9,en;q=0.8',
            'Accept-Encoding: gzip, deflate',
            'Connection: keep-alive',
            'Upgrade-Insecure-Requests: 1'
        ]
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 404) {
        return 404;
    }

    return $response !== false ? $response : '';
}

/**
 * 检查字符串长度
 * @param string $str 字符串
 * @param int $max_length 最大长度
 * @return bool
 */
function strlens($str, $max_length = 255)
{
    if (!is_string($str)) {
        $str = (string) $str;
    }
    return strlen($str) > $max_length;
}

/**
 * 网站收录申请
 * @param string $name 网站名称
 * @param string $url 网站URL
 * @param string $icon 图标URL
 * @param int $group_id 分组ID
 * @param int $status 状态
 * @return string JSON响应
 */
function apply($name, $url, $icon, $group_id, $status)
{
    header('Content-Type: application/json; charset=utf-8');

    $name = strip_tags(daddslashes($name));
    $url = strip_tags(daddslashes($url));
    $icon = strip_tags(daddslashes($icon));
    $group_id = (int) strip_tags(daddslashes($group_id));
    $status = (int) strip_tags(daddslashes($status));
    $userip = get_real_ip();
    $date = date("Y-m-d H:i:s");

    if (empty($name) || empty($url) || empty($group_id)) {
        return '{"code": "-1", "msg": "必填项不能为空"}';
    }

    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return '{"code": "-2", "msg": "链接不符合要求"}';
    }

    if (strlens($name) || strlens($url) || strlens($icon) || strlens($group_id) || strlens($userip)) {
        return '{"code": "500", "msg": "参数过长"}';
    }

    global $DB;
    if (!isset($DB) || !($DB instanceof DB)) {
        return '{"code": "-4", "msg": "数据库连接失败"}';
    }

    // 检查链接是否已存在
    $check_query = $DB->query("SELECT * FROM `lylme_apply` WHERE `apply_url` = '$url'");
    if ($check_query !== false && $DB->num_rows($check_query) > 0) {
        return '{"code": "-3", "msg": "链接已存在，请勿重复提交"}';
    }

    $sql = "INSERT INTO `lylme_apply` 
            (`apply_name`, `apply_url`, `apply_group`, `apply_icon`, `apply_desc`, `apply_time`, `apply_status`) 
            VALUES ('$name', '$url', $group_id, '$icon', '$userip', '$date', $status)";

    if ($DB->query($sql)) {
        switch ($status) {
            case 0:
                return '{"code": "200", "msg": "请等待管理员审核"}';
            case 1:
                if (ins_link($name, $url, $icon, $group_id)) {
                    return '{"code": "200", "msg": "网站已收录"}';
                } else {
                    return '{"code": "-5", "msg": "收录失败，请联系网站管理员"}';
                }
            default:
                return '{"code": "-4", "msg": "未知状态码"}';
        }
    } else {
        return '{"code": "-4", "msg": "数据库操作失败，请联系网站管理员"}';
    }
}

/**
 * 插入链接
 * @param string $name 网站名称
 * @param string $url 网站URL
 * @param string $icon 图标URL
 * @param int $group_id 分组ID
 * @return bool
 */
function ins_link($name, $url, $icon, $group_id)
{
    global $DB;

    if (!isset($DB) || !($DB instanceof DB)) {
        return false;
    }

    $name = strip_tags(daddslashes($name));
    $url = strip_tags(daddslashes($url));
    $icon = strip_tags(daddslashes($icon));
    $group_id = (int) strip_tags(daddslashes($group_id));

    // 获取最大排序值
    $link_order = 1;
    $count_result = $DB->query("SELECT MAX(id) as max_id FROM `lylme_links`");
    if ($count_result !== false) {
        $count_row = $DB->fetch($count_result);
        if ($count_row && isset($count_row['max_id'])) {
            $link_order = (int) $count_row['max_id'] + 1;
        }
    }

    $sql = "INSERT INTO `lylme_links` 
            (`name`, `group_id`, `url`, `icon`, `link_desc`, `link_order`) 
            VALUES ('$name', $group_id, '$url', '$icon', '', $link_order)";

    return $DB->query($sql) !== false;
}

/**
 * 获取主题自定义设置
 * @param string $name 参数名称
 * @param mixed $default 默认值
 * @return mixed
 */
function theme_config($name, $default = '')
{
    $config = isset($GLOBALS['conf']) ? $GLOBALS['conf'] : [];

    if (empty($config) || !isset($config['template'])) {
        return $default;
    }

    $template = $config['template'];
    $theme_name = "theme_config_" . $template;

    // 从数据库配置中获取
    if (isset($config[$theme_name]) && !empty($config[$theme_name])) {
        $theme_themes = @json_decode($config[$theme_name], true);
        if (is_array($theme_themes) && isset($theme_themes[$name])) {
            return $theme_themes[$name];
        }
    }

    // 从主题配置文件获取
    $theme_config_path = defined('ROOT') ? ROOT . 'template/' . $template . '/config.php' : __DIR__ . '/../../template/' . $template . '/config.php';

    if (file_exists($theme_config_path) && is_readable($theme_config_path)) {
        $theme_config = [];
        @include $theme_config_path;

        if (is_array($theme_config)) {
            foreach ($theme_config as $config_item) {
                if (isset($config_item['name']) && $config_item['name'] == $name) {
                    return isset($config_item['value']) ? $config_item['value'] : $default;
                }
            }
        }
    }

    return $default;
}

/**
 * 生成CSRF Token
 * @return string
 */
function csrf_token()
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

/**
 * 验证CSRF Token
 * @param string $token 提交的token
 * @return bool
 */
function csrf_verify($token = null)
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if ($token === null) {
        $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
    }

    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }

    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * 输出CSRF Token隐藏字段
 * @return string
 */
function csrf_field()
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

// 初始化背景图片变量
$background_img = '';
if (isset($conf['background']) && !empty($conf['background'])) {
    $background_img = $conf['background'];
}
