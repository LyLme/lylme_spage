<?php
/**
 * Palette 主题辅助函数库
 *
 * 引入方式：在 index.php 顶部
 *     require_once __DIR__ . '/functions.php';
 *
 * 约定：所有函数以 theme_ 为前缀，避免与核心函数冲突。
 * 兼容 PHP 5.4+：统一使用 array() 语法，不使用 PHP 7+ 专有语法。
 */

/**
 * 读取 theme.ini 中的 theme_version，去除非字母数字字符（1.1.0 → 110）
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
 * 输出样式标签，自动附加版本参数
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
 * 输出脚本标签（defer 异步加载，自动附加版本参数）
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
 * 获取导航菜单
 *
 * @return array 每项含 name / link / blank（是否新窗口打开）
 */
function theme_tags()
{
    global $site, $DB;
    $tags = array();
    $result = $site->getTags();
    while ($row = $DB->fetch($result)) {
        $tags[] = array(
            'name'  => $row['tag_name'],
            'link'  => $row['tag_link'],
            'blank' => ((int) $row['tag_target'] === 1),
        );
    }
    return $tags;
}

/**
 * 获取启用的搜索引擎
 * link 字段已按当前设备解析：移动端优先使用 sou_waplink
 *
 * @return array 每项含 alias / name / hint / icon / link
 */
function theme_sou()
{
    global $site, $DB;
    $list = array();
    // 设备 UA 只在循环外解析一次
    $is_mobile = checkmobile();
    // SITE::getSou() 的 SQL 已按 sou_st = 1 过滤，无需在此重复判断
    $result = $site->getSou();
    while ($row = $DB->fetch($result)) {
        $link = $row['sou_link'];
        if ($is_mobile && !empty($row['sou_waplink'])) {
            $link = $row['sou_waplink'];
        }
        $list[] = array(
            'alias' => $row['sou_alias'],
            'name'  => $row['sou_name'],
            'hint'  => $row['sou_hint'],
            'icon'  => $row['sou_icon'],
            'link'  => $link,
        );
    }
    return $list;
}

/**
 * 输出 ICP 备案链接（后台「ICP备案号」留空时不输出）
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
 * @param string $record 主题配置中的公安备案号配置项名（默认 gonganbei）
 * @param string|null $value 已读取的备案号；传入时不再重复读取主题配置
 */
function theme_security_filing($record = 'gonganbei', $value = null)
{
    $record = trim((string) ($value === null ? theme_config($record, '') : $value));
    if ($record === '') {
        return;
    }
    preg_match_all('/\d+/', $record, $gab);
    $code = isset($gab[0][0]) ? $gab[0][0] : '';
    // 提取不到备案编号时降级为纯文本，避免输出 recordcode= 空参数的无效外链
    if ($code === '') {
        echo '<span class="gonganbei">' . theme_e($record) . '</span>' . "\n";
        return;
    }
    echo '<a class="gonganbei" href="http://www.beian.gov.cn/portal/registerSystemInfo?recordcode='
        . theme_e($code) . '" target="_blank" rel="nofollow noopener">'
        . theme_e($record) . '</a>' . "\n";
}

/**
 * 输出图标雪碧图脚本
 *
 * 官方规范要求固定引入 assets/js/icon.js 与 assets/js/svg.js：
 * 数据库中全部分组 / 链接 / 搜索引擎图标都是 <use xlink:href="#lyicon-*">，缺失即图标空白。
 * 因此这里无条件输出两行，不做本地文件探测——即使 $cdnpublic 指向远程 CDN、
 * 本地 assets 目录不存在，也能按 CDN 路径正常加载。
 * 两个脚本自身会等待 DOM 就绪，统一加 defer，避免阻塞解析、拖慢 script.js 的初始化。
 */
function theme_icon_scripts()
{
    global $cdnpublic;
    $base = (isset($cdnpublic) && $cdnpublic !== '') ? rtrim($cdnpublic, '/') : '.';
    echo '<script defer src="' . htmlspecialchars($base . '/assets/js/icon.js', ENT_QUOTES, 'UTF-8') . '"></script>' . "\n";
    echo '<script defer src="' . htmlspecialchars($base . '/assets/js/svg.js', ENT_QUOTES, 'UTF-8') . '"></script>' . "\n";
}

function theme_go_enabled()
{
    $value = theme_config('go_enable', '');
    return is_string($value) && $value === 'on';
}