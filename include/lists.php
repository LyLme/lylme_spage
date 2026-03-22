<?php
/* 
 * @Description: 渲染页面
 * @Author: LyLme admin@lylme.com
 * @Date: 2024-01-23 12:25:35
 * @LastEditors: LyLme admin@lylme.com
 * @LastEditTime: 2024-04-14 05:43:14
 * @FilePath: /lylme_spage/include/lists.php
 * @Copyright (c) 2024 by LyLme, All Rights Reserved. 
 */
// +----------------------------------------------------------+
// | LyLme Spage                                              |
// +----------------------------------------------------------+
// | Copyright (c) 2022 LyLme                                 |
// +----------------------------------------------------------+
// | File: lists.php                                          |
// +----------------------------------------------------------+
// | Authors: LyLme <admin@lylme.com>                         |
// | date: 2022-06-10                                         |
// +----------------------------------------------------------+

// 初始化全局变量，防止未定义错误
if (!isset($conf)) {
    $conf = [];
}

if (!isset($GLOBALS['conf'])) {
    $GLOBALS['conf'] = &$conf;
}

/**
 * 渲染链接列表
 * @param array $htmls HTML模板数组
 * @return void
 */
function lists($htmls)
{
    global $DB, $conf;

    // 安全检查：确保$DB和$conf已定义
    if (!isset($DB) || !($DB instanceof DB)) {
        error_log("Database connection not initialized in lists() function");
        return;
    }

    $groups = $DB->query("SELECT * FROM `lylme_groups` ORDER BY `group_order` ASC");
    if ($groups === false) {
        error_log("Failed to query groups from database");
        return;
    }

    $i = 0;
    $sessionList = isset($_SESSION['list']) ? $_SESSION['list'] : [];

    while ($group = $DB->fetch($groups)) {
        if ($group === false) {
            continue;
        }

        $html = rearr($group, $htmls);

        if (isset($group["group_status"]) && $group["group_status"] == '0') {
            continue;
        }

        $groupPwd = isset($group['group_pwd']) ? $group['group_pwd'] : '';
        if (!empty($groupPwd) && !in_array($groupPwd, $sessionList, true)) {
            continue;
        }

        $groupId = isset($group['group_id']) ? (int) $group['group_id'] : 0;
        $sql = "SELECT * FROM `lylme_links` WHERE `group_id` = " . $groupId . " ORDER BY `link_order` ASC;";
        $group_links = $DB->query($sql);

        if ($group_links === false) {
            if (isset($html['g1']) && isset($html['g2'])) {
                echo $html['g1'] . $html['g2'];
            }
            if (isset($html['g3'])) {
                echo $html['g3'] . "\n\n";
            }
            $i = 0;
            continue;
        }

        $link_num = $DB->num_rows($group_links);

        if (isset($html['g1']) && isset($html['g2'])) {
            echo $html['g1'] . $html['g2'];
        }

        if ($link_num == 0) {
            if (isset($html['g3'])) {
                echo $html['g3'] . "\n\n";
            }
            $i = 0;
            continue;
        }

        while ($link = $DB->fetch($group_links)) {
            if ($link === false) {
                break;
            }

            $html = rearr($link, $htmls);
            $lpwd = true;

            if ($link_num > $i) {
                $i++;

                $linkPwd = isset($link['link_pwd']) ? $link['link_pwd'] : '';
                if (empty($groupPwd) && !empty($linkPwd) && !in_array($linkPwd, $sessionList, true)) {
                    $lpwd = false;
                }

                $linkStatus = isset($link['link_status']) ? $link['link_status'] : 1;
                if ($linkStatus && $lpwd && isset($html['l1']) && isset($html['l2']) && isset($html['l3'])) {
                    echo "\n" . $html['l1'] . $html['l2'] . $html['l3'];
                }
            }

            if ($link_num == $i) {
                if (isset($html['g3'])) {
                    echo $html['g3'] . "\n\n";
                }
                $i = 0;
                break;
            }
        }
    }
}

/**
 * 生成JSON格式的链接列表
 * @return array
 */
function listjson()
{
    global $DB, $conf;

    // 安全检查
    if (!isset($DB) || !($DB instanceof DB)) {
        error_log("Database connection not initialized in listjson() function");
        return [];
    }

    $groups = $DB->query("SELECT * FROM `lylme_groups` ORDER BY `group_order` ASC");
    if ($groups === false) {
        error_log("Failed to query groups from database in listjson()");
        return [];
    }

    $i = 0;
    $g = 0;
    $arr = [];
    $sessionList = isset($_SESSION['list']) ? $_SESSION['list'] : [];

    while ($group = $DB->fetch($groups)) {
        if ($group === false) {
            continue;
        }

        if (isset($group["group_status"]) && $group["group_status"] == '0') {
            continue;
        }

        $groupPwd = isset($group['group_pwd']) ? $group['group_pwd'] : '';
        if (!empty($groupPwd) && !in_array($groupPwd, $sessionList, true)) {
            continue;
        }

        $groupId = isset($group['group_id']) ? (int) $group['group_id'] : 0;
        $sql = "SELECT * FROM `lylme_links` WHERE `group_id` = " . $groupId . " ORDER BY `link_order` ASC;";
        $group_links = $DB->query($sql);

        if ($group_links === false) {
            continue;
        }

        $link_num = $DB->num_rows($group_links);
        $arr[$g] = [
            "id" => $groupId,
            "title" => isset($group['group_name']) ? $group['group_name'] : '',
            "icon" => isset($group['group_icon']) ? $group['group_icon'] : '',
            "items" => []
        ];

        while ($link = $DB->fetch($group_links)) {
            if ($link === false) {
                break;
            }

            $linkId = isset($link['id']) ? (int) $link['id'] : 0;
            $linkName = isset($link['name']) ? $link['name'] : '';
            $linkIcon = isset($link['icon']) ? $link['icon'] : '';
            $linkUrl = isset($link['url']) ? $link['url'] : '';

            $arr[$g]['items'][] = [
                "id" => $linkId,
                "title" => $linkName,
                "alias" => 'link' . $linkId,
                "keyword" => $linkName,
                "category_id" => $groupId,
                "icon" => $linkIcon,
                "url" => $linkUrl,
                "out" => true
            ];

            $lpwd = true;
            if ($link_num > $i) {
                $i++;

                $linkPwd = isset($link['link_pwd']) ? $link['link_pwd'] : '';
                if (empty($groupPwd) && !empty($linkPwd) && !in_array($linkPwd, $sessionList, true)) {
                    $lpwd = false;
                }

                $linkStatus = isset($link['link_status']) ? $link['link_status'] : 1;
                if ($linkStatus && $lpwd) {
                    // 链接正常显示
                }
            }
        }
        $g++;
    }

    return $arr;
}

