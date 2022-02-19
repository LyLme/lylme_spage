<?php 
$title='链接管理';
include './head.php';
?>
  <div class="container" style="padding-top:100px;">
    <div class="col-sm-12 col-md-10 center-block" style="float: none;">
<?php
$set=isset($_GET['set'])?$_GET['set']:null;

if($set=='add')
{
echo '<div class="col-lg-12">
<div class="card">
<div class="card-header">
<h4>新增链接</h4>	</div>';
echo '<div class="panel-body">';
echo '<form action="./set_link.php?set=add_submit" method="POST">
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
<input type="text" class="form-control" name="url" value="" required>
</div>
<div class="form-group">
<label>*是否显示:</label><br>
<select class="form-control" name="active"><option value="1">1_是</option><option value="0">0_否</option></select>
</div>
<br/>

<input type="submit" class="btn btn-primary btn-block" value="确定添加"></form>';
echo '<br/><a href="./set_link.php">>>返回链接列表</a>';
echo '</div></div>
<script>
$("select[name=\'is_curl\']").change(function(){
	if($(this).val() == 1){
		$("#curl_display").css("display","inherit");
	}else{
		$("#curl_display").css("display","none");
	}
});
function Addstr(id, str) {
	$("#"+id).val($("#"+id).val()+str);
}
</script>';
}
elseif($set=='edit')
{
$id=$_GET['id'];
$row=$DB->get_row("select * from lylme_links where id='$id' limit 1");
echo '<div class="col-lg-12">
<div class="card">
<div class="card-header"><h4>修改链接信息</h4></div>';
echo '<div class="panel-body">';
echo '<form action="./set_link.php?set=edit_submit&id='.$id.'" method="POST">
<div class="form-group">
<label>链接名称:</label><br>
<input type="text" class="form-control" name="name" value="'.$row['name'].'" required>
</div>
<div class="form-group">
<label>价格:</label><br>
<input type="text" class="form-control" name="url" value="'.$row['url'].'" required>
</div>
<div class="form-group">
<label>是否显示:</label><br>
<select class="form-control" name="active" default="'.$row['active'].'"><option value="1">1_是</option><option value="0">0_否</option></select>
</div>
<br/>

<input type="submit" class="btn btn-primary btn-block"
value="确定修改"></form>
';
echo '<br/><a href="./set_link.php">>>返回链接列表</a>';
echo '</div></div>
<script>
$("select[name=\'is_curl\']").change(function(){
	if($(this).val() == 1){
		$("#curl_display").css("display","inherit");
	}else{
		$("#curl_display").css("display","none");
	}
});
function Addstr(id, str) {
	$("#"+id).val($("#"+id).val()+str);
}
var items = $("select[default]");
for (i = 0; i < items.length; i++) {
	$(items[i]).val($(items[i]).attr("default")||0);
}
</script>';
}
elseif($set=='add_submit')
{
$name=$_POST['name'];
$url=$_POST['url'];
$active=$_POST['active'];
if($name==NULL or $url==NULL){
showmsg('保存错误,请确保每项都不为空!',3);
} else {
$sql="insert into `lylme_links` (`name`,`url`,`active`) values ('".$name."','".$url."','".$active."')";
if($DB->query($sql)){
	showmsg('添加链接成功！<br/><br/><a href="./set_link.php">>>返回链接列表</a>',1);
}else
	showmsg('添加链接失败！'.$DB->error(),4);
}
}
elseif($set=='edit_submit')
{
$id=$_GET['id'];
$rows=$DB->get_row("select * from lylme_links where id='$id' limit 1");
if(!$rows)
	showmsg('当前记录不存在！',3);
$name=$_POST['name'];
$url=$_POST['url'];
$active=$_POST['active'];
if($name==NULL or $url==NULL){
showmsg('保存错误,请确保每项都不为空!',3);
} else {
if($DB->query("update lylme_links set name='$name',url='$url',active='$active' where id='{$id}'"))
	showmsg('修改链接成功！<br/><br/><a href="./set_link.php">>>返回链接列表</a>',1);
else
	showmsg('修改链接失败！'.$DB->error(),4);
}
}
elseif($set=='delete')
{
$id=$_GET['id'];
$sql="DELETE FROM lylme_links WHERE id='$id'";
if(mysqli_query($con,$sql))
	showmsg('删除成功！<br/><br/><a href="./set_link.php">>>返回链接列表</a>',1);
else
	showmsg('删除失败！'.$DB->error(),4);
}
else
{

$sql=" 1";
$con='系统共有 <b>'.$numrows.'</b> 个链接<br/><a href="./set_link.php?set=add" class="btn btn-primary">新增链接</a>';

echo '<div class="alert alert-info">';
echo $con;
echo '</div>';

?>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead><tr><th>ID</th><th>名称</th><th>链接</th><th>状态</th><th>操作</th></tr></thead>
          <tbody>
<?php
$pagesize=30;
$pages=intval($numrows/$pagesize);
if ($numrows%$pagesize)
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

$rs=mysqli_query("SELECT * FROM lylme_links WHERE{$sql} order by id asc");
while($res = mysqli_fetch_array($rs))
{
echo '<tr><td><b>'.$res['id'].'</b></td><td>'.$res['name'].'</td><td>'.$res['url'].'</td><td>'.($res['active']==1?'<font color=green>显示中</font>':'<font color=red>未显示</font>').'</td><td><a href="./set_link.php?set=edit&id='.$res['id'].'" class="btn btn-info btn-xs">编辑</a>&nbsp;<a href="./set_link.php?set=delete&id='.$res['id'].'" class="btn btn-xs btn-danger" onclick="return confirm(\'你确实要删除此链接链接吗？\');">删除</a></td></tr>';
}
?>
          </tbody>
        </table>
      </div>
      
<?php 
include './footer.php';
}
?>