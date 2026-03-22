<?php

include("../include/common.php");

// 设置响应头
header('Content-Type: text/html; charset=UTF-8');

// 确保 session 已启动（用于验证码等）
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// 安全检查
if (isset($_POST['user']) && isset($_POST['pass'])) {
  // 检查验证码
  if (isset($_REQUEST['authcode'])) {

    $session_code = $_SESSION['authcode'] ?? '';
    $input_code = strtolower($_REQUEST['authcode']);

    if ($input_code === strtolower($session_code)) {
      // 安全的用户输入处理
      $user = daddslashes($_POST['user']);
      $pass = md5('lylme' . daddslashes($_POST['pass']));

      // 获取配置中的用户信息
      $admin_user = $conf['admin_user'] ?? '';
      $admin_pwd = $conf['admin_pwd'] ?? '';

      if ($user === $admin_user && $pass === $admin_pwd) {
        $session = md5($user . $pass);
        $token = authcode("{$user}\t{$session}", 'ENCODE', SYS_KEY);

        // 设置cookie - 使用PHP 7.3+ 兼容方式
        // PHP 8.0+ samesite 参数可能在某些服务器配置下不兼容，所以分开处理
        if (version_compare(PHP_VERSION, '8.0.0', '>=')) {
          // PHP 8.0+ 使用完整参数
          setcookie("admin_token", $token, [
            'expires' => time() + 604800,
            'path' => '/',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Lax'
          ]);
        } elseif (version_compare(PHP_VERSION, '7.3.0', '>=')) {
          // PHP 7.3-7.4 使用不带 samesite 的参数（samesite 是 PHP 8.0+ 才支持）
          setcookie("admin_token", $token, [
            'expires' => time() + 604800,
            'path' => '/',
            'secure' => false,
            'httponly' => true
          ]);
        } else {
          // PHP 7.3以下兼容方式
          setcookie("admin_token", $token, time() + 604800, "/");
        }

        unset($_SESSION['authcode']);

        // 登录成功，使用服务端重定向（更可靠）
        if (!headers_sent($file, $line)) {
          header("Location: ./index.php");
          exit;
        }

        // 如果 headers 已发送，使用 JavaScript
        echo '<script>window.location.href="./index.php";</script>';
        exit;
      } elseif ($pass !== $admin_pwd) {
        // 设置登录失败cookie
        $fail_cookie_value = '1';
        if (version_compare(PHP_VERSION, '8.0.0', '>=')) {
          setcookie("login_failed", $fail_cookie_value, [
            'expires' => time() + 3600,
            'path' => '/',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Lax'
          ]);
        } elseif (version_compare(PHP_VERSION, '7.3.0', '>=')) {
          setcookie("login_failed", $fail_cookie_value, [
            'expires' => time() + 3600,
            'path' => '/',
            'secure' => false,
            'httponly' => true
          ]);
        } else {
          setcookie("login_failed", $fail_cookie_value, time() + 3600, "/");
        }
        unset($_SESSION['authcode']);
        exit("<script language='javascript'>alert('用户名或密码不正确！');history.go(-1);</script>");
      }
    } else {
      unset($_SESSION['authcode']);
      exit("<script language='javascript'>alert('验证码错误');history.go(-1);</script>");
    }
  }
} elseif (isset($_GET['logout'])) {
  // 退出登录 - 清除cookie
  if (version_compare(PHP_VERSION, '8.0.0', '>=')) {
    setcookie("admin_token", '', [
      'expires' => time() - 604800,
      'path' => '/',
      'secure' => false,
      'httponly' => true,
      'samesite' => 'Lax'
    ]);
  } elseif (version_compare(PHP_VERSION, '7.3.0', '>=')) {
    setcookie("admin_token", '', [
      'expires' => time() - 604800,
      'path' => '/',
      'secure' => false,
      'httponly' => true
    ]);
  } else {
    setcookie("admin_token", '', time() - 604800, "/");
  }
  exit("<script language='javascript'>alert('您已成功注销本次登陆！');window.location.href='./login.php';</script>");
} elseif (isset($islogin)) {
  if ($islogin == 1) {
    exit("<script language='javascript'>alert('您已登陆！');window.location.href='./';</script>");
  }
}

