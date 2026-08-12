<?php

include("../include/common.php");

// Bootstrap 5 官方 CDN 地址（可自由更换其他官方版本 CDN）
$bootstrap_cdn = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist';

// 设置响应头
header('Content-Type: text/html; charset=UTF-8');

// 确保 session 已启动（用于验证码、登录失败计数等）
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---------- 登录安全策略常量 ----------
define('LOGIN_MAX_FAILURES', 5);          // 连续失败达到该次数后临时锁定
define('LOGIN_LOCK_SECONDS', 60);         // 基础锁定 1 分钟（连续多次触发锁定时递增）
define('LOGIN_LOCK_MAX_SECONDS', 1800);   // 递增锁定上限 30 分钟

/**
 * 兼容不同 PHP 版本的安全 Cookie 写入
 * @param string $name     Cookie 名
 * @param string $value    Cookie 值
 * @param int    $lifetime 有效期（秒），传 0 表示清除
 */
function admin_set_cookie($name, $value, $lifetime = 604800)
{
    $expires = $lifetime > 0 ? (time() + $lifetime) : (time() - 3600);
    if (version_compare(PHP_VERSION, '7.3.0', '>=')) {
        setcookie($name, $value, [
            'expires' => $expires,
            'path' => '/',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    } else {
        setcookie($name, $value, $expires, '/');
    }
}

/**
 * 获取 IP 级登录锁定状态存储文件路径
 * 优先使用系统临时目录，不可用时回退到站点 cache 目录
 */
function ip_lock_file()
{
    static $file = null;
    if ($file !== null) {
        return $file;
    }
    $dir = sys_get_temp_dir();
    if (!is_dir($dir) || !is_writable($dir)) {
        $dir = ROOT . 'cache';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
    }
    $key = defined('SYS_KEY') ? SYS_KEY : '';
    $file = rtrim($dir, '/\\') . DIRECTORY_SEPARATOR . 'lylme_ip_login_' . md5(ROOT . $key) . '.json';
    return $file;
}

/** 获取客户端 IP（仅信任 REMOTE_ADDR，避免 XFF 头伪造） */
function get_client_ip()
{
    return isset($_SERVER['REMOTE_ADDR']) ? (string)$_SERVER['REMOTE_ADDR'] : 'unknown';
}

/**
 * 读取当前 IP 的失败计数与锁定状态
 * @return array ['count'=>, 'lock_until'=>, 'lock_level'=>]
 */
function ip_get_lock_state()
{
    $file = ip_lock_file();
    $data = [];
    if (is_file($file)) {
        $decoded = @json_decode((string)@file_get_contents($file), true);
        if (is_array($decoded)) {
            $data = $decoded;
        }
    }
    // 清理超过 1 天的旧记录，防止文件无限增长
    $now = time();
    foreach ($data as $k => $v) {
        if (isset($v['ts']) && $now - (int)$v['ts'] > 86400) {
            unset($data[$k]);
        }
    }
    $ip = get_client_ip();
    if (!isset($data[$ip]) || !is_array($data[$ip])) {
        $data[$ip] = ['count' => 0, 'lock_until' => 0, 'lock_level' => 0, 'ts' => $now];
    }
    // 锁定到期后自动解除并清空计数
    if ((int)$data[$ip]['lock_until'] > 0 && $now > (int)$data[$ip]['lock_until']) {
        $data[$ip] = ['count' => 0, 'lock_until' => 0, 'lock_level' => 0, 'ts' => $now];
        @file_put_contents($file, json_encode($data), LOCK_EX);
    }
    return $data[$ip];
}

/**
 * 记录一次 IP 级登录失败，达到阈值则锁定。
 * 锁定时间随锁定轮次递增：1、2、4、8... 分钟，上限 LOGIN_LOCK_MAX_SECONDS，
 * 即使攻击者不断更换 Session（清 Cookie）也无法绕过。
 */
function ip_record_failure()
{
    $file = ip_lock_file();
    $data = [];
    if (is_file($file)) {
        $decoded = @json_decode((string)@file_get_contents($file), true);
        if (is_array($decoded)) {
            $data = $decoded;
        }
    }
    $now = time();
    $ip = get_client_ip();
    $cur = isset($data[$ip]) && is_array($data[$ip]) ? $data[$ip] : ['count' => 0, 'lock_until' => 0, 'lock_level' => 0, 'ts' => $now];

    // 上一轮锁定已过期，重置轮次
    if ((int)$cur['lock_until'] > 0 && $now > (int)$cur['lock_until']) {
        $cur = ['count' => 0, 'lock_until' => 0, 'lock_level' => 0, 'ts' => $now];
    }

    $cur['count'] = (int)$cur['count'] + 1;
    $cur['ts'] = $now;

    if ($cur['count'] >= LOGIN_MAX_FAILURES) {
        $level = (int)$cur['lock_level'] + 1;
        $cur['lock_level'] = $level;
        // 第 N 轮锁定 = 基础锁定时间 * 2^(N-1)，封顶 30 分钟
        $lockSeconds = (int)min(LOGIN_LOCK_SECONDS * pow(2, $level - 1), LOGIN_LOCK_MAX_SECONDS);
        $cur['lock_until'] = $now + $lockSeconds;
        $cur['count'] = 0; // 锁定后清零，下一轮重新计数
    }

    $data[$ip] = $cur;
    @file_put_contents($file, json_encode($data), LOCK_EX);
    return $cur;
}

/** 登录成功后清除当前 IP 的失败记录 */
function ip_reset_failures()
{
    $file = ip_lock_file();
    if (!is_file($file)) {
        return;
    }
    $data = @json_decode((string)@file_get_contents($file), true);
    if (!is_array($data)) {
        return;
    }
    unset($data[get_client_ip()]);
    @file_put_contents($file, json_encode($data), LOCK_EX);
}

/** 记录一次登录失败，并返回累计失败次数 */
function record_login_failure()
{
    $count = isset($_SESSION['login_fail_count']) ? (int)$_SESSION['login_fail_count'] : 0;
    $count++;
    $_SESSION['login_fail_count'] = $count;

    if ($count >= LOGIN_MAX_FAILURES) {
        $_SESSION['login_lock_until'] = time() + LOGIN_LOCK_SECONDS;
    }

    // 兼容旧版：Cookie 标记失败，用于显示“忘记密码”链接
    admin_set_cookie('login_failed', '1', 3600);

    return $count;
}

/** 登录失败提示（PRG 模式：重定向到 GET 页面内联展示，避免 alert 弹窗） */
function login_fail_redirect($message, $user = '')
{
    $_SESSION['login_error'] = $message;
    $_SESSION['login_error_user'] = $user;
    header('Location: ./login.php');
    exit;
}

/** 安全重定向：header 不可用时降级为 JS 跳转 */
function safe_redirect($url)
{
    if (!headers_sent()) {
        header('Location: ' . $url);
        exit;
    }
    echo '<script>window.location.href=' . json_encode($url) . ';</script>';
    exit;
}

// ---------- 退出登录 ----------
if (isset($_GET['logout'])) {
    admin_set_cookie('admin_token', '', 0);
    admin_set_cookie('login_failed', '', 0);
    session_regenerate_id(true);
    unset(
        $_SESSION['login_fail_count'],
        $_SESSION['login_lock_until'],
        $_SESSION['login_error'],
        $_SESSION['login_error_user'],
        $_SESSION['authcode']
    );
    safe_redirect('./login.php');
}

// ---------- 已登录用户访问登录页：直接跳转，不再弹窗 ----------
if (isset($islogin) && $islogin === 1) {
    safe_redirect('./');
}

// ---------- 登录失败计数与锁定状态 ----------
$failCount = isset($_SESSION['login_fail_count']) ? (int)$_SESSION['login_fail_count'] : 0;
$lockUntil = isset($_SESSION['login_lock_until']) ? (int)$_SESSION['login_lock_until'] : 0;

// 锁定时间已过，自动解除并重置计数
if ($lockUntil > 0 && time() > $lockUntil) {
    $failCount = 0;
    $lockUntil = 0;
    unset($_SESSION['login_fail_count'], $_SESSION['login_lock_until']);
}

// IP 级防护：即使攻击者不断更换 Session（清 Cookie）也无法绕过锁定
$ipState     = ip_get_lock_state();
$ipFailCount = (int)$ipState['count'];
if ((int)$ipState['lock_until'] > $lockUntil) {
    $lockUntil = (int)$ipState['lock_until']; // 取更严格的锁定时间
}

$needCaptcha = ($failCount >= 1 || $ipFailCount >= 1); // 首次登录无需验证码，失败一次后强制验证码
$isLocked    = $lockUntil > time();

// 读取上次登录失败的提示信息（PRG 闪存消息）
$loginError = isset($_SESSION['login_error']) ? (string)$_SESSION['login_error'] : '';
$loginUser  = isset($_SESSION['login_error_user']) ? (string)$_SESSION['login_error_user'] : '';
if ($loginError !== '') {
    unset($_SESSION['login_error'], $_SESSION['login_error_user']);
}

// ---------- 登录提交处理 ----------
if (isset($_POST['user'], $_POST['pass'])) {
    $rawUser = trim((string)$_POST['user']);
    $passRaw = (string)$_POST['pass'];

    // 锁定期内：直接拒绝
    if ($isLocked) {
        $remain = (int)ceil(($lockUntil - time()) / 60);
        login_fail_redirect('登录尝试次数过多，请 ' . $remain . ' 分钟后再试', $rawUser);
    }

    // 需要验证码时，先校验验证码（一次性使用，防止重放）
    if ($needCaptcha) {
        $inputCode = strtolower(trim(isset($_POST['authcode']) ? (string)$_POST['authcode'] : ''));
        $sessionCode = strtolower(isset($_SESSION['authcode']) ? (string)$_SESSION['authcode'] : '');
        unset($_SESSION['authcode']);
        if ($inputCode === '' || !hash_equals($sessionCode, $inputCode)) {
            record_login_failure();
            ip_record_failure();
            login_fail_redirect('验证码错误，请重新输入', $rawUser);
        }
    }

    // 计算凭据（与旧版保持一致的加密方式）
    $user = daddslashes($rawUser);
    $pass = md5('lylme' . daddslashes($passRaw));
    $admin_user = isset($conf['admin_user']) ? $conf['admin_user'] : '';
    $admin_pwd  = isset($conf['admin_pwd']) ? $conf['admin_pwd'] : '';

    // 使用 hash_equals 常量时间比较，避免时序攻击
    if ($user !== '' && hash_equals($admin_user, $user) && hash_equals($admin_pwd, $pass)) {
        // 登录成功：重置失败计数、刷新会话 ID，防止会话固定攻击
        unset($_SESSION['login_fail_count'], $_SESSION['login_lock_until'], $_SESSION['authcode']);
        ip_reset_failures(); // 清除当前 IP 的失败记录
        session_regenerate_id(true);

        $session = md5($user . $pass);
        $token = authcode("{$user}\t{$session}", 'ENCODE', SYS_KEY);
        admin_set_cookie('admin_token', $token, 604800); // 7 天
        admin_set_cookie('login_failed', '', 0);         // 清除失败标记

        safe_redirect('./index.php');
    }

    // 用户名或密码错误
    record_login_failure();
    ip_record_failure();
    login_fail_redirect('用户名或密码不正确', $rawUser);
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
  <link href="<?php echo $bootstrap_cdn; ?>/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/admin/css/materialdesignicons.min.css" rel="stylesheet">
  <link href="../assets/admin/css/style.min.css" rel="stylesheet">
  <style>
    /* 全屏布局容器：手机/电脑自动居中 */
    .login-page {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1rem;
      background-color: #eef2f7;
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
    }

    .login-center {
      background: #fff;
      width: 100%;
      max-width: 27.5rem; /* 440px：电脑端卡片，手机端全宽 */
      padding: 2.5rem 2rem;
      border-radius: 1.25rem;
      box-shadow: 0 20px 60px rgba(30, 41, 59, 0.18);
      position: relative;
      overflow: hidden;
    }
    .login-center:before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 6px;
      background: linear-gradient(90deg, #3b82f6, #60a5fa, #2563eb);
    }

    .login-header {
      margin-bottom: 1.5rem !important;
    }
    .login-header h2 {
      color: #1e293b;
      font-weight: 700;
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

    /* 验证码行：输入框保持全宽，验证码图片绝对定位覆盖在输入框右端，
       保证带/不带验证码时表单及输入框宽度完全一致 */
    .login-center .captcha-row {
      position: relative;
    }

    .login-center .captcha-row .form-control {
      padding-right: 112px; /* 为右侧验证码图片留出空间 */
    }

    .login-center .captcha-row .captcha-img {
      position: absolute;
      top: 0;
      right: 0;
      height: 38px;
      width: 100px;
      object-fit: cover;
      border-radius: 3px;
      cursor: pointer;
      z-index: 5;
    }

    /* 手机端：卡片占满宽度、减小留白 */
    @media (max-width: 480px) {
      .login-page {
        padding: 0.75rem;
      }
      .login-center {
        padding: 2rem 1.25rem;
        border-radius: 1rem;
      }
    }

  </style>
</head>

<body>
  <div class="login-page"<?php if (!empty($background)) { $background = str_replace('./', '../', $background); echo ' style="background-image:url(' . htmlspecialchars($background) . ')"'; } ?>>
    <div class="login-center">
      <div class="login-header text-center">
        <h2>后台登录</h2>
      </div>
      <form action="" method="post">
        <?php if ($isLocked): ?>
          <div class="alert alert-warning" role="alert">
            <i class="mdi mdi-clock-alert mdi-alert-icon"></i>登录尝试次数过多，账号已临时锁定，请 <?php echo (int)ceil(($lockUntil - time()) / 60); ?> 分钟后再试。
          </div>
        <?php elseif ($loginError !== ''): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle mdi-alert-icon"></i>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <?php echo htmlspecialchars($loginError); ?>
          </div>
        <?php endif; ?>
        <div class="form-group has-feedback feedback-left mb-3">
          <input type="text" placeholder="用户名" class="form-control" name="user" id="username" autocomplete="username" value="<?php echo htmlspecialchars($loginUser); ?>" />
          <span class="mdi mdi-account form-control-feedback" aria-hidden="true"></span>
        </div>
        <div class="form-group has-feedback feedback-left mb-3">
          <input type="password" placeholder="密码" class="form-control" id="password" name="pass" autocomplete="current-password" value="" />
          <span class="mdi mdi-lock form-control-feedback" aria-hidden="true"></span>
        </div>

        <?php if ($needCaptcha): ?>
          <div class="form-group has-feedback feedback-left captcha-row mb-3">
            <input type="text" name="authcode" autocomplete="off" class="form-control" placeholder="验证码" required>
            <span class="mdi mdi-check form-control-feedback" aria-hidden="true"></span>
            <img id="captcha_img" title="验证码" src='../include/validatecode.php' class="captcha-img" onclick="recode()" />
          </div>
        <?php endif; ?>
        <div class="form-group mb-0">
          <button class="btn btn-primary w-100" type="submit" id="login">登录</button>
        </div>
        <?php
        if ($needCaptcha || isset($_COOKIE['login_failed'])) {
          echo '  <p class="m-b-0 text-end mt-3"><a target="_blank" title="忘记后台密码" href="https://doc.lylme.com/spage/#/reset">忘记密码</a></p>';
        } ?>
      </form>
      <hr class="my-4">
      <footer class="text-center">
        <p class="m-b-0">Copyright <?php echo date('Y'); ?> <a href="/"><?php echo htmlspecialchars($page_title); ?></a></p>
      </footer>
    </div>
  </div>
  <script type="text/javascript" src="../assets/admin/js/jquery.min.js"></script>
  <script type="text/javascript" src="<?php echo $bootstrap_cdn; ?>/js/bootstrap.bundle.min.js"></script>
  <script>
    $(document).ready(function() {
      if ($('#captcha_img').length) {
        recode()
      }
    });

    function recode() {
      $('#captcha_img').attr('src', '../include/validatecode.php?r=' + Math.random());
      $("input[name='authcode']").val('');
    }
  </script>
</body>

</html>