/**
 * 检查字符串是否包含子串
 * @param string $string 原始字符串
 * @param string $find 查找的子串
 * @return bool
 */
function strexists($string, $find)
{
    if (!is_string($string) || !is_string($find)) {
        return false;
    }
    return strpos($string, $find) !== false;
}

/**
 * 检查字符串是否包含数组中的任意子串
 * @param string $string 原始字符串
 * @param array $arr 子串数组
 * @return bool
 */
function dstrpos($string, $arr)
{
    if (!is_string($string) || empty($string) || !is_array($arr) || empty($arr)) {
        return false;
    }

    foreach ($arr as $v) {
        if (is_string($v) && strpos($string, $v) !== false) {
            return true;
        }
    }
    return false;
}

/**
 * 判断是否为移动端
 * @return bool
 */
function checkmobile()
{
    // 检查是否通过命令行访问
    if (php_sapi_name() === 'cli' || !isset($_SERVER['HTTP_USER_AGENT'])) {
        return false;
    }

    $useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
    $ualist = ['android', 'midp', 'nokia', 'mobile', 'iphone', 'ipod', 'blackberry', 'windows phone'];

    // 检查HTTP_ACCEPT
    $httpAccept = isset($_SERVER['HTTP_ACCEPT']) ? strtolower($_SERVER['HTTP_ACCEPT']) : '';

    // 检查HTTP_VIA
    $httpVia = isset($_SERVER['HTTP_VIA']) ? strtolower($_SERVER['HTTP_VIA']) : '';

    if (
        dstrpos($useragent, $ualist) ||
        strexists($httpAccept, "vnd.wap") ||
        strexists($httpVia, "wap")
    ) {
        return true;
    }

    // 额外检查：触摸屏设备
    if (
        isset($_SERVER['HTTP_X_WAP_PROFILE']) ||
        isset($_SERVER['HTTP_PROFILE']) ||
        (isset($_SERVER['HTTP_ACCEPT']) &&
            (strpos($_SERVER['HTTP_ACCEPT'], 'text/vnd.wap.wml') !== false ||
                strpos($_SERVER['HTTP_ACCEPT'], 'application/vnd.wap.xhtml+xml') !== false))
    ) {
        return true;
    }

    return false;
}

/**
 * 获取CDN公共路径
 * @param string|null $cdnpublic CDN配置
 * @return string
 */
function cdnpublic($cdnpublic = null)
{
    global $conf;

    // 如果未传入参数，尝试从全局配置获取
    if ($cdnpublic === null && isset($conf['cdnpublic'])) {
        $cdnpublic = $conf['cdnpublic'];
    }

    if (empty($cdnpublic)) {
        return '.';
    }

    // 获取版本号
    $version = '';
    if (isset($GLOBALS['version'])) {
        $version = $GLOBALS['version'];
    } elseif (defined('VERSION')) {
        $version = VERSION;
    }

    return rtrim($cdnpublic, '/') . '/' . ltrim($version, '/');
}

// === 初始化变量 ===
// 使用更安全的变量初始化方式

// 初始化全局变量
if (!isset($GLOBALS['version'])) {
    $GLOBALS['version'] = defined('VERSION') ? VERSION : '1.0.0';
}

// 获取CDN路径
$cdnpublic = cdnpublic(isset($conf['cdnpublic']) ? $conf['cdnpublic'] : null);

// 获取模板路径
$template = isset($conf["template"]) ? $conf["template"] : 'default';
$templatepath = './template/' . $template;

// 检查模板目录是否存在
if (!is_dir($templatepath) && defined('DEBUG') && DEBUG === true) {
    error_log("Template directory not found: {$templatepath}");
}

$templateFile = $templatepath . '/index.php';

// 检查模板文件是否存在
if (!file_exists($templateFile) && defined('DEBUG') && DEBUG === true) {
    error_log("Template file not found: {$templateFile}");
}

// 设置背景图片
$background = isset($conf["background"]) ? $conf["background"] : '';
$wap_background = isset($conf["wap_background"]) ? $conf["wap_background"] : '';
$background_img = '';

if (checkmobile()) {
    if (!empty($wap_background)) {
        $background_img = $wap_background;
    } else {
        $background_img = $background;
    }
} else {
    $background_img = $background;
}

// 确保背景图片变量被定义
if (empty($background_img) && isset($conf["background"])) {
    $background_img = $conf["background"];
}