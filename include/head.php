<?php if(basename($_SERVER['PHP_SELF']) == basename(__FILE__)) header("Location:/"); include 'common.php';?>
<!DOCTYPE html>
<html lang="zh-CN" element::-webkit-scrollbar {display:none}>
	<head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1"/>
		<title><?php echo $conf['title']?></title>
		<meta name="keywords" content="<?php echo $conf['keywords']?>">
		<meta name="description" content="<?php echo $conf['description']?>">
		<meta name="author" content="LyLme">
		<link rel="icon" sizes="any" mask href="<?php echo $conf['logo']?>">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-touch-fullscreen" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="full-screen" content="yes">
		<meta name="browsermode" content="application">
		<meta name="x5-fullscreen" content="true">
		<meta name="x5-page-mode" content="app">
		<meta name="lsvn" content="MS4xLjE=">
		<script src="https://lf3-cdn-tos.bytecdntp.com/cdn/expire-2-M/jquery/3.5.1/jquery.min.js" type="application/javascript"></script>
		<link href="https://lf26-cdn-tos.bytecdntp.com/cdn/expire-1-M/bootstrap/4.5.3/css/bootstrap.min.css" type="text/css" rel="stylesheet">
		<script src="https://lf6-cdn-tos.bytecdntp.com/cdn/expire-1-M/bootstrap/4.5.3/js/bootstrap.min.js" type="application/javascript"></script>
		<link rel="stylesheet" href="https://cdn.lylme.com/lylmes_page/<?php echo $conf['version']?>/assets/css/style.css" type="text/css">
		<link rel="stylesheet" href="https://cdn.lylme.com/lylmes_page/<?php echo $conf['version']?>/assets/css/fontawesome-free5.13.0.css" type="text/css">
		<link rel="stylesheet" href="./assets/css/font.css" type="text/css">
		<script src="https://cdn.lylme.com/lylmes_page/<?php echo $conf['version']?>/assets/js/script.js"></script>
		<script src="https://cdn.lylme.com/lylmes_page/<?php echo $conf['version']?>/assets/js/svg.js"></script>
	</head>