<?php
/**
 * 主题辅助函数库
 *
 * 引入方式：在 index.php 顶部
 *     require_once __DIR__ . '/functions.php';
 *
 * 约定：
 *   所有函数务必以 theme_ 为前缀，避免与核心函数冲突
 *
 */

/**
 * 读取 theme.ini 中的 theme_version，去除非字母数字字符（1.0.0 → 100）
 * 用于 css/js 的缓存版本参数，静态缓存避免重复读文件
 *
 * @return string
 */
function theme_version()
{
    static $version = null;
    if ($version === null) {
        $ini = @file_get_contents(__DIR__ . '/theme.ini');
        $info = is_string($ini) ? json_decode($ini, true) : array();
        $raw = (is_array($info) && isset($info['theme_version'])) ? $info['theme_version'] : '1.0.0';
        $version = preg_replace('/[^a-zA-Z0-9]/', '', $raw);
        if ($version === '') {
            $version = '100';
        }
    }
    return $version;
}

/**
 * 输出css样式标签（自动附加版本参数）
 *
 * @param string $path 相对主题目录的样式路径
 */
function theme_css($path = 'css/style.css')
{
    global $templatepath;
    $href = rtrim($templatepath, '/') . '/' . ltrim($path, '/') . '?v=' . theme_version();
    echo '<link rel="stylesheet" href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '">' . "\n";
}

/**
 * 输出js脚本标签（defer加载[异步加载（不阻塞 HTML 解析）]，自动附加版本参数）
 *
 * @param string $path 相对主题目录的脚本路径
 */
function theme_js($path = 'js/script.js')
{
    global $templatepath;
    $src = rtrim($templatepath, '/') . '/' . ltrim($path, '/') . '?v=' . theme_version();
    echo '<script defer src="' . htmlspecialchars($src, ENT_QUOTES, 'UTF-8') . '"></script>' . "\n";
}

/**
 * HTML 转义
 *
 * @param mixed $value
 * @return string
 */
function theme_e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * 获取导航菜单（替代 $site->getTags() + while($DB->fetch()) 的样板代码）
 *
 * @return array 每项含 id / name / link / blank(是否新窗口打开)
 */
function theme_tags()
{
    global $site, $DB;
    $tags = array();
    $result = $site->getTags();
    while ($row = $DB->fetch($result)) {
        $tags[] = array(
            'id'    => $row['tag_id'],
            'name'  => $row['tag_name'],
            'link'  => $row['tag_link'],
            'blank' => ((int) $row['tag_target'] === 1),
        );
    }
    return $tags;
}

/**
 * 获取启用的搜索引擎（替代 $site->getSou() + 循环 + checkmobile() 判断）
 * link 字段已按当前设备解析：移动端优先使用 sou_waplink
 *
 * @return array 每项含 alias / name / hint / icon / color / link
 */
function theme_sou()
{
    global $site, $DB;
    $list = array();
    $result = $site->getSou();
    while ($row = $DB->fetch($result)) {
        if ((int) $row['sou_st'] !== 1) {
            continue;
        }
        $link = $row['sou_link'];
        if (checkmobile() && !empty($row['sou_waplink'])) {
            $link = $row['sou_waplink'];
        }
        $list[] = array(
            'alias' => $row['sou_alias'],
            'name'  => $row['sou_name'],
            'hint'  => $row['sou_hint'],
            'icon'  => $row['sou_icon'],
            'color' => $row['sou_color'],
            'link'  => $link,
        );
    }
    return $list;
}

/**
 * 输出 ICP 备案链接（后台「ICP备案号」留空时不输出）
 *
 * 读取系统配置 $conf['icp']，链接指向工信部备案查询
 */
function theme_icp()
{
    $icp = isset($GLOBALS['conf']['icp']) ? trim((string) $GLOBALS['conf']['icp']) : '';
    if ($icp === '') {
        return;
    }
    echo '<a class="icp" href="https://beian.miit.gov.cn/" target="_blank" rel="nofollow noopener">'
        . theme_e($icp) . '</a>' . "\n";
}

/**
 * 输出公安备案链接（主题配置项留空时不输出）
 *
 * 备案号中的数字用于拼接公安备案查询官网的 recordcode 参数
 *
 * @param string $record  主题配置中的公安备案号配置项名[默认值：gonganbei]
 */
function theme_security_filing($record  = 'gonganbei')
{
    $record = trim((string) theme_config($record, ''));
    if ($record === '') {
        return;
    }
    preg_match_all('/\d+/', $record, $gab);
    $code = isset($gab[0][0]) ? $gab[0][0] : '';
    echo '<a class="gonganbei" href="http://www.beian.gov.cn/portal/registerSystemInfo?recordcode='
        . theme_e($code) . '" target="_blank" rel="nofollow noopener">'
        . theme_e($record) . '</a>' . "\n";
}