// 获取页面标题
$page_title = '后台登录';
if (isset($conf['title'])) {
  $titles = explode("-", $conf['title']);
  $page_title = isset($titles[0]) ? $titles[0] : $page_title;
}

// 获取背景图片
$background = background();
?>
<!DOCTYPE html>
<html lang="zh">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
  <title>后台登录 - <?php echo htmlspecialchars($page_title); ?></title>
  <link href="../assets/admin/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/admin/css/materialdesignicons.min.css" rel="stylesheet">
  <link href="../assets/admin/css/style.min.css" rel="stylesheet">
  <style>
    .lyear-wrapper {
      position: relative;
    }

    .lyear-login {
      display: flex !important;
      min-height: 100vh;
      align-items: center !important;
      justify-content: center !important;
    }

    .lyear-login:after {
      content: '';
      min-height: inherit;
      font-size: 0;
    }

    .login-center {
      background: #fff;
      min-width: 29.25rem;
      padding: 2.14286em 3.57143em;
      border-radius: 3px;
      margin: 2.85714em;
    }

    .login-header {
      margin-bottom: 1.5rem !important;
    }

    .login-center .has-feedback.feedback-left .form-control {
      padding-left: 38px;
      padding-right: 12px;
    }

    .login-center .has-feedback.feedback-left .form-control-feedback {
      left: 0;
      right: auto;
      width: 38px;
      height: 38px;
      line-height: 38px;
      z-index: 4;
      color: #dcdcdc;
    }

    .login-center .has-feedback.feedback-left.row .form-control-feedback {
      left: 15px;
    }
  </style>
</head>

<body>
  <?php
  if (!empty($background)) {
    $background = str_replace('./', '../', $background);
    echo '<div class="row lylme-wrapper" style="background-image: url(' . htmlspecialchars($background) . ');background-size: cover;">';
  }
  ?>
  <div class="row lyear-wrapper">
    <div class="lyear-login">
      <div class="login-center">
        <div class="login-header text-center">
          <h2>后台登录</h2>
        </div>
        <form action="" method="post">
          <div class="form-group has-feedback feedback-left">
            <input type="text" placeholder="用户名" class="form-control" name="user" id="username" value="<?php echo isset($_POST['user']) ? htmlspecialchars($_POST['user']) : "" ?>" />
            <span class="mdi mdi-account form-control-feedback" aria-hidden="true"></span>
          </div>
          <div class="form-group has-feedback feedback-left">
            <input type="password" placeholder="密码" class="form-control" id="password" name="pass" value="" />
            <span class="mdi mdi-lock form-control-feedback" aria-hidden="true"></span>
          </div>

          <div class="form-group has-feedback feedback-left row">
            <div class="col-xs-8">
              <input type="text" name="authcode" autocomplete="off" class="form-control" placeholder="验证码" required>
              <span class="mdi mdi-check form-control-feedback" aria-hidden="true"></span>
            </div>
            <div class="col-xs-4">
              <img id="captcha_img" title="验证码" src='../include/validatecode.php' class="pull-right code" onclick="recode()" />
            </div>
          </div>
          <div class="form-group">
            <button class="btn btn-block btn-primary" type="submit" id="login">登录</button>
          </div>
          <?php
          if (isset($_COOKIE['login_failed'])) {
            echo '  <p class="m-b-0 text-right"><a target="_blank" title="忘记后台密码" href="https://doc.lylme.com/spage/#/reset">忘记密码</a></p>';
          } ?>
        </form>
        <hr>
        <footer class="col-sm-12 text-center">
          <p class="m-b-0">Copyright <?php echo date('Y'); ?> <a href="/"><?php echo htmlspecialchars($page_title); ?></a></p>
        </footer>
      </div>
    </div>
  </div>
  <script type="text/javascript" src="../assets/admin/js/jquery.min.js"></script>
  <script type="text/javascript" src="../assets/admin/js/bootstrap.min.js"></script>
  <script>
    $(document).ready(function() {
      recode()
    });

    function recode() {
      $('#captcha_img').attr('src', '../include/validatecode.php?r=' + Math.random());
      $("input[name='authcode']").val('');
    }
  </script>
</body>

</html>