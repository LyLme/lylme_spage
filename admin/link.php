<?php 
$title='链接管理';
include './head.php';
$grouplists =mysqli_query($con,"SELECT * FROM `lylme_groups`");
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
echo '<h4>新增链接</h4>
<div class="panel-body">
<form action="./link.php?set=add_submit" method="POST">
<div class="form-group">
<label>*名称:</label><br>
<input type="text" class="form-control" name="name" value="" required>
</div>
<div class="form-group">
<label>*URL链接地址:</label><br>
<input type="text" class="form-control" name="url" value="" required>
</div>
<div class="form-group">
<label>链接图标:</label><br>
<textarea type="text" class="form-control" name="icon"></textarea>
<small class="help-block">方式1：填写图标的<code>URL</code>地址，如<code>/img/logo.png</code>或<code>http://www.xxx.com/img/logo.png</code><br>
方式2：粘贴图标的<code>SVG</code>代码，<a href="/admin/help.php?doc=icon" target="_blank">查看教程</a><br>方式3：留空使用默认图标</small>
</div>
<div class="form-group">
<label>*分组:</label><br>
<select class="form-control" name="group_id">';

while($grouplist = mysqli_fetch_assoc($grouplists)) {
    if($grouplist["group_id"]==$row['group_id']){$select='selected="selected"';}else {$select='';}
 echo '<option  value="'.$grouplist["group_id"].'">'.$grouplist["group_id"].' - '.$grouplist["group_name"].'</option>';
}
echo '</select></div>
<div class="form-group">
<input type="submit" class="btn btn-primary btn-block" value="确定添加"></form>
</div>
<br/><a href="./link.php"><<返回链接列表</a>
</div></div>';
}
elseif($set=='edit')
{
$id=$_GET['id'];
$row2 = mysqli_query($con,"select * from lylme_links where id='$id' limit 1");
$row=mysqli_fetch_assoc($row2);
echo '<h4>修改链接信息</h4>
<div class="panel-body">
<form action="./link.php?set=edit_submit&id='.$id.'" method="POST">
<div class="form-group">
<label>*名称:</label><br>
<input type="text" class="form-control" name="name" value="'.$row['name'].'" required>
</div>
<div class="form-group">
<label>*URL链接地址:</label><br>
<input type="text" class="form-control" name="url" value="'.$row['url'].'" required>
</div>
<div class="form-group">
<label>链接图标:</label><br>
<textarea type="text" class="form-control" name="icon" >'.$row['icon'].'</textarea>
<small class="help-block">方式1：填写图标的<code>URL</code>地址，如<code>/img/logo.png</code>或<code>http://www.xxx.com/img/logo.png</code><br>
方式2：粘贴图标的<code>SVG</code>代码，<a href="/admin/help.php?doc=icon" target="_blank">查看教程</a><br>方式3：留空使用默认图标<br>方式3：留空使用默认图标</small>
</div>

<div class="form-group">
<label>*分组:</label><br>
<select class="form-control" name="group_id">';
while($grouplist = mysqli_fetch_assoc($grouplists)) {
    if($grouplist["group_id"]==$row['group_id']){$select='selected="selected"';}else {$select='';}
 echo '<option  value="'.$grouplist["group_id"].'" '.$select.'>'.$grouplist["group_id"].' - '.$grouplist["group_name"].'</option>';
}
echo '</select>
</div>
<div class="form-group">
<input type="submit" class="btn btn-primary btn-block" value="确定修改"></form>
</div>
<br/><a href="./link.php"><<返回链接列表</a>
</div></div>';
}
elseif($set=='add_submit')
{
$name=$_POST['name'];
$url=$_POST['url'];
$icon=$_POST['icon'];
$group_id=$_POST['group_id'];
$link_order = $linksrows+1;
if($name==NULL or $url==NULL){
     echo '<script>alert("保存错误,请确保带星号的都不为空！");history.go(-1);</script>';
} else {
$sql="INSERT INTO `lylme_links` (`id`, `name`, `group_id`, `url`, `icon`, `PS`,`order`) VALUES (NULL, '".$name."', '".$group_id."', '".$url."', '".$icon."', '".$name."', '".$link_order."');";

if(mysqli_query($con,$sql)){
 echo '<script>alert("添加链接 '.$name.' 成功！");window.location.href="/admin/link.php";</script>';
}else
 echo '<script>alert("添加链接失败");history.go(-1);</script>';
}
}
elseif($set=='edit_submit')
{
$id=$_GET['id'];
$rows2 = mysqli_query($con,"select * from lylme_links where id='$id' limit 1");
$rows=mysqli_fetch_assoc($rows2);
if(!$rows)
 echo '<script>alert("当前记录不存在！");history.go(-1);</script>';
$name=$_POST['name'];
$url=$_POST['url'];
$icon=$_POST['icon'];
$group_id=$_POST['group_id'];
if($name==NULL or $url==NULL){
     echo '<script>alert("保存错误,请确保带星号的都不为空！");history.go(-1);</script>';
} else {
    
$sql = "UPDATE `lylme_links` SET `name` = '".$name."', `url` = '".$url."', `icon` = '".$icon."', `group_id` = '".$group_id."' WHERE `lylme_links`.`id` = '".$id."';";
if(mysqli_query($con,$sql))
echo '<script>alert("修改链接 '.$name.' 成功！");window.location.href="/admin/link.php";</script>';
else
 echo '<script>alert("修改链接失败！");history.go(-1);</script>';
}
}
elseif($set=='delete')
{
$id=$_GET['id'];
$sql="DELETE FROM lylme_links WHERE id='$id'";
if(mysqli_query($con,$sql))
 echo '<script>alert("删除成功！");window.location.href="/admin/link.php";</script>';
else
 echo '<script>alert("删除失败！");history.go(-1);</script>';
}
else
{

$sql=" 1";
$cons='系统共有 <b>'.$linksrows.'</b> 个链接<br/><a href="./link.php?set=add" class="btn btn-primary">新增链接</a>';

echo '<div class="alert alert-info">';
echo $cons;
echo '</div>';

?>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead><tr><th>ID</th><th>名称</th><th>链接</th><th>分组ID</th><th>操作</th></tr></thead>
          <tbody>
<?php
$pagesize=30;
$pages=intval($linksrows/$pagesize);
if ($linksrows%$pagesize)
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

$rs=mysqli_query($con,"SELECT * FROM lylme_links WHERE{$sql} order by id asc");
while($res = mysqli_fetch_array($rs))
{
echo '<tr><td><b>'.$res['id'].'</b></td><td>'.$res['name'].'</td><td>'.$res['url'].'</td><td>'.$res['group_id'].'</td><td><a href="./link.php?set=edit&id='.$res['id'].'" class="btn btn-info btn-xs">编辑</a>&nbsp;<a href="./link.php?set=delete&id='.$res['id'].'" class="btn btn-xs btn-danger" onclick="return confirm(\'你确实要删除 '.$res['name'].' 吗？\');">删除</a></td></tr>';
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