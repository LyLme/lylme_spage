<?php
$title = '主题设置';
include './head.php';

$set = isset($_GET['set']) ? $_GET['set'] : null;
if (!empty($set)) {
  if (saveSetting('template', $set, "网站主题")) {
    exit('<script>$.alert({title:"成功",content:"主题修改成功！",buttons:{confirm:{text:"确定",btnClass:"btn-primary",action:function(){window.location.href="./theme.php";}}}});</script>');
  } else {
    exit('<script>$.alert({title:"错误",content:"主题修改失败！",buttons:{confirm:{text:"确定",btnClass:"btn-primary",action:function(){window.location.href="./theme.php";}}}});</script>');
  }
}
?>
<main class="lyear-layout-content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <h4>主题设置 <a href="https://spage.lylme.com/themes" target="_blank">更多主题 >></a></h4>
          </div>
          <div class="card-body">
            <a href="./theme_setting.php" class="btn btn-label btn btn-default"><label><i class="mdi mdi-contrast-circle"></i></label>主题自定义设置</a>
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>主题名称</th>
                    <th>主题说明</th>
                    <th>主题作者</th>
                    <th>在线演示</th>
                    <th>选择主题</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $theme_path = ROOT . 'template/';
                  $themes = glob($theme_path . "*", GLOB_ONLYDIR);
                  foreach ($themes as $theme) {
                    $theme =  str_replace($theme_path, "", $theme);
                  ?>
<tr><td><h5><?php echo theme($theme, "theme_name"); ?> </h5>版本：<?php echo theme($theme, "theme_version"); ?></td>

<td><p><?php echo theme($theme, "theme_explain"); ?></p>
<?php if (theme($theme, "theme_course")): ?>
<a href="<?php echo theme($theme, "theme_course"); ?>" target="_blank">主题教程</a>
<?php endif; ?>
</td>
<td><p><?php echo theme($theme, "author_name"); ?></p>
<?php if (theme($theme, "author_link")): ?>
<a href="<?php echo theme($theme, "author_link"); ?>" target="_blank">作者主页</a>
<?php endif; ?>
</td><td>
<?php if (theme($theme, "theme_demo")): ?>
<p><a class="btn btn-default" href="<?php echo theme($theme, "theme_demo"); ?>" target="_blank">在线演示</a></p>
<?php endif; ?>
</td>
<?php if ($conf['template'] == $theme): ?>
<td><p class="btn btn-default disabled">当前使用</p></td>
<?php else: ?>
<td><a href="./theme.php?set=<?php echo $theme; ?>" class="btn btn-label btn-primary"><label><i class="mdi mdi-checkbox-marked-circle-outline"></i></label>使用</a></td>
<?php endif; ?>
</tr>
<?php
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
</main>
<?php
include './footer.php';
?>