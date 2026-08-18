<?php
$title = '系统安全';
include './head.php';
$set = isset($_GET['set']) ? $_GET['set'] : null;
// 获取当前实际后台目录名（用于"设置为当前目录"按钮）
$current_admin_dir = isset($_SERVER['SCRIPT_NAME']) ? basename(dirname(str_replace('\\', '/', $_SERVER['SCRIPT_NAME']))) : 'admin';
if (!preg_match('/^[A-Za-z0-9_-]+$/', $current_admin_dir)) {
    $current_admin_dir = 'admin';
}
if ($set == 'syssave') {
    // 系统设置保存：ADMIN_PATH / DEBUG
    $new_admin_path = isset($_POST['admin_path']) ? trim($_POST['admin_path']) : '';
    $new_debug = isset($_POST['debug']) ? $_POST['debug'] : '0';

    // 校验后台目录：仅允许字母、数字、下划线、中划线，防止路径遍历
    if (empty($new_admin_path) || !preg_match('/^[A-Za-z0-9_-]+$/', $new_admin_path)) {
        exit('<script>$.alert({title:"错误",content:"后台目录不合法！只能使用字母、数字、下划线、中划线",buttons:{confirm:{text:"确定",btnClass:"btn-primary",action:function(){history.go(-1);}}}});</script>');
        
    }
    // 校验后台目录是否存在（目录名发生变化时才需要检查，防止配置指向不存在的目录导致后台无法访问）
    if ($new_admin_path !== ADMIN_PATH && !is_dir(ROOT . $new_admin_path)) {
        exit('<script>$.alert({title:"错误",content:"后台目录 ' . $new_admin_path . ' 不存在！请先将当前后台目录重命名为 ' . $new_admin_path . '，再保存设置",buttons:{confirm:{text:"确定",btnClass:"btn-primary",action:function(){history.go(-1);}}}});</script>');
    }
    if ($new_debug !== '0' && $new_debug !== '1') {
        exit('<script>$.alert({title:"错误",content:"调试模式参数无效！",buttons:{confirm:{text:"确定",btnClass:"btn-primary",action:function(){history.go(-1);}}}});</script>');
    }

    $commonFile = ROOT . 'include/common.php';
    $content = @file_get_contents($commonFile);
    if ($content === false) {
        exit('<script>$.alert({title:"错误",content:"无法读取 include/common.php，请检查文件权限",buttons:{confirm:{text:"确定",btnClass:"btn-primary",action:function(){history.go(-1);}}}});</script>');
    }

    // 替换 ADMIN_PATH 常量
    $pattern = "/define\(['\"]ADMIN_PATH['\"]\s*,\s*['\"][^'\"]*['\"]\s*\)/";
    if (!preg_match($pattern, $content)) {
        exit('<script>$.alert({title:"错误",content:"未找到 ADMIN_PATH 配置项，无法修改",buttons:{confirm:{text:"确定",btnClass:"btn-primary",action:function(){history.go(-1);}}}});</script>');
    }
    $content = preg_replace($pattern, "define('ADMIN_PATH', '" . $new_admin_path . "')", $content, 1);

    // 替换 DEBUG 常量
    $pattern = "/define\(['\"]DEBUG['\"]\s*,\s*(?:true|false|0|1|['\"][^'\"]*['\"])\s*\)/";
    if (!preg_match($pattern, $content)) {
        exit('<script>$.alert({title:"错误",content:"未找到 DEBUG 配置项，无法修改",buttons:{confirm:{text:"确定",btnClass:"btn-primary",action:function(){history.go(-1);}}}});</script>');
    }
    $debugVal = $new_debug === '1' ? 'true' : 'false';
    $content = preg_replace($pattern, "define('DEBUG', " . $debugVal . ")", $content, 1);

    if (@file_put_contents($commonFile, $content) === false) {
        exit('<script>$.alert({title:"错误",content:"写入 include/common.php 失败，请检查文件权限",buttons:{confirm:{text:"确定",btnClass:"btn-primary",action:function(){history.go(-1);}}}});</script>');
    }

    // 维护 config.php 中的系统设置标记：非默认设置时写入 $system_settings_modified = true;
    // 使文件完整性校验跳过 include/common.php；恢复默认设置时移除标记
    $is_default = ($new_admin_path === 'admin' && $new_debug === '0');
    $configFile = ROOT . 'config.php';
    $configContent = @file_get_contents($configFile);
    if ($configContent !== false) {
        if ($is_default) {
            // 恢复默认：移除标记
            $cleaned = preg_replace('/\s*\$system_settings_modified\s*=\s*true\s*;/', '', $configContent);
            $cleaned = trim($cleaned) . "\n";
            if ($cleaned !== $configContent) {
                @file_put_contents($configFile, $cleaned);
            }
        } else {
            // 非默认设置：仅当设置发生变更且标记不存在时写入
            $debugChanged = (($new_debug === '1') !== (defined('DEBUG') && DEBUG === true));
            if (($new_admin_path !== ADMIN_PATH || $debugChanged) && strpos($configContent, '$system_settings_modified') === false) {
                // 去掉结尾的 PHP 结束标签，避免追加内容被当作 HTML 输出
                $configContent = preg_replace('/\?>\s*$/', "\n", $configContent, 1);
                $configContent = rtrim($configContent) . "\n\n\$system_settings_modified = true;\n";
                @file_put_contents($configFile, $configContent);
            }
        }
    }

    $msg = '系统设置保存成功！';
    if ($new_admin_path !== ADMIN_PATH) {
        $msg .= '\n后台目录已修改为 ' . $new_admin_path . '，请通过新的地址访问后台（' . $new_admin_path . '/）';
    }
    exit('<script>$.alert({title:"成功",content:"' . $msg . '",buttons:{confirm:{text:"确定",btnClass:"btn-primary",action:function(){window.location.href="./user.php";}}}});</script>');
   
} elseif ($set == 'save') {
    $user = $_POST['new-usernameuser'];
    $oldpwd = $_POST['oldpwd'];
    $newpwd = $_POST['newpwd'];
    $repwd = $_POST['confirmpwd'];

    if (md5('lylme' . $oldpwd) == $conf['admin_pwd']) {
        if (empty($newpwd)) { //未修改密码
            if (empty($user)) {
                exit('<script>$.alert({title:"提示",content:"未做出更改",buttons:{confirm:{text:"确定",btnClass:"btn-primary",action:function(){history.go(-1);}}}});</script>');
            } else {
                //只修改用户名
                saveSetting('admin_user', $user);
                exit('<script>$.alert({title:"成功",content:"用户名修改成功！<br>新用户名：' . $user . '<br>请牢记，将重新登录！",buttons:{confirm:{text:"确定",btnClass:"btn-primary",action:function(){window.location.href="./";}}}});</script>');
            }
        } elseif (!empty($newpwd)) { //修改密码
            if ($newpwd == $repwd  && empty($user)) {
                $admin_pwd = md5('lylme' . $newpwd);
                saveSetting('admin_pwd', $admin_pwd);
                exit('<script>$.alert({title:"成功",content:"密码修改成功！<br>新密码：' . $newpwd . '<br>请牢记，将重新登录！",buttons:{confirm:{text:"确定",btnClass:"btn-primary",action:function(){window.location.href="./";}}}});</script>');
            } elseif ($newpwd == $repwd) {
                $admin_pwd = md5('lylme' . $newpwd);
                saveSetting('admin_user', $user);
                saveSetting('admin_pwd', $admin_pwd);
                exit('<script>$.alert({title:"成功",content:"修改成功！<br>新用户名：' . $user . '<br>新密码：' . $newpwd . '<br>请牢记，将重新登录！",buttons:{confirm:{text:"确定",btnClass:"btn-primary",action:function(){window.location.href="./";}}}});</script>');
            }
        } else {
            exit('<script>$.alert({title:"错误",content:"两次新密码不一致！",buttons:{confirm:{text:"确定",btnClass:"btn-primary",action:function(){history.go(-1);}}}});</script>');
        }
    } else {
        exit('<script>$.alert({title:"错误",content:"当前密码错误！",buttons:{confirm:{text:"确定",btnClass:"btn-primary",action:function(){history.go(-1);}}}});</script>');
    }
} else { ?>

  <!--页面主要内容-->
  <main class="lyear-layout-content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
            <h4>账户安全</h4>
              <form method="post" action="user.php?set=save" class="site-form">
                <div class="form-group">
                  <label for="username">当前用户名</label>
                  <input type="text" class="form-control" name="username" id="username" value="<?php echo $conf['admin_user']; ?>" disabled="disabled">
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
                <button type="submit" class="btn btn-primary d-block w-100">修改</button>
              </form>
            </div>
          </div>
          <div class="card" id="system-security" style="scroll-margin-top: 80px;">
            <div class="card-body">
            <h4>系统安全</h4>
              <p class="text-danger">非必要请勿修改，修改错误可能导致后台无法访问！</p>
              <form method="post" action="user.php?set=syssave" class="site-form">
                <div class="form-group">
                  <label for="admin-path">后台目录（ADMIN_PATH）</label>
                  <div class="input-group">
                    <input type="text" class="form-control" name="admin_path" id="admin-path" value="<?php echo ADMIN_PATH; ?>" placeholder="例如：admin">
                    <span class="input-group-btn">
                      <button type="button" class="btn btn-default" onclick="document.getElementById('admin-path').value='<?php echo $current_admin_dir; ?>'">设置为当前目录</button>
                    </span>
                  </div>
                  <small class="help-block">当前实际目录：<?php echo $current_admin_dir; ?>。仅允许字母、数字、下划线、中划线；修改后需手动将 admin 目录重命名为新名称，否则将无法访问后台</small>
                </div>
                <div class="form-group">
                  <label for="debug">调试模式（DEBUG）</label>
                  <select class="form-control" name="debug" id="debug">
                    <option value="0"<?php echo defined('DEBUG') && DEBUG === true ? '' : ' selected'; ?>>关闭（推荐）</option>
                    <option value="1"<?php echo defined('DEBUG') && DEBUG === true ? ' selected' : ''; ?>>开启</option>
                  </select>
                  <small class="help-block">开启后页面将显示详细错误信息，仅排障时使用，正式环境请保持关闭</small>
                </div>
                <button type="submit" class="btn btn-primary d-block w-100">保存设置</button>
              </form>
            </div>
          </div>
          <div class="card">
            <div class="card-body">
              <div class="form-group">
              <h4>文件安全</h4>
               <p> <a href="./filecheck.php">文件完整性校验</a></p>
              </div>
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