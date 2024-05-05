<?php
/*
 * @Description: 公共函数
 * @Copyright (c) 2024 by LyLme, All Rights Reserved.
 */



//判断蜘蛛
function is_spider()
{
    $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
    $spiders = array(
        'Googlebot',
        'Baiduspider',
        'Yahoo! Slurp',
        'YodaoBot',
        'msnbot',
        '360Spider',
        'spider',
        'Spider'
        //这里可以加入更多的蜘蛛标示
    );
    foreach ($spiders as $spider) {
        $spider = strtolower($spider);
        if (strpos($userAgent, $spider) !== false) {
            return true;
        }
    }
    return false;
}
function daddslashes($string)
{
    if (is_array($string)) {
        foreach ($string as $key => $val) {
            $string[$key] = daddslashes($val);
        }
    } else {
        $string = addslashes($string);
    }
    return $string;
}
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0)
{
    $ckey_length = 4;
    $key = md5($key ? $key : ENCRYPT_KEY);
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';
    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);
    $result = '';
    $box = range(0, 255);
    $rndkey = array();
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
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc . str_replace('=', '', base64_encode($result));
    }
}


$background = $conf["background"];
//网站背景
function background()
{
    return $GLOBALS['background_img'];
}


function getver($ver)
{
    $vn = explode('.', str_replace('v', '', $ver));
    return $vn[0] . sprintf("%02d", $vn[1]) . sprintf("%02d", $vn[2]);
}
//更新设置
function saveSetting($k, $v, $desc = '')
{
    global $DB;
    $v = daddslashes($v);
    $query = "INSERT INTO `lylme_config` (`k`, `v`,`description`) VALUES ('$k', '$v','$desc') ON DUPLICATE KEY UPDATE `v` = '$v';";
    return $DB->query($query);
}

