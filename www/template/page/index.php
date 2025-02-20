<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1"/>
    <title><?php echo $conf['title']?></title>
	<meta name="keywords" content="<?php echo $conf['keywords']?>">
	<meta name="description" content="<?php echo $conf['description']?>">
	<link rel="icon" href="<?php echo $conf['logo']?>" type="image/x-icon">
    <link rel="stylesheet" href="<?php echo $cdnpublic ?>/template/page/css/main.css">
    <style>#bg:after {background-image: url("<?php echo background(); ?>");}</style>
</head>
<body>
<div id="wrapper">
	<header id="header">
	    <div class="logo">
		    <img src="<?php echo $conf['logo']?>" class="logo">
	    </div>
	    <div class="content">
		    <div class="inner">
			    <?php echo theme_config('home_title'); ?>
		    </div>
	    </div>
	    <nav>
	        <ul>
<?php
$tagslists = $site->getTags();
    while ($taglists = $DB->fetch($tagslists)) {
        echo '
        <li><a href="' . $taglists["tag_link"] . '"';
        if ($taglists["tag_target"] == 1) {
            echo ' target="_blank"';
        }
        echo '>' . $taglists["tag_name"] . '</a></li>
 ';
    }
    ?>
	        </ul>
	    </nav>
	</header>
	<footer id="footer">
		 
	<p class="copyright">
	 <!--备案信息-->
	 <?php if (!empty($conf['icp'])) {
                echo '<p><a href="http://beian.miit.gov.cn/"  class="icp nav-link" target="_blank" _mstmutation="1" _istranslated="1">' . $conf['icp'] . '</a></p>';
            }
            ?>	
	<?php echo $conf['copyright']; ?></p>
	</footer>
</div>
<div id="bg">
</div>
<script src="https://lf3-cdn-tos.bytecdntp.com/cdn/expire-2-M/jquery/3.5.1/jquery.min.js" type="application/javascript"></script>
<script src="https://lf3-cdn-tos.bytecdntp.com/cdn/expire-1-M/skel/3.0.1/skel.min.js" type="application/javascript"></script>
</body>
</html>