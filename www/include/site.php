<?php

/**
 * SITE类 - 网站相关数据库操作
 * 继承自DB类
 */

class SITE extends DB
{
    /**
     * 构造函数
     */
    public function __construct($db_host, $db_user, $db_pass, $db_name, $db_port)
    {
        parent::__construct($db_host, $db_user, $db_pass, $db_name, $db_port);
    }

    /**
     * 获取分组信息
     * @return object
     */
    public function getGroups()
    {
        $condition_str = 'AND `group_pwd` = 0 ';

        // 安全处理session变量
        $session_list = isset($_SESSION['list']) ? $_SESSION['list'] : array();
        if (is_array($session_list)) {
            foreach ($session_list as $condition) {
                $safe_condition = is_numeric($condition) ? (int)$condition : 0;
                $condition_str .= ' OR `group_pwd` = ' . $safe_condition;
            }
        }

        return $this->query("SELECT * FROM `lylme_groups` WHERE `group_status` = 1 " . $condition_str . " ORDER BY `group_order` ASC");
    }

    /**
     * 获取指定分组
     * @param int $group_id 分组ID
     * @return object
     */
    public function getCategorys($group_id)
    {
        $group_id = (int)$group_id;
        return $this->query("SELECT * FROM `lylme_groups` WHERE `group_status` = 1 AND `group_id` = " . $group_id . " LIMIT 1");
    }

    /**
     * 获取分组链接
     * @param int $group_id 分组ID
     * @return object
     */
    public function getCategoryLinks($group_id)
    {
        $group_id = (int)$group_id;
        return $this->query("SELECT * FROM `lylme_links` WHERE `group_id` = " . $group_id . " ORDER BY `link_order` ASC");
    }

    /**
     * 获取链接
     * @param int $link_id 链接ID
     * @return object
     */
    public function getLink($link_id)
    {
        $link_id = (int)$link_id;
        // 修复SQL错误: ADN -> AND
        return $this->get_row("SELECT * FROM `lylme_links` WHERE `id` = " . $link_id . " AND `link_pwd` = 0");
    }

    /**
     * 获取标签菜单
     * @return object
     */
    public function getTags()
    {
        return $this->query("SELECT * FROM `lylme_tags` ORDER BY `lylme_tags`.`sort` ASC");
    }

    /**
     * 获取搜索引擎
     * @return object
     */
    public function getSou()
    {
        return $this->query("SELECT * FROM `lylme_sou` WHERE `sou_st` = 1 ORDER BY `lylme_sou`.`sou_order` ASC");
    }
}

// 创建SITE实例 - 兼容旧代码
$site = null;
if (isset($dbconfig) && is_array($dbconfig)) {
    $site = new SITE(
    isset($dbconfig['host']) ? $dbconfig['host'] : 'localhost',
    isset($dbconfig['user']) ? $dbconfig['user'] : '',
    isset($dbconfig['pwd']) ? $dbconfig['pwd'] : '',
    isset($dbconfig['dbname']) ? $dbconfig['dbname'] : '',
    isset($dbconfig['port']) ? $dbconfig['port'] : 3306
);
}

// 安全检查：确保必要的常量已定义
if (!defined('IN_CRONLITE')) {
    define('IN_CRONLITE', true);
}

if (!defined('ADMIN_PATH')) {
    define('ADMIN_PATH', 'admin');
}

if (!defined('DEBUG')) {
    define('DEBUG', false);
}