//获取相对路径
function get_urlpath($srcurl, $baseurl)
{
    if (substr($srcurl, 0, 2) == "//") {
        return parse_url($baseurl)['scheme'] . ':' . $srcurl;
    }
    if (empty($srcurl)) {
        return '';
    }
    $srcinfo = parse_url($srcurl);
    if (isset($srcinfo['scheme'])) {
        return $srcurl;
    }
    $baseinfo = parse_url($baseurl);
    $url = $baseinfo['scheme'] . '://' . $baseinfo['host'];
    if (substr($srcinfo['path'], 0, 1) == '/') {
        $path = $srcinfo['path'];
    } else {
        $path = dirname($baseinfo['path']) . '/' . $srcinfo['path'];
    }
    $rst = array();
    $path_array = explode('/', $path);
    if (!$path_array[0]) {
        $rst[] = '';
    }
    foreach ($path_array as $key => $dir) {
        if ($dir == '..') {
            if (end($rst) == '..') {
                $rst[] = '..';
            } elseif (!array_pop($rst)) {
                $rst[] = '..';
            }
        } elseif ($dir && $dir != '.') {
            $rst[] = $dir;
        }
    }
    if (!end($path_array)) {
        $rst[] = '';
    }
    $url .= implode('/', $rst);
    if (!empty($srcinfo['query'])) {
        $url .= '?' . $srcinfo['query'];
    }
    return str_replace('\\', '/', $url);
}
//获取客户端IP
function get_real_ip()
{
    $real_ip = '';
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos = array_search('unknown', $arr);
        if (false !== $pos) {
            unset($arr[$pos]);
        }
        $real_ip = trim($arr[0]);
    } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $real_ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $real_ip = $_SERVER['REMOTE_ADDR'];
    }
    if (filter_var($real_ip, FILTER_VALIDATE_IP)) {
        return $real_ip;
    } else {
        return "";
    }
    //    return $real_ip;
}
function yan()
{
    $filename = ROOT . '/assets/data/data.dat';
    if (!file_exists($filename) || !is_readable($filename)) {
        die(' 一言数据文件不存在或不可读');
    }
    $data = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    // 随机获取一行索引
    $result = $data[array_rand($data)];
    // 去除多余的换行符
    $result = str_replace(["\r", "\n", "\r\n"], '', $result);
    return $result;
}
function rearr($data, $arr)
{
    $arr = str_replace('{group_id}', isset($data['group_id']) ? $data['group_id'] : '', $arr);
    $arr = str_replace('{group_name}', isset($data['group_name']) ? $data['group_name'] : '', $arr);
    $arr = str_replace('{group_icon}', isset($data['group_icon']) ? $data['group_icon'] : '', $arr);
    $arr = str_replace('{link_id}', isset($data['id']) ? $data['id'] : '', $arr);
    $arr = str_replace('{link_name}', isset($data['name']) ? $data['name'] : '', $arr);
    $url = isset($data['url']) ? ($GLOBALS['conf']["mode"] == 2 ? "/site-" . $data["id"] . ".html" : $data["url"]) : '';
    $arr = str_replace('{link_url}', $url, $arr);
    $arr = str_replace('{group_id}', $data['group_id'], $arr);
    $alt = isset($data['name']) ? $data['name'] : $data['group_name'];
    if (empty($data["icon"])) {
        $icon =  '<img src="/assets/img/default-icon.png" alt="' . strip_tags($alt) . '" />';
    } elseif (!preg_match("/^<svg*/", $data["icon"])) {
        $icon = '<img src="' . $data["icon"] . '" alt="' . strip_tags($alt) . '" />';
    } else {
        $icon = $data["icon"];
    }
    $arr = str_replace('{link_icon}', $icon, $arr);
    return $arr;
}
//获取head
function get_head($url, $cache = false)
{

    if ($cache && is_numeric($url)) {
        global $DB;
        $site_head =   $DB->get_row("SELECT * FROM `lylme_links` WHERE `id` = $url  AND  `link_pwd` = 0 ");
        $url = $site_head['url'];
        $cache_path = ROOT . "cache/";
        $cache_file = $cache_path . md5($url) . ".txt";
        if (file_exists($cache_file)) {
            // 获取缓存文件的修改时间
            $file_mtime = filemtime($cache_file);
            // 如果缓存文件未过期，则直接读取并返回数据
            if ((time() - $file_mtime) < 7 * 24 * 60 * 60) {
                return json_decode(file_get_contents($cache_file), true);
            }
        }
    }
    $data = get_curl($url);
    //获取网站title
    preg_match('/<title.*?>(?<title>.*?)<\/title>/sim', $data, $title);
    $encode = mb_detect_encoding($title['title'], array('GB2312', 'GBK', 'UTF-8', 'CP936'));
    //得到字符串编码
    $file_charset = iconv_get_encoding()['internal_encoding'];
    //当前文件编码
    if ($encode != 'CP936' && $encode != $file_charset) {
        $title =  iconv($encode, $file_charset, $title['title']);
        $data = iconv($encode, $file_charset, $data);
    } else {
        $title = $title['title'];
    }
    // 获取网站icon
    preg_match('/<link rel=".*?icon" * href="(.*?)".*?>/is', $data, $icon);
    preg_match('/<meta +name *=["\']?description["\']? *content=["\']?([^<>"]+)["\']?/i', $data, $description);
    preg_match('/<meta +name *=["\']?keywords["\']? *content=["\']?([^<>"]+)["\']?/i', $data, $keywords);
    $icon = $icon[1];
    if (!empty($icon)) {
        $icon = get_urlpath($icon, $url);
    } else {
        $parse = parse_url($url);
        $port = $parse['port'] == 80 || $parse['port'] == "" ? '' : ":" . $parse['port'];
        $iconurl = $parse['scheme'] . '://' . $parse['host'] . $port . '/favicon.ico';
        if (get_curl($iconurl) != 404) {
            $icon = $iconurl;
        }
    }
    $get_heads = array("title" => $title, "charset" => $encode, "icon" => $icon, "description" => $description[1], "keywords" => $keywords[1], "url" => $url);
    if ($cache && is_numeric($url)) {
        if (!file_exists($cache_path)) {
            mkdir($cache_path);
        }
        file_put_contents($cache_file, json_encode($get_heads));
    }
    return $get_heads;
}
//模拟GET请求
function get_curl($url)
{
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/101.0.4951.54 Safari/537.36 Edg/101.0.1210.39 Lylme/11.24'
        ),
    ));
    $contents = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    if ($httpCode == 404) {
        return $httpCode;
    }
    return $contents;
}
//长度判断
function strlens($str)
{
    if (strlen($str) > 255) {
        return true;
    } else {
        return false;
    }
}
//收录
function apply($name, $url, $icon, $group_id, $status)
{
    header('Content-Type:application/json; charset=utf-8');
    $name = strip_tags(daddslashes($name));
    $url = strip_tags(daddslashes($url));
    $icon = strip_tags(daddslashes($icon));
    $group_id = intval(strip_tags(daddslashes($group_id)));
    $userip = get_real_ip();

    $date = date("Y-m-d H:i:s");
    if (empty($name) || empty($url) || empty($group_id)) {
        //|| empty($icon)
        return ('{"code": "-1", "msg": "必填项不能为空"}');
    } elseif (!preg_match('/^http*/i', $url)) {
        return ('{"code": "-2", "msg": "链接不符合要求"}');
    } elseif (strlens($name) || strlens($url) || strlens($icon) || strlens($group_id) || strlens($userip)) {
        return ('{"code": "500", "msg": "非法参数"}');
    } else {
        global $DB;
        if ($DB->num_rows($DB->query("SELECT * FROM `lylme_apply` WHERE `apply_url` LIKE '" . $url . "';")) > 0) {
            return ('{"code": "-3", "msg": "链接已存在，请勿重复提交"}');
        }
        $sql = "INSERT INTO `lylme_apply` (`apply_name`, `apply_url`, `apply_group`, `apply_icon`, `apply_desc`, `apply_time`, `apply_status`) VALUES ( '$name', '$url', $group_id, '$icon', '$userip', '$date', $status);";
        if ($DB->query($sql)) {
            switch ($status) {
                case 0:
                    return ('{"code": "200", "msg": "请等待管理员审核"}');
                    break;
                case 1:
                    if (ins_link($name, $url, $icon, $group_id)) {
                        return ('{"code": "200", "msg": "网站已收录"}');
                    } else {
                        return ('{"code": "-5", "msg": "请联系网站管理员"}');
                    }
                    break;
            }
        } else {
            return ('{"code": "-4", "msg": "未知错误，请联系网站管理员"}');
        }
    }
}
function ins_link($name, $url, $icon, $group_id)
{
    global $DB;
    $name = strip_tags(daddslashes($name));
    $url = strip_tags(daddslashes($url));
    $icon = strip_tags(daddslashes($icon));
    $group_id = intval(strip_tags(daddslashes($group_id)));
    $link_order = intval($DB->count('select MAX(id) from `lylme_links`') + 1);
    $sql1 = "INSERT INTO `lylme_links` ( `name`, `group_id`, `url`, `icon`, `link_desc`,`link_order`) VALUES (' $name', $group_id, '$url', '$icon', '', $link_order);";
    if ($DB->query($sql1)) {
        return true;
    } else {
        return false;
    }
}


/**
 * 获取主题自定义设置
 * @Author: LyLme
 * @param string $name 参数名称
 * @param mixed  $default 默认值
 * @return mixed 主题参数值
 */
function theme_config($name, $default = '')
{
    $config = $GLOBALS['conf'];
    $theme_name = "theme_config_" . $config['template']; //当前主题配置key;

    if (isset($config[$theme_name])) {
        $theme_themes = json_decode($config[$theme_name], true); //当前主题所有配置
        //从后台配置中获取
        return $theme_themes[$name];
    }

    $theme_config_path = ROOT . 'template/' . $config['template'] . '/config.php';
    if (file_exists($theme_config_path) && (@require $theme_config_path) !== false && is_array($theme_config)) {
        //从主题默认配置中获取
        foreach ($theme_config as $config_item) {
            // 检查当前配置项是否为 $name
            if ($config_item['name'] == $name) {
                $value = array_key_exists("value", $config_item) ? $config_item['value'] : $default;
                break;
            }
        }
        return $value;
    }
    //返回默认值
    return $default;
}
