<?php 
$title = '微信推送';
include './head.php';
$set=isset($_GET['set'])?$_GET['set']:null;
if($set=='save') {
	$userkey=$_POST['userkey'];
	$wxplustime=$_POST['wxplustime'];
	saveSetting('wxplus',$userkey);
	saveSetting('wxplustime',$wxplustime);
	if(empty($userkey)) {
		echo '<script>alert("微信推送已关闭");window.location.href="./wxplus.php";</script>';
	} else {
		echo '<script>alert("微信推送密钥配置成功");window.location.href="./wxplus.php";</script>';
	}
} else {
	?>
	    <!--页面主要内容-->
	    <main class="lyear-layout-content">
	      <div class="container-fluid">
	        <div class="row">
	          <div class="col-lg-12">
	            <div class="card">
	              <div class="card-body">
	                <form method="post" action="wxplus.php?set=save" class="site-form">
	                    <div class="form-group">
	                    <label class="control-label">微信推送密钥</label>
	                    <textarea type="text" class="form-control" name="userkey" id="userkey" ><?php echo $conf['wxplus'];
	?></textarea>
	                    </div>
	                    <div class="form-group">
	                  <label class="control-label">微信推送时间</label>
	                     <input class="form-control" type="time" id="wxplustime" name="wxplustime" placeholder="请选择推送提醒时间" value="<?php echo $conf['wxplustime'];?>" />
	                      </div>
	                   <p class="m-t-15">
	                   1. 功能说明：开启微信推送，用户提交申请收录会推送提醒到<code>上云六零</code>公众号<br>
	                   2. 开启功能：填写用户的微信推送密钥<br>
	                   3. 关闭功能：编辑框留空保存即可关闭该功能<br>
	                   4. 获取密钥：微信关注公众号<code>上云六零</code>回复<code>推送密钥</code>复制到编辑框中保存即可<br>
	                   5. 温馨提示：为了避免打扰，每天最多推送一次。当日<?php echo $conf['wxplustime'];?>前的提交会在<?php echo $conf['wxplustime'];?>前推送，之后提交的在次日<?php echo $conf['wxplustime'];?>前推送</p>
	                  <button type="submit" class="btn btn-primary">保存</button>
	                </form>
	              </div>
	            </div>
	          </div>
	        </div>
	      </div>
	    </main>
<?php
}
include './footer.php';
?>