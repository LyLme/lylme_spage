<?php
$title = '搜索引擎设置';
include './head.php';
$sousrows = $DB->num_rows($DB->query("SELECT * FROM `lylme_sou`"));
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
                            echo '<h4>新增搜索接</h4>
<div class="panel-body">
<form action="./sou.php?set=add_submit" method="POST">
<div class="form-group">
<label>*搜索引擎名称: (*必填)</label><br>
<input type="text" class="form-control" name="name" value="" required  placeholder="如：百度一下">
<small class="help-block">搜索引擎名称，如<code>百度一下</code>或<code>搜狗搜索</code></small>
</div>
<div class="form-group">
<label>*搜索引擎别名: (*必填)</label><br>
<input type="text" class="form-control" name="alias" value="" required  placeholder="如：baidu">
<small class="help-block">注：仅支持字母，不能和其他搜索引擎的别名相同<br>建议填写搜索引擎的拼音或英文，如百度填写<code>baidu</code></small>
</div>
<div class="form-group">
<label>*搜索框提示: (*必填)</label><br>
<input type="text" class="form-control" name="hint" value="" required  placeholder="如：请输入搜索内容">
</div>
<div class="form-group">
<label>*搜索引擎地址: (*必填)</label><br>
<input type="text" class="form-control" name="link" value="" required placeholder="如：https://www.baidu.com/s?word=">
<small class="help-block">例：百度搜索 <code>https://www.baidu.com/s?word=</code>，<a href="https://blog.lylme.com/archives/lylme_spage.html#sou">查看获取接口教程</a>
<br>注意：当前仅支持搜索词作为末尾，例如：<code>https://www.baidu.com/s?word=搜索内容</code><br>如果使用GET请求搜索，搜索词不处于末尾，如<code>https://www.baidu.com/s?wd=搜索词&ie=UTF-8</code>可将搜索词参数调换到末尾，如<code>https://www.baidu.com/s?ie=UTF-8&wd=搜索词</code>多个GET参数用以<code>&</code>分隔<br>如果搜索词不在末尾且非GET请求，如<code>https://xxx.com/s/搜索词.html</code>类似情况，可用PHP页面定制搜索接口<a href="https://blog.lylme.com/archives/lylme_spage.html#souphp">查看教程</a></small>
</div>
<div class="form-group">
<label>搜索引擎手机端地址: (选填) </label><br>
<input type="text" class="form-control" name="waplink" value="" placeholder="一般情况下留空">
<small class="help-block">例：百度搜索的电脑端和手机端不会自适应，需要手动设置手机端，如<code>https://m.baidu.com/s?word=</code><br>如果你添加的搜索区分手机端和PC端，则需要手动设置。<code>一般情况下留空即可</code></small>
</div>
<div class="form-group">
<label>标题文字颜色: (*必填) </label><br>
<input type="text" class="form-control" name="color" value="#696a6d" required>
<small class="help-block">(*必填) 填写颜色的十六进制码，如： <code>#FF0000</code>(红色)<br>默认值：<code>#696a6d</code></small>
</div>
<div class="form-group">
<label>搜索引擎图标:(*必填) </label><br>
<textarea type="text" class="form-control" name="icon" placeholder="<svg" required></textarea>
<small class="help-block">方案1：粘贴图标的<code>SVG</code>代码(推荐) <a href="./help.php?doc=icon" target="_blank">查看教程</a><br>方案2：使用图片地址，需要img标签，如<code>&lt;img src="/assets/img/logo.png" /&gt; </code></small>
</div>
<div class="form-group">
                      <label class="btn-block" for="web_tq_status">启用开关</label>
                      <label class="lyear-switch switch-solid switch-primary">
                        <input type="checkbox" checked="checked" name="st" value="true">
                        <span></span>
                      </label>
                      <small class="help-block">说明：是否启用该搜索引擎(默认启用) </small>
                    </div>
