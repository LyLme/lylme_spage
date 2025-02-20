<?php
/* 
 * @Description: 
 * @Author: LyLme admin@lylme.com
 * @Date: 2024-01-23 12:25:35
 * @LastEditors: LyLme admin@lylme.com
 * @LastEditTime: 2024-04-14 05:29:51
 * @FilePath: /lylme_spage/admin/about.php
 * @Copyright (c) 2024 by LyLme, All Rights Reserved. 
 */
$title = '关于页面设置';
include './head.php';
$set = isset($_GET['set'])?$_GET['set']:"";
if ($set== 'conf_submit') {
    $about = $_POST['about'];
    saveSetting('about_content', $about);
    echo '<script>alert("修改成功！");window.location.href="./about.php";</script>';
    exit();
}
if ($set == 'default') {

    saveSetting('about_content', "<h3>关于本站</h3>\r\n<p>感谢来访，本站致力于简洁高效的上网导航和搜索入口，安全快捷。</p>\r\n<p>如果您喜欢我们的网站，请将本站添加到收藏夹（快捷键<code>Ctrl+D</code>），并<a href=\"https://jingyan.baidu.com/article/4dc40848868eba89d946f1c0.html\" target=\"_blank\">设为浏览器主页</a>，方便您的下次访问，感谢支持。<p>\r\n<hr>\r\n<h3>本站承诺</h3>\r\n<p><strong>绝对不会收集用户的隐私信息</strong><p>\r\n<p>区别于部分导航网站，本站链接直接跳转目标，不会对链接处理再后跳转，不会收集用户的隐藏信息，包括但不限于点击记录，访问记录和搜索记录，请放心使用</p>\r\n<hr>\r\n<h3>申请收录</h3>\r\n<p>请点<a href=\"../apply\" target=\"_blank\">这里</a></p>\r\n<hr>\r\n<h3>联系我们</h3>\r\n<p>若您在使用本站时遇到了包括但不限于以下问题：</p>\r\n<li>图标缺失</li>\r\n<li>目标网站无法打开</li>\r\n<li>描述错误</li>\r\n<li>网站违规</li>\r\n<li>收录加急处理</li>\r\n<li>链接删除</li>\r\n<p><strong>请发邮件与我们联系</strong></p>\r\n<h5>联系邮箱</h5>\r\n<p><a href=\"mailto:无\">无</a></p>\r\n<h5>联系说明</h5>\r\n<p>为了您的问题能快速被处理，建议在邮件主题添加【反馈】【投诉】【推荐】【友链】</p>");
    echo '<script>alert("恢复默认成功！");window.location.href="./about.php";</script>';
    exit();
}
?>
<main class="lyear-layout-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4>修改收录设置</h4>
                        <div class="panel-body">
                            <form action="./about.php?set=conf_submit" method="POST">
                                <div class="form-group" id="about">
                                    <label class="btn-block" for="web_yan_status">关于页面地址</label>
                                    <p><code><?php echo siteurl() ?>/about</code></p>
                                    <a class="btn btn-cyan" href="<?php echo siteurl() ?>/about" target="_blank">访问关于页面</a>
                                    <a class="btn btn-danger" href="./about.php?set=default" onclick="return confirm('确定将关于页面内容恢复默认？\n注意：该操作不可逆');">恢复默认内容</a>
                                </div>
                                <div class="form-group">
                                    <label for="about">关于页内容</label>
                                    <textarea width="200px" type="text" rows="20" class="form-control" name="about" placeholder="显示在关于页面的内容"><?php echo($conf['about_content']); ?></textarea>
                                    <small class="help-block">显示在关于页面的内容<code>使用HTML代码编写</code></small>
                                    工具：<a href="https://www.lylme.com/html/" target="_blank">在线MD编辑器</a> 编辑后复制html代码粘贴
                                </div>
                                <div class="form-about">
                                    <input type="submit" class="btn btn-primary btn-block" value="保存">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
include './footer.php';
?>