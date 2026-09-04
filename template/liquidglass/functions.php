<?php
/**
 * LiquidGlass · 六零导航页主题函数库
 * 说明：以下 theme_version / theme_css / theme_js / theme_e / theme_tags / theme_sou /
 *      theme_icp / theme_security_filing 为程序契约函数，原样保留；
 *      theme_lg_* 为本主题自定义扩展函数。
 * 兼容：PHP 5.4+ / 7.x / 8.x
 */

if (!function_exists('theme_version')) {
    function theme_version() {
        static $version = null;
        if ($version === null) {
            $ini = @file_get_contents(__DIR__ . '/theme.ini');
            $info = is_string($ini) ? json_decode($ini, true) : array();
            $raw = (is_array($info) && isset($info['theme_version'])) ? $info['theme_version'] : '1.0.0';
            $version = preg_replace('/[^a-zA-Z0-9]/', '', $raw);
            if ($version === '') { $version = '100'; }
        }
        return $version;
    }
}

if (!function_exists('theme_css')) {
    function theme_css($path = 'css/style.css') {
        global $templatepath;
        $href = rtrim($templatepath, '/') . '/' . ltrim($path, '/') . '?v=' . theme_version();
        echo '<link rel="stylesheet" href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '">' . "\n";
    }
}

if (!function_exists('theme_js')) {
    function theme_js($path = 'js/script.js') {
        global $templatepath;
        $src = rtrim($templatepath, '/') . '/' . ltrim($path, '/') . '?v=' . theme_version();
        echo '<script defer src="' . htmlspecialchars($src, ENT_QUOTES, 'UTF-8') . '"></script>' . "\n";
    }
}

