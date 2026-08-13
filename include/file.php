<?php
/*
 * @Description: 图片文件处理
 * @FilePath: /lylme_spage/include/file.php
 * @Copyright (c) 2024 by LyLme, All Rights Reserved.
 *
 * ============ 调用方式（均为 POST 请求，返回 JSON {code,msg,url}） ============
 *
 * 【一、本地文件上传】
 *   POST /include/file.php
 *   FormData: file=<图片文件>
 *   处理：默认中心裁剪为 480x480 正方形，保存到 files/upload/
 *   code 说明：200=成功 -1=失败 -3=非法上传 -4=类型/内容无效 -5=文件名不合法 -8=过于频繁
 *
 * 【二、远程图片抓取】
 *   POST /include/file.php
 *   数据: url=<远程图片地址>（可携带 referer 头）
 *   处理：默认中心裁剪为 480x480 正方形，保存到 files/download/
 *   限流：同一 IP 每分钟登录管理员 120 次，游客 3 次
 *
 * 【三、GET 参数（可叠加，作用于图片处理）】
 *   compress=1|yes|true   仅等比压缩不裁剪（保留原宽高比），保存到 files/images/
 *                         默认裁剪模式长边 480，压缩模式长边 1920
 *   width=<像素>          压缩模式目标最大宽度（默认 1920）
 *   height=<像素>         压缩模式目标最大高度（默认 1920）
 *   crop=no               完全不做处理（原样保存），需已登录管理员，优先级最高
 *
 * 【四、后台固定文件白名单上传（LOGO/背景图）】
 *   POST /include/file.php?target=<枚举值>
 *   FormData: file=<图片文件>
 *   target 仅允许以下预设枚举，目录与文件名主体由服务端固定，无法注入任意路径：
 *     web_logo        => assets/img/web-logo.<原后缀>
 *     web_background  => assets/img/web-background.<原后缀>
 *     wap_background  => assets/img/web-wapbackground.<原后缀>
 *   处理：不裁剪不缩放，重新编码去除恶意代码，大小上限 10M；
 *         支持 JPEG/PNG/GIF/WEBP，后缀保持原文件扩展名（不强制转 JPG），GIF 重编码后仅保留首帧
 *
 * ============ 示例 ============
 *   裁剪上传：      POST file.php                    (FormData: file)
 *   抓取裁剪：      POST file.php                    (data: url=...)
 *   只压缩不裁剪：  POST file.php?compress=1         (FormData: file)
 *   自定义尺寸：    POST file.php?compress=1&width=1200&height=800
 *   后台LOGO：      POST file.php?target=web_logo    (FormData: file)
 *
 * ============ 环境兼容 ============
 *   兼容 PHP 5.4+ / 7.x / 8.x（短数组语法与 JSON_UNESCAPED_UNICODE 为 PHP 5.4+ 特性，
 *   项目其余代码同此要求）；Windows 与 Linux 均可用（路径统一使用 / 分隔符）。
 *   依赖扩展：GD（图片处理，必须）、cURL（远程抓图，必须）、fileinfo/Json（可选，内置）。
 *   WebP 限制：imagecreatefromwebp/imagewebp 需 PHP 5.5+ 且 GD 编译时启用；
 *   Windows 官方 PHP 构建的 GD 通常不带 WebP，此时上传 WebP 会被明确拒绝并提示
 *   "服务器不支持该格式"，不会导致致命错误或原样落盘；JPEG/PNG/GIF 不受影响。
 *   getimagesize 对 WebP 的 MIME 识别自 PHP 7.1 才完整，低版本已做空值防御。
 */
header('Content-Type:application/json');
require_once("common.php");
define('SAVE_PATH', 'files/'); //保存路径

// 验证文件后缀是否合法
function validate_filename($filename) {
    // 定义允许的文件后缀
    $allowed_extensions = ['jpeg', 'jpg', 'png', 'gif', 'webp', 'ico'];
    // 提取文件后缀
    $file_extension = pathinfo($filename, PATHINFO_EXTENSION);
    // 检查后缀是否在允许的列表中
    return in_array(strtolower($file_extension), $allowed_extensions);
}

