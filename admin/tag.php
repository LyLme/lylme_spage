<?php
$title = '导航菜单管理';
include './head.php';
$tagsrows = $DB->num_rows($site->getTags());
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
    echo '<h4>新增导航菜单链接</h4>
<div class="panel-body">
<form action="./tag.php?set=add_submit" method="POST">
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
</select></div>
<div class="form-group">
<label>排序权重: (*必填) </label><br>
<input type="text" class="form-control" name="sort" value="10" required>
<small class="help-block">(*必填) 数字越小越靠前</small>
</div>
<div class="form-group">
<input type="submit" class="btn btn-primary btn-block" value="添加"></form>
</div>
<br/><a href="./tag.php"><<返回</a>
</div></div>';
} elseif ($set == 'edit') {
    $id = $_GET['id'];
    $row2 = $DB->query("select * from lylme_tags where tag_id='$id' limit 1");
    $row = $DB->fetch($row2);
    echo '<h4>修改导航菜单链接</h4>
<div class="panel-body">
<form action="./tag.php?set=edit_submit&id=' . $id . '" method="POST">
<div class="form-group">
<label>*名称:</label><br>
<input type="text" class="form-control" name="name" value="' . $row['tag_name'] . '" required>
</div>
<div class="form-group">
<label>*链接地址:</label><br>
<input type="text" class="form-control" name="link" value="' . $row['tag_link'] . '" required>
</div>
<div class="form-group">
<label>*打开方式:</label><br>
<select class="form-control" name="target">
<option  value="0">0. 当前窗口打开</option>
<option ';
    if ($row['tag_target'] == 1) {
        echo 'selected="selected"';
    }
    echo 'value="1">1. 新窗口打开</option>
</select></div>
<div class="form-group">
<label>排序权重: (*必填) </label><br>
<input type="text" class="form-control" name="sort" value="' . $row['sort'] . '" required>
<small class="help-block">(*必填) 数字越小越靠前</small>
</div>
<div class="form-group">
<input type="submit" class="btn btn-primary btn-block" value="修改"></form>
</div>
<br/><a href="./tag.php"><<返回</a>
</div></div>';
} elseif ($set == 'add_submit') {
    $name = $_POST['name'];
    $link = $_POST['link'];
    $sort = $_POST['sort'] ?: 10;
    if ($_POST['target'] == true) {
        $target = 1;
    } else {
        $target = 0;
    }
    if ($name == null or $link == null) {
        echo '<script>alert("保存错误,请确保带星号的都不为空！");history.go(-1);</script>';
    } else {
        $sql = "INSERT INTO `lylme_tags` (`tag_id`, `tag_name`, `tag_link`, `tag_target`,`sort`) VALUES (NULL, '" . $name . "', '" . $link . "', '" . $target . "','" . $sort . "');";
        if ($DB->query($sql)) {
            echo '<script>alert("添加导航菜单 ' . $name . ' 成功！");window.location.href="./tag.php";</script>';
        } else {
            echo '<script>alert("添加导航菜单失败");history.go(-1);</script>';
        }
    }
} elseif ($set == 'edit_submit') {
    $id = $_GET['id'];
    $sort = $_POST['sort'] ?: 10;
    $rows2 = $DB->query("select * from lylme_tags where tag_id='$id' limit 1");
    $rows = $DB->fetch($rows2);
    if (!$rows) {
        echo '<script>alert("当前记录不存在！");history.go(-1);</script>';
    }
    $name = $_POST['name'];
    $link = $_POST['link'];
    if ($_POST['target'] == true) {
        $target = 1;
    } else {
        $target = 0;
    }
    if ($name == null or $link == null) {
        echo '<script>alert("保存错误,请确保带星号的都不为空！");history.go(-1);</script>';
    } else {
        $sql = "UPDATE `lylme_tags` SET `tag_name` = '" . $name . "', `tag_link` = '" . $link . "', `tag_target` = '" . $target . "', `sort` = '" . $sort . "'  WHERE `lylme_tags`.`tag_id` = " . $id . ";";
        if ($DB->query($sql)) {
            echo '<script>alert("修改导航菜单 ' . $name . ' 成功！");window.location.href="./tag.php";</script>';
        } else {
            echo '<script>alert("修改导航菜单失败！");history.go(-1);</script>';
        }
    }
} elseif ($set == 'delete') {
    $id = $_GET['id'];
    $sql = "DELETE FROM lylme_tags WHERE tag_id='$id'";
    if ($DB->query($sql)) {
        echo '<script>alert("删除成功！");window.location.href="./tag.php";</script>';
    } else {
        echo '<script>alert("删除失败！");history.go(-1);</script>';
    }
} else {
    echo '<div class="alert alert-info">系统共有 <b>' . $tagsrows . '</b> 个导航菜单<br/><a href="./tag.php?set=add" class="btn btn-primary">新增导航菜单</a></div>';
    ?>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead><tr><th>排序权重</th><th>名称</th><th>链接</th><th>操作</th></tr></thead>
          <tbody>
<?php
        $rs = $site->getTags();
    while ($res = $DB->fetch($rs)) {
        echo '<tr><td>' . $res['sort'] . '</td><td>' . $res['tag_name'] . '</td><td>' . $res['tag_link'] . '</td><td><a href="./tag.php?set=edit&id=' . $res['tag_id'] . '" class="btn btn-info btn-xs">编辑</a>&nbsp;<a href="./tag.php?set=delete&id=' . $res['tag_id'] . '" class="btn btn-xs btn-danger" onclick="return confirm(\'确定删除 ' . $res['tag_name'] . ' ？\');">删除</a></td></tr>';
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