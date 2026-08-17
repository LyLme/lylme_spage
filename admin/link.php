<?php
$title = '链接管理';
include './head.php';

// 预取分组、加密组数据（新增/编辑/列表共用，避免结果集被消费后二次读取为空）
$grouplists = array();
$pwd_lists  = array();
$gq = $DB->query("SELECT * FROM `lylme_groups`");
while ($g = $DB->fetch($gq)) $grouplists[] = $g;
$pq = $DB->query("SELECT * FROM `lylme_pwd`");
while ($p = $DB->fetch($pq)) $pwd_lists[] = $p;

$set = isset($_GET['set']) ? $_GET['set'] : null;
?>
<main class="lyear-layout-content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">

<?php if ($set == 'add'): ?>
            <h4>新增链接</h4>
            <form id="addLinkForm" action="./ajax_link.php?submit=add_link" method="POST">
              <div class="form-group">
                <label for="add_url">*URL链接地址:</label>
                <div class="input-group">
                  <input type="text" class="form-control" id="add_url" name="url" placeholder="链接" required>
                  <span class="input-group-btn">
                    <button class="btn btn-default" onclick="geturl()" type="button">获取</button>
                  </span>
                </div>
              </div>

              <div class="form-group">
                <label for="add_name">*网站名称:</label>
                <input type="text" class="form-control" id="urlname" name="name" placeholder="网站名称" required>
              </div>

              <div class="form-group">
                <label for="add_color">链接颜色(留空默认):</label>
                <input type="text" class="coloris form-control" id="add_color" onchange="select_color()" name="color" placeholder="点击选择颜色">
              </div>

              <div class="form-group">
                <label for="add_icon">链接图标:</label>
                <div class="input-group">
                  <textarea class="form-control" id="add_icon" name="icon" placeholder="网站图标"></textarea>
                  <span class="input-group-btn">
                    <input type="file" id="file" onchange="uploadimg(this)" accept="image/png, image/jpeg,image/gif,image/x-icon" style="display: none">
                    <button class="btn btn-default" id="uploadImage" onclick="$('#file').click();" type="button">选择</button>
                  </span>
                </div>
                <small class="help-block"><b>可选方案：</b><br>
                  1. 填写图标的<code>URL</code>地址，如<code>/img/logo.png</code>或<code>http://www.xxx.com/img/logo.png</code><br>
                  2. 粘贴图标的<code>SVG</code>代码，<a href="./help.php?doc=icon" target="_blank">查看教程</a><br>
                  3. 留空使用默认图标<br>
                  4. 从本地上传</small>
              </div>

              <div class="form-group">
                <label for="add_group">*分组:</label>
                <select class="form-control" id="add_group" name="group_id">
                  <?php foreach ($grouplists as $grouplist): ?>
                    <option value="<?php echo $grouplist['group_id']; ?>"><?php echo $grouplist['group_id']; ?> - <?php echo $grouplist['group_name']; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="form-group">
                <label for="add_desc">链接描述:</label>
                <textarea rows="2" class="form-control" id="add_desc" name="link_desc" placeholder="仅部分主题支持，可不填"></textarea>
                <small class="help-block">链接描述仅部分主题支持显示和详情页SEO，访问详情页时若为空将自动采集写入，采集失败写入"无"</small>
              </div>

              <div class="form-group">
                <label for="add_keywords">链接关键词:</label>
                <input type="text" class="form-control" id="add_keywords" name="link_keywords" maxlength="512" placeholder="多个关键词用逗号分隔，留空访问详情页时自动采集">
                <small class="help-block">关键词用于详情页 SEO，访问详情页时若为空将自动采集写入，采集失败写入"无"</small>
              </div>

              <div class="form-group">
                <input type="submit" class="btn btn-primary d-block w-100" value="添加">
              </div>
            </form>
            <br><a href="./link.php"><<返回</a>

