<?php


// 访问统计
$tj_log_dir = ROOT . 'logs/';
if (!is_dir($tj_log_dir)) {
    @mkdir($tj_log_dir, 0755, true);
}

/**
 * 读取统计文件（JSON），失败返回默认值
 */
function tj_read_file($file, $default = array())
{
    if (file_exists($file)) {
        $data = @json_decode(@file_get_contents($file), true);
        if (is_array($data)) {
            return $data;
        }
    }
    return $default;
}

/**
 * 写入统计文件（JSON，带文件锁，原子替换）
 */
function tj_write_file($file, $data)
{
    $tmp = $file . '.tmp';
    $fp = @fopen($tmp, 'w');
    if (!$fp) {
        return false;
    }
    if (flock($fp, LOCK_EX)) {
        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, json_encode($data));
        fflush($fp);
        flock($fp, LOCK_UN);
    }
    fclose($fp);
    return @rename($tmp, $file);
}

// ---- 旧数据迁移：include/log.txt -> logs/（仅执行一次） ----
$tj_old_file = SYSTEM_ROOT . 'log.txt';
$tj_total_file = $tj_log_dir . 'total.log';
if (file_exists($tj_old_file) && !file_exists($tj_total_file)) {
    $tj_old_data = @unserialize(@file_get_contents($tj_old_file));
    if (is_array($tj_old_data)) {
        $tj_old_total = isset($tj_old_data['total']) ? intval($tj_old_data['total']) : 0;
        $tj_old_today = isset($tj_old_data[date('Ymd')]) ? intval($tj_old_data[date('Ymd')]) : 0;
        $tj_write_data = array('pv' => $tj_old_total, 'ip' => 0, 'ips' => array());
        @file_put_contents($tj_total_file, json_encode($tj_write_data), LOCK_EX);
        // 旧系统今日浏览量一并迁入今日日志（无IP数据）
        if ($tj_old_today > 0) {
            $tj_write_data = array('pv' => $tj_old_today, 'ips' => array());
            @file_put_contents($tj_log_dir . date('Ymd') . '.log', json_encode($tj_write_data), LOCK_EX);
        }
    }
   @unlink($tj_old_file);
}

$linksrows = $DB->num_rows($DB->query("SELECT * FROM `lylme_links`")); //链接数量
$groupsrows = $DB->num_rows($DB->query("SELECT * FROM `lylme_groups`")); //分类数量

// 后台访问只读取统计，不计数
$isAdminReq = isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], ADMIN_PATH) !== false;
// 访客 IP
$tj_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';

// 今日 / 昨日 日期
$tj_today_key = date('Ymd');
$tj_yesterday_key = date('Ymd', strtotime('-1 day'));

if (!$isAdminReq && $tj_ip !== '') {
    // ===== 写入统计 =====

    // 1. 每日文件
    $tj_today_file = $tj_log_dir . $tj_today_key . '.log';
    $tj_day = tj_read_file($tj_today_file, array('pv' => 0, 'ips' => array()));
    if (!isset($tj_day['pv'])) {
        $tj_day['pv'] = 0;
    }
    if (!isset($tj_day['ips']) || !is_array($tj_day['ips'])) {
        $tj_day['ips'] = array();
    }
    $tj_day['pv'] = intval($tj_day['pv']) + 1;
    $tj_day['ips'][$tj_ip] = (isset($tj_day['ips'][$tj_ip]) ? intval($tj_day['ips'][$tj_ip]) : 0) + 1;
    tj_write_file($tj_today_file, $tj_day);

    // 2. 累计文件
    $tj_total_data = tj_read_file($tj_total_file, array('pv' => 0, 'ip' => 0, 'ips' => array()));
    if (!isset($tj_total_data['pv'])) {
        $tj_total_data['pv'] = 0;
    }
    if (!isset($tj_total_data['ips']) || !is_array($tj_total_data['ips'])) {
        $tj_total_data['ips'] = array();
    }
    $tj_total_data['pv'] = intval($tj_total_data['pv']) + 1;
    $tj_total_data['ips'][$tj_ip] = (isset($tj_total_data['ips'][$tj_ip]) ? intval($tj_total_data['ips'][$tj_ip]) : 0) + 1;
    $tj_total_data['ip'] = count($tj_total_data['ips']);
    tj_write_file($tj_total_file, $tj_total_data);
}

// ===== 读取统计输出 =====

// 今日 / 昨日 / 累计 浏览量
$tj_today_data = tj_read_file($tj_log_dir . $tj_today_key . '.log', array('pv' => 0, 'ips' => array()));
$tj_yesterday_data = tj_read_file($tj_log_dir . $tj_yesterday_key . '.log', array('pv' => 0, 'ips' => array()));
$tj_total_data = tj_read_file($tj_total_file, array('pv' => 0, 'ip' => 0, 'ips' => array()));

$tjtoday = isset($tj_today_data['pv']) ? intval($tj_today_data['pv']) : 0;
$tjyesterday = isset($tj_yesterday_data['pv']) ? intval($tj_yesterday_data['pv']) : 0;
$tjtotal = isset($tj_total_data['pv']) ? intval($tj_total_data['pv']) : 0;
// 今日独立IP数
$tjtodayip = (isset($tj_today_data['ips']) && is_array($tj_today_data['ips'])) ? count($tj_today_data['ips']) : 0;

// 本月浏览量（兼容旧变量：遍历当月已有日志文件求和）
$tjmonth = 0;
$tj_this_month = date('Ym');
for ($d = 1; $d <= (int)date('d'); $d++) {
    $tj_md_data = tj_read_file($tj_log_dir . $tj_this_month . str_pad($d, 2, '0', STR_PAD_LEFT) . '.log', array('pv' => 0));
    $tjmonth += isset($tj_md_data['pv']) ? intval($tj_md_data['pv']) : 0;
}

// 近7天图表数据（今天往前7天，含今天）
// 近3天标签显示"今天/昨天/前天"，更早显示日期（不带年份，如 08-03）
$tj_chart_labels = array();
$tj_chart_pv = array();
$tj_chart_ip = array();
for ($i = 6; $i >= 0; $i--) {
    $tj_d = strtotime('-' . $i . ' day');
    if ($i === 0) {
        $tj_chart_labels[] = '今天';
    } elseif ($i === 1) {
        $tj_chart_labels[] = '昨天';
    } elseif ($i === 2) {
        $tj_chart_labels[] = '前天';
    } else {
        $tj_chart_labels[] = date('m-d', $tj_d);
    }
    $tj_chart_data = tj_read_file($tj_log_dir . date('Ymd', $tj_d) . '.log', array('pv' => 0, 'ips' => array()));
    $tj_chart_pv[] = isset($tj_chart_data['pv']) ? intval($tj_chart_data['pv']) : 0;
    $tj_chart_ip[] = (isset($tj_chart_data['ips']) && is_array($tj_chart_data['ips'])) ? count($tj_chart_data['ips']) : 0;
}