if (!defined('ROOT')) {
    define('ROOT', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
}

// 初始化全局变量
$conf = isset($conf) ? $conf : array();
$GLOBALS['conf'] = &$conf;

//拦截开关(1为开启，0关闭)
$webscan_switch = 1;
//提交方式拦截(1开启拦截,0关闭拦截,post,get,cookie,referre选择需要拦截的方式)
$webscan_post = 1;
$webscan_get = 1;
$webscan_cookie = 1;
$webscan_referre = 1;
//后台白名单,后台操作将不会拦截,添加"|"隔开白名单目录下面默认是网址带 admin  /dede/ 放行
$webscan_white_directory = '^\/' . ADMIN_PATH . '\/set\.php$';
//url白名单,可以自定义添加url白名单
$webscan_white_url = [
    '/' . ADMIN_PATH . '/ajax_theme.php' => "set=save",
    '/' . ADMIN_PATH . '/about.php' => "set=conf_submit"
];

//get拦截规则
$getfilter = "\\<.+javascript:window\\[.{1}\\\\x|<.*=(&#\\d+?;?)+?>|<.*(data|src)=data:text\\/html.*>|\\b(alert\\(|confirm\\(|expression\\(|prompt\\(|benchmark\s*?\(.*\)|sleep\s*?\(.*\)|\\b(group_)?concat[\\s\\/\\*]*?\\([^\\)]+?\\)|\bcase[\s\/\*]*?when[\s\/\*]*?\([^\)]+?\)|load_file\s*?\\()|<[a-z]+?\\b[^>]*?\\bon([a-z]{4,})\s*?=|^\\+\\/v(8|9)|\\b(and|or)\\b\\s*?([\\(\\)'\"\\d]+?=[\\(\\)'\"\\d]+?|[\\(\\)'\"a-zA-Z]+?=[\\(\\)'\"a-zA-Z]+?|>|<|\s+?[\\w]+?\\s+?\\bin\\b\\s*?\(|\\blike\\b\\s+?[\"'])|\\/\\*.*\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT\s*(\(.+\)\s*|@{1,2}.+?\s*|\s+?.+?|(`|'|\").*?(`|'|\")\s*)|UPDATE\s*(\(.+\)\s*|@{1,2}.+?\s*|\s+?.+?|(`|'|\").*?(`|'|\")\s*)SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE)@{0,2}(\\(.+\\)|\\s+?.+?\\s+?|(`|'|\").*?(`|'|\"))FROM(\\(.+\\)|\\s+?.+?|(`|'|\").*?(`|'|\"))|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)|<.*(iframe|frame|style|embed|object|frameset|meta)";

//post拦截规则
$postfilter = "<.*=(&#\\d+?;?)+?>|<.*data=data:text\\/html.*>|\\b(alert\\(|confirm\\(|expression\\(|prompt\\(|benchmark\s*?\(.*\)|sleep\s*?\(.*\)|\\b(group_)?concat[\\s\\/\\*]*?\\([^\\)]+?\\)|\bcase[\s\/\*]*?when[\s\/\*]*?\([^\)]+?\)|load_file\s*?\\()|<[^>]*?\\b(onerror|onmousemove|onload|onclick|onmouseover)\\b|\\b(and|or)\\b\\s*?([\\(\\)'\"\\d]+?=[\\(\\)'\"\\d]+?|[\\(\\)'\"a-zA-Z]+?=[\\(\\)'\"a-zA-Z]+?|>|<|\s+?[\\w]+?\\s+?\\bin\\b\\s*?\(|\\blike\\b\\s+?[\"'])|\\/\\*.*\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT\s*(\(.+\)\s*|@{1,2}.+?\s*|\s+?.+?|(`|'|\").*?(`|'|\")\s*)|UPDATE\s*(\(.+\)\s*|@{1,2}.+?\s*|\s+?.+?|(`|'|\").*?(`|'|\")\s*)SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE)(\\(.+\\)|\\s+?.+?\\s+?|(`|'|\").*?(`|'|\"))FROM(\\(.+\\)|\\s+?.+?|(`|'|\").*?(`|'|\"))|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)|<.*(iframe|frame|style|embed|object|frameset|meta)";

//cookie拦截规则
$cookiefilter = "benchmark\s*?\(.*\)|sleep\s*?\(.*\)|load_file\s*?\\(|\\b(and|or)\\b\\s*?([\\(\\)'\"\\d]+?=[\\(\\)'\"\\d]+?|[\\(\\)'\"a-zA-Z]+?=[\\(\\)'\"a-zA-Z]+?|>|<|\s+?[\\w]+?\\s+?\\bin\\b\\s*?\(|\\blike\\b\\s+?[\"'])|\\/\\*.*\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT\s*(\(.+\)\s*|@{1,2}.+?\s*|\s+?.+?|(`|'|\").*?(`|'|\")\s*)|UPDATE\s*(\(.+\)\s*|@{1,2}.+?\s*|\s+?.+?|(`|'|\").*?(`|'|\")\s*)SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE)@{0,2}(\\(.+\\)|\\s+?.+?\\s+?|(`|'|\").*?(`|'|\"))FROM(\\(.+\\)|\\s+?.+?|(`|'|\").*?(`|'|\"))|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";

//referer获取 - PHP 8.1+兼容性
$webscan_referer = empty($_SERVER['HTTP_REFERER']) ? [] : ['HTTP_REFERER' => $_SERVER['HTTP_REFERER']];

/**
 * 禁用错误报告
 * @param bool $debug 是否开启调试
 */
function disable_error($debug)
{
    if (!$debug) {
        error_reporting(0);
    }
}

// 调用禁用错误函数
disable_error(DEBUG);

/**
 * 数据统计回传
 * @param array $logs 日志数据
 * @return bool
 */
function webscan_slog($logs)
{
    if (defined('DEBUG') && DEBUG) {
        // 日志记录
        $log_file = dirname(__FILE__) . "/webscan.log";
        $fh = @fopen($log_file, "a");
        if ($fh) {
            fwrite($fh, json_encode($logs, JSON_UNESCAPED_UNICODE) . "\n");
            fclose($fh);
        }
    }
    return true;
}

/**
 * 参数拆分 - 递归处理数组
 * @param mixed $arr 输入数据
 * @return mixed
 */
function webscan_arr_foreach($arr)
{
    static $str = [];
    static $keystr = '';

    if (!is_array($arr)) {
        return $arr;
    }

    foreach ($arr as $key => $val) {
        $keystr .= $key;
        if (is_array($val)) {
            webscan_arr_foreach($val);
        } else {
            $str[] = $val . $keystr;
        }
    }

    $result = implode($str);
    // 重置静态变量
    $str = [];
    $keystr = '';
    return $result;
}

/**
 * 防护提示页
 */
function webscan_pape()
{
    $pape = '<!doctype html><html><head><meta charset="utf-8"><meta name="viewport"content="width=device-width"><title>网站防火墙</title><style>*{margin:0;padding:0;color:#444}body{font-size:14px}.main{width:90%;max-width:600px;margin:10%auto}.title{background:#2c93df;color:#fff;font-size:16px;height:40px;line-height:40px;padding-left:20px;text-align:center}.content{background-color:#f3f7f9;border:1px dashed#c6d9b6;padding:20px}.t1{border-bottom:1px dashed#c6d9b6;color:#ff4000;font-weight:bold;margin:0 0 20px;padding-bottom:18px}.t2{margin-bottom:8px;font-weight:bold}ol{margin:0 0 20px 22px;padding:0}ol li{line-height:30px}</style></head><body><div class="main"><div class="title">六零导航页网站防火墙</div><div class="content"><p class="t1">您的请求带有不合法参数，已被网站防火墙拦截！</p><p class="t2">原因：</p><p>您提交的内容包含危险的攻击请求</p></div></div></body></html>';
    echo $pape;
}

/**
 * 攻击检查拦截
 * @param string $StrFiltKey 键名
 * @param mixed $StrFiltValue 键值
 * @param string $ArrFiltReq 过滤规则
 * @param string $method 请求方法
 */
function webscan_StopAttack($StrFiltKey, $StrFiltValue, $ArrFiltReq, $method)
{
    $StrFiltValue = webscan_arr_foreach($StrFiltValue);

    if (preg_match("/" . $ArrFiltReq . "/is", $StrFiltValue) == 1) {
    webscan_slog([
        'ip' => isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : 'unknown',
        'time' => date("Y-m-d H:i:s"),
        'page' => isset($_SERVER["PHP_SELF"]) ? $_SERVER["PHP_SELF"] : '',
        'method' => $method,
        'rkey' => $StrFiltKey,
        'rdata' => $StrFiltValue,
        'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
        'request_url' => isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : ''
    ]);
    exit(webscan_pape());
}

if (preg_match("/" . $ArrFiltReq . "/is", $StrFiltKey) == 1) {
    webscan_slog([
        'ip' => isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : 'unknown',
        'time' => date("Y-m-d H:i:s"),
        'page' => isset($_SERVER["PHP_SELF"]) ? $_SERVER["PHP_SELF"] : '',
        'method' => $method,
        'rkey' => $StrFiltKey,
        'rdata' => $StrFiltKey,
        'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
        'request_url' => isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : ''
    ]);
    exit(webscan_pape());
}
}

/**
 * 拦截目录白名单
 * @param string $webscan_white_name 白名单目录
 * @param array $webscan_white_url 白名单URL
 * @return bool
 */
function webscan_white($webscan_white_name, $webscan_white_url = [])
{
$url_path = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
$url_var = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';

    if (preg_match("/" . $webscan_white_name . "/is", $url_path) == 1 && !empty($webscan_white_name)) {
        return false;
    }

    foreach ($webscan_white_url as $key => $value) {
        if (!empty($url_var) && !empty($value)) {
            if (stristr($url_path, $key) && stristr($url_var, $value)) {
                return false;
            }
        } elseif (empty($url_var) && empty($value)) {
            if (stristr($url_path, $key)) {
                return false;
            }
        }
    }

    return true;
}

// 启动WAF防护
if (!empty($webscan_switch) && webscan_white($webscan_white_directory, $webscan_white_url)) {
    if (!empty($webscan_get)) {
        foreach ($_GET as $key => $value) {
            webscan_StopAttack($key, $value, $getfilter, "GET");
        }
    }
    if (!empty($webscan_post)) {
        foreach ($_POST as $key => $value) {
            webscan_StopAttack($key, $value, $postfilter, "POST");
        }
    }
    if (!empty($webscan_cookie)) {
        foreach ($_COOKIE as $key => $value) {
            webscan_StopAttack($key, $value, $cookiefilter, "COOKIE");
        }
    }
    if (!empty($webscan_referre)) {
        foreach ($webscan_referer as $key => $value) {
            webscan_StopAttack($key, $value, $postfilter, "REFERRER");
        }
    }
}
