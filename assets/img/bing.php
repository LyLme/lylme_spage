<?php
/**
 * 作者：LyLme
 * 说明：用于获取Bing每日壁纸，以PHP文件返回图片
 * 时间：2022-01-20
 */

$str=file_get_contents('http://cn.bing.com/HPImageArchive.aspx?idx=0&n=1');
if(preg_match("/<url>(.+?)<\/url>/is",$str,$matches)){
$imgurl='http://cn.bing.com'.$matches[1];
}
if($imgurl){
header('Content-Type: image/JPEG');
@ob_end_clean();
@readfile($imgurl);
@flush(); @ob_flush();
exit();
}else{
exit('error');
}
?>