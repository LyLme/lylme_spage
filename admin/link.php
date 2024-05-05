<?php
/* 
 * @Description: 
 * @Author: LyLme admin@lylme.com
 * @Date: 2024-01-23 12:25:35
 * @LastEditors: LyLme admin@lylme.com
 * @LastEditTime: 2024-04-09 02:10:23
 * @FilePath: /lylme_spage/admin/link.php
 * @Copyright (c) 2024 by LyLme, All Rights Reserved. 
 */
$title = '链接管理';
include './head.php';
$grouplists = $DB->query("SELECT * FROM `lylme_groups`");
$pwd_lists = $DB->query("SELECT * FROM `lylme_pwd`");
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
                            echo '<h4>新增链接</h4>
<div class="panel-body">
<form action="./link.php?set=add_submit" method="POST">
<div class="form-group">
<label>*URL链接地址:</label>
<div class="input-group">
<input type="text" class="form-control" name="url" placeholder="链接" value="" required>
<span class="input-group-btn">
  <button class="btn btn-default" onclick="geturl()" type="button">获取</button>
</span>
</div></div>

<div class="form-group">
<label>*网站名称:</label><br>
<input type="text" class="form-control" placeholder="网站名称" name="name" value="" required>
<input type="text" class="coloris form-control" onchange="select_color()" placeholder="链接颜色(留空默认)" name="color" value="" /> 
</div>
<div class="input-group">
<label>链接图标:</label><br>
<textarea type="text" class="form-control" name="icon" placeholder="网站图标"></textarea>
<span class="input-group-btn">
    <input  type="file" id="file" onchange="uploadimg()" accept="image/png, image/jpeg,image/gif,image/x-icon"  style="display: none" /> 
  <button class="btn btn-default" id="uploadImage" onclick="$(&quot;#file&quot;).click();"  type="button">选择</button>
</span>
</div>
<small class="help-block"><b>可选方案：</b><br>1. 填写图标的<code>URL</code>地址，如<code>/img/logo.png</code>或<code>http://www.xxx.com/img/logo.png</code><br>
2. 粘贴图标的<code>SVG</code>代码，<a href="./help.php?doc=icon" target="_blank">查看教程</a><br>3. 留空使用默认图标<br>4. 从本地上传</small>
</div>
<div class="form-group">
<label>*分组:</label><br>
<select class="form-control" name="group_id">';
                            while ($grouplist = $DB->fetch($grouplists)) {
                                if ($grouplist["group_id"] == $row['group_id']) {
                                    $select = 'selected="selected"';
                                } else {
                                    $select = '';
                                }
                                echo '<option  value="' . $grouplist["group_id"] . '">' . $grouplist["group_id"] . ' - ' . $grouplist["group_name"] . '</option>';
                            }
                            echo '</select></div>
