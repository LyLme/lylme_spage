<?php
include_once("../include/common.php");
if (!isset($islogin) || $islogin !== 1) {
    exit("<script>window.location.href='./login.php';</script>");
}
// 预取加密组（pwd_id => pwd_key）
$pwd_map = array();
$pq = $DB->query("SELECT `pwd_id`, `pwd_key` FROM `lylme_pwd`");
while ($p = $DB->fetch($pq)) {
    $pwd_map[$p['pwd_id']] = $p['pwd_key'];
}
// 预取各分组链接数（group_id => 数量）
$link_count_map = array();
$lq = $DB->query("SELECT `group_id`, COUNT(`id`) AS cnt FROM `lylme_links` GROUP BY `group_id`");
while ($l = $DB->fetch($lq)) {
    $link_count_map[$l['group_id']] = intval($l['cnt']);
}
$groupsrows = $DB->num_rows($DB->query("SELECT `group_id` FROM `lylme_groups`")); //分组数量
$rs = $DB->query("SELECT * FROM lylme_groups ORDER BY group_order ASC");
?>
<div class="alert alert-info alert-stat"><div><i class="mdi mdi-folder-multiple mdi-alert-icon"></i>系统共有 <b><?php echo $groupsrows; ?></b> 个分组</div><a href="./group.php?set=add" class="btn btn-primary btn-sm">新建分组</a></div>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead><tr><th>名称</th><th>排序</th><th>链接数</th><th>加密</th><th>状态</th><th>操作</th></tr></thead>
          <tbody>
<?php
while ($res = $DB->fetch($rs)) {
  $pwd = null;
  if (isset($res['group_pwd']) && $res['group_pwd'] !== 0) {
    if (isset($pwd_map[$res['group_pwd']])) {
      $pwd = $pwd_map[$res['group_pwd']];
    }
  }
  $link_count = isset($link_count_map[$res['group_id']]) ? $link_count_map[$res['group_id']] : 0;
?>
<tr><td><input type="hidden" name="group_id" value="<?php echo $res['group_id']; ?>"><?php echo $res['group_name']; ?></td><td>
  <button class="btn btn-primary btn-xs sort-up">上移</button>&nbsp;<button class="btn btn-primary btn-xs sort-down">下移</button></td>
  <td><?php echo $link_count; ?></td>
  <td>
  <?php if ($pwd || $res['group_pwd']): ?>
    <?php if (empty($pwd)): ?>
    <font color="red">失效[请重新设置加密组]</font>
    <?php else: ?>
    <font color="f96197"><?php echo $pwd; ?></font>
    <?php endif; ?>
  <?php else: ?>
    <font color="green">未加密</font>
  <?php endif; ?>
  </td><td>
  <?php if ($res['group_status']): ?>
    <button class="btn btn-pink btn-xs" onclick="off_group(<?php echo $res['group_id']; ?>)">禁用</button>
  <?php else: ?>
    <button class="btn btn-success btn-xs" onclick="on_group(<?php echo $res['group_id']; ?>)">启用</button>
  <?php endif; ?>
  </td><td>&nbsp;<a href="./group.php?set=edit&id=<?php echo $res['group_id']; ?>" class="btn btn-info btn-xs">编辑</a>&nbsp;<button class="btn btn-xs btn-danger" onclick="del_group(<?php echo $res['group_id']; ?>)">删除</button></td></tr>
<?php
}
?>
          </tbody>
        </table>
      </div>
