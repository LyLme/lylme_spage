<?php
/* ## 百度主动推动
** 说明：
** 用于百度自动推送，建议添加GET方式的CRON执行
** 仅适用于六零导航页1.3.5及以上版本，并且已配置伪静态，子链格式如：http://域名/site-66.html
--------------------------------------
** 使用方法：
** 获取推送接口：https://ziyuan.baidu.com/linksubmit/index
** 修改$api为自己的推送接口地址

*/
$api = 'http://data.zz.baidu.com/urls?site=https://hao.lylme.com&token=xxxxxxxx';

//以下内容无需修改
include_once("../include/common.php");
$urls = array();
array_push($urls,siteurl(),siteurl().'/apply',siteurl().'/about');
$sites = $DB->query("SELECT `id` FROM `lylme_links` WHERE `link_pwd` = 0");
while ( $site = $DB->fetch($sites)) {
    $url = siteurl().'/site-'.$site['id'].'.html';
    array_push($urls,$url);
}
$ch = curl_init();
$options =  array(
    CURLOPT_URL => $api,
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POSTFIELDS => implode("\n", $urls),
    CURLOPT_HTTPHEADER => array('Content-Type: text/html'),
);
curl_setopt_array($ch, $options);

$results = curl_exec($ch);
$httpCode = curl_getinfo($ch,CURLINFO_HTTP_CODE); 

echo $results;
if($httpCode == 200){
    $result = json_decode($results,true);
    echo('<p>>>>>>>Successful</p>
        本次推送：'.count($urls).'条<br>
        成功推送：'.$result['success'].'条<br>
        --------------------------------------<br>'.
        implode("<br>", $urls));
    }
else {
    echo('<p>推送失败<br><a href="https://ziyuan.baidu.com/college/courseinfo?id=267&page=3#h2_article_title12">帮助</a></p>');
}
?>