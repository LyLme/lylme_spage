<?php
/**
 * PHP获取bing每日壁纸
 * bing每日壁纸更新时间为UTC+8 16：00
 */
if($_GET['idx']==null){
$str=file_get_contents('http://cn.bing.com/HPImageArchive.aspx?idx=0&n=1');
//

}
else{
    $str=file_get_contents('http://cn.bing.com/HPImageArchive.aspx?idx='.$_GET['idx'].'&n=1');
}

if(preg_match("/<url>(.+?)<\/url>/ies",$str,$matches)){

if(preg_match("/<fullstartdate>(.+?)<\/fullstartdate>/ies",$str,$cdata)){
 
}
$crdate = date('Y年m月d日 H:i', strtotime($cdata[1]));
$imgurl = 'http://cn.bing.com'.$matches[1];

	echo "壁纸地址：" . $imgurl."<br>";
	echo "发布时间：" . $crdate."<br>";

}


/**
 * 将bing每日壁纸保存到 当前目录/background.jpg
 */
function GrabImage($imgurl, $dir, $filename='/background.jpg'){
	if(empty($imgurl)){
		return false;
	}
	$ext = strrchr($imgurl, '.');
	if($ext == '.js' && $ext == ".html" && $ext == ".php"){
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

	$fp2 = fopen($filename , "w"); 
	if(fwrite($fp2, $img)==true){
	echo "壁纸大小：" . round($size / 1024) .'KB<br>';
	
	echo "<p><font color='green'>成功：当前壁纸已与Bing同步！</font></p>"; 

	} 
	else{
	echo "<p><font color='red'>错误： 保存文件 <b>". $filename."</b> 失败，请检查目录权限</font></p>";

	}
	fclose($fp2); 




// 	echo "保存路径：" . $filename."<br>";
	
	return $filename; 
}


GrabImage($imgurl,dirname(__FILE__))


/**
 * 返回bing每日壁纸
 */
// if($imgurl){
// header('Content-Type: image/JPEG');
// @ob_end_clean();
// @readfile($imgurl);
// @flush(); @ob_flush();
// exit();
// }else{
// exit('error');
// }
?>