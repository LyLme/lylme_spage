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
//判断蜘蛛
function is_spider() {
	$userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
	$spiders = array( 
	        'Googlebot', 
	        'Baiduspider', 
	        'Yahoo! Slurp', 
	        'YodaoBot', 
	        'msnbot',
	        '360Spider',
	        'spider',
	        'Spider'
	        //这里可以加入更多的蜘蛛标示
	);
	foreach ($spiders as $spider) {
		$spider = strtolower($spider);
		if (strpos($userAgent, $spider) !== false) {
			return true;
		}
	}
	return false;
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
	return $GLOBALS['background_img'];
}
//程序更新
function update() {
	$update_host = 'https://cdn.lylme.com/api/update';	//程序更新服务器,请勿删除和修改，否则将导致无法接收版本更新和程序报错
	@$update = json_decode(get_curl($update_host.'?ver='.VERSION.'&domain='.$_SERVER['HTTP_HOST']),true);
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
    if(substr($srcurl,0,2)=="//"){
        return parse_url($baseurl)['scheme'].':'.$srcurl;
    }
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
    $real_ip = '';
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos = array_search('unknown', $arr);
        if (false !== $pos) {
            unset($arr[$pos]);
        }
        $real_ip = trim($arr[0]);
    } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $real_ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $real_ip = $_SERVER['REMOTE_ADDR'];
    }
    return $real_ip;
}
function yan() {
	$filename = ROOT.'/assets/data/data.dat';
	//随机一言文件路径
	if (file_exists($filename)) {
		$data = explode(PHP_EOL, file_get_contents($filename));
		$result = str_replace(array(
		            "\r",
		            "\n",
		            "\r\n"
		        ) , '', $data[array_rand($data) ]);
		return $result;
	}
}
function rearr($data,$arr) {
	$arr = str_replace('{group_id}', $data['group_id'],$arr);
	$arr = str_replace('{group_name}', $data['group_name'],$arr);
	$arr = str_replace('{group_icon}', $data['group_icon'],$arr);
	$arr = str_replace('{link_id}', $data['id'],$arr);
	$arr = str_replace('{link_name}', $data['name'],$arr);
	$arr = str_replace('{link_url}', $data['url'],$arr);
	if (empty($data["icon"])) {
		$icon =  '<img src="/assets/img/default-icon.png" alt="' . $data["name"] . '" />';
	} else if (!preg_match("/^<svg*/", $data["icon"])) {
		$icon = '<img src="' . $data["icon"] . '" alt="' . $data["name"] . '" />';
	} else {
		$icon = $data["icon"];
	}
	$arr = str_replace('{link_icon}', $icon,$arr);
	return $arr;
}
//获取head
function get_head($url) {
	header("Content-type:text/html;charset=utf-8");
	$data = get_curl($url);
	//获取网站title
	preg_match('/<title.*?>(?<title>.*?)<\/title>/sim', $data, $title);
	$encode = mb_detect_encoding($title['title'], array('GB2312','GBK','UTF-8', 'CP936'));
	//得到字符串编码
	$file_charset = iconv_get_encoding()['internal_encoding'];
	//当前文件编码
	if ( $encode != 'CP936' && $encode != $file_charset) {
		$title =  iconv($encode, $file_charset, $title['title']);
		$data = iconv($encode, $file_charset, $data);
	} else {
		$title = $title['title'];
	}
	// 获取网站icon
	preg_match('/<link rel=".*?icon" * href="(.*?)".*?>/is', $data,$icon);
	preg_match('/<meta +name *=["\']?description["\']? *content=["\']?([^<>"]+)["\']?/i', $data, $description);
	preg_match('/<meta +name *=["\']?keywords["\']? *content=["\']?([^<>"]+)["\']?/i', $data, $keywords);
    $icon = $icon[1];
    if(!empty($icon)){
        $icon = get_urlpath($icon,$url);
    }else{
        $parse = parse_url($url);
    	$port = $parse['port']==80||$parse['port']=="" ? '': ":".$parse['port'];
    	$iconurl = $parse['scheme'].'://'.$parse['host'].$port.'/favicon.ico';
    	if(get_curl($iconurl)!=404) {
    			$icon = $iconurl;
		}
    }
	$get_heads=array("title" =>$title,"charset"=> $encode,"icon" => $icon,"description"=>$description[1],"keywords"=>$keywords[1],"url"=>$url);
	return $get_heads;
}
//模拟GET请求
function get_curl($url) {
	$curl = curl_init();
	curl_setopt_array($curl, array(
	    CURLOPT_URL => $url,
	    CURLOPT_RETURNTRANSFER => true,
	    CURLOPT_ENCODING => '',
	    CURLOPT_MAXREDIRS => 10,
	    CURLOPT_TIMEOUT => 0,
	    CURLOPT_FOLLOWLOCATION => true,
	    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	    CURLOPT_CUSTOMREQUEST => 'GET',
	    CURLOPT_HTTPHEADER => array(
	        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/101.0.4951.54 Safari/537.36 Edg/101.0.1210.39 Lylme/11.24'
	        ),
	    ));
	$contents = curl_exec($curl);
	$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	curl_close($curl);
	if($httpCode==404) {
		return $httpCode;
	}
	return $contents;
}
//长度判断	
function strlens($str) {
	if(strlen($str) > 255) {
		return true;
	} else {
		return false;
	}
}
//apply($name, $url, $icon, $group_id);		
function apply($name, $url, $icon, $group_id, $status) {
	$name=strip_tags(daddslashes($name));
	$url=strip_tags(daddslashes($url));
	$icon=strip_tags(daddslashes($icon));
	$group_id=strip_tags(daddslashes($group_id));
	$userip = get_real_ip();
	$date = date("Y-m-d H:i:s");
	if(empty($name) || empty($url) || empty($group_id)) {
		//|| empty($icon)
		return('{"code": "-1", "msg": "必填项不能为空"}');
	} else if(!preg_match('/^http*/i', $url)) {
		return('{"code": "-2", "msg": "链接不符合要求"}');
	} else if(strlens($name)||strlens($url)||strlens($icon)||strlens($group_id)||strlens($userip)) {
		return('{"code": "500", "msg": "非法参数"}');
	} else {
		global $DB;
		if($DB->num_rows($DB->query("SELECT * FROM `lylme_apply` WHERE `apply_url` LIKE '".$url."';"))>0) {
			return('{"code": "-3", "msg": "链接已存在，请勿重复提交"}');
		}
		$sql = "INSERT INTO `lylme_apply` (`apply_id`, `apply_name`, `apply_url`, `apply_group`, `apply_icon`, `apply_mail`, `apply_time`, `apply_status`) VALUES (NULL, '".$name."', '".$url."', '".$group_id."', '".$icon."', '".$userip."', '".$date."', '".$status."');";
		if($DB->query($sql)) {
			switch ($status) {
				case 0:
							    return('{"code": "200", "msg": "请等待管理员审核"}');
				break;
				case 1:
							    if(ins_link($name, $url, $icon, $group_id, $status,$userip)) {
					return('{"code": "200", "msg": "网站已收录"}');
				} else {
					return('{"code": "-5", "msg": "请联系网站管理员"}');
				}
				break;
			}
		} else {
			return('{"code": "-4", "msg": "未知错误，请联系网站管理员"}');
		}
	}
}
function ins_link($name, $url, $icon, $group_id, $status) {
	global $DB;
	$name=strip_tags(daddslashes($name));
	$url=strip_tags(daddslashes($url));
	$icon=strip_tags(daddslashes($icon));
	$group_id=strip_tags(daddslashes($group_id));
	$userip = get_real_ip();
	$date = date("Y-m-d H:i:s");
	$link_order = $DB->count('select MAX(id) from `lylme_links`')+1;
	$sql1 = "INSERT INTO `lylme_links` (`id`, `name`, `group_id`, `url`, `icon`, `PS`,`link_order`) VALUES (NULL, '" . $name . "', '" . $group_id . "', '" . $url . "', '" . $icon . "', '" . $userip . "的提交 ', '" . $link_order . "');";
	if($DB->query($sql1)) {
		return true;
	} else {
		return false;
	}
}
function theme_file($file) {
	global $conf;
	$theme = ROOT.'template/'.$conf['template'].'/'.$file;
	if(file_exists($theme)) {
		return $theme;
	} else {
		return 'template/'.$file;
	}
}
function wxPlus($data){
    //申请收录后推送到微信公众号
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, "https://wx.lylme.com/api/apply/");
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$output = curl_exec($curl);
	curl_close($curl);
	return $output;
}
?>