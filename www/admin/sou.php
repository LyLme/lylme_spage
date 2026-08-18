<?php
$title = '搜索引擎设置';
include './head.php';
$sousrows = $DB->num_rows($DB->query("SELECT * FROM `lylme_sou`"));

$set = isset($_GET['set']) ? $_GET['set'] : null;

// 删除搜索引擎
if ($set == 'delete') {
    $id = intval($_GET['id']);
    $sql = "DELETE FROM lylme_sou WHERE sou_id='$id'";
    if ($DB->query($sql)) {
       exit('<script>$.alert({title:"成功",content:"删除成功！",buttons:{confirm:{text:"确定",btnClass:"btn-primary",action:function(){window.location.href="./sou.php";}}}});</script>');
    } else {
       exit('<script>$.alert({title:"错误",content:"删除失败！",buttons:{confirm:{text:"确定",btnClass:"btn-primary",action:function(){history.go(-1);}}}});</script>');
    }
    exit;
}
?>
<main class="lyear-layout-content">

    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
<?php if ($set == 'add'): ?>
    <h4>新增搜索接</h4>
    <form id="addSearchForm" action="./ajax_link.php?submit=add_sou" method="POST">
        <div class="form-group">
            <label>*搜索引擎名称: (*必填)</label>
            <input type="text" class="form-control" name="name" value="" required placeholder="如：百度一下">
            <small class="help-block">搜索引擎名称，如<code>百度一下</code>或<code>搜狗搜索</code></small>
        </div>
        <div class="form-group">
            <label>*搜索引擎别名: (*必填)</label>
            <input type="text" class="form-control" name="alias" value="" required placeholder="如：baidu">
            <small class="help-block">注：仅支持字母，不能和其他搜索引擎的别名相同<br>建议填写搜索引擎的拼音或英文，如百度填写<code>baidu</code></small>
        </div>
        <div class="form-group">
            <label>*搜索框提示: (*必填)</label>
            <input type="text" class="form-control" name="hint" value="" required placeholder="如：请输入搜索内容">
        </div>
        <div class="form-group">
            <label>*搜索引擎地址: (*必填)</label>
            <input type="text" class="form-control" name="link" value="" required placeholder="如：https://www.baidu.com/s?word=">
            <small class="help-block">例：百度搜索 <code>https://www.baidu.com/s?word=</code>，<a href="https://doc.lylme.com/spage/#/search-help">查看获取接口教程</a>
            <br>注意：当前仅支持搜索词作为末尾，例如：<code>https://www.baidu.com/s?word=搜索内容</code><br>如果使用GET请求搜索，搜索词不处于末尾，如<code>https://www.baidu.com/s?wd=搜索词&ie=UTF-8</code>可将搜索词参数调换到末尾，如<code>https://www.baidu.com/s?ie=UTF-8&wd=搜索词</code>多个GET参数用以<code>&</code>分隔<br>如果搜索词不在末尾且非GET请求，如<code>https://xxx.com/s/搜索词.html</code>类似情况，可用PHP页面定制搜索接口<a href="https://doc.lylme.com/spage/#/search-help">查看教程</a></small>
        </div>
        <div class="form-group">
            <label>搜索引擎手机端地址: (选填)</label>
            <input type="text" class="form-control" name="waplink" value="" placeholder="一般情况下留空">
            <small class="help-block">例：百度搜索的电脑端和手机端不会自适应，需要手动设置手机端，如<code>https://m.baidu.com/s?word=</code><br>如果你添加的搜索区分手机端和PC端，则需要手动设置。<code>一般情况下留空即可</code></small>
        </div>
        <div class="form-group">
            <label>标题文字颜色: (*必填)</label>
            <input type="text" class="form-control" name="color" value="#696a6d" required>
            <small class="help-block">(*必填) 填写颜色的十六进制码，如： <code>#FF0000</code>(红色)<br>默认值：<code>#696a6d</code></small>
        </div>
        <div class="form-group">
            <label>搜索引擎图标:(*必填)</label>
            <textarea class="form-control" name="icon" placeholder="<svg" required></textarea>
            <small class="help-block">方案1：粘贴图标的<code>SVG</code>代码(推荐) <a href="./help.php?doc=icon" target="_blank">查看教程</a><br>方案2：使用图片地址，需要img标签，如<code>&lt;img src="/assets/img/logo.png" /&gt; </code></small>
        </div>
        <div class="form-group">
            <label class="d-block w-100" for="web_tq_status">启用开关</label>
            <label class="lyear-switch switch-solid switch-primary">
                <input type="checkbox" checked="checked" name="st" value="true">
                <span></span>
            </label>
            <small class="help-block">说明：是否启用该搜索引擎(默认启用) </small>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary d-block w-100" value="添加">
        </div>
    </form>
    <br/><a href="./sou.php">&lt;&lt;返回</a>
