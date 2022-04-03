<?php 
function strexists($string, $find) {
	return !(strpos($string, $find) === FALSE);
}
function dstrpos($string, $arr) {
	if(empty($string)) return false;
	foreach((array)$arr as $v) {
		if(strpos($string, $v) !== false) {
			return true;
		}
	}
	return false;
}
//判断移动端
function checkmobile() {
	$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
	$ualist = array('android', 'midp', 'nokia', 'mobile', 'iphone', 'ipod', 'blackberry', 'windows phone');
	if((dstrpos($useragent, $ualist) || strexists($_SERVER['HTTP_ACCEPT'], "VND.WAP") || strexists(isset($_SERVER['HTTP_VIA']),"wap"))) {
		return true;
	} else {
		return false;
	}
}
function daddslashes($string) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = daddslashes($val);
		}
	} else {
		$string = addslashes($string);
	}
	return $string;
}
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
	$ckey_length = 4;
	$key = md5($key ? $key : ENCRYPT_KEY);
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()) , -$ckey_length)) : '';
	$cryptkey = $keya . md5($keya . $keyc);
	$key_length = strlen($cryptkey);
	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb) , 0, 16) . $string;
	$string_length = strlen($string);
	$result = '';
	$box = range(0, 255);
	$rndkey = array();
	for ($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}
	for ($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}
	for ($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result.= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}
	if ($operation == 'DECODE') {
		if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb) , 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc . str_replace('=', '', base64_encode($result));
	}
}
//CDN
function cdnpublic($cdnpublic) {
	if(empty($cdnpublic)) {
		return '.';
	} else {
		return $cdnpublic.$conf['version'];
	}
}
//获取协议和域名
function siteurl() {
	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	$domainName = $_SERVER['HTTP_HOST'];
	return $protocol . $domainName;
}
$background = $conf["background"];
//网站背景
function background() {
	if (empty($GLOBALS['background'])) {
		if (file_exists(ROOT.'assets/img/background.jpg'))return '../assets/img/background.jpg'; else return '../assets/img/bing.php';
	} else {
		return $GLOBALS['background'];
	}
}
//程序更新
function update() {
	$update_host = 'cdn.lylme.com';
	//程序更新服务器,请勿删除和修改，否则将导致无法接收版本更新和程序报错
	@$update = json_decode(file_get_contents('https://' . $update_host . '/lylmes_page/update.json') , true);
	return $update;
}
function getver($ver) {
	$vn = explode('.', str_replace('v', '', $ver));
	return $vn[0] . sprintf("%02d", $vn[1]) . sprintf("%02d", $vn[2]);
}
//更新设置
function saveSetting($k, $v) {
	global $DB;
	$v = daddslashes($v);
	return $DB->query("UPDATE `lylme_config` SET `v` = '$v' WHERE `lylme_config`.`k` = '$k';");
}
//获取相对路径
function get_urlpath($srcurl,$baseurl) {
	if(empty($srcurl))return '';
	$srcinfo = parse_url($srcurl);
	if(isset($srcinfo['scheme'])) {
		return $srcurl;
	}
	$baseinfo = parse_url($baseurl);
	$url = $baseinfo['scheme'].'://'.$baseinfo['host'];
	if(substr($srcinfo['path'], 0, 1) == '/') {
		$path = $srcinfo['path'];
	} else {
		$path = dirname($baseinfo['path']).'/'.$srcinfo['path'];
	}
	$rst = array();
	$path_array = explode('/', $path);
	if(!$path_array[0]) {
		$rst[] = '';
	}
	foreach ($path_array AS $key => $dir) {
		if ($dir == '..') {
			if (end($rst) == '..') {
				$rst[] = '..';
			} elseif(!array_pop($rst)) {
				$rst[] = '..';
			}
		} elseif($dir && $dir != '.') {
			$rst[] = $dir;
		}
	}
	if(!end($path_array)) {
		$rst[] = '';
	}
	$url .= implode('/', $rst);
	if( !empty($srcinfo['query']) ) $url .= '?'.$srcinfo['query'];
	return str_replace('\\', '/', $url);
}
//获取客户端IP
function get_real_ip() {
	$ip=FALSE;
	//客户端IP 或 NONE
	if(!empty($_SERVER["HTTP_CLIENT_IP"])) {
		$ip = $_SERVER["HTTP_CLIENT_IP"];
	}
	//多重代理服务器下的客户端真实IP地址（可能伪造）,如果没有使用代理，此字段为空
	if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ips = explode (", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
		if ($ip) {
			array_unshift($ips, $ip);
			$ip = FALSE;
		}
		for ($i = 0; $i < count($ips); $i++) {
			if (!eregi ("^(10│172.16│192.168).", $ips[$i])) {
				$ip = $ips[$i];
				break;
			}
		}
	}
	//客户端IP 或 (最后一个)代理服务器 IP
	return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
}
?>