<?php elseif ($set == 'edit'): ?>
            <?php
            $id = intval($_GET['id']);
            $row2 = $DB->query("select * from lylme_links where id='$id' limit 1");
            $row = $DB->fetch($row2);
            if (!$row) exit('该条记录不存在！');
            preg_match_all('/<font color=[\"|\']+(.*?)[\"|\']>/i', $row['name'], $color);
            $link_color = isset($color[1][0]) ? $color[1][0] : '';
            ?>
            <h4>修改链接信息</h4>
            <form id="editLinkForm" action="./ajax_link.php?submit=edit_link&id=<?php echo $id; ?>" method="POST">
              <div class="form-group">
                <label for="edit_url">*URL链接地址:</label>
                <div class="input-group">
                  <input type="text" class="form-control" id="edit_url" name="url" placeholder="链接" value="<?php echo htmlspecialchars($row['url']); ?>" required>
                  <span class="input-group-btn">
                    <button class="btn btn-default" onclick="geturl()" type="button">获取</button>
                  </span>
                </div>
              </div>

              <div class="form-group">
                <label for="urlname">*网站名称:</label>
                <input type="text" class="form-control" id="urlname" name="name" value="<?php echo htmlspecialchars(strip_tags($row['name'])); ?>" required>
              </div>

              <div class="form-group">
                <label for="edit_color">链接颜色(留空默认):</label>
                <input type="text" class="coloris form-control" id="edit_color" onchange="select_color()" name="color" value="<?php echo htmlspecialchars($link_color); ?>" placeholder="点击选择颜色">
              </div>

              <div class="form-group">
                <label for="edit_icon">链接图标:</label>
                <div class="input-group">
                  <textarea class="form-control" id="edit_icon" name="icon" placeholder="网站图标"><?php echo htmlspecialchars($row['icon']); ?></textarea>
                  <span class="input-group-btn">
                    <input type="file" id="edit_file" onchange="uploadimg(this)" accept="image/png, image/jpeg,image/gif,image/x-icon" style="display: none">
                    <button class="btn btn-default" id="edit_uploadImage" onclick="$('#edit_file').click();" type="button">选择</button>
                  </span>
                </div>
                <small class="help-block"><b>可选方案：</b><br>
                  1. 填写图标的<code>URL</code>地址，如<code>/img/logo.png</code>或<code>http://www.xxx.com/img/logo.png</code><br>
                  2. 粘贴图标的<code>SVG</code>代码，<a href="./help.php?doc=icon" target="_blank">查看教程</a><br>
                  3. 留空使用默认图标<br>
                  4. 从本地上传</small>
              </div>

              <div class="form-group">
                <label for="edit_group">*分组:</label>
                <select class="form-control" id="edit_group" name="group_id">
                  <?php foreach ($grouplists as $grouplist): ?>
                    <option value="<?php echo $grouplist['group_id']; ?>"<?php echo $grouplist['group_id'] == $row['group_id'] ? ' selected' : ''; ?>><?php echo $grouplist['group_id']; ?> - <?php echo $grouplist['group_name']; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="form-group">
                <label for="edit_pwd">链接加密:</label>
                <select class="form-control" id="edit_pwd" name="link_pwd" required>
                  <?php foreach ($pwd_lists as $pwd_list): ?>
                    <option value="<?php echo $pwd_list['pwd_id']; ?>"<?php echo $row['link_pwd'] == $pwd_list['pwd_id'] ? ' selected' : ''; ?>><?php echo $pwd_list['pwd_id']; ?> - <?php echo $pwd_list['pwd_name']; ?> | 密码[<?php echo $pwd_list['pwd_key']; ?>]</option>
                  <?php endforeach; ?>
                  <option value="0"<?php echo empty($row['link_pwd']) ? ' selected' : ''; ?>>0 - 不加密</option>
                </select>
                <small class="help-block"><code>注意：对链接所在的分组加密后，单独设置的链接加密将会失效</code><br>
                  加密后只能通过输入密码访问，使用该功能先配置加密组
                  <a href="./pwd.php" target="_blank">管理加密组</a></small>
              </div>

              <div class="form-group">
                <label for="edit_desc">链接描述:</label>
                <textarea rows="2" class="form-control" id="edit_desc" name="link_desc" placeholder="仅部分主题支持，可不填"><?php echo htmlspecialchars($row['link_desc']); ?></textarea>
                <small class="help-block">链接描述仅部分主题支持显示和详情页SEO，访问详情页时若为空将自动采集写入，采集失败写入"无"</small>
              </div>

              <div class="form-group">
                <label for="edit_keywords">链接关键词:</label>
                <input type="text" class="form-control" id="edit_keywords" name="link_keywords" maxlength="512" placeholder="多个关键词用逗号分隔" value="<?php echo htmlspecialchars($row['link_keywords']); ?>">
                <small class="help-block">关键词用于详情页 SEO，为空时访问详情页将自动采集写入</small>
              </div>

              <div class="form-group">
                <input type="submit" class="btn btn-primary d-block w-100" value="修改">
              </div>
            </form>
            <br><a href="./link.php"><<返回</a>

