<?php
/* 
 * @Description: 关于页面
 * @Author: LyLme admin@lylme.com
 * @Date: 2024-01-23 12:25:35
 * @LastEditors: LyLme admin@lylme.com
 * @LastEditTime: 2026-03-22 18:10:13
 * @FilePath: /lylme_spage/admin/about.php
 * @Copyright (c) 2024 by LyLme, All Rights Reserved. 
 */
$title = '关于页面设置';
include './head.php';
$set = isset($_GET['set'])?$_GET['set']:"";
if ($set== 'conf_submit') {
    $about = $_POST['about'];
    saveSetting('about_content', $about);
    echo '<script>$.alert({title:"成功",content:"修改成功！",buttons:{confirm:{text:"确定",btnClass:"btn-primary",action:function(){window.location.href="./about.php";}}}});</script>';
    exit();
}
if ($set == 'default') {

    saveSetting('about_content', "<h3>关于本站</h3>\r\n<p>欢迎访问本站，这是一个开源的网址导航与搜索入口项目，旨在提供简洁、轻量的上网起始页体验。</p>\r\n<p>如果您喜欢本站，可将本页添加到收藏夹（快捷键 <code>Ctrl+D</code>）方便下次访问；也可将其设为浏览器主页。感谢您的支持。</p>\r\n<hr>\r\n<h3>隐私说明</h3>\r\n<p>本项目为开源程序，默认仅提供网址导航与跳转功能，链接直接指向目标地址，不对访问链接做二次中转。</p>\r\n<p>程序本身不强制收集用户隐私信息（如点击记录、访问记录、搜索记录等）。但您所访问的具体实例由部署者自行搭建与维护，其实际的数据收集与处理方式以该实例部署者的隐私政策为准。建议您在使用前了解所在实例的相关说明，并注意保护个人信息。</p>\r\n<hr>\r\n<h3>申请收录</h3>\r\n<p>如需将您的网站加入导航，请点<a href=\"../apply\" target=\"_blank\">这里</a>提交申请。</p>\r\n<hr>\r\n<h3>联系我们</h3>\r\n<p>若您在使用本站时遇到了以下问题，欢迎与我们联系：</p>\r\n<ul>\r\n<li>图标缺失</li>\r\n<li>目标网站无法打开</li>\r\n<li>描述错误</li>\r\n<li>网站违规</li>\r\n<li>收录加急处理</li>\r\n<li>链接删除</li>\r\n</ul>\r\n<h5>联系方式</h5>\r\n<ul>\r\n<li>邮箱：<a href=\"mailto:#\">未配置</a></li>\r\n</ul>\r\n<h5>联系说明</h5>\r\n<p>为了您的问题能快速被处理，建议在邮件主题中添加【反馈】【投诉】【建议】【友链】等标识。</p>");
    echo '<script>$.alert({title:"成功",content:"恢复默认成功！",buttons:{confirm:{text:"确定",btnClass:"btn-primary",action:function(){window.location.href="./about.php";}}}});</script>';
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
                                    <label class="d-block w-100" for="web_yan_status">关于页面地址</label>
                                    <p><code><?php echo siteurl() ?>/about</code></p>
                                    <a class="btn btn-primary" href="<?php echo siteurl() ?>/about" target="_blank">访问关于页面</a>
                                    <a class="btn btn-danger" href="./about.php?set=default" onclick="var h=this.href;event.preventDefault();$.confirm({title:'警告',content:'确定将关于页面内容恢复默认？<br>注意：该操作不可逆',type:'red',buttons:{confirm:{text:'确定恢复',btnClass:'btn-danger',action:function(){window.location.href=h;}},cancel:{text:'取消'}}});return false;">恢复默认内容</a>
                                </div>
                                <div class="form-group">
                                    <label for="about">关于页内容</label>
                                    <textarea width="200px" type="text" rows="20" class="form-control" name="about" placeholder="显示在关于页面的内容"><?php echo($conf['about_content']); ?></textarea>
                                    <small class="help-block">显示在关于页面的内容<code>使用HTML代码编写</code></small>
                                    工具：<a href="https://www.lylme.com/html/" target="_blank">在线MD编辑器</a> 编辑后复制html代码粘贴
                                </div>
                                <div class="form-about">
                                    <input type="submit" class="btn btn-primary d-block w-100" value="保存">
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