<?php elseif ($set == 'edit'): ?>
<?php
    $id = intval($_GET['id']);
    $row2 = $DB->query("select * from lylme_sou where sou_id='$id' limit 1");
    $row = $DB->fetch($row2);
    if (!$row) {
?>
    <div class="alert alert-warning">该搜索引擎不存在！</div>
    <a href="./sou.php">&lt;&lt;返回</a>
<?php } else {
        $sou_icon = $row['sou_icon'];
?>
    <h4>修改搜索引擎</h4>
    <form id="editSearchForm" action="./ajax_link.php?submit=edit_sou&id=<?php echo $id; ?>" method="POST">
        <div class="form-group">
            <label>*搜索引擎名称: (*必填)</label>
            <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($row['sou_name'], ENT_QUOTES, 'UTF-8'); ?>" required placeholder="如：百度一下">
            <small class="help-block">搜索引擎名称，如<code>百度一下</code>或<code>搜狗搜索</code></small>
        </div>
        <div class="form-group">
            <label>*搜索引擎别名: (*必填)</label>
            <input type="text" class="form-control" name="alias" value="<?php echo htmlspecialchars($row['sou_alias'], ENT_QUOTES, 'UTF-8'); ?>" required placeholder="如：baidu">
            <small class="help-block">注：仅支持字母，<code>不能和其他搜索引擎的别名相同</code><br>建议填写搜索引擎的拼音或英文，如百度填写<code>baidu</code></small>
        </div>
        <div class="form-group">
            <label>*搜索框提示: (*必填)</label>
            <input type="text" class="form-control" name="hint" value="<?php echo htmlspecialchars($row['sou_hint'], ENT_QUOTES, 'UTF-8'); ?>" required placeholder="如：请输入搜索内容">
        </div>
        <div class="form-group">
            <label>*搜索引擎地址: (*必填)</label>
            <input type="text" class="form-control" name="link" value="<?php echo htmlspecialchars($row['sou_link'], ENT_QUOTES, 'UTF-8'); ?>" required placeholder="如：https://www.baidu.com/s?word=">
            <small class="help-block">例：百度搜索 <code>https://www.baidu.com/s?word=</code>，<a href="https://doc.lylme.com/spage/#/search-help">查看获取接口教程</a>
            <br>注意：当前仅支持搜索词作为末尾，例如：<code>https://www.baidu.com/s?word=搜索内容</code><br>如果使用GET请求搜索，搜索词不处于末尾，如<code>https://www.baidu.com/s?wd=搜索词&ie=UTF-8</code>可将搜索词参数调换到末尾，如<code>https://www.baidu.com/s?ie=UTF-8&wd=搜索词</code>多个GET参数用以<code>&</code>分隔<br>如果搜索词不在末尾的，如<code>https://xxx.com/s/搜索词.html</code>类似情况，可用PHP定制搜索接口<a href="https://doc.lylme.com/spage/#/search-help">查看教程</a></small>
        </div>
        <div class="form-group">
            <label>搜索引擎手机端地址: (选填)</label>
            <input type="text" class="form-control" name="waplink" value="<?php echo htmlspecialchars($row['sou_waplink'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="一般情况下留空">
            <small class="help-block">注：百度搜索的电脑端和手机端不会自适应，需要手动设置手机端，如<code>https://m.baidu.com/s?word=</code><br>如果你添加的搜索区分手机端和PC端，则需要手动设置。<code>一般情况下留空即可</code></small>
        </div>
        <div class="form-group">
            <label>标题文字颜色: (*必填)</label>
            <input type="text" class="form-control" name="color" value="<?php echo htmlspecialchars($row['sou_color'], ENT_QUOTES, 'UTF-8'); ?>" required>
            <small class="help-block">(*必填) 填写颜色的十六进制码，如： <code>#FF0000</code>(红色)<br>默认值：<code>#696a6d</code></small>
        </div>
        <div class="form-group">
            <label>搜索引擎图标:(*必填)</label>
            <textarea class="form-control" name="icon" placeholder="<svg" required><?php echo htmlspecialchars($sou_icon, ENT_QUOTES, 'UTF-8'); ?></textarea>
            <small class="help-block">方案1：粘贴图标的<code>SVG</code>代码(推荐) <a href="./help.php?doc=icon" target="_blank">查看教程</a><br>方案2：使用图片地址，需要img标签，如<code>&lt;img src="/assets/img/logo.png" /&gt; </code></small>
        </div>
        <div class="form-group">
            <label>排序权重: (*必填)</label>
            <input type="text" class="form-control" name="order" value="<?php echo htmlspecialchars($row['sou_order'], ENT_QUOTES, 'UTF-8'); ?>" required>
            <small class="help-block">(*必填) 数字越小越靠前</small>
        </div>
        <div class="form-group">
            <label class="d-block w-100" for="web_tq_status">启用开关</label>
            <label class="lyear-switch switch-solid switch-primary">
                <input type="checkbox"<?php if ($row['sou_st'] == 1) echo ' checked="checked"'; ?> name="st" value="true">
                <span></span>
            </label>
            <small class="help-block">说明：是否启用该搜索引擎(默认启用) </small>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary d-block w-100" value="修改">
        </div>
    </form>
    <br/><a href="./sou.php">&lt;&lt;返回</a>
<?php } ?>
<?php else: ?>
                    <div class="alert alert-info alert-stat"><div><i class="mdi mdi-magnify mdi-alert-icon"></i>系统共有 <b><?php echo $sousrows; ?></b> 个搜索引擎</div><a href="./sou.php?set=add" class="btn btn-primary btn-sm">新增搜索引擎</a></div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead><tr><th>排序权重</th><th>名称</th><th>别名</th><th>地址</th><th>状态</th><th>操作</th></tr></thead>
                            <tbody>


