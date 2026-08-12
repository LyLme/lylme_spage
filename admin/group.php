<?php
$title = '分组管理';
include './head.php';

$set = isset($_GET['set']) ? $_GET['set'] : null;

// 预取加密组数据（pwd_id => 名称|密码 映射，供新增/编辑下拉使用）
$pwd_lists = array();
$pq = $DB->query("SELECT * FROM `lylme_pwd`");
while ($p = $DB->fetch($pq)) {
    $pwd_lists[$p['pwd_id']] = $p['pwd_name'] . ' | 密码[' . $p['pwd_key'] . ']';
}

// 新增加分组
if ($set == 'add_submit') {
    $name = daddslashes($_POST['group_name']);
    $icon = daddslashes($_POST['group_icon']);
    $pwd = intval($_POST['group_pwd']);
    $max_order = $DB->get_row("SELECT MAX(`group_order`) AS max_order FROM `lylme_groups`");
    $group_order = intval($max_order['max_order']) + 1;
    if ($name == NULL) {
        echo '<script>alert("保存错误,请确保带星号的都不为空！");history.go(-1);</script>';
    } else {
        $sql = "INSERT INTO `lylme_groups` (`group_id`, `group_name`, `group_icon`,`group_order`,`group_pwd`) VALUES (NULL, '" . $name . "', '" . $icon . "', '" . $group_order . "', '" . $pwd . "')";
        if ($DB->query($sql)) {
            echo '<script>alert("添加分组 ' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . ' 成功！");window.location.href="./group.php";</script>';
            exit();
        } else echo '<script>alert("添加分组失败");history.go(-1);</script>';
        exit();
    }
    echo '<script>alert("添加分组失败,名称重复");history.go(-1);</script>';
    exit();
}

// 修改分组
if ($set == 'edit_submit') {
    $id = intval($_GET['id']);
    $rows2 = $DB->query("select * from lylme_groups where group_id='$id' limit 1");
    $rows = $DB->fetch($rows2);
    if (!$rows) {
        echo '<script>alert("当前记录不存在！");history.go(-1);</script>';
        exit;
    }
    $name = daddslashes($_POST['group_name']);
    $icon = daddslashes($_POST['group_icon']);
    $pwd = intval($_POST['group_pwd']);
    if ($name == NULL) {
        echo '<script>alert("保存错误,请确保带星号的都不为空！");history.go(-1);</script>';
    } else {
        $sql = "UPDATE `lylme_groups` SET `group_name` = '" . $name . "', `group_icon` = '" . $icon . "',`group_pwd` = '" . $pwd . "' WHERE `lylme_groups`.`group_id` = '" . $id . "';";
        if ($DB->query($sql)) echo '<script>alert("修改分组 ' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . ' 成功！");window.location.href="./group.php";</script>';
        else echo '<script>alert("修改分组失败");history.go(-1);</script>';
    }
    exit();
}

