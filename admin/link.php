<?php
$title = '链接管理';
include './head.php';
$grouplists = $DB->query("SELECT * FROM `lylme_groups`");
$pwd_lists = $DB->query("SELECT * FROM `lylme_pwd`");
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
    echo '<h4>新增链接</h4>
<div class="panel-body">
<form action="./link.php?set=add_submit" method="POST">
<div class="form-group">
<label>*URL链接地址:</label>
<div class="input-group">
<input type="text" class="form-control" name="url" placeholder="链接" value="" required>
<span class="input-group-btn">
  <button class="btn btn-default" onclick="geturl()" type="button">获取</button>
</span>
</div></div>
<div class="form-group">
<label>*网站名称:</label><br>
<input type="text" class="form-control" placeholder="网站名称" name="name" value="" required>
</div>
<div class="form-group">
<label>链接图标:</label><br>
<textarea type="text" class="form-control" name="icon" placeholder="网站图标"></textarea>
<small class="help-block">方式1：填写图标的<code>URL</code>地址，如<code>/img/logo.png</code>或<code>http://www.xxx.com/img/logo.png</code><br>
方式2：粘贴图标的<code>SVG</code>代码，<a href="./help.php?doc=icon" target="_blank">查看教程</a><br>方式3：留空使用默认图标</small>
</div>
<div class="form-group">
<label>*分组:</label><br>
<select class="form-control" name="group_id">';
    while ($grouplist = $DB->fetch($grouplists)) {
        if ($grouplist["group_id"] == $row['group_id']) {
            $select = 'selected="selected"';
        } else {
            $select = '';
        }
        echo '<option  value="' . $grouplist["group_id"] . '">' . $grouplist["group_id"] . ' - ' . $grouplist["group_name"] . '</option>';
    }
    echo '</select></div>
