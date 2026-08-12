<?php
include_once("../include/common.php");
if (!isset($islogin) || $islogin !== 1) {
  exit("<script>window.location.href='./login.php';</script>");
}
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$grows = $DB->query("SELECT * FROM `lylme_groups` ORDER BY `group_order` ASC"); //获取分组
$group_lists = array(); // 分组 id => 名称 映射，供分页导航与链接行使用
while ($g = $DB->fetch($grows)) {
  $group_lists[$g['group_id']] = $g['group_name'];
}
// 当前分组加密状态
$gpwd = '';
$gpq = $DB->query("SELECT `group_id`, `group_pwd` FROM `lylme_groups` WHERE `group_id` = " . $page . " limit 1");
$gp = $DB->fetch($gpq);
if ($gp) {
  $gpwd = $gp['group_pwd'];
}
$rs = $DB->query("SELECT * FROM `lylme_links` WHERE `group_id` = " . $page . " ORDER BY `lylme_links`.`link_order` ASC"); //获取链接
$grouprows = $DB->num_rows($rs);
$linksrows = $DB->num_rows($DB->query("SELECT * FROM `lylme_links`")); //链接数量
?>
<div class="alert alert-info alert-stat">
  <div><i class="mdi mdi-information-outline mdi-alert-icon"></i>系统收录： <b><?php echo $linksrows; ?></b> 个链接 / 当前分组： <b><?php echo $grouprows; ?></b> 个链接</div>
  <i class="mdi mdi-help-circle tips"></i>
</div>

<!-- 功能按钮 S-->
<div id="toolbar" class="toolbar-btn-action mb-2">
  <button class="btn btn-label btn btn-purple" id="save_order" style="display:none" onclick="save_order()">
    <label><i class="mdi mdi-checkbox-marked-circle-outline"></i></label> 保存排序</button>
  <a href="./link.php?set=add" class="btn btn-primary btn-label">
    <label><i class="mdi mdi-plus" aria-hidden="true"></i></label>新增</a>
  <button id="btn_edit" type="button" class="btn btn-success btn-label" onclick="on_link()">
    <label><i class="mdi mdi-check" aria-hidden="true"></i></label>启用</button>
  <button id="btn_edit" type="button" class="btn btn-warning btn-label" onclick="off_link()">
    <label><i class="mdi mdi-block-helper" aria-hidden="true"></i></label>禁用 </button>
  <button id="btn_delete" type="button" class="btn btn-danger btn-label" onclick="del_link()">
    <label><i class="mdi mdi-window-close" aria-hidden="true"></i></label>删除</button>
  <button id="edit_group" type="button" class="btn btn-info btn-label" onclick="edit_group(mv_group)">
    <label><i class="mdi mdi-account-edit" aria-hidden="true"></i></label>移动</button>
  <?php if (empty($gpwd)): ?>
    <button id="btn_delete" type="button" class="btn btn btn-pink btn-label" onclick="pwd_link(pwd_list)">
      <label><i class="mdi mdi-key-variant" aria-hidden="true"></i></label>加密</button>
  <?php else: ?>
    <button id="btn_delete" type="button" class="btn btn btn-pink btn-label" onclick="pwd_links()">
      <label><i class="mdi mdi-key-variant" aria-hidden="true"></i></label>分组已加密</button>
  <?php endif; ?>

  <a href="./batch_add.php" target="_blank" class="btn btn-label btn btn-purple"> <label><i class="mdi mdi-import" aria-hidden="true"></i> </label> 批量导入链接</a>
</div>

<!-- 功能按钮 E -->
 <nav class="mb-3">
  <ul class="pagination">
    <?php foreach ($group_lists as $group_id => $group_name): ?>
      <li<?php if ($page == $group_id) echo ' class="active"'; ?>><a href="?page=<?php echo $group_id; ?>"><?php echo $group_name; ?></a></li>
      <?php endforeach; ?>
  </ul>
</nav>
<div class="table-responsive">
  <table class="table table-striped" id="classlisttbody">
    <thead>
      <tr style="cursor: pointer">
        <th><input type="checkbox" class="checkbox-parent" id="check_all" onclick="check_all()"></th>
        <th>排序</th>
        <th>名称</th>
        <th>链接</th>
        <th>分组</th>
        <th>启用</th>
        <th>操作</th>
      </tr>
    </thead>
    <tbody id="link">
      <?php
      $rs = $DB->query("SELECT * FROM `lylme_links` WHERE `group_id` = " . $page . " ORDER BY `lylme_links`.`link_order` ASC");
      while ($res = $DB->fetch($rs)) {
        $gname = isset($group_lists[$res['group_id']]) ? $group_lists[$res['group_id']] : '';
      ?>
        <tr>
          <td><input type="checkbox" name="link-check" value="<?php echo $res['id']; ?>"></td>
          <!-- 链接排序 S -->
          <td><a class="btn btn-success btn-xs sort-goup" data-bs-toggle="tooltip" data-bs-placement="top" title="移到顶部"><i class="mdi mdi-arrow-collapse-up"></i></a>
            <a class="btn btn-info btn-xs sort-godown" data-bs-toggle="tooltip" data-bs-placement="top" title="移到底部"><i class="mdi mdi-arrow-collapse-down"></i></a>
            <a class="btn btn-primary btn-xs sort-up" data-bs-toggle="tooltip" data-bs-placement="top" title="移到上一行"><i class="mdi mdi-arrow-up"></i></a>
            <a class="btn btn-cyan btn-xs sort-down" data-bs-toggle="tooltip" data-bs-placement="top" title="移到下一行"><i class="mdi mdi-arrow-down"></i></a>
          </td>
          <!-- 链接排序 E -->
          <td class="lylme"><?php echo $res['name']; ?></td>
          <td>
            <?php if (!empty($res['link_pwd']) || !empty($gpwd)): ?><font color="#f96197"><?php echo $res['url']; ?></font><?php else: ?><?php echo $res['url']; ?><?php endif; ?>
          </td>
          <td><?php echo $gname; ?></td>
          <td>
            <?php if ($res['link_status'] == "0"): ?><font color="red">禁用</font><?php else: ?><font color="green">启用</font><?php endif; ?>
          </td>
          <td><a href="./link.php?set=edit&id=<?php echo $res['id']; ?>" class="btn btn-info btn-primary btn-xs">编辑</a>&nbsp;<button class="btn btn-primary btn-danger  btn-xs" onclick="del_link('<?php echo $res['id']; ?>')">删除</button></td>
        </tr>
      <?php
      }
      ?>
    </tbody>
  </table>
</div>