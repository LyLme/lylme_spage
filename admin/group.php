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
    // 如果是纯图片URL（非SVG/HTML），自动包裹img标签
    if ($icon !== '' && $icon[0] !== '<') {
        $icon = '<img src="' . $icon . '" class="lylme-group-img" />';
    }
    $pwd = intval($_POST['group_pwd']);
    $max_order = $DB->get_row("SELECT MAX(`group_order`) AS max_order FROM `lylme_groups`");
    $group_order = intval($max_order['max_order']) + 1;
    if ($name == NULL) {
        echo '<script>lightyear.notify("保存错误,请确保带星号的都不为空！", "danger", 3000);</script>';
    } else {
        $sql = "INSERT INTO `lylme_groups` (`group_id`, `group_name`, `group_icon`,`group_order`,`group_pwd`) VALUES (NULL, '" . $name . "', '" . $icon . "', '" . $group_order . "', '" . $pwd . "')";
        if ($DB->query($sql)) {
            echo '<script>$.alert({title:"成功",content:"添加分组 ' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . ' 成功！",buttons:{confirm:{text:"确定",btnClass:"btn-primary",action:function(){window.location.href="./group.php";}}}});</script>';
            exit();
        } else echo '<script>lightyear.notify("添加分组失败", "danger", 3000);</script>';
        exit();
    }
    echo '<script>lightyear.notify("添加分组失败,名称重复", "danger", 3000);</script>';
    exit();
}

