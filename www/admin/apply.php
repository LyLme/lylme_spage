<?php
/* 
 * @Description: 申请收录
 * @Author: LyLme admin@lylme.com
 * @Date: 2024-01-23 12:25:35
 * @LastEditors: LyLme admin@lylme.com
 * @LastEditTime: 2024-04-13 17:05:21
 * @FilePath: /lylme_spage/admin/apply.php
 * @Copyright (c) 2024 by LyLme, All Rights Reserved. 
 */
$title = '收录管理';
include './head.php';
$applyrows = $DB->num_rows($DB->query("SELECT * FROM `lylme_apply`"));
// 预取分组数据（group_id => group_name 映射，供编辑/列表使用）
$grouplists = array();
$gq = $DB->query("SELECT * FROM `lylme_groups`");
while ($g = $DB->fetch($gq)) {
    $grouplists[$g['group_id']] = $g['group_name'];
}

$set = isset($_GET['set']) ? $_GET['set'] : null;

// 收录设置保存
if ($set == 'conf_submit') {
    saveSetting('apply', intval($_POST['apply']));
    saveSetting('apply_gg', $_POST['apply_gg']);
    echo '<script>$.alert({title:"成功",content:"修改成功！",buttons:{confirm:{text:"确定",btnClass:"btn-primary",action:function(){window.location.href="./apply.php";}}}});</script>';
    exit;
}

// 修改申请信息
if ($set == 'edit_submit') {
    $id = intval($_GET['id']);
    $rows2 = $DB->query("select * from lylme_apply where apply_id='$id' limit 1");
    $rows = $DB->fetch($rows2);
    if (!$rows) {
        echo '<script>lightyear.notify("当前记录不存在！", "danger", 3000);</script>';
        exit;
    }
    $name = $_POST['apply_name'];
    $icon = $_POST['apply_icon'];
    $url = $_POST['apply_url'];
    $group = $_POST['apply_group'];
    if ($name == '') {
        echo '<script>lightyear.notify("保存错误,请确保带星号的都不为空！", "danger", 3000);</script>';
        exit;
    }
    $sql = "UPDATE `lylme_apply` SET `apply_name` = '" . $name . "', `apply_group` = '" . $group . "',`apply_icon` = '" . $icon . "',`apply_url` = '" . $url . "' WHERE `lylme_apply`.`apply_id` = '" . $id . "';";
    if ($DB->query($sql)) {
        echo '<script>$.alert({title:"成功",content:"修改成功！",buttons:{confirm:{text:"确定",btnClass:"btn-primary",action:function(){window.location.href="./apply.php";}}}});</script>';
    } else {
        echo '<script>lightyear.notify("修改失败！", "danger", 3000);</script>';
    }
    exit;
}
?>
<script src="../assets/js/svg.js"></script>
<style>
    td img,
    td svg.icon {
        width: 35px;
        height: 35px;
        max-width: 35px;
    }

    pre {
        line-height: 1 !important;
    }