<div class="form-group">
<input type="submit" class="btn btn-primary btn-block" value="添加"></form>
</div>
<br/><a href="./sou.php"><<返回</a>
</div></div>';
                        } elseif ($set == 'edit') {
                            $id = $_GET['id'];

                            $row2 = $DB->query("select * from lylme_sou where sou_id='$id' limit 1");
                            $row = $DB->fetch($row2);
                            echo '<h4>修改搜索引擎</h4>
<div class="panel-body">
<form action="./sou.php?set=edit_submit&id=' . $id . '" method="POST">
<div class="form-group">
<label>*搜索引擎名称: (*必填)</label><br>
<input type="text" class="form-control" name="name" value="' . $row['sou_name'] . '" required  placeholder="如：百度一下">
<small class="help-block">搜索引擎名称，如<code>百度一下</code>或<code>搜狗搜索</code></small>
</div>
<div class="form-group">
<label>*搜索引擎别名: (*必填)</label><br>
<input type="text" class="form-control" name="alias" value="' . $row['sou_alias'] . '" required  placeholder="如：baidu">
<small class="help-block">注：仅支持字母，<code>不能和其他搜索引擎的别名相同</code><br>建议填写搜索引擎的拼音或英文，如百度填写<code>baidu</code></small>
</div>
<div class="form-group">
<label>*搜索框提示: (*必填)</label><br>
<input type="text" class="form-control" name="hint" value="' . $row['sou_hint'] . '" required  placeholder="如：请输入搜索内容">
</div>
<div class="form-group">
<label>*搜索引擎地址: (*必填)</label><br>
<input type="text" class="form-control" name="link" value="' . $row['sou_link'] . '" required placeholder="如：https://www.baidu.com/s?word=">
<small class="help-block">例：百度搜索 <code>https://www.baidu.com/s?word=</code>，<a href="https://blog.lylme.com/archives/lylme_spage.html#sou">查看获取接口教程</a>
<br>注意：当前仅支持搜索词作为末尾，例如：<code>https://www.baidu.com/s?word=搜索内容</code><br>如果使用GET请求搜索，搜索词不处于末尾，如<code>https://www.baidu.com/s?wd=搜索词&ie=UTF-8</code>可将搜索词参数调换到末尾，如<code>https://www.baidu.com/s?ie=UTF-8&wd=搜索词</code>多个GET参数用以<code>&</code>分隔<br>如果搜索词不在末尾的，如<code>https://xxx.com/s/搜索词.html</code>类似情况，可用PHP定制搜索接口<a href="https://doc.lylme.com/spage/#/search-help">查看教程</a></small>
</div>
<div class="form-group">
<label>搜索引擎手机端地址: (选填) </label><br>
<input type="text" class="form-control" name="waplink" value="' . $row['sou_waplink'] . '" placeholder="一般情况下留空">
<small class="help-block">注：百度搜索的电脑端和手机端不会自适应，需要手动设置手机端，如<code>https://m.baidu.com/s?word=</code><br>如果你添加的搜索区分手机端和PC端，则需要手动设置。<code>一般情况下留空即可</code></small>
</div>
<div class="form-group">
<label>标题文字颜色: (*必填) </label><br>
<input type="text" class="form-control" name="color" value="' . $row['sou_color'] . '" required>
<small class="help-block">(*必填) 填写颜色的十六进制码，如： <code>#FF0000</code>(红色)<br>默认值：<code>#696a6d</code></small>
</div>
<div class="form-group">
<label>搜索引擎图标:(*必填) </label><br>
<textarea type="text" class="form-control" name="icon" placeholder="<svg" required>' . $row['sou_icon'] . '</textarea>
<small class="help-block">方案1：粘贴图标的<code>SVG</code>代码(推荐) <a href="./help.php?doc=icon" target="_blank">查看教程</a><br>方案2：使用图片地址，需要img标签，如<code>&lt;img src="/assets/img/logo.png" /&gt; </code></small>
</div>
<div class="form-group">
<label>排序权重: (*必填) </label><br>
<input type="text" class="form-control" name="order" value="' . $row['sou_order'] . '" required>
<small class="help-block">(*必填) 数字越小越靠前</small>
</div>
<div class="form-group">
                      <label class="btn-block" for="web_tq_status">启用开关</label>
                      <label class="lyear-switch switch-solid switch-primary">
                        <input type="checkbox"';
                            if ($row['sou_st'] == 1) {
                                echo 'checked="checked"';
                            }
                            echo 'name="st" value="true">
                        <span></span>
                      </label>
                      <small class="help-block">说明：是否启用该搜索引擎(默认启用) </small>
                    </div>
<div class="form-group">
<input type="submit" class="btn btn-primary btn-block" value="修改"></form>
</div>
<br/><a href="./sou.php"><<返回</a>
</div></div>';
                        } elseif ($set == 'add_submit') {
                            $name = $_POST['name'];
                            $alias = $_POST['alias'];
                            $hint = $_POST['hint'];
                            $link = $_POST['link'];
                            $waplink = $_POST['waplink'];
                            $color = $_POST['color'];
                            $icon = $_POST['icon'];
                            if ($_POST['st'] == true) {
                                $st = 1;
                            } else {
                                $st = 0;
                            }
                            $sou_order = $sousrows + 1;

                            if (empty($name) && empty($alias) && empty($hint) && empty($link) && empty($color) && empty($icon)) {
                                echo '<script>alert("保存错误,请确保带星号的都不为空！");history.go(-1);</script>';
                            } else {

                                $sql = "INSERT INTO `lylme_sou` (`sou_id`, `sou_alias`, `sou_name`, `sou_hint`, `sou_color`, `sou_link`, `sou_waplink`, `sou_icon`, `sou_st`, `sou_order`) VALUES
(NULL, '" . $alias . "', '" . $name . "', '" . $hint . "', '" . $color . "', '" . $link . "', '" . $waplink . "', '" . $icon . "', '" . $st . "', '" . $sou_order . "');
";

                                if ($DB->query($sql)) {
                                    echo '<script>alert("添加搜索引擎 ' . $name . ' 成功！");window.location.href="./sou.php";</script>';
                                } else {
                                    echo '<script>alert("添加搜索引擎失败！");history.go(-1);</script>';
                                }
                            }
                        } elseif ($set == 'edit_submit') {
                            $id = $_GET['id'];
                            $rows2 = $DB->query("select * from lylme_sou where sou_id='$id' limit 1");
                            $rows = $DB->fetch($rows2);
                            if (!$rows) {
                                echo '<script>alert("当前记录不存在！");history.go(-1);</script>';
                            }
                            $name = $_POST['name'];
                            $alias = $_POST['alias'];
                            $hint = $_POST['hint'];
                            $link = $_POST['link'];
                            $waplink = $_POST['waplink'];
                            $color = $_POST['color'];
                            $icon = $_POST['icon'];
                            $order = $_POST['order'];
                            if ($_POST['st'] == true) {
                                $st = 1;
                            } else {
                                $st = 0;
                            }

                            if (empty($name) && empty($alias) && empty($hint) && empty($link) && empty($color) && empty($icon) && empty($order)) {
                                echo '<script>alert("保存错误,请确保带星号的都不为空！");history.go(-1);</script>';
                            } else {

                                $sql = "UPDATE `lylme_sou` SET `sou_alias` = '" . $alias . "', `sou_name` = '" . $name . "', `sou_hint` = '" . $hint . "', `sou_color` = '" . $color . "', `sou_link` = '" . $link . "', `sou_waplink` = '" . $waplink . "', `sou_icon` = '" . $icon . "', `sou_st` = '" . $st . "', `sou_order` = '" . $order . "' WHERE `lylme_sou`.`sou_id` = " . $id . ";";


                                if ($DB->query($sql)) {
                                    echo '<script>alert("修改搜索引擎 ' . $name . ' 成功！");window.location.href="./sou.php";</script>';
                                } else {
                                    echo '<script>alert("修改失败！");history.go(-1);</script>';
                                }
                            }
                        } elseif ($set == 'delete') {
                            $id = $_GET['id'];
                            $sql = "DELETE FROM lylme_sou WHERE sou_id='$id'";
                            if ($DB->query($sql)) {
                                echo '<script>alert("删除成功！");window.location.href="./sou.php";</script>';
                            } else {
                                echo '<script>alert("删除失败！");history.go(-1);</script>';
                            }
                        } else {
                            echo '<div class="alert alert-info">系统共有 <b>' . $sousrows . '</b> 个搜索引擎<br/><a href="./sou.php?set=add" class="btn btn-primary">新增搜索引擎</a></div> <div class="table-responsive">
        <table class="table table-striped">
          <thead><tr><th>排序权重</th><th>名称</th><th>别名</th><th>地址</th><th>状态</th><th>操作</th></tr></thead>
          <tbody>';


                            $rs = $DB->query("SELECT * FROM `lylme_sou` ORDER BY `lylme_sou`.`sou_order` ASC");
                            while ($res = $DB->fetch($rs)) {
                                echo '<tr><td><b>' . $res['sou_order'] . '</b></td><td><b><font color="' . $res['sou_color'] . '">' . $res['sou_name'] . '</font></b></td><td>' . $res['sou_alias'] . '</td><td>' . $res['sou_link'] . '</td><td>';

                                if ($res['sou_st'] == 1) {
                                    echo '<span class="label label-success">开启</span>';
                                } else {
                                    echo '<span class="label label-danger">关闭</span>';
                                }

                                echo '</td><td><a href="./sou.php?set=edit&id=' . $res['sou_id'] . '" class="btn btn-info btn-xs">编辑</a>&nbsp;<a href="./sou.php?set=delete&id=' . $res['sou_id'] . '" class="btn btn-xs btn-danger" onclick="return confirm(\'确定删除 ' . $res['sou_name'] . '\');">删除</a></td></tr>';
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