<?php
header("status: 404 Not Found");
?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
<title>404 - 页面不存在</title>
<link href="https://lf9-cdn-tos.bytecdntp.com/cdn/expire-1-M/bootstrap/5.1.3/css/bootstrap.min.css" type="text/css" rel="stylesheet" />
<style>
body{background-color:#fff}
.error-page{height:100%;position:fixed;width:100%}
.error-body{padding-top:5%}
.error-body h1{font-size:210px;font-weight:700;text-shadow:4px 4px 0 #f5f6fa,6px 6px 0 #33cabb;line-height:210px;color:#33cabb}
.error-body h4{margin:30px 0px}
</style>
</head>
<body>
<section class="error-page">
  <div class="error-box">
    <div class="error-body text-center">
      <h1>404</h1>
      <h4>抱歉，页面不存在！</h4>
      <a href="/" class="btn btn-primary ">返回首页</a>
    </div>
  </div>
</section>
</body>
</html>

