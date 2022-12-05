<html lang="zh-cn">
  <head>
    <meta charset="UTF-8">
    <title><?php echo $site['name'] . " - " .explode("-", $conf['title'])[0];?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="keywords" content="<?php echo $site['name'].','.$conf['keywords']?>" />
	<meta name="description" content="<?php echo explode("-", $conf['title'])[0];?>已收录站点：<?php echo $site['name'].'。'.$conf['description']?>" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <link href="https://lf9-cdn-tos.bytecdntp.com/cdn/expire-1-M/docsify/4.12.2/themes/vue.min.css" type="text/css" rel="stylesheet">
    <style>
    body{background:#fff!important}
    body:not(.ready){overflow:auto!important}
    #main{max-width:90%}
    p.footer{margin-top:60px}
    a{text-decoration:none}
    svg.icon{width:40px;height:40px}
    p.footer{width:100%;position:fixed;bottom:0;text-align:center}
    span.site_name{font-size:22px;margin:0;font-weight:700;margin-left:5px}
    .service-wrap-w a{color:#fff!important}
    #SOHUCS #SOHU_MAIN .module-mobile-cmt-header{background-color:#fff!important}
    </style>
   <link href="https://cdn.lylme.com/admin/lyear/css/style.min.css" rel="stylesheet">
 </head>
 <body> 

<div class="markdown-section" id="main">
<?php 
echo $site["icon"].'<span class="site_name">'. $site["name"].'</span>';
if(!empty($info['title'])){
    echo '<p><b>网站标题：</b>'.$info['title'].'</p>';
}
if(!empty($info['description'])){
    echo '<p><b>网站描述：</b>'.$info['description'].'</p>';
}
if(!empty($info['keywords'])){
    echo '<p><b>网站关键词：</b>'.$info['keywords'].'</p>';
}

echo '<p><b>所在分组：</b>'.$group['group_name'].'</p>
';
echo "<p><a  class='btn btn-pink' href='/'>返回</a> <a rel='nofollow' class='btn btn-success' href='". $site["url"]."'>访问</a></p>";
?>

<script src="/assets/js/svg.js"></script>
</div>
</body>
</html>