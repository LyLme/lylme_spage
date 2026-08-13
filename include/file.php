<?php
/*
 * @Description: 图片文件处理
 * @FilePath: /lylme_spage/include/file.php
 * @Copyright (c) 2024 by LyLme, All Rights Reserved.
 */
header('Content-Type:application/json');
require_once("common.php");
define('SAVE_PATH', 'files/'); //保存路径

// 验证文件后缀是否合法
function validate_filename($filename) {
    // 定义允许的文件后缀
    $allowed_extensions = ['jpeg', 'jpg', 'png', 'gif', 'ico'];
    // 提取文件后缀
    $file_extension = pathinfo($filename, PATHINFO_EXTENSION);
    // 检查后缀是否在允许的列表中
    return in_array(strtolower($file_extension), $allowed_extensions);
}

// 安全输出 JSON（防止文件名等动态内容破坏 JSON 结构）
// $exit = true 时立即结束脚本；false 时仅输出，供后续继续处理（如裁剪）
function output_json($data, $exit = true) {
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    if ($exit) {
        exit;
    }
}

// 校验下载 URL，防止 SSRF：仅允许 http/https，且目标主机不能是内网/回环/保留地址
function is_safe_download_url($url)
{
    $parts = parse_url($url);
    if ($parts === false || empty($parts['scheme']) || empty($parts['host'])) {
        return false;
    }
    $scheme = strtolower($parts['scheme']);
    if (!in_array($scheme, ['http', 'https'], true)) {
        return false;
    }
    $host = $parts['host'];
    // 获取主机名对应的 IP（gethostbynamel 返回 IPv4 列表）
    $ips = gethostbynamel($host);
    if ($ips === false) {
        // 主机名本身是 IP 则直接检查，否则视为无法解析的域名直接拒绝
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            $ips = array($host);
        } else {
            return false;
        }
    }
    foreach ($ips as $ip) {
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return false; // 内网 / 回环 / 保留地址
        }
    }
    return true;
}

/**
 * 通过curl下载
 * @param string $url网上资源图片的url
 * @param string $subdir 保存的子目录（不含 SAVE_PATH 前缀）
 * @return string
 */
function download_img($url, $subdir = 'download/')
{
    // SSRF 防护：仅允许 http/https 且目标不能是内网/回环地址
    if (!is_safe_download_url($url)) {
        output_json(array('code' => '-6', 'msg' => '不安全的下载地址'));
    }
    $IMG_NAME = uniqid("img_"); //文件名
    $maxsize = pow(1024, 2) * 5; //文件大小5M
    $size = remote_filesize($url); //文件大小
    if ($size > $maxsize) {
        output_json(array('code' => '-1', 'msg' => '抓取的图片超过' . $maxsize / pow(1024, 2) . 'M，当前为：' . round($size / pow(1024, 2), 2) . 'M'));
    }

    $img_ext = '.' . strtolower(pathinfo($url, PATHINFO_EXTENSION));
    //文件后缀名
    if (!validate_file_type($img_ext)) {
        output_json(array('code' => '-4', 'msg' => '抓取的图片类型' . $img_ext . '不支持'));
    }
    $img_name = $IMG_NAME  . $img_ext;
    // 验证文件名合法性
    if (!validate_filename($img_name)) {
        output_json(array('code' => '-5', 'msg' => '文件后缀不合法'));
    }
    //文件名
    $dir = ROOT . SAVE_PATH . $subdir;
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
    // 实际连接 IP 二次校验（防 DNS rebinding 绕过初检）
    $connected_ip = curl_getinfo($ch, CURLINFO_PRIMARY_IP);
    curl_close($ch);
    if (empty($connected_ip) || !filter_var($connected_ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
        output_json(array('code' => '-6', 'msg' => '不安全的下载地址'));
    }
    if ($data === false || strlen($data) < 1024) {
        output_json(array('code' => '-1', 'msg' => '抓取图片失败'));
    }
    $fileSize = strlen($data);
    // 实际下载大小二次校验（防止远端不返回 Content-Length 时绕过大小限制）
    if ($fileSize > $maxsize) {
        output_json(array('code' => '-1', 'msg' => '抓取的图片超过' . $maxsize / pow(1024, 2) . 'M'));
    }
    // 验证文件内容是否为图片
    if (!is_valid_image($data)) {
        output_json(array('code' => '-4', 'msg' => '抓取的文件不是有效的图片'));
    }
    $downloaded_file = fopen($save_to, 'w');
    fwrite($downloaded_file, $data);
    fclose($downloaded_file);
    $fileurl =  '/' . SAVE_PATH . $subdir . $img_name;
    output_json(array('code' => '200', 'msg' => '抓取图片成功', 'url' => $fileurl, 'size' => round($fileSize / 1024, 2) . 'KB'), false);
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
    $regex = '/Content-Length:\s*([0-9]+)/i';
    $count = preg_match($regex, $head, $matches);
    return isset($matches[1]) ? $matches[1] : "0";
}
/**
 * PHP上传图片
 * @param file 生成的文件
 * @param string $subdir 保存的子目录（不含 SAVE_PATH 前缀）
 * @return string
 */
function upload_img($upfile, $subdir = 'upload/')
{
    $IMG_NAME =  uniqid("img_"); //文件名
    $maxsize = pow(1024, 2) * 5;
    //文件大小5M
    $dir = ROOT . SAVE_PATH . $subdir;
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        //创建路径
    }
    $type = $upfile["type"];
    $size = $upfile["size"];
    $tmp_name = $upfile["tmp_name"];
    // 文件是否为正常上传（阻止直接伪造 $_FILES 数组导致路径读取风险）
    if (!isset($tmp_name) || !is_uploaded_file($tmp_name)) {
        output_json(array('code' => '-3', 'msg' => '非法上传请求'));
    }
    $parts = explode('.', $upfile["name"]);
    $img_ext = "." . strtolower(end($parts));
    if (!validate_file_type($img_ext)) {
        output_json(array('code' => '-4', 'msg' => '上传的图片类型' . $img_ext . '不支持'));
    }
    if ($size > $maxsize) {
        output_json(array('code' => '-1', 'msg' => '图片不能超过' . $maxsize / pow(1024, 2) . 'M'));
    }
    $img_name = $IMG_NAME . $img_ext;
    // 验证文件名合法性
    if (!validate_filename($img_name)) {
        output_json(array('code' => '-5', 'msg' => '文件后缀不合法'));
    }
    //文件名
    $save_to = $dir . $img_name;
    $url =  '/' . SAVE_PATH . $subdir . $img_name;
    // 验证文件内容是否为图片（基于文件内容而非扩展名）
    $content = file_get_contents($tmp_name);
    if ($content === false || !is_valid_image($content)) {
        output_json(array('code' => '-4', 'msg' => '上传的文件不是有效的图片'));
    }
    // 实际大小二次校验（$_FILES['size'] 可能被伪造）
    if (strlen($content) > $maxsize) {
        output_json(array('code' => '-1', 'msg' => '图片不能超过' . $maxsize / pow(1024, 2) . 'M'));
    }
    if (move_uploaded_file($tmp_name, $dir . $img_name)) {
        output_json(array('code' => '200', 'msg' => '上传成功', 'url' => $url), false);
        return  $dir . $img_name;
    }
    output_json(array('code' => '-1', 'msg' => '上传失败'));
}
//文件验证
function validate_file_type($type)
{
    $type = strtolower($type);
    switch ($type) {
        case '.jpeg':
        case '.jpg':
            $type = 'image/jpeg';
            break;
        case '.png':
            $type = 'image/png';
            break;
        case '.gif':
            $type = 'image/gif';
            break;
        case '.ico':
            $type = 'image/x-icon';
            break;
    }

    $allowed_types = array("image/jpeg", "image/png", "image/gif", "image/x-icon");
    return in_array($type, $allowed_types);
}

