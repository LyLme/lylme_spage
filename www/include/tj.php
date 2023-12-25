<?php

$linksrows = $DB->num_rows($DB->query("SELECT * FROM `lylme_links`")); //链接数量
$groupsrows = $DB->num_rows($DB->query("SELECT * FROM `lylme_groups`")); //分类数量
$file = SYSTEM_ROOT . "/log.txt";
if(!file_exists($file)) {
    touch($file);
} else {
    $fp = fopen($file, 'r+');
    $content = '';
    if (flock($fp, LOCK_EX)) {
        while (($buffer = fgets($fp, 1024)) != false) {
            $content = $content . $buffer;
        }
        $tjdate = unserialize($content);
        //设置记录键值
        $tjtotal = 'total';
        $tjmonth = date('Ym');
        $tjtoday = date('Ymd');
        $tjyesterday = date('Ymd', strtotime("-1 day"));
        $tongji = array();
        if(strpos($_SERVER['REQUEST_URI'], 'admin') == false) {

            $tongji[$tjtotal] = $tjdate[$tjtotal] + 1;
            // 本月访问量增加
            $tongji[$tjmonth] = $tjdate[$tjmonth] + 1;
            // 今日访问增加
            $tongji[$tjtoday] = $tjdate[$tjtoday] + 1;
            //保持昨天访问
            $tongji[$tjyesterday] = $tjdate[$tjyesterday];
            ftruncate($fp, 0); // 将文件截断到给定的长度
            rewind($fp); // 倒回文件指针的位置
            fwrite($fp, serialize($tongji));
        } else {
            $tongji[$tjtotal] = $tjdate[$tjtotal] ;
            // 本月访问量增加
            $tongji[$tjmonth] = $tjdate[$tjmonth];
            // 今日访问增加
            $tongji[$tjtoday] = $tjdate[$tjtoday];
            //保持昨天访问
            $tongji[$tjyesterday] = $tjdate[$tjyesterday];
        }
        flock($fp, LOCK_UN);
        fclose($fp);
        //输出数据
        $tjtotal = $tongji[$tjtotal];
        $tjmonth = $tongji[$tjmonth];
        $tjtoday = $tongji[$tjtoday];
        $tjyesterday = $tongji[$tjyesterday] ? $tongji[$tjyesterday] : 0;
        //访总问 {$tjtotal}   本月 {$tjmonth}   昨日 {$tjyesterday}     今日 {$tjtoday}
    }
}