// 列表页 AJAX 接口
if ($set == 'del') {
    $id = intval($_POST['group_id']);
    $delsql1 = 'DELETE FROM `lylme_links` WHERE group_id =' . $id;
    $delsql2 = 'DELETE FROM `lylme_groups` WHERE group_id=' . $id;
    $DB->query($delsql1);
    $DB->query($delsql2);
    exit();
}
if ($set == 'on') {
    $id = intval($_POST['group_id']);
    $DB->query("UPDATE `lylme_groups` SET `group_status` = '1' WHERE `lylme_groups`.`group_id` =" . $id);
    exit();
}
if ($set == 'off') {
    $id = intval($_POST['group_id']);
    $DB->query("UPDATE `lylme_groups` SET `group_status` = '0' WHERE `lylme_groups`.`group_id` =" . $id);
    exit();
}
if ($set == 'sort') {
    for ($i = 0; $i < count($_POST["groups"]); $i++) {
        $group_id = intval($_POST["groups"][$i]);
        $sql = "UPDATE `lylme_groups` SET `group_order` = '" . $i . "' WHERE `lylme_groups`.`group_id` = " . $group_id . ";";
        $DB->query($sql);
    }
    exit();
}
?>
 <main class="lyear-layout-content">
      
      <div class="container-fluid">
        
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-body">
<?php if ($set == 'add'): ?>
<h4>新增分组</h4>
<div class="panel-body">
<form action="./group.php?set=add_submit" method="POST">
<div class="form-group">
<label>*名称:</label><br>
<input type="text" class="form-control" name="group_name" value="" required>
</div>
<div class="form-group">
<label>分组图标:</label><br>
<textarea type="text" class="form-control" name="group_icon"></textarea>
<small class="help-block">方式1：使用图片地址，需要img标签，如<code>&lt;img src="/assets/img/logo.png" /&gt; </code><br>
方式2：粘贴图标的<code>SVG</code>代码，<a href="./help.php?doc=icon" target="_blank">查看教程</a><br>方式3：留空使用默认图标</small>
</div>
<div class="form-group">
<label>分组加密:</label><br>
<select class="form-control" required name="group_pwd">
<?php foreach ($pwd_lists as $pwd_id => $pwd_name): ?>
<option value="<?php echo $pwd_id; ?>"><?php echo $pwd_id; ?> - <?php echo $pwd_name; ?></option>
<?php endforeach; ?>
<option value="0" selected="selected">0 - 不加密</option>
</select>
<small class="help-block"><code>注意：对链接所在的分组加密后，单独设置的链接加密将会失效</code><br>
加密后只能通过输入密码访问，使用该功能先配置加密组
<a href="./pwd.php" target="_blank">管理加密组</a></small>
</div>
<div class="form-group">
<input type="submit" class="btn btn-primary btn-block" value="确定添加">
</div>
</form>
<br/><a href="./group.php"><<返回分组列表</a>
</div>
<?php elseif ($set == 'edit'): ?>
<?php
$id = intval($_GET['id']);
$row2 = $DB->query("select * from lylme_groups where group_id='$id' limit 1");
$row = $DB->fetch($row2);
if (!$row) {
    echo '<script>alert("当前记录不存在！");window.location.href="./group.php";</script>';
    exit;
}
$esc_group_name = htmlspecialchars($row['group_name'], ENT_QUOTES, 'UTF-8');
$esc_group_icon = htmlspecialchars($row['group_icon'], ENT_QUOTES, 'UTF-8');
?>
<h4>修改分组信息</h4>
<div class="panel-body">
<form action="./group.php?set=edit_submit&id=<?php echo $id; ?>" method="POST">
<div class="form-group">
<label>*名称:</label><br>
<input type="text" class="form-control" name="group_name" value="<?php echo $esc_group_name; ?>" required>
</div>
<div class="form-group">
<label>分组图标:</label><br>
<textarea type="text" class="form-control" name="group_icon"><?php echo $esc_group_icon; ?></textarea>
<small class="help-block">方式1：使用图片地址，需要img标签，如<code>&lt;img src="/assets/img/logo.png" /&gt; </code><br>
方式2：粘贴图标的<code>SVG</code>代码，<a href="./help.php?doc=icon" target="_blank">查看教程</a><br>方式3：留空使用默认图标</small>
</div>
<div class="form-group">
<label>分组加密:</label><br>
<select class="form-control" required name="group_pwd">
<?php foreach ($pwd_lists as $pwd_id => $pwd_name): ?>
<?php $sel = ($row['group_pwd'] == $pwd_id) ? 'selected="selected"' : ''; ?>
<option value="<?php echo $pwd_id; ?>" <?php echo $sel; ?>><?php echo $pwd_id; ?> - <?php echo $pwd_name; ?></option>
<?php endforeach; ?>
<?php $sele = empty($row['group_pwd']) ? 'selected="selected"' : ''; ?>
<option value="0" <?php echo $sele; ?>>0 - 不加密</option>
</select>
<small class="help-block"><code>优先级：分组加密>链接加密</code><br>
加密后只能通过输入密码访问，使用该功能先配置加密组
<a href="./pwd.php" target="_blank">管理加密组</a></small>
</div>
<div class="form-group">
<input type="submit" class="btn btn-primary btn-block" value="确定修改">
</div>
</form>
<br/><a href="./group.php"><<返回分组列表</a>
</div>
<?php else: ?>
<div id="listTable"></div>
                    </div>
            </div>
          </div>
          
        </div>
        
      </div>
      
    </main>
<?php endif; ?>
<?php
include './footer.php';
?>
<script src="/assets/admin/js/layer.min.js" type="application/javascript"></script>
<script type="text/javascript" src="/assets/admin/js/lightyear.js"></script>
<script src="/assets/admin/js/bootstrap-notify.min.js"></script>
<link href="/assets/admin/js/jquery-confirm.min.css" type="text/css" rel="stylesheet" />
<script src="/assets/admin/js/jquery-confirm.min.js" type="application/javascript"></script>
<script type="text/javascript" src="/assets/admin/js/group.js"></script>
