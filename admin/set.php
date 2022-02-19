<?php 
$title = '基本设置';
include './head.php';

$set=isset($_GET['set'])?$_GET['set']:null;
if($set=='save'){
	$title=$_POST['title'];
	$logo=$_POST['logo'];
	$background=$_POST['background'];
	$keywords=$_POST['keywords'];
	$description=$_POST['description'];
	$copyright=$_POST['copyright'];
	$icp=$_POST['icp'];


	saveSetting('title',$title);
	saveSetting('logo',$logo);
	saveSetting('background',$background);
	saveSetting('keywords',$keywords);
	saveSetting('description',$description);
	saveSetting('copyright',$copyright);
	saveSetting('icp',$icp);
	
	echo '<script>alert("修改成功！");history.go(-1);</script>';
}
else{
?>
	
    
    <!--页面主要内容-->
    <main class="lyear-layout-content">
      
      <div class="container-fluid">
        
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              
              <div class="tab-content">
                <div class="tab-pane active">
                  
                  <form action="set.php?set=save" method="post" name="edit-form" class="edit-form">
                    <div class="form-group">
                      <label for="web_site_title">网站标题</label>
                      <input class="form-control" type="text" id="web_site_title" name="title" value="<?php echo $conf['title']?>" placeholder="请输入站点标题" required >
                      <!--<small class="help-block">调用方式：<code>config('web_site_title')</code></small>-->
                    </div>
                    <div class="form-group">
                      <label for="web_site_logo">网站LOGO</label>
                      <!--<div class="input-group">-->
                        <input type="text" class="form-control" name="logo" id="web_site_logo" value="<?php echo $conf['logo']?>" />
                        <small class="help-block">填写相对于网站根目录的绝对地址或http开头的url</small>
                        <!--<div class="input-group-btn"><button class="btn btn-default" type="button">上传图片</button></div>-->
                      </div>
                     <div class="form-group">
                      <label for="web_site_logo">网站背景</label>
                      <!--<div class="input-group">-->
                        <input type="text" class="form-control" name="background" id="web_site_background" value="<?php echo $conf['background']?>" />
                        <small class="help-block">填写相对于网站根目录的绝对地址或http开头的url</small>
                        <!--<div class="input-group-btn"><button class="btn btn-default" type="button">上传图片</button></div>-->
                      </div>
                    </div>
                    
                    <div class="form-group">
                      <label for="web_site_keywords">站点关键词</label>
                      <input class="form-control" type="text" id="web_site_keywords" name="keywords" value="<?php echo $conf['keywords']?>" placeholder="请输入站点关键词" >
                      <small class="help-block">网站搜索引擎关键字</small>
                    </div>
                    <div class="form-group">
                      <label for="web_site_description">站点描述</label>
                      <textarea class="form-control" id="web_site_description" rows="5" name="description" placeholder="请输入站点描述" ><?php echo $conf['description']?></textarea>
                      <small class="help-block">网站描述，有利于搜索引擎抓取相关信息</small>
                    </div>
                   
                    <div class="form-group">
                      <label for="web_site_copyright">版权所有</label>
                      <input class="form-control" type="text" id="web_site_copyright" name="copyright" value="<?php echo $conf['copyright']?>" placeholder="请输入版权信息" >
                    </div>
                    <div class="form-group">
                      <label for="web_site_icp">备案信息</label>
                      <input class="form-control" type="text" id="web_site_icp" name="icp" value="<?php echo $conf['icp']?>" placeholder="请输入备案信息" >
                      
                    </div>
                    <!--<div class="form-group">-->
                    <!--  <label class="btn-block" for="web_site_status">站点开关</label>-->
                    <!--  <label class="lyear-switch switch-solid switch-primary">-->
                    <!--    <input type="checkbox" checked="">-->
                    <!--    <span></span>-->
                    <!--  </label>-->
                    <!--  <small class="help-block">站点关闭后将不能访问，后台可正常登录</small>-->
                    <!--</div>-->
                    <div class="form-group">
                      <button type="submit" class="btn btn-primary m-r-5">保 存</button>
                    
                    </div>
                  </form>
                  
                </div>
              </div>

            </div>
          </div>
          
        </div>
        
      </div>
      
    </main>
    <!--End 页面主要内容-->
  </div>
</div>

<?php 
include './footer.php';
}

?>