// 验证文件内容是否为图片
function is_valid_image($data)
{
    $image_info = @getimagesizefromstring($data);
    return $image_info !== false;
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
    if ($source_image === false) {
        return false; // 图片解码失败，不进行裁剪
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

/**
 * 仅压缩不裁剪（保持原始宽高比等比缩放）
 * @param string $source_path 原图路径
 * @param int $max_width 最大宽度
 * @param int $max_height 最大高度
 */
function imagecompress($source_path, $max_width = 480, $max_height = 480)
{
    if (filesize($source_path) < 10000) {
        return false;
    }
    $source_info = getimagesize($source_path);
    if ($source_info === false) {
        return false;
    }
    $source_width = $source_info[0];
    $source_height = $source_info[1];
    $source_mime = $source_info['mime'];
    // 等比缩放比例（图片小于目标尺寸时不放大）
    $ratio = min($max_width / $source_width, $max_height / $source_height);
    if ($ratio >= 1) {
        return true;
    }
    $target_width = (int)round($source_width * $ratio);
    $target_height = (int)round($source_height * $ratio);
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
    if ($source_image === false) {
        return false; // 图片解码失败，不进行压缩
    }
    $target_image = imagecreatetruecolor($target_width, $target_height);
    imagesavealpha($source_image, true);
    // 保留源图片透明度
    imagealphablending($target_image, false);
    // 不合并图片颜色
    imagesavealpha($target_image, true);
    // 保留目标图片透明
    imagecopyresampled($target_image, $source_image, 0, 0, 0, 0, $target_width, $target_height, $source_width, $source_height);
    // 等比缩放
    imagepng($target_image, $source_path);
    imagedestroy($source_image);
    imagedestroy($target_image);
    return true;
}

// compress=1 时仅等比压缩不裁剪（保存到 nocrop/ 目录），默认裁剪为正方形（保存到 upload/、download/）
$compress = isset($_GET["compress"]) ? $_GET["compress"] : '';
$is_compress = ($compress === "1" || $compress === "yes" || $compress === "true");

if (empty($_POST["url"]) && !empty($_FILES["file"])) {
    $filename = upload_img($_FILES["file"], $is_compress ? 'nocrop/' : 'upload/');
    $crop = isset($_GET["crop"]) ? $_GET["crop"] : '';
    if (isset($islogin) && $islogin === 1 && $crop === "no") {
        //不压缩图片
        exit();
    }
    //上传图片
} elseif (!empty($_POST["url"])) {
    $client_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
    // 已登录管理员放宽频率限制（后台批量导入图标需要），未登录游客保持 3 次/分钟
    $rate_max = (isset($islogin) && $islogin === 1) ? 120 : 3;
    if (!rate_limit('download_img_' . $client_ip, $rate_max, 60)) {
        output_json(array('code' => '-8', 'msg' => '抓取请求过于频繁，请稍后再试'));
    }
    $filename = download_img($_POST["url"], $is_compress ? 'nocrop/' : 'download/');
    //下载图片
} else {
    output_json(array('code' => '0', 'msg' => 'error'));
}
if ($is_compress) {
    // 仅压缩不裁剪：文章图片场景，长边默认不超过 1920，可通过 width/height 参数自定义
    $max_w = (isset($_GET["width"]) && intval($_GET["width"]) > 0) ? intval($_GET["width"]) : 1920;
    $max_h = (isset($_GET["height"]) && intval($_GET["height"]) > 0) ? intval($_GET["height"]) : 1920;
    imagecompress($filename, $max_w, $max_h);
} else {
    imagecropper($filename, 480, 480);
}