<?php
$title = '分组管理';
include './head.php';
?>
 <main class="lyear-layout-content">
      
      <div class="container-fluid">
        
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-body">
<?php
$set = isset($_GET['set']) ? $_GET['set'] : null;
if ($set == 'add') {
    echo '<h4>新增分组</h4>	
<div class="panel-body"><form action="./group.php?set=add_submit" method="POST">
<div class="form-group">
<label>*名称:</label><br>
<input type="text" class="form-control" name="group_name" value="" required>
</div>
<div class="form-group">
<label>分组图标:</label><br>
<textarea type="text" class="form-control" name="group_icon" value=""></textarea>
<small class="help-block">方式1：填写图标的<code>URL</code>地址，如<code>/img/logo.png</code>或<code>http://www.xxx.com/img/logo.png</code><br>
方式2：粘贴图标的<code>SVG</code>代码，<a href="./help.php?doc=icon" target="_blank">查看教程</a><br>方式3：留空使用默认图标</small>
</div>
<div class="form-group">
<label>分组加密:</label><br>
<select class="form-control" required name="group_pwd">';
$pwd_lists = $DB->query("SELECT * FROM `lylme_pwd`");
while ($pwd_list = $DB->fetch($pwd_lists)) {
    echo '<option  value="' . $pwd_list["pwd_id"] . '">' . $pwd_list["pwd_id"] . ' - ' . $pwd_list["pwd_name"] . ' | 密码['. $pwd_list["pwd_key"].']</option>';
}
echo '
<option value="0" selected="selected">0 - 不加密</option></select>
<small class="help-block"><code>注意：对链接所在的分组加密后，单独设置的链接加密将会失效</code><br>
加密后只能通过输入密码访问，使用该功能先配置加密组
<a href="./pwd.php" target="_blank">管理加密组</a></small>
</div>
<div class="form-group">
<input type="submit" class="btn btn-primary btn-block" value="确定添加"></form>
</div>
<br/><a href="./group.php"><<返回分组列表</a>
</div></div>
';
} elseif ($set == 'edit') {
    $id = $_GET['id'];
    $row2 = $DB->query("select * from lylme_groups where group_id='$id' limit 1");
    $row = $DB->fetch($row2);
    echo '<h4>修改分组信息</h4>
<div class="panel-body">
<form action="./group.php?set=edit_submit&id=' . $id . '" method="POST">
<div class="form-group">
<label>*名称:</label><br>
<input type="text" class="form-control" name="group_name" value="' . $row['group_name'] . '" required>
</div>
<div class="form-group">
<label>分组图标:</label><br>
<textarea type="text" class="form-control" name="group_icon">' . $row['group_icon'] . '</textarea>
<small class="help-block">方式1：填写图标的<code>URL</code>地址，如<code>/img/logo.png</code>或<code>http://www.xxx.com/img/logo.png</code><br>
方式2：粘贴图标的<code>SVG</code>代码，<a href="./help.php?doc=icon" target="_blank">查看教程</a><br>方式3：留空使用默认图标</small>
</div>
<div class="form-group">
<label>分组加密:</label><br>
<select class="form-control" required name="group_pwd">';
$pwd_lists = $DB->query("SELECT * FROM `lylme_pwd`");
while ($pwd_list = $DB->fetch($pwd_lists)) {
    if($row['group_pwd']==$pwd_list["pwd_id"]){$sel = 'selected="selected"';}else{$sel = '';}
    echo '<option  value="' . $pwd_list["pwd_id"] . '" '.$sel.' >' . $pwd_list["pwd_id"] . ' - ' . $pwd_list["pwd_name"] . ' | 密码['. $pwd_list["pwd_key"].']</option>';
}
if(empty($row['group_pwd'])) $sele = 'selected="selected"';
echo '
<option value="0" '.$sele.'>0 - 不加密</option></select>
<small class="help-block"><code>优先级：分组加密>链接加密</code><br>
加密后只能通过输入密码访问，使用该功能先配置加密组
<a href="./pwd.php" target="_blank">管理加密组</a></small>
</div>
<div class="form-group">
<input type="submit" class="btn btn-primary btn-block" value="确定修改"></form>
</div>
<br/><a href="./group.php"><<返回分组列表</a></div></div>';
} elseif ($set == 'add_submit') {
    $name = $_POST['group_name'];
    $icon = $_POST['group_icon'];
    $pwd = $_POST['group_pwd'];
    $group_order = $groupsrows + 1;
    if ($name == NULL) {
        echo '<script>alert("保存错误,请确保带星号的都不为空！");history.go(-1);</script>';
    } else {
        $sql = "INSERT INTO `lylme_groups` (`group_id`, `group_name`, `group_icon`,`group_order`,`group_pwd`) VALUES (NULL, '" . $name . "', '" . $icon . "', '" . $group_order . "', '" . $pwd . "')";
        if ($DB->query($sql)) {
            echo '<script>alert("添加分组 ' . $name . ' 成功！");window.location.href="./group.php";</script>';
            exit();
        } else echo '<script>alert("添加分组失败");history.go(-1);</script>';
        exit();
    }
    echo '<script>alert("添加分组失败,名称重复");history.go(-1);</script>';
} elseif ($set == 'edit_submit') {
    $id = $_GET['id'];
    $rows2 = $DB->query("select * from lylme_groups where group_id='$id' limit 1");
    $rows = $DB->fetch($rows2);
    if (!$rows) echo '<script>alert("当前记录不存在！");history.go(-1);</script>';
    $name = $_POST['group_name'];
    $icon = $_POST['group_icon'];
    $pwd = $_POST['group_pwd'];
    if ($name == NULL) {
        echo '<script>alert("保存错误,请确保带星号的都不为空！");history.go(-1);</script>';
    } else {
        $sql = "UPDATE `lylme_groups` SET `group_name` = '" . $name . "', `group_icon` = '" . $icon . "',`group_pwd` = '" . $pwd . "' WHERE `lylme_groups`.`group_id` = '" . $id . "';";
        if ($DB->query($sql)) echo '<script>alert("修改分组 ' . $name . ' 成功！");window.location.href="./group.php";</script>';
        else echo '<script>alert("' . $sql . '修改分组失败");history.go(-1);</script>';
    }
} elseif ($set == 'del') {
    $id = $_POST['group_id'];
    $delsql1 = 'DELETE FROM `lylme_links` WHERE group_id =' . $id;
    $delsql2 = 'DELETE FROM `lylme_groups` WHERE group_id=' . $id;
    $DB->query($delsql1);
    $DB->query($delsql2);
    exit();
} elseif ($set == 'on') {
    $id = $_POST['group_id'];
    $sql = "UPDATE `lylme_groups` SET `group_status` = '1' WHERE `lylme_groups`.`group_id` =" . $id;
    $DB->query($sql);
    exit();
} elseif ($set == 'off') {
    $id = $_POST['group_id'];
    $sql = "UPDATE `lylme_groups` SET `group_status` = '0' WHERE `lylme_groups`.`group_id` =" . $id;
    $DB->query($sql);
    exit();
} 
elseif ($set == 'sort') {
     for ($i=0; $i<count($_POST["groups"]); $i++) {
		$sql = "UPDATE `lylme_groups` SET `group_order` = '".$i."' WHERE `lylme_groups`.`group_id` = ".$_POST["groups"][$i].";";
		$DB->query($sql);
	}
	exit();
} 
// elseif ($set == 'up') {
//     //上移
//     $id = $_GET['id'];
//     $order = $DB->get_row("SELECT `group_id`, `group_order` FROM `lylme_groups` WHERE `group_id` = ".$id.";")['group_order'];  //当前排序
//     $pl = $DB->get_row("SELECT * FROM `lylme_groups` WHERE `group_order` < ".$order." ORDER BY `lylme_groups`.`group_order` DESC LIMIT 1");  //上一行
//     $DB->query("UPDATE `lylme_groups` SET `group_order` = '".$pl['group_order']."' WHERE `lylme_groups`.`group_id` = ".$id.";");  //设置为上一行的排序
//     $DB->query("UPDATE `lylme_groups` SET `group_order` = '".$order."' WHERE `lylme_groups`.`group_id` = ".$pl['group_id'].";");  //设置上一行的排序为当前行
//     echo '<script>window.location.href="./group.php?orderid=' . $id . '";</script>';
//     exit();
    
