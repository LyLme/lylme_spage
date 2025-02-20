<?php
$title = '主题设置';
include './head.php';

$set = isset($_GET['set']) ? $_GET['set'] : null;
if (!empty($set)) {
  if (saveSetting('template', $set, "网站主题")) {
    exit('<script>alert("主题修改成功！");window.location.href="./theme.php";</script>');
  } else {
    exit('<script>alert("主题修改失败！");window.location.href="./theme.php";</script>');
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
                    echo '<tr><td><h4>' . theme($theme, "theme_name") . ' </h4>版本：' . theme($theme, "theme_version") . '</td>';

                    echo '<td><p>' . theme($theme, "theme_explain") . '</p>';
                    if (theme($theme, "theme_course")) {
                      echo ' <a href="' . theme($theme, "theme_course") . '" target="_blank">主题教程</a>';
                    }
                    echo '</td>';
                    echo '<td><p>' . theme($theme, "author_name") . '</p>';
                    if (theme($theme, "author_link")) {
                      echo ' <a href="' . theme($theme, "author_link") . '" target="_blank">作者主页</a>';
                    }
                    echo '</td><td>';
                    if (theme($theme, "theme_demo")) {
                      echo '<p><a  class="btn btn-default" href="' . theme($theme, "theme_demo") . '" target="_blank">在线演示</a></p>';
                    }
                    echo '</td>';
                    if ($conf['template'] == $theme) {
                      echo '<td><p class="btn btn-default disabled">当前使用</p></td>';
                    } else {
                      echo '<td><a href="./theme.php?set=' . $theme . '" class="btn btn-label btn-primary"><label><i class="mdi mdi-checkbox-marked-circle-outline"></i></label>使用</a></td>';
                    }
                    echo '</tr>' . "\n";
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