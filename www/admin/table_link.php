<?php
include_once("../include/common.php");
if (!isset($islogin) || $islogin !== 1) {
  exit("<script>window.location.href='./login.php';</script>");
}
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$grouplists = array();
$group_lists = array();
$grows = $DB->query("SELECT * FROM `lylme_groups` ORDER BY `group_order` ASC");
while ($g = $DB->fetch($grows)) {
  $grouplists[] = $g;
  $group_lists[$g['group_id']] = $g['group_name'];
}
$gpwd = '';
foreach ($grouplists as $_g) {
  if ($_g['group_id'] == $page) {
    $gpwd = $_g['group_pwd'];
    break;
  }
}
$rs = $DB->query("SELECT * FROM `lylme_links` WHERE `group_id` = " . $page . " ORDER BY `link_order` ASC");
$grouprows = $DB->num_rows($rs);
$linksrows = $DB->num_rows($DB->query("SELECT * FROM `lylme_links`"));
?>
<div class="alert alert-info alert-stat">
  <div><i class="mdi mdi-information-outline mdi-alert-icon"></i>系统收录： <b><?php echo $linksrows; ?></b> 个链接 / 当前分组： <b><?php echo $grouprows; ?></b> 个链接</div>
  <i class="mdi mdi-help-circle tips"></i>
</div>

<div class="toolbar-btn-action mb-2 d-flex flex-row flex-wrap justify-content-around d-md-block">
  <button class="my-1 btn btn-label btn-info" id="save_order" style="display:none" onclick="save_order()">
    <label><i class="mdi mdi-checkbox-marked-circle-outline"></i></label> 保存排序</button>
  <a href="./link.php?set=add" class="my-1 btn btn-label btn-primary">
    <label><i class="mdi mdi-plus" aria-hidden="true"></i></label>新增</a>
  <button type="button" class="my-1 btn btn-label btn-success" onclick="on_link()">
    <label><i class="mdi mdi-check" aria-hidden="true"></i></label>启用</button>
  <button type="button" class="my-1 btn btn-label btn-warning" onclick="off_link()">
    <label><i class="mdi mdi-block-helper" aria-hidden="true"></i></label>禁用</button>
  <button type="button" class="my-1 btn btn-label btn-danger" onclick="del_link()">
    <label><i class="mdi mdi-window-close" aria-hidden="true"></i></label>删除</button>
  <button type="button" class="my-1 btn btn-label btn-cyan" onclick="edit_group(mv_group)">
    <label><i class="mdi mdi-account-edit" aria-hidden="true"></i></label>移动</button>
  <?php if (empty($gpwd)): ?>
    <button type="button" class="my-1 btn btn-label btn-pink" onclick="pwd_link(pwd_list)">
      <label><i class="mdi mdi-key-variant" aria-hidden="true"></i></label>加密</button>
  <?php else: ?>
    <button type="button" class="my-1 btn btn-label btn-pink" onclick="pwd_links()">
      <label><i class="mdi mdi-key-variant" aria-hidden="true"></i></label>分组已加密</button>
  <?php endif; ?>
  <a href="./batch_add.php" target="_blank" class="my-1 btn btn-label btn-purple">
    <label><i class="mdi mdi-import" aria-hidden="true"></i></label>批量导入</a>
  <button type="button" class="my-1 btn btn-label btn-dark" onclick="check_group_links()">
    <label><i class="mdi mdi-radar" aria-hidden="true"></i></label>失效检测</button>
</div>
<nav class="mb-3">
  <ul class="pagination">
    <?php foreach ($group_lists as $group_id => $group_name): ?>
      <li class="my-1 page-item<?php if ($page == $group_id) echo ' active'; ?>"><button class="page-link"  onclick="listTable('page=<?php echo $group_id; ?>')"><?php echo $group_name; ?></button></li>
    <?php endforeach; ?>
    <li class="my-1 page-item"> <button id="refreshBtn" class="page-link font-weight-bold text-success" type="button" title="刷新">
        <i class="mdi mdi-refresh"></i>刷新
      </button></li>
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
      $rs = $DB->query("SELECT * FROM `lylme_links` WHERE `group_id` = " . $page . " ORDER BY `link_order` ASC");
      while ($res = $DB->fetch($rs)) {
        $gname = isset($group_lists[$res['group_id']]) ? $group_lists[$res['group_id']] : '';
      ?>
        <tr>
          <td><input type="checkbox" name="link-check" value="<?php echo $res['id']; ?>"></td>
   <td>
  <div class="btn-group btn-group-xs">
    <a class="btn btn-primary sort-goup" data-bs-toggle="tooltip" title="移到顶部">
      <i class="mdi mdi-arrow-collapse-up"></i>
    </a>
    <a class="btn btn-primary sort-up" data-bs-toggle="tooltip" title="上移">
      <i class="mdi mdi-arrow-up"></i>
    </a>
    <a class="btn btn-success sort-down" data-bs-toggle="tooltip" title="下移">
      <i class="mdi mdi-arrow-down"></i>
    </a>
    <a class="btn btn-success sort-godown" data-bs-toggle="tooltip" title="移到底部">
      <i class="mdi mdi-arrow-collapse-down"></i>
    </a>
  </div>
</td>
          <td class="lylme"><?php echo $res['name']; ?></td>
          <td class="link-url" data-url="<?php echo htmlspecialchars($res['url'], ENT_QUOTES); ?>">
            <?php if (!empty($res['link_pwd']) || !empty($gpwd)): ?><span style="color:#f96197"><?php echo $res['url']; ?></span><?php else: ?><?php echo $res['url']; ?><?php endif; ?>
          </td>
          <td><?php echo $gname; ?></td>
          <td>
            <?php if ($res['link_status'] == "0"): ?><span style="color:red; cursor: pointer;" onclick="on_link('<?php echo $res['id']; ?>')">禁用</span><?php else: ?><span onclick="off_link('<?php echo $res['id']; ?>')" style="color:green; cursor: pointer;">启用</span><?php endif; ?>
          </td>
          <td>
             <!-- <a href="./link.php?set=edit&id=<?php echo $res['id']; ?>&page=<?php echo $page ?>" class="btn btn-xs btn-info my-1">编辑</a> -->
            <button onclick="edit_link('<?php echo $res['id']; ?>')" class="btn btn-xs btn-info my-1">编辑</a> 
           
           <button class="btn btn-xs btn-danger" onclick="del_link('<?php echo $res['id']; ?>')">删除</button></td>
        </tr>
      <?php
      }
      ?>
    </tbody>
  </table>
</div>