<?php if(basename($_SERVER['PHP_SELF']) == basename(__FILE__)) header("Location:/"); ?>
<script src="https://lf6-cdn-tos.bytecdntp.com/cdn/expire-1-M/bootstrap/4.5.3/js/bootstrap.min.js" type="application/javascript"></script>
<script src="<?php echo $templatepath;?>/js/script.js?v=20220611"></script>
<script src="<?php echo $cdnpublic ?>/assets/js/svg.js"></script>
<div style="display:none;" class="back-to" id="toolBackTop"> 
<a title="返回顶部" onclick="window.scrollTo(0,0);return false;" href="#top" class="back-top"></a> 
</div> 
<div class="mt-5 mb-3 footer text-muted text-center"> 
  <!--备案信息-->
  <?php if($conf['icp'] != NULL){
  echo '<img src="./assets/img/icp.png" width="16px" height="16px" /><a href="http://beian.miit.gov.cn/" class="icp" target="_blank" _mstmutation="1" _istranslated="1">'.$conf['icp'].'</a>'; } ?> 
  <!--版权信息-->
  <p> <?php echo $conf['copyright']; ?></p>
  <!--网站统计-->
 <?php if($conf['wztj'] != NULL){echo $conf["wztj"];}?>
  </div>  
</html>