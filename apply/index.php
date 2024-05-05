<?php
include("../include/common.php");
$grouplists = $DB->query("SELECT * FROM `lylme_groups` WHERE `group_pwd` = 0");
if (!empty($url = isset($_GET['url']) ? $_GET['url'] : null)) {
    header('Content-Type:application/json');
    //获取网站信息
    $head = get_head($_GET['url']);
    $head = json_encode($head, JSON_UNESCAPED_UNICODE);  //将合并后的数组转换为json
    exit($head);  //输出json

} elseif (isset($_GET['submit']) == 'post') {
    if (isset($_REQUEST['authcode'])) {
        session_start();
        if (strtolower($_REQUEST['authcode']) == $_SESSION['authcode']) {
            $status = isset($conf["apply"]) ? $conf["apply"] : 0;
            if ($status == 2) {
                exit('{"code": "400", "msg": "网站已关闭收录"}');
            }
            exit(apply($_POST['name'], $_POST['url'], $_POST['icon'], $_POST['group_id'], $status));
        } else {
            exit('{"code": "-6", "msg": "验证码错误"}');
        }
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <title>申请收录 - <?php echo explode("-", $conf['title'])[0];
                    ?></title>
    <link rel="icon" href="<?php echo get_urlpath($conf['logo'], siteurl() . '/apply'); ?>" type="image/ico">
    <link href="../assets/admin/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/admin/css/style.min.css" rel="stylesheet">
    <style>
        #loading {
            position: absolute;
            left: 0;
            top: 0;
            height: 100vh;
            width: 100vw;
            z-index: 100;
            display: none;
            align-items: center;
            justify-content: center;
            color: #bbb;
            font-size: 16px
        }

        #loading>img {
            height: 18px;
            width: 18px
        }

        .lylme-wrapper {
            position: relative
        }

        .lylme-form {
            display: flex !important;
            min-height: 100vh;
            align-items: center !important;
            justify-content: center !important
        }

        .lylme-form:after {
            content: '';
            min-height: inherit;
            font-size: 0
        }

        .lylme-center {
            background: #fff;
            min-width: 29.25rem;
            padding: 30px;
            border-radius: 20px;
            margin: 2.85714em
        }

        .lylme-header {
            margin-bottom: 1.5rem !important
        }

        .lylme-center .has-feedback.feedback-left .form-control-feedback {
            left: 0;
            right: auto;
            width: 38px;
            height: 38px;
            line-height: 38px;
            z-index: 4;
            color: #dcdcdc
        }

        .lylme-center .has-feedback.feedback-left.row .form-control-feedback {
            left: 15px
        }

        .code {
            height: 38px
        }

        .apply_gg {
            margin: 20px 0;
            font-size: 15px;
            line-height: 2
        }

        .home {
            text-decoration: none;
            color: #bbb;
            line-height: 2
        }

        li {
            list-style-type: none
        }

        ol,
        ul {
            padding-left: 10px
        }
    </style>
</head>

<body>
    <div id="loading"><img src="../assets/admin/loading.gif" /> &nbsp;
        正在获取....</div>
    <?php
    if (!empty($background = background())) {
        $background = str_replace('./', '../', $background);
        echo '<div class="row lylme-wrapper" style="background-image:  url(' . $background . ');background-size: cover;">';
    } else {
        echo '<div class="row lylme-wrapper">';
    }
    ?>
    <div class="lylme-form">
        <div class="lylme-center">
            <?php if ($conf["apply"] == 2) {
                exit('<div class="lylme-header text-center"><h2>网站已关闭收录</h2></div>' . $conf['apply_gg'] . '</div>');
            }
            ?>
            <div class="lylme-header text-center">
                <h2>申请收录</h2>
            </div>
            <div class="apply_gg">
                <?php echo $conf['apply_gg'] ?>
            </div>
            <div class="form-group">
                <label>*URL链接地址:</label>
                <div class="input-group">
                    <input type="text" class="form-control" name="url" placeholder="完整链接或域名" value="" onchange="gurl()" required>
                    <span class="input-group-btn">
                        <button class="btn btn-default" onclick="get_url()" type="button">自动获取</button>
                    </span>
                </div>
            </div>
            <div class="form-group has-feedback feedback-left row">
                <div class="col-xs-12">
                    <label>* 选择分组:</label>
                    <select title="分组" class="form-control" name="group_id" required>
                        <option value="">请选择</option>
                        <?php
                        $applygroup = $site->getGroups();
                        while ($grouplist = $DB->fetch($applygroup)) {
                            echo '
	<option value="' . $grouplist["group_id"] . '">' . $grouplist["group_name"] . '</option>';
                        }
                        ?>
                    </select>
                    <span class="mdi mdi-folder form-control-feedback" aria-hidden="true"></span>
                </div>
            </div>
            <div class="form-group has-feedback feedback-left row">
                <div class="col-xs-12">
                    <label>* 网站名称:</label>
                    <input type="text" class="form-control" id="title" name="name" value="" required placeholder="网站名称">
                    <span class="mdi mdi-format-title form-control-feedback" aria-hidden="true"></span>
                    <small class="help-block">填写网站名称</small>
                </div>
            </div>
            <div class="form-group">
                <label>网站图标:</label>
                <div class="input-group">
                    <!-- 用于展示上传文件名的表单 -->
                    <input type="text" id="icon" class="form-control" name="icon" placeholder="填写图标的URL地址">
                    <!-- 点击触发按钮 -->
                    <span class="input-group-btn">
                        <input type="file" id="file" onchange="uploadimg()" accept="image/png, image/jpeg,image/gif,image/x-icon" style="display: none" />
                        <button class="btn btn-default" id="uploadImage" onclick="$('#file').click();" type="button">选择</button>
                    </span>
                </div>
                <img id="review" src="" width="100px" height="100px" class="center-block" style="display: none;" />
                <span class="mdi mdi-emoticon form-control-feedback" aria-hidden="true"></span>
                <small class="help-block">填写图标的<code>URL</code>地址，如：<code>http://www.xxx.com/logo.png</code><br>
                    部分网站无法自动获取，请手动填写</small>
            </div>
            <div class="form-group has-feedback feedback-left row">
                <label>* 验证码:</label>
                <div class="col-xs-8">
                    <input type="text" name="authcode" class="form-control" placeholder="验证码" required>
                    <span class="mdi mdi-check form-control-feedback" aria-hidden="true"></span>
                </div>
                <div class="col-xs-4">
                    <img id="captcha_img" title="验证码" src='../include/validatecode.php' class="pull-right code" onclick="recode()" />
                </div>
            </div>
            <div class="form-group">
                <button class="btn btn-primary btn-block" onclick="submit()">提交</button>
            </div>
            <center>
                <p><a href="../" class="home">返回首页</a></p><?php echo $conf['copyright'] ?>
            </center>
        </div>
    </div>
</body>
<script type="text/javascript" src="../assets/js/jquery.min.js"></script>
<script src="../assets/js/layer.js" type="application/javascript"></script>
<script src="../assets/js/sweetalert.min.js" type="application/javascript"></script>
<script src="./apply.js" type="application/javascript"></script>

</html>