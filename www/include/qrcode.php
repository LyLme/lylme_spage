<?php
/* 
 * @Description: 生成二维码
 * @Author: LyLme admin@lylme.com
 * @Date: 2024-04-09 03:36:21
 * @LastEditors: LyLme admin@lylme.com
 * @LastEditTime: 2024-04-09 04:22:36
 * @FilePath: /lylme_spage/include/qrcode.php
 * @Copyright (c) 2024 by LyLme, All Rights Reserved. 
 */
include "./common.php";
include "./lib/phpqrcode.php";


$text = $_GET['text'];
if(empty($text)){
      exit('缺少参数text');
    
}
$errorCorrectionLevel = 'L';//容错级别   
$matrixPointSize = 4;//生成图片大小   
//生成二维码图片   
QRcode::png($text, 'qrcode.png', $errorCorrectionLevel, $matrixPointSize, 2);   

if (!preg_match("/^(http|https):\/\//", $conf['logo'])) {  
    $logo = realpath(ROOT. '/' . $conf['logo']);//准备好的logo图片  
    if(!file_exists($logo)){
        $logo =  false;
    }
} else {  
    $logo = $conf['logo'];
} 
$QR = 'qrcode.png';//已经生成的原始二维码图   
if ($logo != FALSE) {  
    $QR = imagecreatefromstring(file_get_contents($QR));   
    $logo = imagecreatefromstring(file_get_contents($logo));   
    $QR_width = imagesx($QR);//二维码图片宽度   
    $QR_height = imagesy($QR);//二维码图片高度   
    $logo_width = imagesx($logo);//logo图片宽度   
    $logo_height = imagesy($logo);//logo图片高度   
    $logo_qr_width = $QR_width / 5;   
    $scale = $logo_width/$logo_qr_width;   
    $logo_qr_height = $logo_height/$scale;   
    $from_width = ($QR_width - $logo_qr_width) / 2;   
    //重新组合图片并调整大小   
    imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,   
    $logo_qr_height, $logo_width, $logo_height);   
}   
//输出图片   
header('Content-Type: image/png');  
header('Cache-Control: max-age=300');
imagepng($QR);  

// 释放内存  
imagedestroy($QR);  