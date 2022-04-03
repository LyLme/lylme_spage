<?php
include_once("../include/common.php");
if(isset($islogin)==1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");
$update = update();
?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
<title><?php echo $title.' - '.$conf['title'];
?></title>
<link rel="icon" href="/assets/img/logo.png" type="image/ico">
<meta name="author" content="yinqi">
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/materialdesignicons.min.css" rel="stylesheet">
<link href="css/style.min.css" rel="stylesheet">
</head>
<body>
<div class="lyear-layout-web">
  <div class="lyear-layout-container">
    <!--左侧导航-->
    <aside class="lyear-layout-sidebar"> 
      <!-- logo -->
      <div id="logo" class="sidebar-header">
        <a href="/"><img src="/assets/img/logo-sidebar.png"  alt="LyLme" title="返回首页" /></a>
      </div>
      <div class="lyear-layout-sidebar-scroll"> 
        <nav class="sidebar-main">
          <ul class="nav nav-drawer">
            <li class="nav-item active"> <a href="./"><i class="mdi mdi-home-map-marker"></i>后台首页</a> </li>
            <li class="nav-item nav-item-has-subnav">
              <a href="javascript:void(0)"><i class="mdi mdi-palette"></i>网站配置</a>
              <ul class="nav nav-subnav">
                <li> <a href="./set.php">网站基本设置</a> </li>
                <li> <a href="./tag.php">导航菜单设置</a> </li>
                <li> <a href="./sou.php">搜索引擎设置</a> </li>
                <li> <a href="./user.php">修改账号密码</a> </li>
              </ul>
            </li>
            <li class="nav-item active"> <a href="./apply.php"><i class="mdi mdi-link"></i>收录管理 </a>
<?php $applyrows = $DB->num_rows($DB->query("SELECT * FROM `lylme_apply` WHERE `apply_status` = 0"));
if($applyrows>0) {
	echo'<style>
    .applyrow{
    width: 18px;
    height: 18px;
    top: 15px;
    right: 24px;
    font-size: 10px;
    font-weight: bold;
    color: #fff;
    background-color: red;
    border-radius: 100%;
    text-align: center;
    vertical-align: middle;
    position: absolute;
    line-height: 1.5;
    }
    </style><div class="applyrow">'.$applyrows.'</div>';
}
?></li>
            <li class="nav-item nav-item-has-subnav">
              <a href="javascript:void(0)"><i class="mdi mdi-folder"></i> <span>分组管理</span></a>
              <ul class="nav nav-subnav">
                <li> <a href="./group.php?set=add">添加分组</a> </li>
                <li> <a href="./group.php">管理分组</a> </li>
              </ul>
            </li>
            <li class="nav-item nav-item-has-subnav">
              <a href="javascript:void(0)"><i class="mdi mdi-web"></i> <span>链接管理</span></a>
              <ul class="nav nav-subnav">
                <li> <a href="./link.php?set=add">添加链接</a> </li>
                <li> <a href="./link.php">管理链接</a> </li>
              </ul>
            </li>
             <li class="nav-item active"> <a href="./update.php"><i class="mdi mdi-update"></i>检查更新</a> </li>
             <li> <a href="javascript:loginout()"><i class="mdi mdi-logout"></i> 退出登录</a> </li>
             </ul>
        </nav>
        <div class="sidebar-footer">
          <p class="copyright">Copyright &copy;
<?php echo(date('Y'));
?> By LyLme.<br> <a href="https://gitee.com/LyLme/lylme_spage"><?php echo $conf['title'];
?> </a></p>
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
          </ul>
        </div>
      </nav>
    </header>
    <!--End 头部信息-->