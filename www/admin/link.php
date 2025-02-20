<?php
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
                        <form id="addLinkForm" action="./ajax_link.php?submit=add_link" method="POST">
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
                        <input type="submit" class="btn btn-primary btn-block" value="添加">
                        </form>
                        </div>
                        <br/><a href="./link.php"><<返回</a>
                        </div></div>';
                            echo '<script>
                                document.getElementById("addLinkForm").addEventListener("submit", function(event) {
                                    event.preventDefault(); // 阻止表单默认提交行为
                                    var form = this;
                                    var formData = new FormData(form); // 创建FormData对象来收集表单数据
                                    var xhr = new XMLHttpRequest(); // 创建XMLHttpRequest对象
                                    xhr.open("POST", form.action, true); // 打开一个POST请求
                                    xhr.onreadystatechange = function() {
                                        if (xhr.readyState === 4 && xhr.status === 200) {
                                            alert(xhr.responseText); // 显示服务器响应信息
                                        }
                                    };
                                    xhr.send(formData); // 发送表单数据
                                });
                            </script>';
                        } elseif ($set == 'edit') {
                            $id = $_GET['id'];
                            $row2 = $DB->query("select * from lylme_links where id='$id' limit 1");
                            $row = $DB->fetch($row2);
                            preg_match_all('/<font color=[\"|\']+(.*?)[\"|\']>/i', $row['name'], $color);
                            echo '<h4>修改链接信息</h4>
                        <div class="panel-body">
                        <form id="editLinkForm" action="./ajax_link.php?submit=edit_link&id=' . $id . '" method="POST">
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
                            echo '<script>
                                document.getElementById("editLinkForm").addEventListener("submit", function(event) {
                                    event.preventDefault(); // 阻止表单默认提交行为
                                    var form = this;
                                    var formData = new FormData(form); // 创建FormData对象来收集表单数据
                                    var xhr = new XMLHttpRequest(); // 创建XMLHttpRequest对象
                                    xhr.open("POST", form.action, true); // 打开一个POST请求
                                    xhr.onreadystatechange = function() {
                                        if (xhr.readyState === 4 && xhr.status === 200) {
                                            alert(xhr.responseText); // 显示服务器响应信息
                                        }
                                    };
                                    xhr.send(formData); // 发送表单数据
                                });
                            </script>';
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