</style>
<main class="lyear-layout-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
<?php if ($set == 'edit'): ?>
<?php
    $id = intval($_GET['id']);
    $row2 = $DB->query("select * from lylme_apply where apply_id='$id' limit 1");
    $row = $DB->fetch($row2);
    if (!$row) {
        echo '<div class="alert alert-warning">该申请记录不存在！</div><a href="./apply.php">&lt;&lt;返回收录管理列表</a>';
    } else {
?>
    <h4>修改链接信息</h4>
    <form action="./apply.php?set=edit_submit&id=<?php echo $id; ?>" method="POST">
        <div class="form-group">
            <label for="apply_name">*名称:</label>
            <input type="text" id="apply_name" class="form-control" name="apply_name" value="<?php echo htmlspecialchars($row['apply_name'], ENT_QUOTES, 'UTF-8'); ?>" required>
        </div>
        <div class="form-group">
            <label for="apply_url">*链接:</label>
            <input type="text" id="apply_url" class="form-control" name="apply_url" value="<?php echo htmlspecialchars($row['apply_url'], ENT_QUOTES, 'UTF-8'); ?>" required>
        </div>
        <div class="form-group">
            <label for="apply_icon">图标:</label>
            <textarea id="apply_icon" class="form-control" name="apply_icon" rows="3"><?php echo htmlspecialchars($row['apply_icon'], ENT_QUOTES, 'UTF-8'); ?></textarea>
            <small class="help-block">方式1：填写图标的<code>URL</code>地址，如<code>/img/logo.png</code>或<code>http://www.xxx.com/img/logo.png</code><br>
            方式2：粘贴图标的<code>SVG</code>代码，<a href="./help.php?doc=icon" target="_blank">查看教程</a><br>方式3：留空使用默认图标<br><b>注：修改为svg代码后审核列表可能存在显示异常，不会影响首页效果，忽略即可！</b></small>
        </div>
        <div class="form-group">
            <label for="apply_group">*分组:</label>
            <select id="apply_group" class="form-control" name="apply_group">
<?php foreach ($grouplists as $gid => $gname): ?>
                <option value="<?php echo $gid; ?>"<?php if ($gid == $row['apply_group']) echo ' selected="selected"'; ?>><?php echo $gid . ' - ' . $gname; ?></option>
<?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary d-block w-100" value="确定修改">
        </div>
    </form>
    <br/><a href="./apply.php">&lt;&lt;返回收录管理列表</a>
<?php } ?>
<?php elseif ($set == 'conf'): ?>
    <h4>修改收录设置</h4>
    <form action="./apply.php?set=conf_submit" method="POST">
        <div class="form-group">
            <label for="apply_status">申请收录</label>
            <select class="form-control" id="apply_status" name="apply">
                <option value="0"<?php if ($conf['apply'] == 0) echo ' selected="selected"'; ?>>开启-需要审核</option>
                <option value="1"<?php if ($conf['apply'] == 1) echo ' selected="selected"'; ?>>开启-无需审核</option>
                <option value="2"<?php if ($conf['apply'] == 2) echo ' selected="selected"'; ?>>关闭-关闭申请</option>
            </select>
            <small class="help-block">申请收录开关，地址：<code><?php echo siteurl(); ?>/apply</code><br>前往<a href="<?php echo siteurl(); ?>/apply" target="_blank">申请收录</a>提交页</small>
        </div>
        <div class="form-group">
            <label for="apply_gg">收录页公告</label>
            <textarea rows="5" class="form-control" id="apply_gg" name="apply_gg" placeholder="显示在收录页的公告"><?php echo $conf['apply_gg']; ?></textarea>
            <small class="help-block">显示在收录页的公告<code>使用HTML代码编写</code><br>工具：<a href="https://www.lylme.com/html/" target="_blank">在线MD编辑器</a> 编辑后复制html代码粘贴</small>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary d-block w-100" value="保存">
        </div>
    </form>
    <br/><a href="./apply.php">&lt;&lt;返回收录管理列表</a>
<?php else: ?>
                    <div class="alert alert-info">
                        <div class="alert-stat">
                            <div><i class="mdi mdi-bell-ring-outline mdi-alert-icon"></i>收录申请统计： <b><?php echo $applyrows; ?></b> 次&nbsp;|&nbsp;收录申请开关： <b><?php
                            switch ($conf['apply']) {
                                case 0:
                                    echo '开启-需要审核';
                                    break;
                                case 1:
                                    echo '开启-无需审核';
                                    break;
                                case 2:
                                    echo '关闭-关闭申请';
                                    break;
                            }
                            ?></b></div>
                            <a href="./apply.php?set=conf" class="btn btn-primary btn-sm">修改设置</a>
                        </div>
                        <div class="mt-2">申请收录地址：<code><?php echo siteurl(); ?>/apply</code> <a href="<?php echo siteurl(); ?>/apply" target="_blank">访问</a></div>
                        <span class="alert-help">已审核的图标会被隐藏，点击图标可重新加载<br>部分网站图标一直处于加载或无法显示，可能原因：无法访问或跨域问题，建议建将图标本地化</span>
                    </div>
                            <div id="toolbar" class="toolbar-btn-action mb-2">
                                <button id="btn_edit" type="button" class="btn btn-success btn-label" onclick="checked_status(1)">
                                    <label><i class="mdi mdi-check" aria-hidden="true"></i></label>通过</button>
                                <button id="btn_edit" type="button" class="btn btn-warning  btn-label" onclick="checked_status(2)">
                                    <label><i class="mdi mdi-block-helper" aria-hidden="true"></i></label>拒绝</button>
                                <button id="btn_delete" type="button" class="btn btn-danger btn-label" onclick="checked_del()">
                                    <label><i class="mdi mdi-window-close" aria-hidden="true"></i></label>删除</button>


                            </div>
                            <div class="table-responsive" id="applylist">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" class="checkbox-parent" id="check_all" onclick="check_all()"></th>
                                            <th>序号</th>
                                            <th>图标</th>
                                            <th>名称</th>
                                            <th>链接</th>
                                            <th>访问</th>
                                            <th>分组</th>
                                            <th>审核</th>
                                            <th>操作</th>
                                            <th>申请时间</th>
                                        </tr>
                                    </thead>
                                    <tbody>
