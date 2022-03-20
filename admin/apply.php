<?php
$title = '收录管理';
include './head.php';
$applyrows=$DB->num_rows($DB->query("SELECT * FROM `lylme_apply`"));
?>
<script src="../assets/js/svg.js"></script>
<style>td img,td svg.icon{width: 40px;height: 40px;}</style>
 <main class="lyear-layout-content">
      
      <div class="container-fluid">
        
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-body">
<?php
$set = isset($_GET['set']) ? $_GET['set'] : null;
if ($set == 'edit') {
    $id = $_GET['id'];
    $row2 = $DB->query("select * from lylme_apply where apply_id='$id' limit 1");
    $row = $DB->fetch($row2);
    echo '<h4>修改链接信息</h4>
<div class="panel-body">
<form action="./apply.php?set=edit_submit&id=' . $id . '" method="POST">
<div class="form-apply">
<label>*名称:</label><br>
<input type="text" class="form-control" name="apply_name" value="' . $row['apply_name'] . '" required>
</div>
<div class="form-apply">
<label>*链接:</label><br>
<input type="text" class="form-control" name="apply_url" value="' . $row['apply_url'] . '" required>
</div>
<div class="form-apply">
<label>图标:</label><br>
<textarea type="text" class="form-control" name="apply_icon">' . $row['apply_icon'] . '</textarea>
<small class="help-block">方式1：填写图标的<code>URL</code>地址，如<code>/img/logo.png</code>或<code>http://www.xxx.com/img/logo.png</code><br>
方式2：粘贴图标的<code>SVG</code>代码，<a href="./help.php?doc=icon" target="_blank">查看教程</a><br>方式3：留空使用默认图标</small>
</div>

<div class="form-apply">
<input type="submit" class="btn btn-primary btn-block" value="确定修改"></form>
</div>
<br/><a href="./apply.php"><<返回分组列表</a></div></div>';

} elseif ($set == 'edit_submit') {
    $id = $_GET['id'];
    $rows2 = $DB->query("select * from lylme_apply where apply_id='$id' limit 1");
    $rows = $DB->fetch($rows2);
    if (!$rows) echo '<script>alert("当前记录不存在！");history.go(-1);</script>';
    $name = $_POST['apply_name'];
    $icon = $_POST['apply_icon'];
    $url = $_POST['apply_url'];
    if ($name == NULL) {
        echo '<script>alert("保存错误,请确保带星号的都不为空！");history.go(-1);</script>';
    } else {
        $sql = "UPDATE `lylme_apply` SET `apply_name` = '" . $name . "', `apply_icon` = '" . $icon . "',`apply_url` = '" . $url . "' WHERE `lylme_apply`.`apply_id` = '" . $id . "';";
        if ($DB->query($sql)) echo '<script>alert("修改分组 ' . $name . ' 成功！");window.location.href="./apply.php";</script>';
        else echo '<script>alert("' . $sql . '修改分组失败");history.go(-1);</script>';
    }
} elseif ($set == 'delete') {
    $id = $_GET['id'];
    $delsql = 'DELETE FROM `lylme_apply` WHERE apply_id =' . $id;
    if ($DB->query($delsql)) echo '<script>alert("删除成功！");window.location.href="./apply.php";</script>';
    else echo '<script>alert("删除失败！");history.go(-1);</script>';
}
elseif ($set == 'status') {
    $id = $_GET['id'];
    $sw = $_GET['sw'];
    $sql = "UPDATE `lylme_apply` SET `apply_status` = '".$sw."' WHERE `lylme_apply`.`apply_id` = ".$id.";";

    if ($DB->query($sql)) {echo '<script>window.location.href="./apply.php";</script>';
        if($sw==1){
         echo '<script>alert("功能开发中！");history.go(-1);</script>';
       //  $sql = "INSERT INTO `lylme_links` (`id`, `name`, `group_id`, `url`, `icon`, `PS`,`link_order`) VALUES (NULL, '" . $name . "', '" . $group_id . "', '" . $url . "', '" . $icon . "', '" . $mail ."的提交', '" . $link_order . "');";
       // $DB->query($sql);
        }
    }
    else echo '<script>alert("审核失败！");history.go(-1);</script>';
}
else {
    echo '<div class="alert alert-info">
    当前收到 <b>' . $applyrows . '</b> 次收录申请<br/>
    </div>';
    
?>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead><tr><th>序号</th><th>图标</th><th>名称</th><th>链接</th><th>申请人</th><th>审核</th><th>操作</th></tr></thead>
          <tbody>
<?php
    $rs = $DB->query("SELECT * FROM `lylme_apply` ORDER BY `lylme_apply`.`apply_id` ASC");
$i=0;
    while ($res = $DB->fetch($rs)) {
        $i++;
        echo '<tr><td><b>'.$i.'</b></td><td>';
        
        if ($res["apply_icon"] == '') {
                echo '<img src="/assets/img/default-icon.png" alt="默认' . $res["apply_name"] . '" />';
            } else if (!preg_match("/^<svg*/", $res["apply_icon"])) {
                echo '<img src="' . $res["apply_icon"] . '" alt="' . $res["apply_name"] . '" />';
            } else {
                echo $res["apply_icon"];
            }
           
        echo'</td><td>' . $res['apply_name'] . '</td><td>' . $res['apply_url'] .'</td><td>'.$res['apply_mail'].'</td><td>';
        if($res["apply_status"]==2){
         echo '<font color="#f96868">已拒绝</font>';
        }
        else if($res["apply_status"]==1){  echo '<font color="#3c763d">已通过</font>';}
        else{echo '
    <a href="./apply.php?set=status&id=' . $res['apply_id'] . '&sw=1" class="btn btn-primary btn-xs">通过</a>&nbsp; 
    <a href="./apply.php?set=status&id=' . $res['apply_id'] . '&sw=2" class="btn btn-cyan btn-xs">拒绝</a>';
    }
    echo '</td>
        
        <td>&nbsp;<a href="./apply.php?set=edit&id=' . $res['apply_id'] . '" class="btn btn-info btn-xs">编辑</a>&nbsp;<a href="./apply.php?set=delete&id=' . $res['apply_id'] . '" class="btn btn-xs btn-danger" onclick="return confirm(\'确定删除 ' . $res['apply_name'] . ' 的提交记录吗？\');">删除</a> </td></tr>';
    
    }
?>

          </tbody>
        </table>
              
      </div>
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