// 安全输出 JSON（防止文件名等动态内容破坏 JSON 结构）
// $exit = true 时立即结束脚本；false 时仅输出，供后续继续处理（如裁剪）
function output_json($data, $exit = true) {
    // JSON_UNESCAPED_UNICODE 为 PHP 5.4+ 常量，低版本时降级为默认转义
    echo json_encode($data, defined('JSON_UNESCAPED_UNICODE') ? JSON_UNESCAPED_UNICODE : 0);
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

    // 从 URL 路径提取扩展名（忽略 query 参数，防止 ext 被污染如 ".jpg?x=1" 导致误拒）
    $url_path = parse_url($url, PHP_URL_PATH);
    $img_ext = '.' . strtolower(pathinfo($url_path, PATHINFO_EXTENSION));
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
    // CURLINFO_PRIMARY_IP 为 PHP 5.4.7+ 常量，低版本无法获取时按不安全拒绝
    $connected_ip = defined('CURLINFO_PRIMARY_IP') ? curl_getinfo($ch, CURLINFO_PRIMARY_IP) : '';
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
    // 验证文件内容是否为图片且当前 GD 支持处理（防止 GD 无 WebP 支持时原样落盘）
    $image_info = @getimagesizefromstring($data);
    if ($image_info === false || !gd_supports(isset($image_info['mime']) ? $image_info['mime'] : '')) {
        output_json(array('code' => '-4', 'msg' => '抓取的文件不是有效的图片或服务器不支持该格式'));
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
 * @param string $save_dir 完整保存目录（固定文件名模式使用，优先于 $subdir）
 * @param string $save_name 固定文件名（固定文件名模式使用，默认随机命名）
 * @param string $url_prefix 返回 URL 前缀（固定文件名模式使用）
 * @param int $maxsize 大小上限（字节），默认 5M
 * @return string
 */
function upload_img($upfile, $subdir = 'upload/', $save_dir = '', $save_name = '', $url_prefix = '', $maxsize = null)
{
    $IMG_NAME =  uniqid("img_"); //文件名
    if ($maxsize === null) {
        $maxsize = pow(1024, 2) * 5;
    }
    //保存目录：固定文件名模式使用完整目录，否则 SAVE_PATH 子目录
    $dir = ($save_dir !== '') ? $save_dir : (ROOT . SAVE_PATH . $subdir);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        //创建路径
    }
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
    // 固定文件名模式使用白名单名称，否则随机命名
    $img_name = ($save_name !== '') ? $save_name : ($IMG_NAME . $img_ext);
    // 验证文件名合法性
    if (!validate_filename($img_name)) {
        output_json(array('code' => '-5', 'msg' => '文件后缀不合法'));
    }
    //文件名
    $save_to = $dir . $img_name;
    $url = ($url_prefix !== '') ? ($url_prefix . $img_name) : ('/' . SAVE_PATH . $subdir . $img_name);
    // 验证文件内容是否为图片且当前 GD 支持处理（基于文件内容而非扩展名）
    $content = file_get_contents($tmp_name);
    $image_info = ($content === false) ? false : @getimagesizefromstring($content);
    if ($image_info === false || !gd_supports(isset($image_info['mime']) ? $image_info['mime'] : '')) {
        output_json(array('code' => '-4', 'msg' => '上传的文件不是有效的图片或服务器不支持该格式'));
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
        case '.webp':
            $type = 'image/webp';
            break;
        case '.ico':
            $type = 'image/x-icon';
            break;
    }

    $allowed_types = array("image/jpeg", "image/png", "image/gif", "image/webp", "image/x-icon");
    return in_array($type, $allowed_types);
}

// 验证文件内容是否为图片
function is_valid_image($data)
{
    $image_info = @getimagesizefromstring($data);
    return $image_info !== false;
}

// GD 是否支持指定 MIME 的解码/编码
// WebP 需要 PHP 5.5+ 且 GD 编译时启用（Windows 官方 PHP 构建长期缺失，Linux 发行版大多已支持）
function gd_supports($mime)
{
    if (!is_string($mime) || $mime === '') {
        return false; // 无法确认 MIME 时安全降级为不支持
    }
    switch ($mime) {
        case 'image/webp':
            // PHP < 5.5 或 GD 未启用 WebP 时这两个函数不存在，直接调用会致命错误，必须检测
            return function_exists('imagecreatefromwebp') && function_exists('imagewebp');
        default:
            return true; // JPEG/PNG/GIF 所有 GD 版本均内置支持
    }
}

/**
 * 按 MIME 输出 GD 图像（jpg/png/gif/webp），保证扩展名与文件内容格式一致
 * @param GdImage $image GD 图像资源（PHP8+ 为 GdImage，PHP7 为 resource）
 * @param string $path 输出路径
 * @param string $mime 输出格式 MIME（image/jpeg|image/png|image/gif|image/webp）
 */
function image_output($image, $path, $mime)
{
    switch ($mime) {
        case 'image/jpeg':
            return @imagejpeg($image, $path, 90);
        case 'image/gif':
            // GIF 仅支持 256 色调色板，交由 GD 内部转换（保留透明像素）
            return @imagegif($image, $path);
        case 'image/webp':
            if (!function_exists('imagewebp')) {
                return false; // GD 无 WebP 输出能力（PHP5 或未编译支持）
            }
            return @imagewebp($image, $path, 80);
        default:
            return @imagepng($image, $path, 9);
    }
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
    $source_mime = isset($source_info['mime']) ? $source_info['mime'] : '';
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
        case 'image/webp':
            // PHP < 5.5 或 GD 未启用 WebP 时函数不存在，调用即致命错误，必须检测
            $source_image = function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($source_path) : false;
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
    image_output($target_image, $source_path, $source_mime);
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
    $source_mime = isset($source_info['mime']) ? $source_info['mime'] : '';
    // 等比缩放比例（图片小于目标尺寸时不放大）
    $ratio = min($max_width / $source_width, $max_height / $source_height);
    if ($ratio >= 1) {
        // 无需缩放：仍重新编码以去除可能嵌入的恶意代码
        return image_reencode($source_path, pathinfo($source_path, PATHINFO_EXTENSION));
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
        case 'image/webp':
            // PHP < 5.5 或 GD 未启用 WebP 时函数不存在，调用即致命错误，必须检测
            $source_image = function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($source_path) : false;
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
    image_output($target_image, $source_path, $source_mime);
    imagedestroy($source_image);
    imagedestroy($target_image);
    return true;
}

/**
 * 重新编码图片（去除潜在恶意代码），不裁剪不缩放
 * @param string $source_path 源图片路径（GD 可直接解码）
 * @param string $ext 输出格式扩展名（jpg/jpeg/png/gif/webp，为空时按源图 MIME）
 * @param string $dest_path 输出路径（为空则覆盖源文件）
 */
function image_reencode($source_path, $ext = '', $dest_path = '')
{
    $source_info = @getimagesize($source_path);
    if ($source_info === false) {
        return false;
    }
    $source_mime = isset($source_info['mime']) ? $source_info['mime'] : '';
    switch ($source_mime) {
        case 'image/jpeg':
            $src = @imagecreatefromjpeg($source_path);
            break;
        case 'image/png':
            $src = @imagecreatefrompng($source_path);
            break;
        case 'image/gif':
            $src = @imagecreatefromgif($source_path);
            break;
        case 'image/webp':
            // PHP < 5.5 或 GD 未启用 WebP 时函数不存在，调用即致命错误，必须检测
            $src = function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($source_path) : false;
            break;
        default:
            return false;
    }
    if ($src === false) {
        return false;
    }
    // 输出格式：优先按传入扩展名决定，未指定时按源图 MIME（保证扩展名与内容一致）
    $ext = strtolower($ext);
    $out_mime = $source_mime;
    if ($ext === 'jpg' || $ext === 'jpeg') {
        $out_mime = 'image/jpeg';
    } elseif ($ext === 'png') {
        $out_mime = 'image/png';
    } elseif ($ext === 'gif') {
        $out_mime = 'image/gif';
    } elseif ($ext === 'webp') {
        $out_mime = 'image/webp';
    }
    $out_path = ($dest_path !== '') ? $dest_path : $source_path;
    $ret = image_output($src, $out_path, $out_mime);
    @imagedestroy($src);
    return $ret;
}

// compress=1 时仅等比压缩不裁剪（保存到 files/images/ 目录），默认裁剪为正方形（保存到 upload/、download/）
// 除 crop=no（后台登录管理员原样保存）外，所有图片最终均经 GD 重新编码输出，剥离可能嵌入的恶意代码
$compress = isset($_GET["compress"]) ? $_GET["compress"] : '';
$is_compress = ($compress === "1" || $compress === "yes" || $compress === "true");

// 后台网站设置固定文件上传白名单（LOGO/背景图）：仅允许覆盖预设路径，防止任意路径写入
// 注意：name 为文件名主体（不含后缀），最终文件名 = 主体 + 原文件扩展名，如 web-logo.png / web-logo.gif
$fixed_targets = array(
    'web_logo'       => array('dir' => 'assets/img/', 'name' => 'web-logo'),
    'web_background' => array('dir' => 'assets/img/', 'name' => 'web-background'),
    'wap_background' => array('dir' => 'assets/img/', 'name' => 'web-wapbackground'),
);

// 固定文件名模式：重新编码去除恶意代码，不裁剪不缩放，原子覆盖白名单预设路径（仅成功才覆盖原文件）
$target = isset($_GET['target']) ? $_GET['target'] : '';
if ($target !== '' && isset($fixed_targets[$target]) && !empty($_FILES["file"])) {
    $fixed = $fixed_targets[$target];
    $tmp = $_FILES["file"];
    // 是否为正常上传
    if (!isset($tmp["tmp_name"]) || !is_uploaded_file($tmp["tmp_name"])) {
        output_json(array('code' => '-3', 'msg' => '非法上传请求'));
    }
    // 大小校验（与原 set.php 一致，上限 10M）
    if ($tmp["size"] > pow(1024, 2) * 10) {
        output_json(array('code' => '-1', 'msg' => '图片不能超过10M'));
    }
    // 扩展名校验
    $parts = explode('.', $tmp["name"]);
    $ext = '.' . strtolower(end($parts));
    if (!validate_file_type($ext)) {
        output_json(array('code' => '-4', 'msg' => '上传的图片类型' . $ext . '不支持'));
    }
    // 内容校验 + 实际大小二次校验
    $content = file_get_contents($tmp["tmp_name"]);
    if ($content === false || !is_valid_image($content) || strlen($content) > pow(1024, 2) * 10) {
        output_json(array('code' => '-4', 'msg' => '上传的文件不是有效的图片'));
    }
    // LOGO/背景图仅接受 GD 可重编码的 JPEG/PNG/GIF/WEBP，且当前 GD 需支持该格式
    $source_mime = @getimagesizefromstring($content);
    $mime = ($source_mime !== false && isset($source_mime['mime'])) ? $source_mime['mime'] : '';
    if (!in_array($mime, array('image/jpeg', 'image/png', 'image/gif', 'image/webp'), true) || !gd_supports($mime)) {
        output_json(array('code' => '-4', 'msg' => '请上传JPEG、PNG、GIF或WEBP格式的图片（当前服务器不支持 ' . ($mime === '' ? '该格式' : $mime) . '）'));
    }
    // 文件名 = 白名单主体 + 原文件扩展名（扩展名已过白名单校验，无法注入路径），保持原格式不强制转 JPG
    $save_name = $fixed['name'] . $ext;
    // 确保目录存在，解码源临时文件并重新编码写入固定路径（成功前不覆盖旧文件）
    $fixed_dir = ROOT . $fixed['dir'];
    if (!is_dir($fixed_dir)) {
        mkdir($fixed_dir, 0755, true);
    }
    if (!image_reencode($tmp["tmp_name"], ltrim($ext, '.'), $fixed_dir . $save_name)) {
        output_json(array('code' => '-4', 'msg' => '图片处理失败，请上传有效的图片'));
    }
    output_json(array('code' => '200', 'msg' => '上传成功', 'url' => '/' . $fixed['dir'] . $save_name));
}

if (empty($_POST["url"]) && !empty($_FILES["file"])) {
    $filename = upload_img($_FILES["file"], $is_compress ? 'images/' : 'upload/');
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
    $filename = download_img($_POST["url"], $is_compress ? 'images/' : 'download/');
    //下载图片
} else {
    output_json(array('code' => '0', 'msg' => 'error'));
}
// 图片处理：优先裁剪/压缩（GD 输出即重新编码，剥离恶意代码）
if ($is_compress) {
    // 仅压缩不裁剪：文章图片场景，长边默认不超过 1920，可通过 width/height 参数自定义
    $max_w = (isset($_GET["width"]) && intval($_GET["width"]) > 0) ? intval($_GET["width"]) : 1920;
    $max_h = (isset($_GET["height"]) && intval($_GET["height"]) > 0) ? intval($_GET["height"]) : 1920;
    $processed = imagecompress($filename, $max_w, $max_h);
} else {
    $processed = imagecropper($filename, 480, 480);
}
// 兜底：处理函数未实际重编码时（小文件等），强制重新编码去除恶意代码
if ($processed === false) {
    image_reencode($filename, pathinfo($filename, PATHINFO_EXTENSION));
}