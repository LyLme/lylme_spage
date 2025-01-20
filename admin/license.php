<?php
$title = '网站授权设置';
include './head.php';

$set = isset($_GET['set']) ? $_GET['set'] : null;
if ($set == 'save') {
	$licensecode = $_POST['licensecode'];
	$hostmode =  $_POST['hostmode'];
	saveSetting('c', $licensecode, "程序授权码");
	saveSetting('hostmode', $hostmode, "域名获取方式");
	echo '<script>alert("修改成功！");window.location.href="./license.php";</script>';
} else {
?>
	<!--页面主要内容-->
	<main class="lyear-layout-content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-lg-12">
					<div class="card">
						<div class="card-header">
							<h4>六零导航页正版授权</h4>
						</div>
						<ul class="list-group">
							
							<li class="list-group-item">微信关注公众号【上云六零】按提示免费获取授权 </li>
							<li class="list-group-item">授权后点击<a href="./update.php">检查更新</a>会自动下发授权密钥，若未自动下发成功需复制粘贴授权秘钥到下方编辑框</li>
							<li class="list-group-item">授权正常请勿修改密钥，内网用户需手动填入授权密钥，已授权用户可在[上云六零]公众号回复”查询授权“查询授权秘钥</li>
							<a class="btn btn-default btn-block" href="https://www.lylme.com/spage/?url=<?php echo  siteurl(2, 2) ?>" target="_blank">授权查询</a>
						</ul>
						
					</div>

					<div class="card">
						<div class="tab-content">
							<div class="tab-pane active">

								<form action="license.php?set=save" method="post" name="edit-form" class="edit-form" enctype="multipart/form-data">

									<div class="form-group">
										<label for="web_site_licensecode">六零导航页授权密钥</label>
										<input class="form-control" type="text" id="web_site_licensecode" name="licensecode" value="<?php echo isset($conf['c']) ? $conf['c'] : '' ?>" placeholder="请输入授权密钥">

										<small class="help-block">当前域名：<code><?php echo  siteurl(2, 2) ?></code>，若与实际使用域名不符，可在下方切换"域名获取方式切换<br>微信关注【上云六零】公众号免费获取授权，授权后点击<a href="./update.php">检查更新</a>会自动下发授权密钥。<a href="https://doc.lylme.com/spage/#/license" target="_blank">查看说明</a> </small>
									</div>
									<div class="form-group">
										<label for="web_site_home-title">域名获取方式</label>
										<label class="lyear-radio radio-primary m-t-10">
											<input type="radio" <?php $hostmode = isset($conf['hostmode']) ? $conf['hostmode'] : "2";
																if ($hostmode == "1") {
																	echo 'checked="checked"';
																} ?> value="1" name="hostmode"> <span><strong>[模式1_HTTP_HOST]</strong>[<code><?php echo siteurl(1) ?></code>]</span>
										</label>
										<label class="lyear-radio radio-primary m-t-10">
											<input type="radio" <?php if ($hostmode == "2") {
																	echo 'checked="checked"';
																} ?> value="2" name="hostmode">
											<span><strong>[模式2_SERVER_NAME]</strong>[<code><?php echo siteurl(2) ?></code>]</span>
										</label>

										<small class="help-block">请选择您实际使用的域名，用于授权验证</small>
									</div>
									<div class="form-group">
										<button type="submit" class="btn btn-primary btn-block">保 存</button>
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
<?php
}
include './footer.php';
?>