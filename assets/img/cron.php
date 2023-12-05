<?php
header("Content-Type: text/html; charset=utf-8");

$pass = "";     // 在这里配置密钥
//GET  https://域名/assets/img/cron.php?key=填你的密钥

if (empty($pass)) {
    exit('错误：禁止空密钥执行CRON，请在cron.php文件配置密钥');
} else if (empty($_GET['key'])) {
    exit('错误：密钥为空，请传入包含参数key的GET请求<br>
    请求示例：<b>https://域名/assets/img/cron.php?key=adsij' . $_SERVER['HTTP_HOST'] . '/assets/img/cron.php?key=秘钥</b>');
} else if ($pass != $_GET['key']) {
    exit('错误：传入参数key与密钥不匹配');
} else {
    // 密钥正确，执行下面代码

    /**
     * PHP下载NASA APOD每日高清图片并保存为background.jpg
     */

    $api_key = '';  // 替换成你的NASA API密钥
    $apod_url = 'https://api.nasa.gov/planetary/apod?api_key=' . $api_key;
    $json_content = file_get_contents($apod_url);
    $data = json_decode($json_content);

    if ($data && isset($data->hdurl)) {
        $hdurl = $data->hdurl;

        echo "高清图片地址：" . $hdurl . "<br>";

        /**
         * 下载高清图片并保存为background.jpg
         */
        function DownloadAndSaveImage($imgurl, $dir, $filename = '/background.jpg')
        {
            if (empty($imgurl)) {
                return false;
            }

            $dir = realpath($dir);
            $filename = $dir . $filename;

            $img_content = file_get_contents($imgurl);

            if ($img_content !== false) {
                file_put_contents($filename, $img_content);
                echo "成功：高清图片已下载并保存为 " . $filename . "<br>";
            } else {
                echo "<p><font color='red'>错误：下载图片失败</font></p>";
            }
        }

        DownloadAndSaveImage($hdurl, dirname(__FILE__));
    } else {
        echo "<p><font color='red'>错误：未能获取到高清图片地址</font></p>";
    }
}
?>