<?php else: ?>
            <div id="listTable"></div>
<?php endif; ?>

          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<?php include './footer.php'; ?>

<script type="text/javascript" src="/assets/admin/js/jquery.dragsort-0.5.2.min.js"></script>
<script src="/assets/admin/js/layer.min.js" type="application/javascript"></script>
<link rel="stylesheet" type="text/css" href="/assets/admin/css/coloris.min.css" />
<script type="text/javascript" src="/assets/admin/js/coloris.min.js"></script>
<script type="text/javascript">
  Coloris({
    el: '.coloris',
    swatches: ['#000000', '#555555', '#666666', '#264653', '#2a9d8f', '#f4a261', '#e76f51', '#ff0000', '#d62828', '#023e8a', '#0077b6', '#0096c7']
  });
</script>
<style>
  .clr-alpha {
    display: none !important;
  }
</style>
<script type="text/javascript">
  function select_color() {
    var fontcolor = $('input[name="color"]').val();
    $('#urlname').css("color", fontcolor);
  }
  select_color();
</script>
<script type="text/javascript" src="/assets/admin/js/link.js?v=20260813f"></script>
<script type="text/javascript">
  // 新增/编辑表单 AJAX 提交（阻止默认跳转，弹窗显示服务端返回）
  function bindFormAjax(formId) {
    var form = document.getElementById(formId);
    if (!form) return;
    form.addEventListener('submit', function (event) {
      event.preventDefault();
      var xhr = new XMLHttpRequest();
      xhr.open('POST', form.action, true);
      xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
          try {
            var resp = JSON.parse(xhr.responseText);
            var _d = resp.code == 200 ? 'success' : 'danger';
            lightyear.notify(resp.msg || xhr.responseText, _d, _d == 'success' ? 1000 : 3000);
          } catch(e) {
            lightyear.notify(xhr.responseText, 'info', 2000);
          }
        }
      };
      xhr.send(new FormData(form));
    });
  }
  bindFormAjax('addLinkForm');
  bindFormAjax('editLinkForm');
</script>
<script type="text/javascript">
  //分组移动
  var mv_group = '<form action="" class="formName">' + '<select class="form-control group_id" required><option value="">请选择分组...</option>' + '<?php foreach ($grouplists as $grouplist) echo '<option value="' . $grouplist['group_id'] . '">' . $grouplist['group_id'] . ' - ' . $grouplist['group_name'] . '</option>'; ?>' + '</select>';
  //链接加密
  var pwd_list = '<form action="" class="formName">' + '<select class="form-control pwd_id" required>' + '<?php foreach ($pwd_lists as $pwd_list) echo '<option value="' . $pwd_list['pwd_id'] . '">' . $pwd_list['pwd_id'] . ' - ' . $pwd_list['pwd_name'] . '</option>'; ?>' + '<option value="0">0 - 取消加密</option></select><br><a href="./pwd.php" target="_blank">管理加密组</a>';
</script>
