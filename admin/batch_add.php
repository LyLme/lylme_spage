<?php
/**
 * 链接批量导入 / 书签快捷添加
 *
 * 两种模式（通过 URL 参数自动区分）：
 *  1. 快捷添加模式：浏览器书签访问（带 from=bm 及 url/title 参数），弹出窗口单个添加链接
 *  2. 批量导入模式：直接访问本页面，支持批量粘贴链接导入，并提供书签代码复制引导（仅 PC 端）
 *
 * URL 参数：from=bm / url / title / icon / group_id
 */
include_once("../include/common.php");
include_once("../include/member.php");

if (!isset($islogin) || $islogin !== 1) {
    header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="2;url=./login.php">
    <title>提示</title>
    <style>
        body { text-align:center; margin-top:100px; font-size:16px; }
    </style>
</head>
<body>
    请先登录，正在跳转…
</body>
</html>
<?php
    exit;
}

$url     = isset($_GET['url'])     ? trim($_GET['url'])     : '';
$title   = isset($_GET['title'])   ? trim($_GET['title'])   : '';
$icon    = isset($_GET['icon'])    ? trim($_GET['icon'])    : '';
$groupid = isset($_GET['group_id']) ? intval($_GET['group_id']) : 0;

// 模式判断：带 url 参数或 from=bm 视为书签快捷添加，否则为批量导入
$mode = 'batch';
if ((isset($_GET['from']) && $_GET['from'] === 'bm') || $url !== '') {
    $mode = 'quick';
}

// 读取全部分组
$groups = array();
$grouplists = $DB->query("SELECT * FROM `lylme_groups` ORDER BY `group_order` ASC, `group_id` ASC");
while ($g = $DB->fetch($grouplists)) {
    $groups[] = $g;
}
if (empty($groupid) && count($groups) > 0) {
    $groupid = intval($groups[0]['group_id']);
}

