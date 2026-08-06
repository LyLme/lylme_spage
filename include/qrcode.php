<?php


include "./common.php";
include "./lib/phpqrcode.php";
$text = $_GET['text'];
if (empty($text)) {
    exit('缺少参数text');
}

if (strlen($text) > 1024) {
    http_response_code(400);
    exit('text 过长');
}
$errorCorrectionLevel = 'L'; //容错级别   
$matrixPointSize = 4; //生成图片大小   
ob_start();
QRcode::png($text, false, $errorCorrectionLevel, $matrixPointSize, 2);
$qrData = ob_get_clean();
$QR = imagecreatefromstring($qrData);
if (!$QR) {
    exit('二维码生成失败');
}
$logoPath = '';

if (!preg_match("#^(http|https)://#i", $conf['logo'])) {
    $logoPath = realpath(ROOT . '/' . $conf['logo']);
    if (!file_exists($logoPath)) {
        $logoPath = '';
    }
} else {
    $logoPath = $conf['logo'];
}
if (!empty($logoPath)) {

    $logoData = file_get_contents($logoPath);
    $logo = imagecreatefromstring($logoData);

    if ($logo) {

        $QR_width = imagesx($QR); //二维码图片宽度   
        $QR_height = imagesy($QR); //二维码图片高度   
        $logo_width = imagesx($logo); //logo图片宽度   
        $logo_height = imagesy($logo); //logo图片高度   
        $logo_qr_width = $QR_width / 5;
        $scale = $logo_width / $logo_qr_width;
        $logo_qr_height = $logo_height / $scale;

        $from_width = ($QR_width - $logo_qr_width) / 2;
        imagecopyresampled(
            $QR,
            $logo,
            $from_width,
            $from_width,
            0,
            0,
            $logo_qr_width,
            $logo_qr_height,
            $logo_width,
            $logo_height
        );

        imagedestroy($logo);
    }
}
//输出图片 
header('Content-Type: image/png');
header('Cache-Control: max-age=300');
imagepng($QR);
// 释放内存 
imagedestroy($QR);