<?php
/* 
 * @Description: 后台网站配置
 * @Author: LyLme admin@lylme.com
 * @Date: 2024-01-23 12:25:35
 * @LastEditors: LyLme admin@lylme.com
 * @LastEditTime: 2024-04-14 14:08:30
 * @FilePath: /lylme_spage/admin/set.php
 * @Copyright (c) 2024 by LyLme, All Rights Reserved. 
 */
$title = '网站设置';
include './head.php';
$last = date("Ym");
if (@file_get_contents('log.txt') != $last || !file_exists('cache.php')) {
    $update = update();
    file_put_contents('log.txt', $last);
    var_export($update, true);
    $content = "<?php\nreturn " . var_export($update, true) . "\n?>";
    file_put_contents('cache.php', $content);
}
function uploadimg($arr, $uppath, $uptype)
{
	if ((($arr["type"] == "image/jpeg") || ($arr["type"] == "image/jpg") || ($arr["type"] == "image/png")) && $arr["size"] < 10485760) {
		copy($arr["tmp_name"], ROOT . $uppath);
		saveSetting($uptype, '/' . $uppath);
	} elseif ($arr["size"] == 0) {
	} else {
		echo '<script>alert("上传的图片大小超过10MB或类型不符！");history.go(-1);</script>';
	}
}
$set = isset($_GET['set']) ? $_GET['set'] : null;
if ($set == 'save') {
	$title = $_POST['title'];
	$logo = $_POST['logo'];
	$background = $_POST['background'];
	$wapbackground = $_POST['wapbackground'];
	$keywords = $_POST['keywords'];
	$description = $_POST['description'];
	$mode = $_POST['mode'];
	$snapshot = $_POST['snapshot'];
	$licensecode = $_POST['licensecode'];
	$copyright = $_POST['copyright'];
	$icp = $_POST['icp'];
	$yan = $_POST['yan'];
	$wztj = $_POST['wztj'];
	$cdnpublic = $_POST['cdnpublic'];
	if ($yan == 'true') {
		saveSetting('yan', 'true');
	} else {
		saveSetting('yan', 'false');
	}
	saveSetting('tq', 'false');
	saveSetting('title', $title, "网站名称");
	saveSetting('logo', $logo, "网站LOGO");
	saveSetting('background', $background, "背景图片");
	saveSetting('wap_background', $wapbackground, "手机背景图片");
	saveSetting('keywords', $keywords, "网站关键词");
	saveSetting('description', $description, "网站描述");
	saveSetting('mode', $mode, "网站运行模式");
	saveSetting('snapshot', $snapshot, "详情页快照API");
	saveSetting('copyright', $copyright, "底部版权");
	saveSetting('icp', $icp, "ICP备案号");
	saveSetting('wztj', $wztj, "自定义footer");
	saveSetting('cdnpublic', $cdnpublic, "CDN地址");
	saveSetting('c', $licensecode, "程序授权码");
	uploadimg($_FILES["logoimg"], 'assets/img/web-logo.png', 'logo');
	uploadimg($_FILES["wapbackgroundimg"], 'assets/img/web-wapbackground.jpg', 'wap_background');
	uploadimg($_FILES["backgroundimg"], 'assets/img/web-background.jpg', 'background');
	echo '<script>alert("修改成功！");window.location.href="./set.php";</script>';
} else {
?>
	<script>
		function updatetext(check) {
			document.getElementById(check).innerHTML = "重新选择";
		}
	</script>
	<!--页面主要内容-->
	<main class="lyear-layout-content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-lg-12">
					<div class="card">
						<div class="tab-content">
							<div class="tab-pane active">
								<form action="set.php?set=save" method="post" name="edit-form" class="edit-form" enctype="multipart/form-data">
									<div class="form-group">
										<label for="web_site_title">网站标题</label>
										<input class="form-control" type="text" id="web_site_title" name="title" value="<?php echo $conf['title'] ?>" placeholder="请输入站点标题" required>
									</div>
									<div class="form-group">
										<label for="web_site_logo">网站LOGO</label>
										<div class="input-group">
											<input type="text" class="form-control" name="logo" id="web_site_logo" value="<?php echo $conf['logo'] ?>" />
											<div class="input-group-btn">
												<label class="btn btn-default" for="logoimg" id="checklogo" type="button">选择图片</label>
												<input type="file" style="display:none" accept=".png,.jpeg,.jpg" id="logoimg" name="logoimg" onclick="updatetext('checklogo');" />
											</div>
										</div>
										<small class="help-block">比例1:1(正方形)，可填写图片的URL，默认值：<code>./assets/img/logo.png</code>或<code><?php echo siteurl() ?>/assets/img/logo.png</code>或从<code>本地上传</code></small>
									</div>
									<div class="form-group">
										<label for="web_site_background">网站背景</label>
										<div class="input-group">
											<input type="text" class="form-control" name="background" accept="image/png,image/jpeg" id="web_site_background" value="<?php echo $conf['background'] ?>" />
											<div class="input-group-btn">
												<label class="btn btn-default" id="checkbackground" for="backgroundimg" type="button">选择图片</label>
												<input type="file" style="display:none" accept="image/png,image/jpeg" id="backgroundimg" name="backgroundimg" onclick="updatetext('checkbackground');" />
											</div>
										</div>
										<small class="help-block">填写图片的URL,如：<code>/assets/img/background.jpg</code>或从<code>本地上传</code><br>设置Bing每日壁纸：<code>/assets/img/bing.php</code><br>注：修改后需要清除浏览器缓存才会改变</small>
									</div>
									<div class="form-group">
										<label for="wap_site_background">手机端背景图片</label>
										<div class="input-group">
											<input type="text" class="form-control" name="wapbackground" accept="image/png,image/jpeg" id="wap_site_background" value="<?php echo $conf['wap_background'] ?>" />
											<div class="input-group-btn">
												<label class="btn btn-default" id="checkwapbackground" for="wapbackgroundimg" type="button">选择图片</label>
												<input type="file" style="display:none" accept="image/png,image/jpeg" id="wapbackgroundimg" name="wapbackgroundimg" onclick="updatetext('checkwapbackground');" />
											</div>
										</div>
										<small class="help-block">手机端独立背景，留空则使用PC端壁纸<br>注:修改后需要清除浏览器缓存才会改变</small>
									</div>
									<div class="form-group">
										<label for="web_site_keywords">站点关键词</label>
										<input class="form-control" type="text" id="web_site_keywords" name="keywords" value="<?php echo $conf['keywords'] ?>" placeholder="请输入站点关键词">
										<small class="help-block">网站搜索引擎关键字</small>
									</div>
									<div class="form-group">
										<label for="web_site_description">站点描述</label>
										<textarea class="form-control" id="web_site_description" rows="2" name="description" placeholder="请输入站点描述"><?php echo $conf['description'] ?></textarea>
										<small class="help-block">网站描述，用于搜索引擎抓取相关信息</small>
									</div>


									<div class="form-group">
										<label for="web_site_home-title">运行模式</label>
										<label class="lyear-radio radio-primary m-t-10">
											<input type="radio" <?php if (!$mode = $conf['mode'] == 2) echo 'checked="checked"'; ?> value="1" name="mode">
											<span>直接访问(默认)</span>
										</label>
										<label class="lyear-radio radio-primary m-t-10">
											<input type="radio" <?php if ($mode) echo 'checked="checked"'; ?> value="2" name="mode">
											<span>详情页模式</span>
										</label>

										<small class="help-block">导航链接访问方式,详情页模式需要启用伪静态 <a href="https://doc.lylme.com/spage/#/urlwrite" target="_blank">查看教程</a></small>
									</div>

									<div class="form-group">
										<label for="web_site_copyright">版权信息</label>
										<textarea width="200px" type="text" rows="3" class="form-control" name="copyright" placeholder="请输入版权信息，支持HTML代码"><?php echo $conf['copyright'] ?></textarea>
										<small class="help-block">显示在首页底部的版权提示，<code>支持HTML代码</code></small>
									</div>
									<div class="form-group">
										<label for="web_site_wztj">自定义footer</label>
										<textarea type="text" rows="10" class="form-control" name="wztj" placeholder="可填写网站统计、引用JS文件等"><?php echo $conf['wztj'] ?></textarea>
										<small class="help-block">站点底部自定义，可填写网站统计、JS代码(需要script标签)、CSS代码(需要style标签)等<code>支持HTML代码</code> <a href="https://doc.lylme.com/spage/#/footer" target="_blank">查看教程</a></small>
									</div>
									<div class="form-group">
										<label for="web_site_icp">备案号</label>
										<input class="form-control" type="text" id="web_site_icp" name="icp" value="<?php echo $conf['icp'] ?>" placeholder="请输入备案号，留空首页不显示备案信息">
									</div>
									<div class="form-group">
										<label class="btn-block" for="web_yan_status">随机一言开关</label>
										<label class="lyear-switch switch-solid switch-cyan">
											<input type="checkbox" <?php if ($conf['yan'] != 'false') {
																		echo 'checked="checked"';
																	} ?> name="yan" value="true">
											<span></span>
										</label>
										<small class="help-block">显示在首页的随机一言，自定义一言文件路径，一行一条<code>/assets/date/date.dat</code> </small>
									</div>
					
									<div class="form-group">
										<label for="web_site_snapshot">详情页快照生成API</label>
										<input class="form-control" type="text" id="web_site_snapshot" name="snapshot" value="<?php echo $conf['snapshot'] ?>" placeholder="请输入API接口地址">
										<small class="help-block">用于详情页生成网站缩略图，不填不启用，若不了解请留空 <a href="https://doc.lylme.com/spage/#/snapshot" target="_blank">查看教程</a></small>
									</div>
									<div class="form-group">
										<label for="web_site_licensecode">六零导航页授权密钥</label>
										<input class="form-control" type="text" id="web_site_licensecode" name="licensecode" value="<?php echo $conf['c'] ?>" placeholder="请输入授权密钥">
										<small class="help-block">微信关注【上云六零】公众号免费获取授权，授权后点击<a href="./update.php">检查更新</a>会自动下发授权密钥。<a href="https://doc.lylme.com/spage/#/license" target="_blank">查看说明</a> <br>内网用户需手动填入，当前域名：<code><?php echo $_SERVER['HTTP_HOST'] ?></code><br>授权正常请勿修改密钥</small>
									</div>
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
<?php
}
include './footer.php';
?>