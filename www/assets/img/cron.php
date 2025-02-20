<?php

header("Content-Type: text/html; charset=utf-8");

/*
为保证安全，已禁止空密钥执行，请在 $pass 的引号内添加密钥
CRON任务：GET  http://域名/assets/img/cron.php?key=设置的密钥

例如：
$pass = 'lylme';
CRON地址为：http://hao.lylme.com/assets/img/cron.php?key=lylme

*/
$pass = "";     //在这里配置密钥





//########以下内容可忽略########
if (empty($_GET['key'])) {
	//未传入key
	exit('错误：密钥为空，请传入包含参数key的GET请求<br>
    请求示例：<b>http://' . $_SERVER['HTTP_HOST'] . '/assets/img/cron.php?key=秘钥</b>');
} else if (empty($pass)) {
	//密钥为空
	exit('<b>错误：</b>未配置CRON密钥，请在cron.php文件配置密钥');
} else if ($pass != $_GET['key']) {
	//密钥错误
	exit('错误：传入参数key与密钥不匹配');
}
//密钥正确，执行下面代码


if (empty($_GET['idx'])) {
	$str = get_curl('http://cn.bing.com/HPImageArchive.aspx?idx=0&n=1');
} else {
	$str = get_curl('http://cn.bing.com/HPImageArchive.aspx?idx=' . $_GET['idx'] . '&n=1');
}

if (preg_match("/<url>(.+?)<\/url>/is", $str, $matches)) {
	preg_match("/<fullstartdate>(.+?)<\/fullstartdate>/is", $str, $cdata);
	$crdate = date('Y年m月d日 H:i', strtotime($cdata[1]));
	$imgurl = 'http://cn.bing.com' . $matches[1];
	echo "壁纸地址：" . $imgurl . "<br>";
	echo "发布时间：" . $crdate . "<br>";
}
/**
 * 将bing每日壁纸保存到 当前目录/background.jpg
 */
function GrabImage($imgurl, $dir, $filename = '/background.jpg')
{
	if (empty($imgurl)) {
		return false;
	}
	$ext = strrchr($imgurl, '.');
	if ($ext == '.js' && $ext == ".html" && $ext == ".php") {
		echo "Format not supported！";
		return false;
	}
	$dir = realpath($dir);
	$filename = $dir . $filename;
	ob_start();
	readfile($imgurl);
	$img = ob_get_contents();
	ob_end_clean();
	$size = strlen($img);

	$fp2 = fopen($filename, "w");
	if (fwrite($fp2, $img) == true) {
		echo "壁纸大小：" . round($size / 1024) . 'KB<br>';

		echo "<p><font color='green'>成功：当前壁纸已与Bing同步！</font></p>";
	} else {
		echo "<p><font color='red'>错误： 保存文件 <b>" . $filename . "</b> 失败，请检查目录权限</font></p>";
	}
	fclose($fp2);
	return $filename;
}


GrabImage($imgurl, dirname(__FILE__));


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