<?php
                                        $rs = $DB->query("SELECT * FROM `lylme_apply` ORDER BY `lylme_apply`.`apply_time` DESC");
                                        $i = 0;
                                        while ($res = $DB->fetch($rs)) {
                                            $i++;
                                            $apply_status = intval($res['apply_status']);
                                        ?>
                                        <tr>
                                            <td><input type="checkbox" name="link-check" value="<?php echo $res['apply_id']; ?>"></td>
                                            <td><?php if ($apply_status == 0) echo '<font color="#48b0f7"><b>' . $i . '</b></font>'; else echo '<b>' . $i . '</b>'; ?></td>
                                            <td>
<?php if ($apply_status == 0) { ?>
<?php if (empty($res['apply_icon'])) { echo '未提交图标'; }
elseif (preg_match("/^<svg*/", $res['apply_icon'])) { echo $res['apply_icon']; }
else { ?>
                                                <img class="lazy" src="https://cdn.lylme.com/admin/lyear/img/loading.gif" data-original="<?php echo htmlspecialchars($res['apply_icon'], ENT_QUOTES, 'UTF-8'); ?>">
<?php } ?>
<?php } else { ?>
                                                <img class="lazys" title="获取" src="https://cdn.lylme.com/admin/lyear/img/get.png" data-original="<?php echo htmlspecialchars($res['apply_icon'], ENT_QUOTES, 'UTF-8'); ?>">
<?php } ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($res['apply_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($res['apply_url'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><a class="btn btn-purple btn-xs" href="../include/go.php?url=<?php echo htmlspecialchars($res['apply_url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank">访问</a></td>
                                            <td><?php echo isset($grouplists[$res['apply_group']]) ? htmlspecialchars($grouplists[$res['apply_group']], ENT_QUOTES, 'UTF-8') : $res['apply_group']; ?></td>
                                            <td>
<?php if ($apply_status == 2) { echo '<font color="#f96868">已拒绝</font>'; }
elseif ($apply_status == 1) { echo '<font color="#3c763d">已通过</font>'; }
else { ?>
                                                <button class="btn btn-primary btn-xs" onclick="status(<?php echo $res['apply_id']; ?>,1)">通过</button>&nbsp;
                                                <button class="btn btn-warning btn-xs" onclick="status(<?php echo $res['apply_id']; ?>,2)">拒绝</button>
<?php } ?>
                                            </td>
                                            <td>
<?php if ($apply_status == 0) { ?>
                                                <a href="./apply.php?set=edit&id=<?php echo $res['apply_id']; ?>" class="btn btn-info btn-xs">编辑</a>&nbsp;
<?php } ?>
                                                <button class="btn btn-xs btn-danger" onclick="deletes(<?php echo $res['apply_id']; ?>)">删除</button>
                                            </td>
                                            <td><?php echo $res['apply_time']; ?></td>
                                        </tr>
<?php } ?>
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
<?php include './footer.php'; ?>
<script src="/assets/admin/js/jquery.lazyload.min.js" type="application/javascript"></script>
<script src="/assets/admin/js/layer.min.js" type="application/javascript"></script>
<script src="/assets/admin/js/apply.js"></script>