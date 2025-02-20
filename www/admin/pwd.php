<?php
/* 
 * @Description: 
 * @Author: LyLme admin@lylme.com
 * @Date: 2024-01-23 12:25:35
 * @LastEditors: LyLme admin@lylme.com
 * @LastEditTime: 2024-04-09 02:15:34
 * @FilePath: /lylme_spage/admin/pwd.php
 * @Copyright (c) 2024 by LyLme, All Rights Reserved. 
 */
$title = '加密组管理';
include './head.php';
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
                            echo '<h4>新增加密组</h4>	
<div class="panel-body"><form action="./pwd.php?set=add_submit" method="POST">
<div class="form-group">
<label>*加密组名称:</label><br>
<input type="text" class="form-control" name="pwd_name" value="" required>
<small class="help-block">加密组名称，如：<code>会员组</code></small>
</div>
<div class="form-group">
<label>*加密组密码:</label><br>
<input type="text" class="form-control" name="pwd_key" value="" required>
<small class="help-block">加密组的密码(不超过20个字符)<br><code>提示：密码和其他加密组密码相同时，登录时显示同密码的所有加密链接</code></small>
</div>
<div class="form-group">
<label>加密组备注:</label><br>
<input type="text" class="form-control" name="pwd_ps" value="">
<small class="help-block">加密组备注，仅在后台显示(可不填)</small>
</div>
<div class="form-group">
<input type="submit" class="btn btn-primary btn-block" value="添加"></form>
</div>
<br/><a href="./pwd.php"><<返回加密组列表</a>
</div></div>
';
                        } elseif ($set == 'edit') {
                            $id = $_GET['id'];
                            $pg = $DB->fetch($DB->query("SELECT * FROM `lylme_pwd` WHERE `pwd_id` = " . $id));
                            echo '<h4>修改加密组信息</h4>
    <div class="panel-body"><form action="./pwd.php?set=edit_submit&id=' . $id . '" method="POST">
    <div class="form-group">
    <label>*加密组名称:</label><br>
    <input type="text" class="form-control" name="pwd_name" value="' . $pg['pwd_name'] . '" required>
    <small class="help-block">加密组名称，如：<code>会员组</code></small>
    </div>
    <div class="form-group">
    <label>*加密组密码:</label><br>
    <input type="text" class="form-control" name="pwd_key" value="' . $pg['pwd_key'] . '" required>
    <small class="help-block">加密组的密码(不超过20个字符)<br><code>提示：密码和其他加密组密码相同时，登录时显示同密码的所有加密链接</code></small>
    </div>
    <div class="form-group">
    <label>加密组备注:</label><br>
    <input type="text" class="form-control" name="pwd_ps" value="' . $pg['pwd_key'] . '">
    <small class="help-block">加密组备注，仅在后台显示(可不填)</small>
    </div>
    <div class="form-group">
    <input type="submit" class="btn btn-primary btn-block" value="修改"></form>
    </div>
    <br/><a href="./pwd.php"><<返回加密组列表</a>
    </div></div>
    ';
                        } elseif ($set == 'add_submit') {
                            $pwd_name = $_POST['pwd_name'];
                            $pwd_key = $_POST['pwd_key'];
                            $pwd_ps = $_POST['pwd_ps'];
                            if (empty($pwd_name) || empty($pwd_key)) {
                                echo '<script>alert("失败，请确保带星号的项目都不为空！");history.go(-1);</script>';
                            } else {
                                $sql = "INSERT INTO `lylme_pwd` (`pwd_id`, `pwd_name`, `pwd_key`, `pwd_ps`) VALUES (NULL, '" . $pwd_name . "', '" . $pwd_key . "', '" . $pwd_ps . "');";
                                if ($DB->query($sql)) {
                                    echo '<script>window.location.href="./pwd.php";</script>';
                                } else {
                                    echo '<script>alert("添加失败");history.go(-1);</script>';
                                }
                            }
                        } elseif ($set == 'edit_submit') {
                            $id = $_GET['id'];
                            $pwd_name = $_POST['pwd_name'];
                            $pwd_key = $_POST['pwd_key'];
                            $pwd_ps = $_POST['pwd_ps'];
                            if (empty($pwd_name) || empty($pwd_key)) {
                                echo '<script>alert("失败，请确保带星号的项目都不为空！");history.go(-1);</script>';
                            } else {
                                $sql = "UPDATE `lylme_pwd` SET `pwd_name` = '" . $pwd_name . "', `pwd_key` = '" . $pwd_key . "', `pwd_ps` = '" . $pwd_ps . "' WHERE `lylme_pwd`.`pwd_id` = " . $id . ";";
                                if ($DB->query($sql)) {
                                    echo '<script>window.location.href="./pwd.php";</script>';
                                } else {
                                    echo '<script>alert("修改失败' . $sql . '");history.go(-1);</script>';
                                }
                            }
                        } elseif ($set == 'delete') {
                            $id = $_GET['id'];
                            $delsql = "DELETE FROM `lylme_pwd` WHERE `lylme_pwd`.`pwd_id` = " . $id;
                            if ($DB->query($delsql)) {
                                echo '<script>window.location.href="./pwd.php";</script>';
                            } else {
                                echo '<script>alert("删除失败！");history.go(-1);</script>';
                            }
                        } else {
                            echo '<div class="alert alert-info"><h4>链接加密</h4><li>加密后的链接地址在本页面显示为<font color="#f96197">粉色</font>，以便标识</li><li>加密分组后该分组下的链接单独设置的加密将失效，删除分组的加密后即可恢复</li><li><b>加密后链接只能使用密码登录后方可查看</b></li><li>若多个加密组使用相同的密码，登录后会同时显示使用该密码的所有链接</li><li>默认登录地址：<code>' . siteurl() . '/pwd</code>(可自行修改目录名更改)</li><br><a href="./pwd.php?set=add" class="btn btn-primary">新建加密</a></div>';

                        ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>名称</th>
                                            <th>密码</th>
                                            <th>备注</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $pgs = $DB->query("SELECT * FROM `lylme_pwd`");
                                        while ($pg = $DB->fetch($pgs)) {
                                            echo '<tr><td>' . $pg['pwd_name'] . '</td>
        <td>' . $pg['pwd_key'] . '</td><td>' . $pg['pwd_ps'] . '</td><td>&nbsp;<a href="./pwd.php?set=edit&id=' . $pg['pwd_id'] . '" class="btn btn-info btn-xs">编辑</a>&nbsp;<a href="./pwd.php?set=delete&id=' . $pg['pwd_id'] . '" class="btn btn-xs btn-danger" onclick="return confirm(\'是否删除加密组 ' . $pg['pwd_name'] . '\');">删除</a> </td></tr>';
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