<?php
/*
 * @Description: 图片文件处理
 * @FilePath: /lylme_spage/include/file.php
 * @Copyright (c) 2024 by LyLme, All Rights Reserved.
 */
header('Content-Type:application/json');
require_once("common.php");
define('SAVE_PATH', 'files/'); //保存路径

/**
 * 通过curl下载
 * @param string $url网上资源图片的url
 * @return string
 */
function download_img($url)
{
    $IMG_NAME = uniqid("img_"); //文件名
    $maxsize = pow(1024, 2) * 5; //文件大小5M
    $size = remote_filesize($url); //文件大小
    if ($size > $maxsize) {
        exit('{"code": "-1","msg":"抓取的图片超过' . $maxsize / pow(1024, 2) . 'M，当前为：' . round($size / pow(1024, 2), 2) . 'M"}');
    }

    $img_ext = pathinfo($url, PATHINFO_EXTENSION);
    //文件后缀名
    if (!validate_file_type($img_ext)) {
        exit('{"code": "-4","msg":"抓取的图片类型不支持"}');
    }
    $img_name = $IMG_NAME . '.' . $img_ext;
    //文件名
    $dir = ROOT . SAVE_PATH . 'download/';
    $save_to = $dir . $img_name;
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        //创建路径
    }
    $header = array(
        'User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.103 Safari/537.36',
        'Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3',
        'Accept-Encoding: gzip, deflate',
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    //超过10秒不处理
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //执行之后信息以文件流的形式返回
    $data = curl_exec($ch);
    curl_close($ch);
    $fileSize = strlen($data);
    if ($fileSize < 1024) {
        exit('{"code": "-1","msg":"抓取图片失败"}');
    }
    $downloaded_file = fopen($save_to, 'w');
    fwrite($downloaded_file, $data);
    fclose($downloaded_file);
    $fileurl =  '/' . SAVE_PATH . 'download/' . $img_name;
    echo('{"code": "200","msg":"抓取图片成功","url":"' . $fileurl . '","size":"' . round($fileSize / 1024, 2) . 'KB"}');
    return $save_to;
}
// 获取远程文件大小
function remote_filesize($url)
{
    ob_start();
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_NOBODY, 1);
    $ok = curl_exec($ch);
    curl_close($ch);
    $head = ob_get_contents();
    ob_end_clean();
    $regex = '/Content-Length:\s([0-9].+?)\s/';
    $count = preg_match($regex, $head, $matches);
    return isset($matches[1]) ? $matches[1] : "0";
}
/**
 * PHP上传图片
 * @param file 生成的文件
 * @return string
 */
function upload_img($upfile)
{
    $IMG_NAME =  uniqid("img_"); //文件名
    $maxsize = pow(1024, 2) * 5;
    //文件大小5M
    $dir = ROOT . SAVE_PATH . 'upload/';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        //创建路径
    }
    $type = $upfile["type"];
    $size = $upfile["size"];
    $tmp_name = $upfile["tmp_name"];
    if (!validate_file_type($type)) {
        exit('{"code": "-4","msg":"上传的图片类型不支持"}');
    }
    $parts = explode('.', $upfile["name"]);
    $img_ext = "." . end($parts);
    if ($size > $maxsize) {
        exit('{"code": "-1","msg":"图片不能超过' . $maxsize / pow(1024, 2) . 'M"}');
    }
    $img_name = $IMG_NAME . $img_ext;
    //文件名
    $save_to = $dir . $img_name;
    $url =  '/' . SAVE_PATH . 'upload/' . $img_name;
    if (move_uploaded_file($tmp_name, $dir . $img_name)) {
        echo('{"code": "200","msg":"上传成功","url":"' . $url . '"}');
        return  $dir . $img_name;
    }
}
//文件验证
function validate_file_type($type)
{
    switch ($type) {
        case 'jpeg':
            $type = 'image/jpeg';
            break;
        case 'jpg':
            $type = 'image/jpeg';
            break;
        case 'png':
            $type = 'image/png';
            break;
        case 'gif':
            $type = 'image/gif';
            break;
        case 'ico':
            $type = 'image/x-icon';
            break;
    }

    $allowed_types = array("image/jpeg", "image/png", "image/gif", "image/x-icon");
    return in_array($type, $allowed_types);
}
/**
 * 图像裁剪
 * @param $title string 原图路径
 * @param $content string 需要裁剪的宽
 * @param $encode string 需要裁剪的高
 */
function imagecropper($source_path, $target_width, $target_height)
{
    if (filesize($source_path) < 10000) {
        return false;
    }
    $source_info = getimagesize($source_path);
    $source_width = $source_info[0];
    $source_height = $source_info[1];
    $source_mime = $source_info['mime'];
    $source_ratio = $source_height / $source_width;
    $target_ratio = $target_height / $target_width;
    // 源图过高
    if ($source_ratio > $target_ratio) {
        $cropped_width = $source_width;
        $cropped_height = $source_width * $target_ratio;
        $source_x = 0;
        $source_y = ($source_height - $cropped_height) / 2;
    }
    // 源图过宽
    elseif ($source_ratio < $target_ratio) {
        $cropped_width = $source_height / $target_ratio;
        $cropped_height = $source_height;
        $source_x = ($source_width - $cropped_width) / 2;
        $source_y = 0;
    }
    // 源图适中
    else {
        $cropped_width = $source_width;
        $cropped_height = $source_height;
        $source_x = 0;
        $source_y = 0;
    }
    switch ($source_mime) {
        case 'image/gif':
            $source_image = imagecreatefromgif($source_path);
            break;
        case 'image/jpeg':
            $source_image = imagecreatefromjpeg($source_path);
            break;
        case 'image/png':
            $source_image = imagecreatefrompng($source_path);
            break;
        case 'image/x-icon':
            $source_image = imagecreatefrompng($source_path);
            break;
        default:
            return false;
            break;
    }
    imagesavealpha($source_image, true);
    // 保留源图片透明度
    $target_image = imagecreatetruecolor($target_width, $target_height);
    $cropped_image = imagecreatetruecolor($cropped_width, $cropped_height);
    imagealphablending($target_image, false);
    // 不合并图片颜色
    imagealphablending($cropped_image, false);
    // 不合并图片颜色
    imagesavealpha($target_image, true);
    // 保留目标图片透明
    imagesavealpha($cropped_image, true);
    // 保留目标图片透明
    imagecopy($cropped_image, $source_image, 0, 0, $source_x, $source_y, $cropped_width, $cropped_height);
    // 裁剪
    imagecopyresampled($target_image, $cropped_image, 0, 0, 0, 0, $target_width, $target_height, $cropped_width, $cropped_height);
    // 缩放
    imagepng($target_image, $source_path);
    imagedestroy($target_image);
    return true;
}

if (empty($_POST["url"]) && !empty($_FILES["file"])) {
    $filename = upload_img($_FILES["file"]);
    if (isset($islogin) == 1 && $_GET["crop"] == "no") {
        //不压缩图片
        exit();
    }
    //上传图片
} elseif (!empty($_POST["url"])) {
    $filename = download_img($_POST["url"], $_POST["referer"]);
    //下载图片
} else {
    exit('{"code": "0","msg":"error"}');
}
imagecropper($filename, 480, 480);
