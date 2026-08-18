<?php
$title = '网站授权设置';
include './head.php';

$set = isset($_GET['set']) ? $_GET['set'] : null;
if ($set == 'save') {
	$licensecode = $_POST['licensecode'];
	$hostmode = $_POST['hostmode'];
	saveSetting('c', $licensecode, "程序授权码");
	saveSetting('hostmode', $hostmode, "域名获取方式");
	@unlink('log.txt');
	echo '<script>$.alert({title:"成功",content:"修改成功！",buttons:{confirm:{text:"确定",btnClass:"btn-primary",action:function(){window.location.href="./license.php";}}}});</script>';
} else {
	?>
	<main class="lyear-layout-content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-lg-12">
					<div class="card">
						<div class="card-header"><h4>授权说明</h4></div>
						<div class="card-body">
							<div class="d-flex align-items-start gap-3 mb-3">
								<i class="mdi mdi-wechat" style="font-size:28px;color:#07c160;flex-shrink:0"></i>
								<div>
									<h5 class="mt-0 mb-1">获取授权</h5>
									<p class="mb-0 text-muted">微信关注公众号【上云六零】按提示免费获取授权</p>
								</div>
							</div>
							<div class="d-flex align-items-start gap-3 mb-3">
								<i class="mdi mdi-update" style="font-size:28px;color:#3b82f6;flex-shrink:0"></i>
								<div>
									<h5 class="mt-0 mb-1">自动下发</h5>
									<p class="mb-0 text-muted">授权后点击<a href="./update.php">检查更新</a>会自动下发授权密钥，若未自动下发需手动填入</p>
								</div>
							</div>
							<div class="d-flex align-items-start gap-3">
								<i class="mdi mdi-key-variant" style="font-size:28px;color:#f59e0b;flex-shrink:0"></i>
								<div>
									<h5 class="mt-0 mb-1">查询密钥</h5>
									<p class="mb-0 text-muted">已授权用户可在【上云六零】公众号回复"查询授权"获取密钥</p>
								</div>
							</div>
						</div>
						<div class="card-footer">
							<a class="btn btn-outline-primary d-block w-100"
								href="https://www.lylme.com/spage/?url=<?php echo siteurl(2, 2) ?>"
								target="_blank"><i class="mdi mdi-magnify"></i> 授权查询</a>
						</div>
					</div>
	
					<div class="card">
						<div class="card-header"><h4>授权配置</h4></div>
						<div class="tab-content">
							<div class="tab-pane active">
								<form action="license.php?set=save" method="post" name="edit-form" class="edit-form"
									enctype="multipart/form-data">
									<div class="form-group">
										<label for="web_site_licensecode">六零导航页授权密钥</label>
										<div class="input-group">
											<input class="form-control license-input" type="text" id="web_site_licensecode" name="licensecode"
												value="<?php echo isset($conf['c']) ? htmlspecialchars($conf['c']) : '' ?>" placeholder="请输入16位授权密钥"
												maxlength="16">
											<span class="input-group-addon" id="license-counter" style="font-size:12px;color:#999;min-width:40px;text-align:center">0/16</span>
										</div>
										<small class="help-block">授权密钥为 <strong>16位</strong> 字母数字组合，微信关注【上云六零】公众号免费获取授权 &nbsp;|&nbsp; <a href="https://doc.lylme.com/spage/#/license" target="_blank">查看说明</a></small>
									</div>
									<div class="form-group">
										<label>域名获取方式</label>
										<small class="help-block" style="margin-bottom:8px">当前检测域名：<code><?php $hostmode = isset($conf['hostmode']) ? $conf['hostmode'] : "2";
										echo siteurl($hostmode) ?></code>，若与实际使用域名不符可切换下方模式</small>
										<div class="row" id="hostmode-cards">
											<div class="col-sm-6">
												<label class="radio-card<?php echo $hostmode == '1' ? ' active' : '' ?>">
													<input type="radio" value="1" name="hostmode" <?php echo $hostmode == '1' ? 'checked' : '' ?>>
													<div>
														<strong>模式1</strong> HTTP_HOST<br>
														<code><?php echo siteurl(1) ?></code>
													</div>
												</label>
											</div>
											<div class="col-sm-6">
												<label class="radio-card<?php echo $hostmode == '2' ? ' active' : '' ?>">
													<input type="radio" value="2" name="hostmode" <?php echo $hostmode == '2' ? 'checked' : '' ?>>
													<div>
														<strong>模式2</strong> SERVER_NAME<br>
														<code><?php echo siteurl(2) ?></code>
													</div>
												</label>
											</div>
										</div>
									</div>
									<div class="form-group">
										<button type="submit" class="btn btn-primary d-block w-100">保 存</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</main>
	<script>
	(function(){
		var input = document.getElementById('web_site_licensecode');
		var counter = document.getElementById('license-counter');
		function updateCounter(){
			var len = input.value.replace(/[^a-zA-Z0-9]/g,'').length;
			counter.textContent = len + '/16';
			counter.style.color = len === 16 ? '#22c55e' : (len > 0 ? '#f59e0b' : '#999');
		}
		input.addEventListener('input', updateCounter);
		input.addEventListener('keyup', function(){
			this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g,'');
			updateCounter();
		});
		updateCounter();

		var cards = document.querySelectorAll('.radio-card');
		cards.forEach(function(card){
			card.addEventListener('click', function(){
				cards.forEach(function(c){ c.classList.remove('active'); });
				card.classList.add('active');
			});
		});
	})();
	</script>
	<?php
}
include './footer.php';
?>