<?php 
$title = '后台管理';
include './head.php';
$title = 'SVG代码获取教程';
?>
    <main class="lyear-layout-content">
      
      <div class="container-fluid">
        
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-body">
<?php
$set=isset($_GET['doc'])?$_GET['doc']:null;
if($set=='icon'){
?>
<div class="card">
              <div class="card-header"><h4>SVG代码获取教程</h4></div>
              <div class="card-body">
                
               <p>1. 以阿里图标库为例，访问<a href="https://www.iconfont.cn/" target="_blank">https://www.iconfont.cn/</a>注册登录</p>
              <p>2. 点击网站顶部的搜索，输入需要的图标名称，在出现的结果中选择适合的图标</p>  
              <p>3. 鼠标悬停在图标上，会出现三个选项，选择最下面的下载按钮</p>
              <p>4. 可以按个人喜好选择适合的颜色及大小（一般默认即可）</p>
              <p>5. 点击底部的<b>复制SVG代码</b>，粘贴到图标的编辑框内</p>
              <p><font color='red'><b>注意：<b>SVG代码必须以<code>&lt;svg </code>开头，粘贴到编辑框的SVG代码前面不能包含任何字符包括空格，否则将导致报错</font></p>
              </div>
            </div>

              </div>
            </div>
          </div>
          
        </div>
        
      </div>
      
    </main>
 
<?php 
}
else {
 echo '<script>alert("无效的访问！");history.go(-1);</script>';
}
include './footer.php';


?>