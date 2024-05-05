<?php
/* 
 * @Description: 
 * @Author: LyLme admin@lylme.com
 * @Date: 2024-01-23 12:25:35
 * @LastEditors: LyLme admin@lylme.com
 * @LastEditTime: 2024-04-14 14:16:13
 * @FilePath: /lylme_spage/admin/head.php
 * @Copyright (c) 2024 by LyLme, All Rights Reserved. 
 */
include_once("../include/common.php");
if (isset($islogin) == 1) {
} else {
  exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
  <title><?php echo $title . ' - ' . $conf['title']; ?></title>
  <link rel="icon" href="/assets/img/logo.png" type="image/ico">
  <meta name="author" content="yinqi">
  <link href="/assets/admin/css/bootstrap.min.css" rel="stylesheet">
  <link href="/assets/admin/css/materialdesignicons.min.css" rel="stylesheet">
  <link href="/assets/admin/css/style.min.css" rel="stylesheet">
</head>
<div class="lyear-layout-web">
  <div class="lyear-layout-container">
    <!--左侧导航-->
    <aside class="lyear-layout-sidebar">
      <!-- logo -->
      <div id="logo" class="sidebar-header">
        <a href="/"><img src="/assets/img/logo-sidebar.png" alt="LyLme" title="返回首页" /></a>
      </div>
      <div class="lyear-layout-sidebar-scroll">
        <nav class="sidebar-main">
          <ul class="nav nav-drawer">
            <li class="nav-item active"> <a href="./"><i class="mdi mdi-home-map-marker"></i>后台首页</a> </li>
            <li class="nav-item nav-item-has-subnav">
              <a href="javascript:void(0)"><i class="mdi mdi-palette"></i>网站配置</a>
              <ul class="nav nav-subnav">
                <li> <a href="./set.php">网站基本设置</a> </li>
                <li> <a href="./about.php">关于页面设置</a> </li>
                <li> <a href="./user.php">修改账号密码</a> </li>

              </ul>
            </li>
            <li class="nav-item active"> <a href="./apply.php"><i class="mdi mdi-link"></i>收录管理 </a>
              <?php $applyrows = $DB->num_rows($DB->query("SELECT * FROM `lylme_apply` WHERE `apply_status` = 0"));
              if ($applyrows > 0) {
                echo '<style> .applyrow{width: 18px;height: 18px;top: 15px;right: 24px;font-size: 10px;font-weight: bold;color: #fff;background-color: red;border-radius: 100%;text-align: center;vertical-align: middle;position: absolute;line-height: 1.5;}</style>
	<div class="applyrow">' . $applyrows . '</div>';
              }
              ?></li>
            <li class="nav-item active"> <a href="./theme.php"><i class="mdi mdi-seal"></i>主题设置</a></li>
            <li class="nav-item active"> <a href="./group.php"><i class="mdi mdi-folder"></i>分组管理</a></li>
            <li class="nav-item active"> <a href="./link.php"><i class="mdi mdi-web"></i>链接管理</a></li>
            <li class="nav-item active"> <a href="./tag.php"><i class="mdi mdi-cube"></i>导航菜单</a></li>
            <li class="nav-item active"> <a href="./sou.php"><i class="mdi mdi-magnify"></i>搜索引擎</a></li>
            <li class="nav-item active"> <a href="./pwd.php"><i class="mdi mdi-key-variant"></i>加密管理</a></li>
            <li class="nav-item active"> <a href="./update.php"><i class="mdi mdi-update"></i>检查更新</a> </li>
            <li class="nav-item active"> <a href="./wxplus.php"><i class="mdi mdi-wechat"></i>微信推送</a> </li>
            <li> <a href="javascript:loginout()"><i class="mdi mdi-logout"></i> 退出登录</a> </li>
          </ul>
        </nav>
        <div class="sidebar-footer">
          <p class="copyright">Copyright &copy;
            <?php echo (date('Y'));
            ?> Powered by <br> <a href="https://github.com/LyLme/lylme_spage"><?php echo explode("-", $conf['title'])[0]; ?> </a></p>
        </div>
      </div>
    </aside>
    <!--End 左侧导航-->
    <!--头部信息-->
    <header class="lyear-layout-header">
      <nav class="navbar navbar-default">
        <div class="topbar">
          <div class="topbar-left">
            <div class="lyear-aside-toggler">
              <span class="lyear-toggler-bar"></span>
              <span class="lyear-toggler-bar"></span>
              <span class="lyear-toggler-bar"></span>
            </div>
            <span class="navbar-page-title"> <?php echo $title;
                                              ?></span>
          </div>
          <ul class="topbar-right">
            <li class="dropdown dropdown-profile">
              <a href="javascript:void(0)" data-toggle="dropdown">
                <span><?php echo $conf['admin_user'];
                      ?><span class="caret"></span></span>
              </a>
              <ul class="dropdown-menu dropdown-menu-right">
                <li> <a href="./user.php"><i class="mdi mdi-lock-outline"></i> 修改密码</a> </li>
                <li class="divider"></li>
                <li> <a href="javascript:loginout()"><i class="mdi mdi-logout-variant"></i> 退出登录</a> </li>
              </ul>
            </li>
            <li class="dropdown dropdown-skin">
              <span data-toggle="dropdown" class="icon-palette" aria-expanded="false"><i class="mdi mdi-palette"></i></span>
              <ul class="dropdown-menu dropdown-menu-right" data-stoppropagation="true">
                <li class="drop-title">
                  <p>主题</p>
                </li>
                <li class="drop-skin-li clearfix">
                  <span class="inverse">
                    <input type="radio" name="site_theme" value="default" id="site_theme_1">
                    <label for="site_theme_1" onclick="theme('default')"></label>
                  </span>
                  <span>
                    <input type="radio" name="site_theme" value="dark" id="site_theme_2" checked="">
                    <label for="site_theme_2" onclick="theme('dark')"></label>
                  </span>
                </li>

              </ul>
            </li>
          </ul>
        </div>
      </nav>
    </header>
    <script>
      function theme(theme) {
        localStorage.setItem("theme", theme);
      }
      var themes = localStorage.getItem("theme");
      if (themes != "dark") {
        var themes = 'default';
        document.getElementById('site_theme_1').checked = true;
      } else {
        document.getElementById('site_theme_2').checked = true;
      }
      document.write('<body data-theme="' + themes + '">');
    </script>
    <!--End 头部信息-->