// 自动生成书签代码（基于当前站点地址与后台目录，复制即用）
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$script_dir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
$bookmark_base = $scheme . '://' . $_SERVER['HTTP_HOST'] . $script_dir . '/batch_add.php';
$bookmark_code = "javascript:(function(){var u=location.href,t=document.title;void(open('" . $bookmark_base . "?from=bm&url='+encodeURIComponent(u)+'&title='+encodeURIComponent(t),'lylme_quick_add','toolbar=yes,location=yes,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=no,copyhistory=yes,left=200,top=200,width=560,height=780'));})();";
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $mode === 'quick' ? '导入导航页 - ' : '批量导入链接 - '; echo $conf['title'];?></title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:"Microsoft YaHei","PingFang SC","Helvetica Neue",Arial,sans-serif;background:#f0f2f5;padding:18px}
.card{max-width:800px;margin:0 auto;background:#fff;border-radius:10px;box-shadow:0 4px 20px rgba(0,0,0,.08);overflow:hidden}
.card-header{background:linear-gradient(135deg, #17b7a7, #33cabb);color:#fff;padding:16px 20px}
.card-header h3{font-size:16px;font-weight:600}
.card-header p{font-size:12px;opacity:.85;margin-top:4px}
.card-body{padding:20px}
.form-group{margin-bottom:14px}
label{display:block;font-size:13px;color:#333;margin-bottom:6px}
label .req{color:#e74c3c;margin-left:2px}
input[type=text],input[type=url],select,textarea{width:100%;padding:9px 12px;border:1px solid #dcdfe6;border-radius:6px;font-size:13px;outline:none;transition:border-color .2s;background:#fff;color:#333}
input:focus,select:focus,textarea:focus{border-color:#33cabb}
textarea{resize:vertical}
.input-group{display:flex;gap:8px}
.input-group input,.input-group textarea{flex:1}
.btn-sm{flex-shrink:0;padding:0 14px;border:1px solid #dcdfe6;border-radius:6px;background:#f5f7fa;color:#555;font-size:13px;cursor:pointer}
.btn-sm:hover{color:#33cabb;border-color:#33cabb}
.color-row{display:flex;align-items:center;gap:10px}
input[type=color]{width:44px;height:34px;border:1px solid #dcdfe6;border-radius:6px;padding:2px;background:#fff;cursor:pointer}
.color-row input[type=text]{flex:1}
.btn-row{display:flex;gap:10px;margin-top:20px}
.btn{flex:1;padding:10px;border:none;border-radius:6px;font-size:14px;cursor:pointer}
.btn-primary{background:linear-gradient(135deg, #17b7a7, #33cabb);color:#fff}
.btn-primary:hover{opacity:.92}
.btn-primary:disabled{opacity:.6;cursor:not-allowed}
.btn-default{background:#fff;color:#666;border:1px solid #dcdfe6}
.btn-default:hover{color:#33cabb;border-color:#33cabb}
.hint{font-size:12px;color:#999;margin-top:5px}
.tip{display:none;padding:9px 12px;border-radius:6px;font-size:13px;margin-bottom:14px}
.tip.show{display:block}
.tip.loading{background:#ecf5ff;color:#3370ff}
.tip.error{background:#fef0f0;color:#e74c3c}
.tip.success{background:#f0f9eb;color:#67c23a}
.section-title{font-size:14px;font-weight:600;color:#333;margin:0 0 10px;padding-left:10px;border-left:3px solid #33cabb}
.bookmark-box{background:#fafbfc;border:1px solid #eef0f3;border-radius:8px;padding:14px;margin-bottom:18px}
.steps{margin:10px 0 0;padding-left:18px;font-size:13px;color:#555;line-height:1.9}
.lylme-bookmark-btn{display:inline-block;background:linear-gradient(135deg, #17b7a7, #33cabb);color:#fff !important;text-decoration:none;padding:12px 22px;border-radius:6px;font-size:0;cursor:move;user-select:none;margin-top:6px;text-align: center;width: 100%;}
.lylme-bookmark-btn::after{content:"拖拽到收藏夹收藏或点击复制";font-size:15px}
.lylme-bookmark-btn:hover{background:#17a98c}
.lylme-bookmark-tip{margin-top:12px;font-size:13px;color:#1abc9c;min-height:20px}
.checkbox-line{display:flex;align-items:center;gap:8px;font-size:13px;color:#333}
.checkbox-line input{width:auto}
.progress{height:8px;background:#eef0f3;border-radius:4px;overflow:hidden;margin-top:12px}
.progress-bar{height:100%;width:0;background:linear-gradient(135deg, #17b7a7, #33cabb);transition:width .2s}
#progressText{font-size:12px;color:#666;margin-top:6px}
#resultList{margin-top:10px;max-height:500px;overflow:auto;font-size:12px;line-height:1.9;padding-right:4px}
.res-item{border:1px solid #eef0f3;border-radius:6px;padding:8px 10px;margin-bottom:8px;background:#fcfcfd}
.res-head{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.res-status{flex-shrink:0;font-weight:600}
.res-status.ok{color:#67c23a}
.res-status.skip{color:#e6a23c}
.res-status.fail{color:#e74c3c}
.res-name{font-weight:600;color:#333;max-width:45%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.res-url{color:#999;flex:1;min-width:80px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.res-edit{flex-shrink:0;padding:2px 12px;border:1px solid #33cabb;border-radius:4px;background:#fff;color:#33cabb;font-size:12px;cursor:pointer}
.res-edit:hover{background:#33cabb;color:#fff}
.res-fail-msg{color:#e74c3c;margin-top:4px;word-break:break-all}
.res-skip-msg{color:#e6a23c;margin-top:4px;word-break:break-all}
.res-edit-form{display:none;margin-top:8px;padding-top:8px;border-top:1px dashed #eef0f3}
.res-edit-form.show{display:block}
.edit-row{display:flex;align-items:center;gap:8px;margin-bottom:6px}
.edit-row label{width:64px;flex-shrink:0;margin:0;font-size:12px;color:#666;text-align:right}
.edit-row input,.edit-row select{flex:1;padding:6px 10px;font-size:12px}
.edit-actions{text-align:right;margin-top:6px}
.edit-actions .btn-sm{padding:5px 18px;background:#33cabb;color:#fff;border:none}
.edit-actions .btn-sm:hover{opacity:.9}
.divider{height:1px;background:#eef0f3;margin:20px 0}
</style>
</head>
<body>
<div class="card">
    <div class="card-header">
        <h3><?php echo $mode === 'quick' ? '六零导航页 - 导入导航页' : '六零导航页 - 批量导入链接'; ?></h3>
        <p><?php echo $mode === 'quick' ? '自动抓取标题与图标，可再次编辑保存' : '支持批量粘贴链接导入，也可通过书签代码随时快捷添加'; ?></p>
    </div>
    <div class="card-body">
        <div class="tip" id="tip"></div>

<?php if ($mode === 'quick'): ?>
        <!-- 快捷添加模式（书签访问） -->
        <form id="quickAddForm" action="./ajax_link.php?submit=add_link" method="POST" autocomplete="off">
            <div class="form-group">
                <label>链接地址<span class="req">*</span></label>
                <div class="input-group">
                    <input type="url" id="urlInput" name="url" value="<?php echo htmlspecialchars($url, ENT_QUOTES); ?>" placeholder="https://example.com" required>
                    <button type="button" class="btn-sm" onclick="fetchInfo()">获取信息</button>
                </div>
            </div>
            <div class="form-group">
                <label>网站名称<span class="req">*</span></label>
                <input type="text" id="nameInput" name="name" value="<?php echo htmlspecialchars($title, ENT_QUOTES); ?>" placeholder="网站名称" required>
            </div>
            <div class="form-group">
                <label>选择分组<span class="req">*</span></label>
                <select name="group_id" id="groupInput" required>
                    <?php foreach ($groups as $g): ?>
                    <option value="<?php echo intval($g['group_id']); ?>" <?php echo intval($g['group_id']) === $groupid ? 'selected' : ''; ?>><?php echo htmlspecialchars($g['group_name'], ENT_QUOTES); ?></option>
                    <?php endforeach; ?>
                    <?php if (count($groups) === 0): ?><option value="0" disabled>暂无分组，请先在后台创建分组</option><?php endif; ?>
                </select>
                <div class="hint">链接将添加到所选分组中</div>
            </div>
            <div class="form-group">
                <label>图标地址</label>
                <input type="text" id="iconInput" name="icon" value="<?php echo htmlspecialchars($icon, ENT_QUOTES); ?>" placeholder="留空则自动获取网站图标">
            </div>
            <div class="form-group">
                <label>名称颜色</label>
                <div class="color-row">
                    <input type="color" id="colorInput" value="#000000" onchange="document.getElementById('colorText').value=this.value">
                    <input type="text" id="colorText" name="color" value="" placeholder="留空为默认颜色" onchange="document.getElementById('colorInput').value=this.value||'#000000'">
                </div>
            </div>
            <div class="form-group">
                <label>网站描述</label>
                <textarea id="descInput" name="link_desc" placeholder="网站简介（可选）"></textarea>
            </div>
            <div class="form-group">
                <label>网站关键词</label>
                <input type="text" id="kwInput" name="link_keywords" placeholder="多个关键词用逗号分隔（可选）">
            </div>
            <div class="btn-row">
                <button type="button" class="btn btn-default" onclick="window.close()">取消</button>
                <button type="submit" class="btn btn-primary" id="submitBtn">添加链接</button>
            </div>
        </form>
<?php else: ?>
        <!-- 批量导入模式（直接访问） -->
        <div id="bookmarkGuide">
            <div class="section-title">方式一：浏览器书签，一键添加当前网页</div>
               <textarea readonly id="bookmarkCode" rows="4" style="margin-top:10px;font-size:12px;color:#555;background:#fff"><?php echo htmlspecialchars($bookmark_code, ENT_QUOTES); ?></textarea>
                       <p class="hint">拖拽下方按钮到浏览器书签栏，或点击按钮复制书签代码后手动新建书签粘贴到网址栏。</p>
            <a id="lylmeBookmarkBtn" class="lylme-bookmark-btn" href="<?php echo htmlspecialchars($bookmark_code, ENT_QUOTES); ?>" title="导入导航页">导入导航页</a>
            <div id="lylmeBookmarkTip" class="lylme-bookmark-tip"></div>
         
            <ol class="steps">
                <li>拖拽上方按钮到浏览器书签栏，收藏名称自动为"导入导航页"。</li>
                <li>若手动添加：在浏览器收藏夹新建书签，名称填"导入导航页"。</li>
                <li>将代码粘贴到书签"网址/地址"栏并保存。</li>
                <li>打开任意网页，点击该书签即可弹出快捷添加窗口。</li>
            </ol>
        </div>

        <div class="divider"></div>

        <div class="section-title">方式二：批量粘贴链接导入</div>
        <div class="form-group" style="margin-top:10px">
            <label>导入到分组<span class="req">*</span></label>
            <select id="batchGroup">
                <?php foreach ($groups as $g): ?>
                <option value="<?php echo intval($g['group_id']); ?>" <?php echo intval($g['group_id']) === $groupid ? 'selected' : ''; ?>><?php echo htmlspecialchars($g['group_name'], ENT_QUOTES); ?></option>
                <?php endforeach; ?>
                <?php if (count($groups) === 0): ?><option value="0" disabled>暂无分组，请先在后台创建分组</option><?php endif; ?>
            </select>
        </div>
        <div class="form-group">
            <label>链接列表<span class="req">*</span></label>
            <textarea id="batchUrls" rows="8" placeholder="每行一个链接，例如：&#10;https://example.com&#10;https://lylme.com&#10;支持逗号、分号分隔"></textarea>
            <div class="hint">支持 http(s) 开头或省略协议；重复链接自动去重</div>
        </div>
        <div class="form-group">
            <label class="checkbox-line"><input type="checkbox" id="autoFetch" checked> 自动抓取每个链接的标题与图标（链接数量较多时速度较慢）</label>
        </div>
        <div class="form-group">
            <label>图标处理方式</label>
            <select id="iconMode">
                <option value="save" selected>抓取图标并保存到服务器（推荐）</option>
                <option value="link">只抓取图标链接（不保存到服务器）</option>
                <option value="none">不抓取图标</option>
            </select>
            <div class="hint">仅在勾选"自动抓取"时生效；保存到服务器会将图标下载至网站 files/download 目录，避免外链失效</div>
        </div>
        <div class="btn-row">
            <button type="button" class="btn btn-primary" id="batchBtn" onclick="batchImport()">开始批量导入</button>
        </div>
        <div class="progress"><div class="progress-bar" id="progressBar"></div></div>
        <div id="progressText"></div>
        <div class="hint">每条抓取完成即入库，可随时点"编辑"修改名称、图标等，其他任务并行继续，互不影响；已存在的链接自动跳过</div>
        <div id="resultList"></div>
<?php endif; ?>
    </div>
</div>
<script>
var tip = document.getElementById('tip');
function showTip(msg, type) {
    tip.className = 'tip show ' + (type || 'loading');
    tip.textContent = msg;
}
function hideTip() {
    tip.className = 'tip';
}
function hostFromUrl(u) {
    try { return new URL(u).hostname; } catch (e) {
        var m = u.match(/^https?:\/\/([^\/]+)/);
        return m ? m[1] : u;
    }
}
// 按字符数截断文本（兼容中文/emoji），超出部分用"..."替代，防止超出数据库字段长度
function truncateText(s, max) {
    if (s == null) return '';
    s = String(s);
    var chars = Array.from(s);
    if (chars.length <= max) return s;
    return chars.slice(0, Math.max(0, max - 3)).join('') + '...';
}
function getXhr(url, timeout) {
    return new Promise(function (resolve) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', url, true);
        xhr.timeout = timeout || 10000;
        xhr.onload = function () {
            try { resolve(JSON.parse(xhr.responseText)); } catch (e) { resolve(null); }
        };
        xhr.onerror = xhr.ontimeout = function () { resolve(null); };
        xhr.send();
    });
}
function postForm(url, fd) {
    return fetch(url, { method: 'POST', body: fd, credentials: 'same-origin' }).then(function (r) { return r.text(); });
}
// 将图标下载保存到服务器本地（参考 apply/apply.js 的 downloadimg），成功返回本地地址，失败返回空字符串
function saveIcon(iconUrl, referer) {
    return new Promise(function (resolve) {
        var fd = new FormData();
        fd.append('url', iconUrl);
        fd.append('referer', referer);
        postForm('../include/file.php', fd).then(function (txt) {
            try {
                var d = JSON.parse(txt);
                resolve(d && d.code == '200' && d.url ? d.url : '');
            } catch (e) { resolve(''); }
        }).catch(function () { resolve(''); });
    });
}

<?php if ($mode === 'quick'): ?>
// ===== 快捷添加模式 =====
var nameInput = document.getElementById('nameInput');
var iconInput = document.getElementById('iconInput');
var descInput = document.getElementById('descInput');
var kwInput = document.getElementById('kwInput');

function fetchInfo() {
    var url = document.getElementById('urlInput').value.trim();
    if (!url) { showTip('请先填写链接地址', 'error'); return; }
    if (!/^https?:\/\//i.test(url)) { url = 'http://' + url; document.getElementById('urlInput').value = url; }
    showTip('正在抓取网站信息...', 'loading');
    getXhr('ajax_link.php?submit=geturl&url=' + encodeURIComponent(url), 15000).then(function (d) {
        if (!d) { showTip('获取失败，目标网站无法访问或防火墙限制，请手动填写', 'error'); return; }
        if (d.title && !nameInput.value) nameInput.value = truncateText(d.title, 255);
        if (d.description && !descInput.value) descInput.value = truncateText(d.description, 255);
        if (d.keywords && !kwInput.value) kwInput.value = truncateText(d.keywords, 512);
        if (d.icon) {
            if (!iconInput.value) {
                showTip('获取成功，正在将图标保存到服务器...', 'loading');
                saveIcon(d.icon, url).then(function (local) {
                    iconInput.value = local || '';
                    showTip(local ? '获取成功，图标已保存到服务器' : '未获取到图标', local ? 'success' : 'error');
                    setTimeout(hideTip, 1500);
                });
            } else {
                showTip('获取成功', 'success');
                setTimeout(hideTip, 1500);
            }
        } else {
            showTip('获取成功', 'success');
            setTimeout(hideTip, 1500);
        }
    });
}

document.getElementById('quickAddForm').addEventListener('submit', function (e) {
    e.preventDefault();
    var btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.textContent = '提交中...';
    postForm(this.action, new FormData(this)).then(function (txt) {
        if (txt.indexOf('成功') > -1) {
            alert(txt);
            window.close();
        } else {
            showTip(txt || '添加失败，请重试', 'error');
            btn.disabled = false;
            btn.textContent = '添加链接';
        }
    }).catch(function () {
        showTip('提交失败，请检查网络连接', 'error');
        btn.disabled = false;
        btn.textContent = '添加链接';
    });
});

if (<?php echo json_encode($url); ?>) { fetchInfo(); }
<?php else: ?>
// ===== 批量导入模式 =====
var GROUPS = <?php echo json_encode($groups); ?>;
var CONCURRENCY = 3; // 并发抓取数量
var isMobile = /Android|iPhone|iPad|iPod|Mobile|Windows Phone/i.test(navigator.userAgent);
if (isMobile) { document.getElementById('bookmarkGuide').style.display = 'none'; }

var bmBtn = document.getElementById('lylmeBookmarkBtn');
var bmTip = document.getElementById('lylmeBookmarkTip');
function showBmTip(msg) {
    bmTip.textContent = msg;
    setTimeout(function () { bmTip.textContent = ''; }, 2000);
}
function copyText(text) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(function () {
            showBmTip('书签代码已复制，请到收藏夹中新建书签并粘贴');
        }).catch(function () { fallbackCopy(text); });
    } else {
        fallbackCopy(text);
    }
}
function fallbackCopy(text) {
    var ta = document.createElement('textarea');
    ta.value = text;
    ta.style.position = 'fixed';
    ta.style.left = '-9999px';
    document.body.appendChild(ta);
    ta.select();
    try {
        document.execCommand('copy');
        showBmTip('书签代码已复制，请到收藏夹中新建书签并粘贴');
    } catch (e) {
        showBmTip('复制失败，请手动复制书签代码');
    }
    document.body.removeChild(ta);
}
function copyBookmark() {
    var ta = document.getElementById('bookmarkCode');
    ta.select();
    ta.setSelectionRange(0, 99999);
    var ok = false;
    try { ok = document.execCommand('copy'); } catch (e) {}
    if (ok) {
        showBmTip('书签代码已复制');
    } else if (navigator.clipboard) {
        navigator.clipboard.writeText(ta.value).then(function () {
            showBmTip('书签代码已复制');
        }, function () {
            showBmTip('复制失败，请手动全选复制');
        });
    } else {
        showBmTip('复制失败，请手动全选复制');
    }
}
bmBtn.addEventListener('click', function (e) {
    e.preventDefault();
    copyText(bmBtn.getAttribute('href'));
});

function updateProgress(done, total, success, fail, skip) {
    var pct = total ? Math.round(done / total * 100) : 0;
    document.getElementById('progressBar').style.width = pct + '%';
    var t = '进度 ' + done + '/' + total + '，成功 ' + success + '，失败 ' + fail;
    if (skip) t += '，跳过 ' + skip;
    document.getElementById('progressText').textContent = t;
}

function esc(s) {
    return String(s == null ? '' : s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function groupOptions(selected) {
    var html = '';
    (GROUPS || []).forEach(function (g) {
        html += '<option value="' + g.group_id + '"' + (String(g.group_id) === String(selected) ? ' selected' : '') + '>' + esc(g.group_name) + '</option>';
    });
    return html;
}

function appendResult(u, name, icon, desc, kw, groupId, id, ok, skip, msg) {
    var list = document.getElementById('resultList');
    var div = document.createElement('div');
    div.className = 'res-item';
    if (ok) div.setAttribute('data-id', id);
    div.setAttribute('data-url', u);
    var stCls = ok ? 'ok' : (skip ? 'skip' : 'fail');
    var stTxt = ok ? 'OK' : (skip ? 'SKIP' : 'FAIL');
    var html = '<div class="res-head">' +
        '<span class="res-status ' + stCls + '">' + stTxt + '</span>' +
        '<span class="res-name" title="' + esc(msg) + '">' + esc(name) + '</span>' +
        '<span class="res-url">' + esc(u) + '</span>';
    if (ok) {
        html += '<button type="button" class="res-edit" onclick="toggleEdit(this)">编辑</button>';
    }
    html += '</div>';
    if (ok) {
        html += '<div class="res-edit-form">' +
            '<div class="edit-row"><label>名称</label><input type="text" class="f-name" value="' + esc(name) + '"></div>' +
            '<div class="edit-row"><label>图标地址</label><input type="text" class="f-icon" value="' + esc(icon) + '"></div>' +
            '<div class="edit-row"><label>分组</label><select class="f-group">' + groupOptions(groupId) + '</select></div>' +
            '<div class="edit-row"><label>颜色</label><input type="text" class="f-color" placeholder="如 #ff0000，留空为默认"></div>' +
            '<div class="edit-row"><label>描述</label><input type="text" class="f-desc" value="' + esc(desc) + '"></div>' +
            '<div class="edit-row"><label>关键词</label><input type="text" class="f-kw" value="' + esc(kw) + '"></div>' +
            '<div class="edit-actions"><button type="button" class="btn-sm" onclick="saveEdit(this)">保存修改</button></div>' +
            '</div>';
    } else if (skip) {
        html += '<div class="res-skip-msg">' + esc(msg) + '</div>';
    } else {
        html += '<div class="res-fail-msg">' + esc(msg) + '</div>';
    }
    div.innerHTML = html;
    list.appendChild(div);
    list.scrollTop = list.scrollHeight;
}

function toggleEdit(btn) {
    var form = btn.closest('.res-item').querySelector('.res-edit-form');
    form.classList.toggle('show');
    btn.textContent = form.classList.contains('show') ? '收起' : '编辑';
}

function saveEdit(btn) {
    var item = btn.closest('.res-item');
    var form = item.querySelector('.res-edit-form');
    var id = item.getAttribute('data-id');
    var fd = new FormData();
    var editName = truncateText(form.querySelector('.f-name').value.trim(), 255);
    fd.append('url', truncateText(item.getAttribute('data-url'), 255));
    fd.append('name', editName);
    fd.append('icon', form.querySelector('.f-icon').value.trim());
    fd.append('color', truncateText(form.querySelector('.f-color').value.trim(), 32));
    fd.append('group_id', form.querySelector('.f-group').value);
    fd.append('link_desc', truncateText(form.querySelector('.f-desc').value.trim(), 255));
    fd.append('link_keywords', truncateText(form.querySelector('.f-kw').value.trim(), 512));
    fd.append('link_pwd', '0');
    if (!fd.get('name')) { showTip('名称不能为空', 'error'); return; }
    postForm('ajax_link.php?submit=edit_link&id=' + id, fd).then(function (txt) {
        if (txt.indexOf('成功') > -1) {
            item.querySelector('.res-name').textContent = editName;
            form.classList.remove('show');
            btn.textContent = '编辑';
            showTip('已保存修改', 'success');
            setTimeout(hideTip, 1500);
        } else {
            showTip(txt || '保存失败，请重试', 'error');
        }
    }).catch(function () {
        showTip('保存失败，请检查网络', 'error');
    });
}

async function batchImport() {
    var btn = document.getElementById('batchBtn');
    if (btn.disabled) return;
    var urls = document.getElementById('batchUrls').value.split(/[\r\n,;]+/).map(function (s) { return s.trim(); }).filter(Boolean);
    if (!urls.length) { showTip('请先粘贴链接，每行一个', 'error'); return; }
    urls = Array.from(new Set(urls));
    var groupId = document.getElementById('batchGroup').value;
    var autoFetch = document.getElementById('autoFetch').checked;
    var total = urls.length, done = 0, success = 0, fail = 0, skip = 0;
    btn.disabled = true;
    btn.textContent = '导入中...';
    document.getElementById('resultList').innerHTML = '';
    updateProgress(0, total, success, fail, skip);
    showTip('开始导入，共 ' + total + ' 条，并发抓取中，每条完成即可点"编辑"修改，其他任务继续...', 'loading');

    // 并发池：多个 worker 同时取任务，每完成一条立即入库并显示"编辑"，互不影响
    var queue = urls.slice();
    var workers = [];
    var workerCount = Math.min(CONCURRENCY, queue.length);
    for (var w = 0; w < workerCount; w++) {
        workers.push((async function () {
            while (queue.length) {

                var raw = queue.shift();
                var u = /^https?:\/\//i.test(raw) ? raw : 'http://' + raw;
                var name = hostFromUrl(u), icon = '', desc = '', kw = '';
                if (autoFetch) {
                    var info = await getXhr('ajax_link.php?submit=geturl&url=' + encodeURIComponent(u), 10000);
                    if (info) {
                        if (info.title) name = truncateText(info.title, 255);
                        if (info.icon) icon = info.icon;
                        if (info.description) desc = truncateText(info.description, 255);
                        if (info.keywords) kw = truncateText(info.keywords, 512);
                    }
                }
                // 根据"图标处理方式"处理图标：save=保存到服务器，link=仅保留链接，none=不抓取
                var iconMode = document.getElementById('iconMode').value;
                if (icon) {
                    if (iconMode === 'none') {
                        icon = '';
                    } else if (iconMode === 'save') {
                        var localIcon = await saveIcon(icon, u);
                        if (localIcon) icon = localIcon; // 下载失败时回退为原始图标链接
                    }
                }
                var fd = new FormData();
                fd.append('url', truncateText(u, 255));
                fd.append('name', truncateText(name, 255));
                fd.append('icon', icon);
                fd.append('group_id', groupId);
                fd.append('link_desc', truncateText(desc, 255));
                fd.append('link_keywords', truncateText(kw, 512));
                fd.append('color', '');
                var msg = '';
                try { msg = await postForm('ajax_link.php?submit=add_link', fd); } catch (e) { msg = '网络错误'; }
                var ok = msg.indexOf('成功') > -1;
                // 后端返回"链接已存在，跳过"时按跳过错行处理，不计入失败
                var isSkip = !ok && msg.indexOf('已存在') > -1;
                var id = '';
                if (ok) {
                    var m = msg.match(/ID=(\d+)/);
                    id = m ? m[1] : '';
                    success++;
                } else if (isSkip) {
                    skip++;
                } else {
                    fail++;
                }
                appendResult(u, name, icon, desc, kw, groupId, id, ok, isSkip, msg);
                done++;
                updateProgress(done, total, success, fail, skip);
            }
        })());
    }
    await Promise.all(workers);
    btn.disabled = false;
    btn.textContent = '开始批量导入';
    showTip('导入完成：成功 ' + success + ' 条，跳过 ' + skip + ' 条，失败 ' + fail + ' 条，可继续点"编辑"完善信息', fail ? 'error' : 'success');
}
<?php endif; ?>
</script>
</body>
</html>
