<?php
function isInternalUrl($url, $currentDomain) {
    $parsedUrl = parse_url($url);
    $host = $parsedUrl['host'] ?? '';
    return strpos($host, $currentDomain) !== false;
}
$currentDomain = $_SERVER['HTTP_HOST'];
if (isset($_GET['url']) && !empty($_GET['url'])) {
    $targetUrl = $_GET['url'];
    if (filter_var($targetUrl, FILTER_VALIDATE_URL)) {
        if (isInternalUrl($targetUrl, $currentDomain)) {
            header('Location: ' . $targetUrl);
            exit;
        } else {
            ?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>跳转网址安全提醒</title>
    <link href="/assets/img/logo.png" rel="shortcut icon" type="image/x-icon" />
    <style>
body,html{margin:0;padding:0;background:#f3f4f5;font-family:PingFang SC,Hiragino Sans GB,Arial,Microsoft YaHei,Verdana,Roboto,Noto,Helvetica Neue,sans-serif;}
a{text-decoration:none;}
.urlArea{margin:auto;width:450px;word-break:break-all;}
.urlArea .main{margin:50% auto;padding:24px;border:1px solid #e1e1e1;border-radius:12px;background:#fff;}
.urlArea .btn,.urlArea .flex{display:flex;align-items:center;}
.urlArea .btn{justify-content:flex-end;}
.urlArea .tip{margin-bottom:16px;padding:12px;border-radius:4px;background:#e8eefa;}
.urlArea .urlColor{color:red;}
.urlArea .urlBox{margin-bottom:24px;color:#222226;font-size:14px;line-height:24px;}
.urlArea .ico{width:24px;height:24px;}
.urlArea .goBtn{display:inline-block;box-sizing:border-box;margin-left:8px;padding:6px 18px;border:1px solid #408ffa;border-radius:18px;background-color:#408ffa;color:#fff;white-space:nowrap;font-size:14px;}
.urlArea .icoTxt{overflow:hidden;margin-left:12px;color:#222226;text-overflow:ellipsis;white-space:nowrap;font-weight:600;font-size:16px;line-height:22px;}
@media (max-width:600px){.urlArea{padding-top:90px;width:94%;}
}
    </style>
  </head>
  <body>
    <div class="urlArea">
      <div class="main">
        <div class="flex tip">
          <img
            class="ico"
            src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAADWUlEQVRoQ+2YvWsUQRiH33fmOMQiRQor7RQSweB9KAQEg1iYKFaZnJJ/wcTOMhLs7Ez8FwLebikasTGCoJDcHSoYQbHRyiKFhajczCu3SS73sbfzsXNJDnLV3s4y83vm3X12dhAG/IcDnh+OAHpVkITISvg2DYyQjwyVcXGt3o9q960CdZELAWC6ERoRn/CgentgAGimOCVJPmsNzJFfx2DjuW8I7xWg+xMZ9enXRwIaaQ2LgJ/Z2aFzvm8l7wBypjBPpB7FzTQiu8uDypLPKngFIDE+rODPFwIYjgUA2GJw7AyGb7d8QXgFkCK/TEB3ksIh4GMeVucOHQCV8qNKwQcCymgA6ozBGJarmz4gvFWgLvKrAHTNLBS+yITVSbNrk6/yAhCnTV04X1pNDdBLmzoAX1pNDZCkTS2EB62mAtBpUwvgQaupAEy0qYdIp1VnAFNtGgCk0qozgJ02tRjOWnUCoFJxUiqZuLJEgB+AbCWKTmqWAE4mYXDGp7C8sapD7Wy3BjDVJke8iUH1aZS/lL8hFUXHvX6uWrUGMNUmByxiWK1sAxTyUqnoOBHCQatWADbadAJw0KoVgJwpLBEpo5WkC0CjOohsmQeVeV21dtuNAWy16QwAaKVVYwBbbboCbM+s+WrVCMBEm50lTwcAYKpVLYCpNrsALDXa7XezTQAtgKk2Y14wVi+yuIfWZBMgEcBGm6bWsLkODbSaCGCjTZtgNtfqtNoTwFabMffwb0DYWQvBLAEdtwm+5/lkrfYEsNVmazgElIzxcSyvr0dLiVu5opL4joC4C0SSVmMBXLTZBoDwnge1863npMjVCKDtnA1ML612Abhqs70C8JPB6VMYhv+iCgiRVfD1OwGcsAndPim4yUaHxjr3VrsAZCk3RwpS718i4gpjdK8RQil8SESzruGbzwODeV6uLXdM1t7fg9amDjBOq20VOAza1EJ0rFabAGm1qRvYVzt2rFabAHWRa3zjetmv9BU2oZ/VTFibarRHACQuXJZQX9uHgb0NwZFdwaDyKgKQorBAoBa99b4PHSHCAx7UFnYqULikUL0mArYPY6ceInrTA05gWHmz9xCL4kWF6ioAZFOP0NcO8C8jerm746H9HuhrFg+dHwF4mMRUXQx8Bf4DeBHHQHvQneAAAAAASUVORK5CYII="
            alt="温馨提醒"
          />
          <div class="icoTxt">请注意您的账号和财产安全</div>
        </div>
        <div class="urlBox">
          <span>
            <div style="font-weight: bold; padding-bottom: 8px">即将跳转到外部网站，安全性未知，是否继续？</div>
            您将要访问的链接不属于本网站，请注意您的账号和财产安全。<br />
            前往：<a class="urlColor"><?php echo htmlspecialchars($targetUrl); ?></a>
          </span>
        </div>
        <div class="btn">
            <label style="color: red; font-size: 14px"><p id="countdown" class="countdown">10秒后自动跳转...</p></label>
            <button class="goBtn" onclick="cancelCountdown()">继续访问</button>
            <script>
                var countdownNumber = 10;
                var countdownElement = document.getElementById('countdown');
                var redirectUrl = '<?php echo htmlspecialchars($targetUrl); ?>';
                var countdownInterval = setInterval(function() {
                    countdownNumber--;
                    countdownElement.textContent = countdownNumber + '秒后自动跳转...';
                    if (countdownNumber <= 0) {
                        clearInterval(countdownInterval);
                        window.location.href = redirectUrl;
                    }
                }, 1000);
                function cancelCountdown() {
                    clearInterval(countdownInterval);
                    window.location.href = redirectUrl;
                }
            </script>
        </div>
      </div>
    </div>
  </body>
</html>
<?php
    exit;
        }
    } else {
        http_response_code(400);
        echo '无效的URL';
    }
} else {
    http_response_code(400);
    echo '缺少url参数';
}
?>