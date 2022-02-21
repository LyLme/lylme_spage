<?php 
$title='链接管理';
include './head.php';
?>
 <main class="lyear-layout-content">
      
      <div class="container-fluid">
        
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-body">
<?php

$set=isset($_GET['set'])?$_GET['set']:null;

if($set=='add')
{
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
方式2：粘贴图标的<code>SVG</code>代码，<a href="/admin/help.php?doc=icon" target="_blank">查看教程</a><br>方式3：留空使用默认图标</small>
</div>

<div class="form-group">
<input type="submit" class="btn btn-primary btn-block" value="确定添加"></form>
</div>
<br/><a href="./group.php"><<返回分组列表</a>
</div></div>
';
}
elseif($set=='edit')
{
$id=$_GET['id'];
$row2 = mysqli_query($con,"select * from lylme_groups where group_id='$id' limit 1");
$row=mysqli_fetch_assoc($row2);
echo '<h4>修改分组信息</h4>
<div class="panel-body">
<form action="./group.php?set=edit_submit&id='.$id.'" method="POST">
<div class="form-group">
<label>*名称:</label><br>
<input type="text" class="form-control" name="group_name" value="'.$row['group_name'].'" required>
</div>
<div class="form-group">
<label>分组图标:</label><br>
<textarea type="text" class="form-control" name="group_icon">'.$row['group_icon'].'</textarea>
<small class="help-block">方式1：填写图标的<code>URL</code>地址，如<code>/img/logo.png</code>或<code>http://www.xxx.com/img/logo.png</code><br>
方式2：粘贴图标的<code>SVG</code>代码，<a href="/admin/help.php?doc=icon" target="_blank">查看教程</a><br>方式3：留空使用默认图标</small>
</div>

<div class="form-group">
<input type="submit" class="btn btn-primary btn-block" value="确定修改"></form>
</div>
<br/><a href="./group.php"><<返回分组列表</a></div></div>';
}
elseif($set=='add_submit')
{
$name=$_POST['group_name'];
$icon=$_POST['group_icon'];
if($name==NULL){
    echo '<script>alert("保存错误,请确保带星号的都不为空！");history.go(-1);</script>';
} else {
    
$sql="INSERT INTO `lylme_groups` (`group_id`, `group_name`, `group_icon`) VALUES (NULL, '".$name."', '".$icon."')";

if(mysqli_query($con,$sql)){
 echo '<script>alert("添加分组 '.$name.' 成功！");window.location.href="/admin/group.php";</script>';
 exit();
}else
 echo '<script>alert("添加分组失败);history.go(-1);</script>';
  exit();
}
echo '<script>alert("添加分组失败,名称重复);history.go(-1);</script>';
}
elseif($set=='edit_submit')
{
$id=$_GET['id'];
$rows2 = mysqli_query($con,"select * from lylme_groups where group_id='$id' limit 1");
$rows=mysqli_fetch_assoc($rows2);
if(!$rows)
 echo '<script>alert("当前记录不存在！");history.go(-1);</script>';
$name=$_POST['group_name'];
$icon=$_POST['group_icon'];
if($name==NULL){
     echo '<script>alert("保存错误,请确保带星号的都不为空！");history.go(-1);</script>';
} else {
    
$sql = "UPDATE `lylme_groups` SET `group_name` = '".$name."', `group_icon` = '".$icon."' WHERE `lylme_groups`.`group_id` = '".$id."';";

if(mysqli_query($con,$sql))
echo '<script>alert("修改分组 '.$name.' 成功！");window.location.href="/admin/group.php";</script>';
else
 echo '<script>alert("'.$sql.'修改分组失败");history.go(-1);</script>';
}
}
elseif($set=='delete')
{
$id=$_GET['id'];
$sql="DELETE FROM lylme_groups WHERE group_id='$id'";
if(mysqli_query($con,$sql))
 echo '<script>alert("删除成功！");window.location.href="/admin/group.php";</script>';
else
 echo '<script>alert("删除失败");history.go(-1);</script>';
}
else
{

$sql=" 1";
$cons='系统共有 <b>'.$groupsrows.'</b> 个分组<br/><a href="./group.php?set=add" class="btn btn-primary">新建分组</a>';

echo '<div class="alert alert-info">';
echo $cons;
echo '</div>';

?>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead><tr><th>ID</th><th>名称</th><th>操作</th></tr></thead>
          <tbody>
<?php
$pagesize=30;
$pages=intval($groupsrows/$pagesize);
if ($groupsrows%$pagesize)
{
 $pages++;
 }
if (isset($_GET['page'])){
$page=intval($_GET['page']);
}
else{
$page=1;
}
$offset=$pagesize*($page - 1);

$rs=mysqli_query($con,"SELECT * FROM lylme_groups WHERE{$sql} order by group_id asc");
while($res = mysqli_fetch_array($rs))
{
echo '<tr><td><b>'.$res['group_id'].'</b></td><td>'.$res['group_name'].'</td><td><a href="./group.php?set=edit&id='.$res['group_id'].'" class="btn btn-info btn-xs">编辑</a>&nbsp;<a href="./group.php?set=delete&id='.$res['group_id'].'" class="btn btn-xs btn-danger" onclick="return confirm(\'你确实要删除 '.$res['group_name'].' 吗？\');">删除</a></td></tr>';
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