// } elseif ($set == 'down') {
//     $id = $_GET['id'];
//     $order = $DB->get_row("SELECT `group_id`, `group_order` FROM `lylme_groups` WHERE `group_id` = ".$id.";")['group_order'];  //当前排序
//     $nl = $DB->get_row("SELECT * FROM `lylme_groups` WHERE `group_order` > ".$order." ORDER BY `lylme_groups`.`group_order` ASC LIMIT 1");  //下一行
//     $DB->query("UPDATE `lylme_groups` SET `group_order` = '".$nl['group_order']."' WHERE `lylme_groups`.`group_id` = ".$id.";");  //设置为下一行的排序
//     $DB->query("UPDATE `lylme_groups` SET `group_order` = '".$order."' WHERE `lylme_groups`.`group_id` = ".$nl['group_id'].";");  //设置下一行的排序为当前行
//     echo '<script>window.location.href="./group.php?orderid=' . $id . '";</script>';
//     exit();
// } 
else {
   
    
?>
<div id="listTable"></div>
                    </div>
            </div>
          </div>
          
        </div>
        
      </div>
      
    </main>
<?php
}
include './footer.php';
?>
<script src="/assets/admin/js/layer.min.js" type="application/javascript"></script>
<script type="text/javascript" src="/assets/admin/js/lightyear.js"></script>
<script src="/assets/admin/js/bootstrap-notify.min.js"></script>
<link href="/assets/admin/js/jquery-confirm.min.css" type="text/css" rel="stylesheet" />
<script src="/assets/admin/js/jquery-confirm.min.js" type="application/javascript"></script>
<script type="text/javascript" src="/assets/admin/js/group.js"></script>