if (!function_exists('theme_e')) {
    function theme_e($value) {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('theme_tags')) {
    function theme_tags() {
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
}

if (!function_exists('theme_sou')) {
    function theme_sou() {
        global $site, $DB;
        $list = array();
        $result = $site->getSou();
        while ($row = $DB->fetch($result)) {
            if ((int) $row['sou_st'] !== 1) { continue; }
            $link = $row['sou_link'];
            if (checkmobile() && !empty($row['sou_waplink'])) { $link = $row['sou_waplink']; }
            $list[] = array(
                'alias' => $row['sou_alias'], 'name' => $row['sou_name'],
                'hint'  => $row['sou_hint'],  'icon' => $row['sou_icon'],
                'color' => $row['sou_color'], 'link' => $link,
            );
        }
        return $list;
    }
}

if (!function_exists('theme_icp')) {
    function theme_icp() {
        $icp = isset($GLOBALS['conf']['icp']) ? trim((string) $GLOBALS['conf']['icp']) : '';
        if ($icp === '') { return; }
        echo '<a class="icp" href="https://beian.miit.gov.cn/" target="_blank" rel="nofollow noopener">'
            . theme_e($icp) . '</a>' . "\n";
    }
}

if (!function_exists('theme_security_filing')) {
    function theme_security_filing($record = 'gonganbei') {
        $record = trim((string) theme_config($record, ''));
        if ($record === '') { return; }
        preg_match_all('/\d+/', $record, $gab);
        $code = isset($gab[0][0]) ? $gab[0][0] : '';
        echo '<a class="gonganbei" href="http://www.beian.gov.cn/portal/registerSystemInfo?recordcode='
            . theme_e($code) . '" target="_blank" rel="nofollow noopener">'
            . theme_e($record) . '</a>' . "\n";
    }
}

/* ------------------------------------------------------------------
 * 本主题扩展：theme_lg_*
 * ------------------------------------------------------------------ */

/** 枚举兜底：配置项只允许在给定白名单内取值 */
if (!function_exists('theme_lg_enum')) {
    function theme_lg_enum($value, $allowed, $default) {
        $value = (string) $value;
        if (in_array($value, $allowed, true)) { return $value; }
        return $default;
    }
}

/**
 * SF Symbols 风格内联图标（stroke 线性，随 currentColor 变化）
 * 仅用于本主题自带的界面控件，与系统注入的站点图标互不冲突
 */
if (!function_exists('theme_lg_glyph')) {
    function theme_lg_glyph($name) {
        $attr = ' viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"'
              . ' stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"';
        $body = '';
        switch ($name) {
            case 'apple':
                return '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false">'
                    . '<path d="M16.4 12.7c0-2.2 1.8-3.3 1.9-3.4-1-1.5-2.6-1.7-3.2-1.7-1.3-.1-2.6.8-3.3.8-.7 0-1.7-.8-2.8-.8-1.4 0-2.8.8-3.5 2.1-1.5 2.6-.4 6.5 1.1 8.6.7 1 1.6 2.2 2.7 2.2 1.1 0 1.5-.7 2.8-.7s1.6.7 2.7.7c1.1 0 1.9-1.1 2.6-2.1.8-1.2 1.1-2.3 1.2-2.4-.1 0-2.2-.9-2.2-3.3z'
                    . 'M14.3 5.8c.6-.7 1-1.7.9-2.7-.9 0-2 .6-2.6 1.3-.6.6-1 1.6-.9 2.6 1 .1 2-.5 2.6-1.2z"/></svg>';
            case 'search':
                $body = '<circle cx="11" cy="11" r="6.8"/><path d="M16.2 16.2 20.5 20.5"/>';
                break;
            case 'sliders':
                $body = '<path d="M4 8h9M17.5 8H20M4 16h3.5M12 16h8"/><circle cx="15" cy="8" r="2.1"/><circle cx="9.8" cy="16" r="2.1"/>';
                break;
            case 'gear':
                $body = '<circle cx="12" cy="12" r="3.1"/><path d="M12 3.6v2.1M12 18.3v2.1M5 7.9l1.8 1M17.2 15.1l1.8 1M5 16.1l1.8-1M17.2 8.9l1.8-1"/>';
                break;
            case 'squares':
                $body = '<rect x="4" y="4" width="7" height="7" rx="2"/><rect x="13" y="4" width="7" height="7" rx="2"/><rect x="4" y="13" width="7" height="7" rx="2"/><rect x="13" y="13" width="7" height="7" rx="2"/>';
                break;
            case 'compass':
                $body = '<circle cx="12" cy="12" r="8.4"/><path d="M15 9l-1.7 4.4L9 15l1.7-4.4z"/>';
                break;
            case 'bookmark':
                $body = '<path d="M7 4.5h10a1 1 0 0 1 1 1V20l-6-3.6L6 20V5.5a1 1 0 0 1 1-1z"/>';
                break;
            case 'wifi':
                $body = '<path d="M2.6 9.2a13 13 0 0 1 18.8 0M6.1 12.7a8 8 0 0 1 11.8 0M9.6 16.2a3.6 3.6 0 0 1 4.8 0"/><circle cx="12" cy="19.2" r="1" fill="currentColor" stroke="none"/>';
                break;
            case 'signal':
                $body = '<path d="M3.5 17.5v3M8.5 13.5v7M13.5 9.5v11M18.5 5.5v15"/>';
                break;
            case 'chevron':
                $body = '<path d="M9.5 6l6 6-6 6"/>';
                break;
            case 'clock':
                $body = '<circle cx="12" cy="12" r="8.4"/><path d="M12 7.4V12l3.2 2"/>';
                break;
            case 'sparkle':
                $body = '<path d="M12 4.2l1.4 4.1 4.1 1.4-4.1 1.4L12 15.2l-1.4-4.1L6.5 9.7l4.1-1.4z"/><path d="M18.4 15.6l.7 2 2 .7-2 .7-.7 2-.7-2-2-.7 2-.7z"/>';
                break;
            case 'eye':
                $body = '<path d="M3.8 12S6.4 7.4 12 7.4 20.2 12 20.2 12 17.6 16.6 12 16.6 3.8 12 3.8 12z"/><circle cx="12" cy="12" r="2.6"/>';
                break;
            case 'link':
                $body = '<path d="M10.2 13.8a3.4 3.4 0 0 0 4.9 0l2.9-2.9a3.4 3.4 0 0 0-4.9-4.9l-1 1"/><path d="M13.8 10.2a3.4 3.4 0 0 0-4.9 0l-2.9 2.9a3.4 3.4 0 0 0 4.9 4.9l1-1"/>';
                break;
            case 'minus':
                $body = '<circle cx="12" cy="12" r="8.4"/><path d="M8.6 12h6.8"/>';
                break;
            case 'back':
                $body = '<path d="M14 6l-6 6 6 6"/>';
                break;
            case 'forward':
                $body = '<path d="M10 6l6 6-6 6"/>';
                break;
            default:
                $body = '<circle cx="12" cy="12" r="8.4"/>';
                break;
        }
        return '<svg' . $attr . '>' . $body . '</svg>';
    }
}

/** 电池组件（iOS / macOS 状态栏通用） */
if (!function_exists('theme_lg_battery')) {
    function theme_lg_battery($cls = '') {
        return '<span class="lg-batt ' . theme_e($cls) . '" aria-hidden="true">'
             . '<span class="lg-batt-shell"><span class="lg-batt-level" data-lg-batt></span></span>'
             . '<span class="lg-batt-cap"></span></span>';
    }
}