<div class="form-group">
<input type="submit" class="btn btn-primary btn-block" value="添加"></form>
</div>
<br/><a href="./link.php"><<返回</a>
</div></div>';
                        } elseif ($set == 'edit') {
                            $id = $_GET['id'];
                            $row2 = $DB->query("select * from lylme_links where id='$id' limit 1");
                            $row = $DB->fetch($row2);
                            preg_match_all('/<font color=[\"|\']+(.*?)[\"|\']>/i', $row['name'], $color);
                            echo '<h4>修改链接信息</h4>
<div class="panel-body">
<form action="./link.php?set=edit_submit&id=' . $id . '" method="POST">
<div class="form-group">
<label>*URL链接地址:</label>
<div class="input-group">
<input type="text" class="form-control" name="url" placeholder="链接" value="' . $row['url'] . '" required>
<span class="input-group-btn">
  <button class="btn btn-default" onclick="geturl()" type="button">获取</button>
</span>
</div></div>
<div class="form-group">
<label>*网站名称:</label><br>
<input type="text" class="form-control" name="name"  id="urlname" value="' . strip_tags($row['name']) . '" required>
<input type="text" class="coloris form-control" onchange="select_color()"  placeholder="链接颜色(留空默认)"  name="color" value="' . $color[1][0] . '" />
</div>
<div class="input-group">
<label>链接图标:</label><br>
<textarea type="text" class="form-control" name="icon" >' . $row['icon'] . '</textarea>
<span class="input-group-btn">
    <input  type="file" id="file" onchange="uploadimg()" accept="image/png, image/jpeg,image/gif,image/x-icon"  style="display: none" /> 
  <button class="btn btn-default" id="uploadImage" onclick="$(&quot;#file&quot;).click();"  type="button">选择</button>
</span>
</div>
<small class="help-block"><b>可选方案：</b><br>1. 填写图标的<code>URL</code>地址，如<code>/img/logo.png</code>或<code>http://www.xxx.com/img/logo.png</code><br>
2. 粘贴图标的<code>SVG</code>代码，<a href="./help.php?doc=icon" target="_blank">查看教程</a><br>2. 留空使用默认图标<br>4. 从本地上传</small>
</div>
<div class="form-group">
<label>*分组:</label><br>
<select class="form-control" name="group_id">';
                            while ($grouplist = $DB->fetch($grouplists)) {
                                if ($grouplist["group_id"] == $row['group_id']) {
                                    $select = 'selected="selected"';
                                } else {
                                    $select = '';
                                }
                                echo '<option  value="' . $grouplist["group_id"] . '" ' . $select . '>' . $grouplist["group_id"] . ' - ' . $grouplist["group_name"] . '</option>';
                            }
                            echo '</select>
</div>
<div class="form-group">
<label>链接加密:</label><br>
<select class="form-control" required name="link_pwd">';
                            $pwd_lists = $DB->query("SELECT * FROM `lylme_pwd`");
                            while ($pwd_list = $DB->fetch($pwd_lists)) {
                                if ($row['link_pwd'] == $pwd_list["pwd_id"]) {
                                    $sel = 'selected="selected"';
                                } else {
                                    $sel = '';
                                }
                                echo '<option  value="' . $pwd_list["pwd_id"] . '" ' . $sel . ' >' . $pwd_list["pwd_id"] . ' - ' . $pwd_list["pwd_name"] . ' | 密码[' . $pwd_list["pwd_key"] . ']</option>';
                            }
                            if (empty($row['link_pwd'])) {
                                $sele = 'selected="selected"';
                            }
                            echo '
<option value="0" ' . $sele . '>0 - 不加密</option></select>
<small class="help-block"><code>注意：对链接所在的分组加密后，单独设置的链接加密将会失效</code><br>
加密后只能通过输入密码访问，使用该功能先配置加密组
<a href="./pwd.php" target="_blank">管理加密组</a></small>
</div>
<div class="form-group">
<input type="submit" class="btn btn-primary btn-block" value="修改"></form>
</div>
<br/><a href="./link.php"><<返回</a>
</div></div>
';
                        } elseif ($set == 'add_submit') {
                            $color = $_POST['color'];
                            $name = $_POST['name'];
                            if (empty($color)) {
                                $name1 = $name;
                            } else {
                                $name1 = '<font color="' . $color . '">' . $name . '</font>';
                            }
                            $url = $_POST['url'];
                            $icon = $_POST['icon'];
                            $group_id = $_POST['group_id'];
                            $link_order = $linksrows + 1;
                            if ($name == null or $url == null) {
                                echo '<script>alert("保存错误,请确保带星号的都不为空！");history.go(-1);</script>';
                            } else {
                                $sql = "INSERT INTO `lylme_links` (`id`, `name`, `group_id`, `url`, `icon`, `link_desc`,`link_order`) VALUES (NULL, '" . $name1 . "', '" . $group_id . "', '" . $url . "', '" . $icon . "', '" . $name . "', '" . $link_order . "');";
                                if ($DB->query($sql)) {
                                    echo '<script>alert("添加链接 ' . $name . ' 成功！");window.location.href="./link.php";</script>';
                                } else {
                                    echo '<script>alert("添加链接失败！");history.go(-1);</script>';
                                }
                            }
                        } elseif ($set == 'edit_submit') {
                            $id = $_GET['id'];
                            $rows2 = $DB->query("select * from lylme_links where id='$id' limit 1");
                            $rows = $DB->fetch($rows2);
                            if (!$rows) {
                                exit('<script>alert("当前记录不存在！");history.go(-1);</script>');
                            }
                            $color = $_POST['color'];
                            $name = $_POST['name'];
                            if (empty($color)) {
                                $name1 = $name;
                            } else {
                                $name1 = '<font color="' . $color . '">' . $name . '</font>';
                            }
                            $url = $_POST['url'];
                            $icon = $_POST['icon'];
                            $link_pwd = $_POST['link_pwd'];
                            $group_id = $_POST['group_id'];
                            if ($name == null or $url == null) {
                                echo '<script>alert("保存错误,请确保带星号的都不为空！");history.go(-1);</script>';
                            } else {
                                $sql = "UPDATE `lylme_links` SET `name` = '" . $name1 . "', `url` = '" . $url . "', `icon` = '" . $icon . "', `group_id` = '" . $group_id . "', `link_pwd` = " . $link_pwd . " WHERE `lylme_links`.`id` = '" . $id . "';";
                                //   exit($sql);
                                if ($DB->query($sql)) {
                                    echo '<script>alert("修改链接 ' . $name . ' 成功！");window.location.href="./link.php";</script>';
                                } else {
                                    echo '<script>alert("修改链接失败！");history.go(-1);</script>';
                                }
                            }
                            // } elseif ($set == 'delete') {
                            //     $id = $_GET['id'];
                            //     $sql = "DELETE FROM lylme_links WHERE id='$id'";
                            //     if ($DB->query($sql)) echo '<script>alert("删除成功！");window.location.href="./link.php";</script>';
                            //     else echo '<script>alert("删除失败！");history.go(-1);</script>';
                        } else {
                            echo '<div id="listTable"></div>
                </div>
            </div>
          </div>
        </div>
      </div>
    </main>
';
                        }
                        include './footer.php';
                        ?>
                        <script type="text/javascript" src="/assets/admin/js/jquery.dragsort-0.5.2.min.js"></script>
                        <link href="/assets/admin/js/jquery-confirm.min.css" type="text/css" rel="stylesheet" />
                        <script src="/assets/admin/js/layer.min.js" type="application/javascript"></script>
                        <script src="/assets/admin/js/jquery-confirm.min.js" type="application/javascript"></script>
                        <!--选色器-->
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
                            select_color();

                            function select_color() {
                                var fontcolor = $('input[name="color"]').val();
                                $('#urlname').css("color", fontcolor);
                            }
                        </script>
                        <!--选色器-->
                        <!--消息提示-->
                        <script src="/assets/admin/js/bootstrap-notify.min.js"></script>
                        <script type="text/javascript" src="/assets/admin/js/lightyear.js"></script>
                        <script type="text/javascript" src="/assets/admin/js/link.js"></script>
                        <script type="text/javascript">
                            //分组移动
                            var mv_group = '<form action="" class="formName">' + '<select class="form-control group_id" required><option value="">请选择分组...</option>' + '<?php while ($grouplist = $DB->fetch($grouplists)) {
                                                                                                                                                                            echo '<option  value="' . $grouplist["group_id"] . '">' . $grouplist["group_id"] . ' - ' . $grouplist["group_name"] . '</option>';
                                                                                                                                                                        } ?>' + '</select>';
                            //链接加密    
                            var pwd_list = '<form action="" class="formName">' + '<select class="form-control pwd_id" required>' + '<?php while ($pwd_list = $DB->fetch($pwd_lists)) {
                                                                                                                                        echo '<option  value="' . $pwd_list["pwd_id"] . '">' . $pwd_list["pwd_id"] . ' - ' . $pwd_list["pwd_name"] . '</option>';
                                                                                                                                    } ?>' + '<option value="0">0 - 取消加密</option></select><br><a href="./pwd.php" target="_blank">管理加密组</a>';
                        </script>