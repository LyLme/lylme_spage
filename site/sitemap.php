<?php
include_once("../include/common.php");
header("Content-type: text/xml");
header('HTTP/1.1 200 OK');
echo '<?xml version="1.0" encoding="utf-8"?>'."\n".'<urlset>'."\n";
?>
    <url>
        <loc><?php echo siteurl()?></loc>
        <lastmod><?php echo date('Y-m-d');?></lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc><?php echo siteurl().'/apply'?></loc>
        <lastmod><?php echo date('Y-m');?>-01</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc><?php echo siteurl().'/about'?></loc>
        <lastmod><?php echo date('Y-m');?>-01</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.9</priority>
    </url>
    <?php
    $sites = $DB->query("SELECT `id` FROM `lylme_links` WHERE `link_pwd` = 0");
    while ( $site = $DB->fetch($sites)) { ?>
    <url>
        <loc><?php echo(siteurl().'/site-'.$site['id'].'.html');?></loc>
        <lastmod><?php echo date('Y-m');?>-01</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    <?php } ?>
</urlset>