<div class="form-group">
<input type="submit" class="btn btn-primary btn-block" value="添加"></form>
</div>
<br/><a href="./link.php"><<返回</a>
</div></div>';
} elseif ($set == 'edit') {
    $id = $_GET['id'];
    $row2 = $DB->query("select * from lylme_links where id='$id' limit 1");
    $row = $DB->fetch($row2);
    echo '<h4>修改链接信息</h4>
<div class="panel-body">
<form action="./link.php?set=edit_submit&id=' . $id . '" method="POST">
<div class="form-group">
<label>*URL链接地址:</label>
<div class="input-group">
<input type="text" class="form-control" name="url" placeholder="链接" value="' . $row['url'] . '" required>
<span class="input-group-btn">
  <button class="btn btn-default" onclick="geturl()" type="button">获取</button>
</span>
</div></div>
<div class="form-group">
<label>*网站名称:</label><br>
<input type="text" class="form-control" name="name" value="' . $row['name'] . '" required>
</div>
<div class="form-group">
<label>链接图标:</label><br>
<textarea type="text" class="form-control" name="icon" >' . $row['icon'] . '</textarea>
<small class="help-block">方式1：填写图标的<code>URL</code>地址，如<code>/img/logo.png</code>或<code>http://www.xxx.com/img/logo.png</code><br>
方式2：粘贴图标的<code>SVG</code>代码，<a href="./help.php?doc=icon" target="_blank">查看教程</a><br>方式3：留空使用默认图标</small>
</div>

<div class="form-group">
<label>*分组:</label><br>
<select class="form-control" name="group_id">';
    while ($grouplist = $DB->fetch($grouplists)) {
        if ($grouplist["group_id"] == $row['group_id']) {
            $select = 'selected="selected"';
        } else {
            $select = '';
        }
        echo '<option  value="' . $grouplist["group_id"] . '" ' . $select . '>' . $grouplist["group_id"] . ' - ' . $grouplist["group_name"] . '</option>';
    }
    echo '</select>
</div>
<div class="form-group">
<label>链接加密:</label><br>
<select class="form-control" required name="link_pwd">';
$pwd_lists = $DB->query("SELECT * FROM `lylme_pwd`");
while ($pwd_list = $DB->fetch($pwd_lists)) {
    if($row['link_pwd']==$pwd_list["pwd_id"]){$sel = 'selected="selected"';}else{ $sel ='';}
    echo '<option  value="' . $pwd_list["pwd_id"] . '" '.$sel.' >' . $pwd_list["pwd_id"] . ' - ' . $pwd_list["pwd_name"] . ' | 密码['. $pwd_list["pwd_key"].']</option>';
}
if(empty($row['link_pwd'])) $sele = 'selected="selected"';
echo '
<option value="0" '.$sele.'>0 - 不加密</option></select>
<small class="help-block"><code>注意：对链接所在的分组加密后，单独设置的链接加密将会失效</code><br>
加密后只能通过输入密码访问，使用该功能先配置加密组
<a href="./pwd.php" target="_blank">管理加密组</a></small>
</div>
<div class="form-group">
<input type="submit" class="btn btn-primary btn-block" value="修改"></form>
</div>
<br/><a href="./link.php"><<返回</a>
</div></div>';
} elseif ($set == 'add_submit') {
    $name = $_POST['name'];
    $url = $_POST['url'];
    $icon = $_POST['icon'];
    $group_id = $_POST['group_id'];
    $link_order = $linksrows + 1;
    if ($name == NULL or $url == NULL) {
        echo '<script>alert("保存错误,请确保带星号的都不为空！");history.go(-1);</script>';
    } else {
        $sql = "INSERT INTO `lylme_links` (`id`, `name`, `group_id`, `url`, `icon`, `PS`,`link_order`) VALUES (NULL, '" . $name . "', '" . $group_id . "', '" . $url . "', '" . $icon . "', '" . $name . "', '" . $link_order . "');";
        if ($DB->query($sql)) {
            echo '<script>alert("添加链接 ' . $name . ' 成功！");window.location.href="./link.php";</script>';
        } else echo '<script>alert("添加链接失败！");history.go(-1);</script>';
    }
} elseif ($set == 'edit_submit') {
    $id = $_GET['id'];
    $rows2 = $DB->query("select * from lylme_links where id='$id' limit 1");
    $rows = $DB->fetch($rows2);
    if (!$rows) echo '<script>alert("当前记录不存在！");history.go(-1);</script>';
    $name = $_POST['name'];
    $url = $_POST['url'];
    $icon = $_POST['icon'];
    $link_pwd = $_POST['link_pwd'];
    $group_id = $_POST['group_id'];
    if ($name == NULL or $url == NULL) {
        echo '<script>alert("保存错误,请确保带星号的都不为空！");history.go(-1);</script>';
    } else {
        $sql = "UPDATE `lylme_links` SET `name` = '" . $name . "', `url` = '" . $url . "', `icon` = '" . $icon . "', `group_id` = '" . $group_id . "', `link_pwd` = " . $link_pwd . " WHERE `lylme_links`.`id` = '" . $id . "';";
     //   exit($sql);
        if ($DB->query($sql)) echo '<script>alert("修改链接 ' . $name . ' 成功！");window.location.href="./link.php";</script>';
        else echo '<script>alert("修改链接失败！");history.go(-1);</script>';
    }
// } elseif ($set == 'delete') {
//     $id = $_GET['id'];
//     $sql = "DELETE FROM lylme_links WHERE id='$id'";
//     if ($DB->query($sql)) echo '<script>alert("删除成功！");window.location.href="./link.php";</script>';
//     else echo '<script>alert("删除失败！");history.go(-1);</script>';
} else {
    echo '<div id="listTable"></div>
                </div>
            </div>
          </div>
        </div>
      </div>
    </main>
';

}
include './footer.php';
?>
<script type="text/javascript" src="js/jquery.dragsort-0.5.2.min.js"></script>
<link href="https://lf6-cdn-tos.bytecdntp.com/cdn/expire-1-M/jquery-confirm/3.3.0/jquery-confirm.min.css" type="text/css" rel="stylesheet" />
<script src="https://lf3-cdn-tos.bytecdntp.com/cdn/expire-1-M/layer/3.1.1/layer.min.js" type="application/javascript"></script>
<script src="https://lf9-cdn-tos.bytecdntp.com/cdn/expire-1-M/jquery-confirm/3.3.0/jquery-confirm.min.js" type="application/javascript"></script>
<!--消息提示-->
<script src="js/bootstrap-notify.min.js"></script>
<script type="text/javascript" src="js/lightyear.js"></script>
<script type="text/javascript" src="js/link.js"></script>
<script type="text/javascript"> 
//分组移动
var  mv_group ='<form action="" class="formName">' + '<select class="form-control group_id" required><option value="">请选择分组...</option>'+'<?php  while ($grouplist = $DB->fetch($grouplists)) {
    echo '<option  value="' . $grouplist["group_id"] . '">' . $grouplist["group_id"] . ' - ' . $grouplist["group_name"] . '</option>';}?>'+ '</select>';
//链接加密    
var pwd_list = '<form action="" class="formName">' + '<select class="form-control pwd_id" required>'+'<?php  while ($pwd_list = $DB->fetch($pwd_lists)) {
    echo '<option  value="' . $pwd_list["pwd_id"] . '">' . $pwd_list["pwd_id"] . ' - ' . $pwd_list["pwd_name"] . '</option>';}?>'+ '<option value="0">0 - 取消加密</option></select><br><a href="./pwd.php" target="_blank">管理加密组</a>';
</script>