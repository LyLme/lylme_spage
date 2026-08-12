<?php
$title = '导航菜单管理';
include './head.php';

$set = isset($_GET['set']) ? $_GET['set'] : null;

// 删除导航菜单
if ($set == 'delete') {
    $id = intval($_GET['id']);
    $sql = "DELETE FROM lylme_tags WHERE tag_id='$id'";
    if ($DB->query($sql)) {
        echo '<script>alert("删除成功！");window.location.href="./tag.php";</script>';
    } else {
        echo '<script>alert("删除失败！");history.go(-1);</script>';
    }
    exit();
}

$tagsrows = $DB->num_rows($site->getTags());
?>
    <main class="lyear-layout-content">
      
      <div class="container-fluid">
        
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-body">
<?php if ($set == 'add'): ?>
<h4>新增导航菜单链接</h4>
<div class="panel-body">
<form id="addNavLinkForm" action="./ajax_link.php?submit=add_tag" method="POST">
<div class="form-group">
<label>*名称:</label><br>
<input type="text" class="form-control" name="name" value="" required>
</div>
<div class="form-group">
<label>*链接地址:</label><br>
<input type="text" class="form-control" name="link" value="" required>
</div>
<div class="form-group">
<label>*打开方式:</label><br>
<select class="form-control" name="target">
<option  value="0">0. 当前窗口打开</option>
<option selected="selected" value="1">1. 新窗口打开</option>
</select>
</div>
<div class="form-group">
<label>排序权重: (*必填) </label><br>
<input type="text" class="form-control" name="sort" value="10" required>
<small class="help-block">(*必填) 数字越小越靠前</small>
</div>
<div class="form-group">
<input type="submit" class="btn btn-primary btn-block" value="添加">
</div>
</form>
<br/><a href="./tag.php"><<返回</a>
</div>
<?php elseif ($set == 'edit'): ?>
<?php
$id = intval($_GET['id']);
$row2 = $DB->query("select * from lylme_tags where tag_id='$id' limit 1");
$row = $DB->fetch($row2);
if (!$row) {
    echo '<script>alert("当前记录不存在！");window.location.href="./tag.php";</script>';
    exit;
}
$esc_tag_name = htmlspecialchars($row['tag_name'], ENT_QUOTES, 'UTF-8');
$esc_tag_link = htmlspecialchars($row['tag_link'], ENT_QUOTES, 'UTF-8');
?>
<h4>修改导航菜单链接</h4>
<div class="panel-body">
<form id="editNavLinkForm" action="./ajax_link.php?submit=edit_tag&id=<?php echo $id; ?>" method="POST">
<div class="form-group">
<label>*名称:</label><br>
<input type="text" class="form-control" name="name" value="<?php echo $esc_tag_name; ?>" required>
</div>
<div class="form-group">
<label>*链接地址:</label><br>
<input type="text" class="form-control" name="link" value="<?php echo $esc_tag_link; ?>" required>
</div>
<div class="form-group">
<label>*打开方式:</label><br>
<select class="form-control" name="target">
<option value="0">0. 当前窗口打开</option>
<option <?php echo ($row['tag_target'] == 1) ? 'selected="selected"' : ''; ?> value="1">1. 新窗口打开</option>
</select>
</div>
<div class="form-group">
<label>排序权重: (*必填) </label><br>
<input type="text" class="form-control" name="sort" value="<?php echo $row['sort']; ?>" required>
<small class="help-block">(*必填) 数字越小越靠前</small>
</div>
<div class="form-group">
<input type="submit" class="btn btn-primary btn-block" value="修改">
</div>
</form>
<br/><a href="./tag.php"><<返回</a>
</div>
<?php else: ?>
<div class="alert alert-info alert-stat"><div><i class="mdi mdi-tag-multiple mdi-alert-icon"></i>系统共有 <b><?php echo $tagsrows; ?></b> 个导航菜单</div><a href="./tag.php?set=add" class="btn btn-primary btn-sm">新增导航菜单</a></div>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead><tr><th>排序权重</th><th>名称</th><th>链接</th><th>操作</th></tr></thead>
          <tbody>
<?php
        $rs = $site->getTags();
    while ($res = $DB->fetch($rs)) {
?>
<tr><td><?php echo $res['sort']; ?></td><td><?php echo $res['tag_name']; ?></td><td><?php echo $res['tag_link']; ?></td><td><a href="./tag.php?set=edit&id=<?php echo $res['tag_id']; ?>" class="btn btn-info btn-xs">编辑</a>&nbsp;<a href="./tag.php?set=delete&id=<?php echo $res['tag_id']; ?>" class="btn btn-xs btn-danger" onclick="return confirm('确定删除 <?php echo $res['tag_name']; ?> ？');">删除</a></td></tr>
<?php
    }
?>
          </tbody>
        </table>
      </div>
<?php endif; ?>
                    </div>
            </div>
          </div>
          
        </div>
        
      </div>
      
    </main>
<?php
include './footer.php';
?>
<script>
    var navForm = document.getElementById('addNavLinkForm') || document.getElementById('editNavLinkForm');
    if (navForm) {
        navForm.addEventListener('submit', function(event) {
            event.preventDefault(); // 阻止表单默认提交行为
            var form = this;
            var formData = new FormData(form); // 创建 FormData 对象来收集表单数据
            var xhr = new XMLHttpRequest(); // 创建 XMLHttpRequest 对象
            xhr.open('POST', form.action, true); // 打开一个 POST 请求
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    alert(xhr.responseText); // 显示服务器响应信息
                }
            };
            xhr.send(formData); // 发送表单数据
        });
    }
</script>
