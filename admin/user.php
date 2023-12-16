<?php
$title = '账号安全';
include './head.php';
$set = isset($_GET['set']) ? $_GET['set'] : null;
if($set == 'save') {
    $user = $_POST['new-usernameuser'];
    $oldpwd = $_POST['oldpwd'];
    $newpwd = $_POST['newpwd'];
    $repwd = $_POST['confirmpwd'];

    if(md5('lylme' . $oldpwd) == $conf['admin_pwd']) {
        if(empty($newpwd)) {//未修改密码
            if(empty($user)) {
                echo '<script>alert("未做出更改");history.go(-1);</script>';
            } else {
              //只修改用户名
                saveSetting('admin_user', $user);
                echo '<script>alert("用户名修改成功！\n新用户名：' . $user . '\n请牢记，将重新登录！");window.location.href="./";</script>';
            }
        } elseif(!empty($newpwd)) {//修改密码
            if ($newpwd == $repwd  && empty($user)) {
                $admin_pwd = md5('lylme' . $newpwd);
                saveSetting('admin_pwd', $admin_pwd);
                echo '<script>alert("密码修改成功！\n新密码：' . $newpwd . '\n请牢记，将重新登录！");window.location.href="./";</script>';
            } elseif($newpwd == $repwd) {
                $admin_pwd = md5('lylme' . $newpwd);
                saveSetting('admin_user', $user);
                saveSetting('admin_pwd', $admin_pwd);
                echo '<script>alert("修改成功！\n新用户名：' . $user . '\n新密码：' . $newpwd . '\n请牢记，将重新登录！");window.location.href="./";</script>';
            }

        } else {
            echo '<script>alert("两次新密码不一致！");history.go(-1);</script>';
        }

    } else {
        echo '<script>alert("当前密码错误！");history.go(-1);</script>';
    }

} else {
    ?>
    
    <!--页面主要内容-->
    <main class="lyear-layout-content">
      
      <div class="container-fluid">
        
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-body">
                
                <form method="post" action="user.php?set=save" class="site-form">
                    <div class="form-group">
                    <label for="username">当前用户名</label>
                    <input type="text" class="form-control" name="username" id="username" value="<?php echo $conf['admin_user'];?>" disabled="disabled">
                  </div>
                    <div class="form-group">
                    <label for="old-password">新用户名</label>
                    <input type="text" class="form-control" name="new-usernameuser" id="new-username" placeholder="请输入新用户名" autocomplete="new-password" value="">
                    <small class="help-block">留空为不修改用户名</small>
                  </div>
                  <div class="form-group">
                    <label for="old-password">*当前密码</label>
                    <input type="password" class="form-control" name="oldpwd" id="old-password" placeholder="输入账号的原登录密码" required autocomplete="new-password">
                
                  </div>
                  <div class="form-group">
                    <label for="new-password">新密码</label>
                    <input type="password" class="form-control" name="newpwd" id="new-password" placeholder="输入新的密码">
                    <small class="help-block">留空为不修改密码</small>
                  </div>
                  <div class="form-group">
                    <label for="confirm-password">确认新密码</label>
                    <input type="password" class="form-control" name="confirmpwd" id="confirm-password" placeholder="重复输入新的密码">
                  </div>
                  <button type="submit" class="btn btn-primary">修改</button>
                </form>
       
              </div>
            </div>
          </div>
          
        </div>
        
      </div>
      
    </main>
    <!--End 页面主要内容-->
<?php
}
include './footer.php';
?>