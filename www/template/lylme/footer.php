<?php if(basename($_SERVER['PHP_SELF']) == basename(__FILE__)) header("Location:/"); ?>
<script src="<?php echo $cdnpublic ?>/template/lylme/js/script.js?v=20240409"></script>
<script src="<?php echo $cdnpublic ?>/assets/js/svg.js"></script>
<div style="display:none;" class="back-to" id="toolBackTop"> 
<a title="返回顶部" onclick="window.scrollTo(0,0);return false;" href="#top" class="back-top"></a> 
</div> 
<div class="mt-5 mb-3 footer text-muted text-center"> 
  <!--备案信息-->
  <?php if($conf['icp'] != NULL){
  echo '<a href="http://beian.miit.gov.cn/" class="icp" target="_blank" _mstmutation="1" _istranslated="1">'.$conf['icp'].'</a>'; } ?> 
  <!--版权信息-->
  <p> <?php echo $conf['copyright']; ?></p>
  <!--网站统计-->
 <?php if($conf['wztj'] != NULL){echo $conf["wztj"];}?>
  </div>  
    <script>

		</script>
 </body>
</html>