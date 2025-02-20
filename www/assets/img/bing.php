<?php
/* 
 * @Description: 用于获取Bing每日壁纸，以PHP文件返回图片
 * @Author: LyLme admin@lylme.com
 * @Date: 2024-01-23 12:25:35
 * @LastEditors: LyLme admin@lylme.com
 * @LastEditTime: 2024-04-13 23:19:55
 * @FilePath: /lylme_spage/assets/img/bing.php
 * @Copyright (c) 2024 by LyLme, All Rights Reserved. 
 */

$str = get_curl('http://cn.bing.com/HPImageArchive.aspx?idx=0&n=1');
if (preg_match("/<url>(.+?)<\/url>/is", $str, $matches)) {
    $imgurl = 'http://cn.bing.com' . $matches[1];
}
if (!empty($imgurl)) {
    $currentHour = date('H'); // 获取当前小时  
    $tenAMTimestamp = strtotime(date('Y-m-d 10:00:00')); 
    $currentTimestamp = strtotime(date('Y-m-d H:i:s')); 
    // 如果当前时间已经超过了10点，则计算下一个10点的时间戳  
    if ($currentHour >= 10) {
        $tenAMTimestamp = strtotime('+1 day 10:00:00');
    }
    $expiresSeconds = $tenAMTimestamp - $currentTimestamp;
    $expires = gmdate('D, d M Y H:i:s', $currentTimestamp + $expiresSeconds) . ' GMT'; 
    $lastModified = gmdate('D, d M Y H:i:s', $currentTimestamp) . ' GMT'; 
    header('Last-Modified: ' . $lastModified);
    header('Expires: ' . $expires);
    header('Cache-Control: public, max-age=86400');
    header('Content-Type: image/jpeg');
    ob_clean();
    readfile($imgurl);
    flush();
    exit();
} else {
    // 如果 $imgurl 无效，输出错误信息  
    header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
    header('Content-Type: text/plain');
    echo 'Error: Invalid image URL.';
    exit();
}
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
