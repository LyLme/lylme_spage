<?php
header("status: 404 Not Found");
?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
<title>404 - 页面不存在</title>
<style>
body{background-color:#fff}
.error-page{height:100%;position:fixed;width:100%}
.error-body{display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;}
.error-body h1{font-size:188px;font-weight:700;text-shadow:4px 4px 0 #f5f6fa,6px 6px 0 #33cabb;color:#33cabb;line-height: 1rem;}
.error-body h4{margin:30px 0px;}
.go_home{
  color: #fff;
    background-color: #007bff;
    border-color: #0062cc;
    border: 1px solid transparent;
    padding: .375rem .75rem;
    font-size: 1rem;
    line-height: 1.5;
    border-radius: .25rem;
    box-shadow: 0 0 0 0.2rem rgba(38,143,255,.5);

    text-decoration: none;
}
.go_home:hover {
    background-color: #0069d9;
    border-color: #0062cc;
  }
</style>
</head>
<body>
<section class="error-page">
  <div class="error-box">
    <div class="error-body text-center">
      <h1>404</h1>
      <h4>抱歉，页面不存在！</h4>
      <a href="/" class="go_home">返回首页</a>
    </div>
  </div>
</section>
</body>
</html>