// 修改分组
if ($set == 'edit_submit') {
    $id = intval($_GET['id']);
    $rows2 = $DB->query("select * from lylme_groups where group_id='$id' limit 1");
    $rows = $DB->fetch($rows2);
    if (!$rows) {
        echo '<script>lightyear.notify("当前记录不存在！", "danger", 3000);</script>';
        exit;
    }
    $name = daddslashes($_POST['group_name']);
    $icon = daddslashes($_POST['group_icon']);
    // 如果是纯图片URL（非SVG/HTML），自动包裹img标签
    if ($icon !== '' && $icon[0] !== '<') {
        $icon = '<img src="' . $icon . '" class="lylme-group-img" />';
    }
    $pwd = intval($_POST['group_pwd']);
    if ($name == NULL) {
        echo '<script>lightyear.notify("保存错误,请确保带星号的都不为空！", "danger", 3000);</script>';
    } else {
        $sql = "UPDATE `lylme_groups` SET `group_name` = '" . $name . "', `group_icon` = '" . $icon . "',`group_pwd` = '" . $pwd . "' WHERE `lylme_groups`.`group_id` = '" . $id . "';";
        if ($DB->query($sql)) echo '<script>$.alert({title:"成功",content:"修改分组 ' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . ' 成功！",buttons:{confirm:{text:"确定",btnClass:"btn-primary",action:function(){window.location.href="./group.php";}}}});</script>';
        else echo '<script>lightyear.notify("修改分组失败", "danger", 3000);</script>';
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
<label>分组图标:</label>
<div class="btn-group btn-group-sm" data-bs-toggle="buttons">
<label class="btn btn-default active" onclick="switchIconType('image')"><input type="radio" name="icon_type" value="image" checked> <i class="mdi mdi-image"></i> 图片</label>
<label class="btn btn-default" onclick="switchIconType('svg')"><input type="radio" name="icon_type" value="svg"> <i class="mdi mdi-code-tags"></i> SVG</label>
</div>
<div id="icon_image_area">
<div class="input-group">
<textarea class="form-control" name="group_icon" id="group_icon_input" placeholder="输入图片URL或点击上传"></textarea>
<span class="input-group-btn">
<input type="file" id="group_icon_file" onchange="uploadimg()" accept="image/png, image/jpeg,image/gif,image/x-icon" style="display: none">
<button class="btn btn-default" onclick="$('#group_icon_file').click();" type="button">上传</button>
</span>
</div>
<small class="help-block">输入图片<code>URL</code>地址或从本地上传，保存时自动转为img标签</small>
</div>
<div id="icon_svg_area" style="display:none">
<textarea class="form-control" name="group_icon_svg" id="group_icon_svg_input" rows="4" placeholder="粘贴SVG代码"></textarea>
<small class="help-block">粘贴图标的<code>SVG</code>代码，<a href="./help.php?doc=icon" target="_blank">查看教程</a></small>
</div>
<small class="help-block">留空使用默认图标</small>
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
<input type="submit" class="btn btn-primary d-block w-100" value="确定添加">
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
    echo '<script>$.alert({title:"提示",content:"当前记录不存在！",buttons:{confirm:{text:"确定",btnClass:"btn-primary",action:function(){window.location.href="./group.php";}}}});</script>';
    exit;
}
$esc_group_name = htmlspecialchars($row['group_name'], ENT_QUOTES, 'UTF-8');
$esc_group_icon = htmlspecialchars($row['group_icon'], ENT_QUOTES, 'UTF-8');
// 检测图标类型：img标签/纯URL为图片模式，svg标签为SVG模式
$icon_type = 'image';
if (stripos($row['group_icon'], '<svg') !== false) {
    $icon_type = 'svg';
} elseif (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/', $row['group_icon'], $icon_matches)) {
    $esc_group_icon = htmlspecialchars($icon_matches[1], ENT_QUOTES, 'UTF-8');
}
?>
<h4>修改分组信息</h4>
<div class="panel-body">
<form action="./group.php?set=edit_submit&id=<?php echo $id; ?>" method="POST">
<div class="form-group">
<label>*名称:</label><br>
<input type="text" class="form-control" name="group_name" value="<?php echo $esc_group_name; ?>" required>
</div>
<div class="form-group">
<label>分组图标:</label>
<div class="btn-group btn-group-sm" data-bs-toggle="buttons">
<label class="btn btn-default<?php echo $icon_type == 'image' ? ' active' : ''; ?>" onclick="switchIconType('image')"><input type="radio" name="icon_type" value="image"<?php echo $icon_type == 'image' ? ' checked' : ''; ?>> <i class="mdi mdi-image"></i> 图片</label>
<label class="btn btn-default<?php echo $icon_type == 'svg' ? ' active' : ''; ?>" onclick="switchIconType('svg')"><input type="radio" name="icon_type" value="svg"<?php echo $icon_type == 'svg' ? ' checked' : ''; ?>> <i class="mdi mdi-code-tags"></i> SVG</label>
</div>
<div id="icon_image_area"<?php echo $icon_type == 'svg' ? ' style="display:none"' : ''; ?>>
<div class="input-group">
<textarea class="form-control" name="group_icon" id="group_icon_input" placeholder="输入图片URL或点击上传"><?php echo $icon_type == 'image' ? $esc_group_icon : ''; ?></textarea>
<span class="input-group-btn">
<input type="file" id="group_icon_file" onchange="uploadimg()" accept="image/png, image/jpeg,image/gif,image/x-icon" style="display: none">
<button class="btn btn-default" onclick="$('#group_icon_file').click();" type="button">上传</button>
</span>
</div>
<small class="help-block">输入图片<code>URL</code>地址或从本地上传，保存时自动转为img标签</small>
</div>
<div id="icon_svg_area"<?php echo $icon_type == 'svg' ? '' : ' style="display:none"'; ?>>
<textarea class="form-control" name="group_icon_svg" id="group_icon_svg_input" rows="4" placeholder="粘贴SVG代码"><?php echo $icon_type == 'svg' ? $esc_group_icon : ''; ?></textarea>
<small class="help-block">粘贴图标的<code>SVG</code>代码，<a href="./help.php?doc=icon" target="_blank">查看教程</a></small>
</div>
<small class="help-block">留空使用默认图标</small>
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
<input type="submit" class="btn btn-primary d-block w-100" value="确定修改">
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
<script type="text/javascript" src="/assets/admin/js/group.js"></script>
<script type="text/javascript">
// 上传分组图标
function uploadimg() {
    var formData = new FormData();
    formData.append("file", $("#group_icon_file")[0].files[0]);
    $.ajax({
        method: 'POST',
        url: '/include/file.php',
        data: formData,
        timeout: 20000,
        cache: false,
        processData: false,
        contentType: false,
        dataType: "JSON",
        success: function (data) {
            if (data.code == '200') {
                layer.msg(data.msg);
                $("#group_icon_input").val(data.url);
                return true;
            }
            else {
                layer.msg(data.msg);
                return false;
            }
        },
        error: function (data) {
            layer.msg('服务器错误');
            return false;
        }
    });
}
// 切换图标类型（图片/SVG）
function switchIconType(type) {
    // 切换按钮激活状态
    var $btns = $('.btn-group[data-bs-toggle="buttons"] label.btn');
    $btns.removeClass('active');
    $btns.filter(function() { return $(this).find('input').val() === type; }).addClass('active');
    if (type === 'image') {
        $('#icon_image_area').show();
        $('#icon_svg_area').hide();
        // 将SVG内容备份并清空，切回图片模式
        var svgVal = $('#group_icon_svg_input').val();
        if (svgVal && !$('#group_icon_input').val()) {
            $('#group_icon_input').val('');
        }
    } else {
        $('#icon_image_area').hide();
        $('#icon_svg_area').show();
        // 将图片内容备份并清空，切到SVG模式
        var imgVal = $('#group_icon_input').val();
        if (imgVal && !$('#group_icon_svg_input').val()) {
            $('#group_icon_svg_input').val('');
        }
    }
}
// 提交前将当前模式的值同步到 group_icon 字段
$(document).on('submit', 'form[action*="group.php"]', function() {
    var type = $('input[name="icon_type"]:checked').val();
    if (type === 'svg') {
        $('#group_icon_input').val($('#group_icon_svg_input').val());
    }
});
</script>