<?php
                            $rs = $DB->query("SELECT * FROM `lylme_sou` ORDER BY `lylme_sou`.`sou_order` ASC");
                            while ($res = $DB->fetch($rs)) {
                                $esc_name = htmlspecialchars($res['sou_name'], ENT_QUOTES, 'UTF-8');
                                $esc_color = htmlspecialchars($res['sou_color'], ENT_QUOTES, 'UTF-8');
                                $esc_alias = htmlspecialchars($res['sou_alias'], ENT_QUOTES, 'UTF-8');
                                $esc_link = htmlspecialchars($res['sou_link'], ENT_QUOTES, 'UTF-8');
                        ?>
                                <tr>
                                    <td><b><?php echo $res['sou_order']; ?></b></td>
                                    <td><b><font color="<?php echo $esc_color; ?>"><?php echo $esc_name; ?></font></b></td>
                                    <td><?php echo $esc_alias; ?></td>
                                    <td><?php echo $esc_link; ?></td>
                                    <td><?php if ($res['sou_st'] == 1) echo '<span class="label label-success">开启</span>'; else echo '<span class="label label-danger">关闭</span>'; ?></td>
                                    <td><a href="./sou.php?set=edit&id=<?php echo $res['sou_id']; ?>" class="btn btn-info btn-xs">编辑</a>&nbsp;<a href="./sou.php?set=delete&id=<?php echo $res['sou_id']; ?>" class="btn btn-xs btn-danger" onclick="var h=this.href;event.preventDefault();$.confirm({title:'警告',content:'确定删除 <?php echo $esc_name; ?>',type:'red',buttons:{confirm:{text:'删除',btnClass:'btn-danger',action:function(){window.location.href=h;}},cancel:{text:'取消'}}});return false;">删除</a></td>
                                </tr>
<?php } ?>
                            </tbody>
                            </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    </div>

</main>
<?php endif; ?>
<?php include './footer.php'; ?>
<script>
    // 新增/修改搜索引擎 AJAX 提交（addSearchForm / editSearchForm 共用）
    var searchForm = document.getElementById('addSearchForm') || document.getElementById('editSearchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', function(event) {
            event.preventDefault();
            var formData = new FormData(this);
            var xhr = new XMLHttpRequest();
            xhr.open('POST', this.action, true);
            xhr.onreadystatechange = function() {
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
            xhr.send(formData);
        });
    }
</script>