//按字符数截断文本并追加省略号，防止超出数据库字段长度
function truncateText(s, max) {
    if (s == null) return '';
    s = String(s);
    var chars = Array.from(s);
    if (chars.length <= max) return s;
    return chars.slice(0, Math.max(0, max - 3)).join('') + '...';
}

//当前检测弹窗 ID（0 表示未打开），删除失效链接后用于保留弹窗
var _checkWin = 0;
//检测控制对象：stopped 关闭时置 true 停止后续检测；pending 存放进行中的请求以便统一中断
var _checkCtrl = null;
var _currentListQuery = (function () {
    return window.location.search.replace(/^\?/, '');
})();
//请求页面；keepLayer=true 时不关闭已打开的 layer 弹窗（如检测进度窗）
function listTable(query, keepLayer) {

    if (query !== undefined && query !== null && query !== '') {
        _currentListQuery = query;
    }
    query = _currentListQuery;

    if (!keepLayer) layer.closeAll();

    var ii = layer.load(2, { shade: [0.1, '#fff'] });
    $.ajax({
        type: 'GET',
        url: 'table_link.php?' + query,
        dataType: 'html',
        cache: false,
        success: function (data) {
            layer.close(ii);
            $("#listTable").html(data);
            if (typeof bootstrap !== 'undefined') {
                document.querySelectorAll('#listTable [data-bs-toggle="tooltip"]').forEach(function (el) {
                    new bootstrap.Tooltip(el, { container: 'body' });
                });
            }
            $("#link").dragsort({
                itemSelector: "tr",
                dragSelector: "td.lylme",
                dragCancel: "a, button, input, select, label",
                dragBetween: true,
                dragEnd: showbutton,
                placeHolderTemplate: "<tr></tr>"
            });
        },
        error: function (data) {
            layer.msg('服务器错误');
            lightyear.loading('hide');
            return false;
        }
    });
}

//载入页面
$(document).ready(function () {
    if ($("#listTable").length > 0) {
        listTable()
    }
});

//获取选中   
function get_check() {
    var chk_value = [];
    $('input[name="link-check"]:checked').each(function () {
        chk_value.push($(this).val());
    });
    return chk_value;
}

//多选启用
function on_link(id) {
    let links = [];
    if (id) {
        links = [String(id)];
        console.log(links);

    } else {
        links = get_check();
    }
    // 未选择任何链接
    if (!links || links.length === 0) {
        $.alert("未选择链接");
        return false;
    }
    lightyear.loading('show');
    $.ajax({
        url: "ajax_link.php?submit=on",
        method: "POST",
        data: { links: links },
        success: function (data) {
            lightyear.loading('hide');
            if (data.code == 200) {
                lightyear.notify(data.msg, 'success', 1000);
                listTable();
            }
            else {
                lightyear.notify(data.msg, 'danger', 1000);
            }

            return true;
        },
        error: function (data) {
            layer.msg('服务器错误');
            lightyear.loading('hide');
            return false;
        }
    });
}

//多选禁用
function off_link(id) {
    let links = [];
    if (id) {
        links = [String(id)];
        console.log(links);

    } else {
        links = get_check();
    }
    // 未选择任何链接
    if (!links || links.length === 0) {
        $.alert("未选择链接");
        return false;
    }

    lightyear.loading('show');
    $.ajax({
        url: "ajax_link.php?submit=off",
        method: "POST",
        data: { links: links },
        success: function (data) {
            lightyear.loading('hide');
            if (data.code == 200) {
                lightyear.notify(data.msg, 'warning', 1000);
                listTable();
            }
            else {
                lightyear.notify(data.msg, 'danger', 1000);
            }
            return true;
        },
        error: function (data) {
            layer.msg('服务器错误');
            lightyear.loading('hide');
            return false;
        }
    });
}
//获取网站信息
function geturl() {
    var url = $("input[name=\'url\']").val();
    if (!url) {
        layer.msg('链接地址不能为空');
        return false;
    }
    $('#loading').css("display", "flex");
    if (!/^http[s]?:\/\/+/.test(url) && url != "") {
        var url = "http://" + url;
        $("input[name=\'url\']").val(url);
    }

    $.ajax({
        url: "ajax_link.php?submit=geturl",
        type: "GET",
        dataType: "json",
        data: { url: url },
        success: function (data) {
            $("input[name='name']").val(truncateText(data.title || '', 255));
            $("textarea[name='link_desc']").val(truncateText(data.description || '', 255));
            $("input[name='link_keywords']").val(truncateText(data.keywords || '', 512));
            if (!data.title && !data.icon) {
                layer.msg('获取失败，请手动填写');
            }
            else if (!data.icon) {
                layer.msg('未获取到网站图标');
            }
            layer.msg('正则抓取目标网站图标...');
            downloadimg(data.icon, url);
            $('#loading').css("display", "none");
            return true;
        },
        error: function (data) {
            layer.msg('获取失败，目标网站无法访问或防火墙限制！');
            $('#loading').css("display", "none");
            return false;
        }
    });
}
//抓取网站图标
function downloadimg(url, referer) {
    $.ajax({
        url: "/include/file.php",
        type: "POST",
        dataType: "json",
        data: { url: url, referer: referer },
        success: function (data) {
            if (data.code == '200') {
                layer.msg(data.msg);
                $("textarea[name=\'icon\']").val(data.url);
                return true;
            }
            else {
                layer.msg(data.msg);
                return false;
            }
        },
        error: function (data) {
            layer.msg('服务器错误');
            return false;
        }
    });
}
//上传图标
function uploadimg(el) {
    var fileInput = el || document.getElementById('file');
    var formData = new FormData();
    formData.append("file", fileInput.files[0]);
    $.ajax({
        method: 'POST',
        url: '/include/file.php',
        data: formData,
        timeout: 20000,
        cache: false,
        processData: false,
        contentType: false,
        dataType: "JSON",
        success: function (data) {
            if (data.code == '200') {
                layer.msg(data.msg);
                $("textarea[name=\'icon\']").val(data.url);
                return true;
            }
            else {
                layer.msg(data.msg);
                return false;
            }
        },
        error: function (data) {
            layer.msg('服务器错误');
            return false;
        }
    });
}
//多选删除
function del_link(id) {
    var link_id = [];
    link_id.push(id);
    link_id = id ? link_id : get_check();
    if (link_id.length == 0) {
        $.alert("未选择链接");
        return false;
    }
    $.alert({
        title: '警告',
        content: '确定要删除吗？删除后不可恢复',
        buttons: {
            confirm: {
                text: '删除',
                btnClass: 'btn-danger',
                action: function () {
                    lightyear.loading('show');
                    $.ajax({
                        url: "ajax_link.php?submit=del",
                        method: "POST",
                        data: {
                            links: link_id
                        },
                        success: function (data) {
                            lightyear.loading('hide');

                            console.log(data.msg);
                            if (data.code == 200) {
                                lightyear.notify(data.msg, 'success', 1000);
                                listTable();
                            }
                            else {
                                lightyear.notify(data.msg, 'danger', 1000);
                            }
                            return true;
                        }
                    });
                },
                error: function (data) {
                    layer.msg('服务器错误');
                    lightyear.loading('hide');
                    return false;
                }
            },
            cancel: {
                text: '取消'
            }
        }
    });
}

//全选
function check_all() {
    var ischecked = $("#check_all").prop('checked');
    if (ischecked == true) {
        $('[name="link-check"]').prop('checked', true);
    } else {
        $('[name="link-check"]').prop('checked', false);
    }
}

//拖拽排序
$(document).ready(function () {
    $("#link").dragsort({
        itemSelector: "tr",
        dragSelector: "td.lylme",
        dragCancel: "a, button, input, select, label",
        dragBetween: true,
        dragEnd: showbutton,
        placeHolderTemplate: "<tr></tr>"
    });

    $(document).on('click', '#refreshBtn', function () {
        listTable();
    });
});

//显示保存
function showbutton() {
    $("#save_order").show();
}

//保存拖拽排序
function save_order() {
    var link_array = [];
    $('input[name="link-check"]').each(function () {
        link_array.push($(this).val());
    });

    lightyear.loading('show');
    $.ajax({
        url: "ajax_link.php?submit=allorder",
        method: "POST",
        data: { link_array: link_array },
        dataType: 'json',
        success: function (data) {
            lightyear.loading('hide');

            if (data.code == 200) {
                lightyear.notify(data.msg, 'success', 1000);
                // 校验前端顺序是否和后端一致
                var frontOrder = link_array.join(',');
                var backOrder = data.order;
                if (frontOrder === backOrder) {
                    $("#save_order").hide();
                    return;
                }


                // 顺序不一致再刷新表格
                listTable();
            } else {
                lightyear.notify(data.msg, 'danger', 1000);
                listTable(); // 出错时强制刷新
            }
        },
        error: function () {
            layer.msg('服务器错误');
            lightyear.loading('hide');
        }
    });
}

//点击排序
$(document).on('click', '.sort-up', function () {
    // 上移一行
    const $tr = $(this).parents('tr');
    if ($tr.prevAll().length > 0) {
        const $newTr = $($tr.prop('outerHTML'));
        $tr.prev().before($newTr);
        $tr.remove();
        highlightRow($newTr);
        save_order();
    } else {
        layer.msg('已在顶部');
    }
}).on('click', '.sort-down', function () {
    // 下移一行
    const $tr = $(this).parents('tr');
    if ($tr.nextAll().length > 0) {
        const $newTr = $($tr.prop('outerHTML'));
        $tr.next().after($newTr);
        $tr.remove();
        highlightRow($newTr);
        save_order();
    } else {
        layer.msg('已在底部');
    }
}).on('click', '.sort-goup', function () {
    // 移到顶部
    const $tr = $(this).parents('tr');
    if ($tr.prevAll().length > 0) {
        const $newTr = $($tr.prop('outerHTML'));
        $tr.parents('tbody').children('tr:first-child').before($newTr);
        $tr.remove();
        highlightRow($newTr);
        save_order();
    } else {
        layer.msg('已在顶部');
    }
}).on('click', '.sort-godown', function () {
    // 移到底部
    const $tr = $(this).parents('tr');
    if ($tr.nextAll().length > 0) {
        const $newTr = $($tr.prop('outerHTML'));
        $tr.parents('tbody').children('tr:last-child').after($newTr);
        $tr.remove();
        highlightRow($newTr);
        save_order();
    } else {
        layer.msg('已在底部');
    }
});
//移到分组
function edit_group(mv_group) {
    if (get_check().length == 0) {
        $.alert("未选择链接");
        return false;
    }
    $.confirm({
        title: '移动分组',
        content: mv_group,
        buttons: {
            formSubmit: {
                text: '移动',
                btnClass: 'btn-blue',
                action: function () {
                    var group_id = this.$content.find('.group_id').val();
                    if (!group_id) {
                        $.alert('请选择要移动到的分组');
                        return false;
                    }
                    lightyear.loading('show');
                    $.ajax({
                        url: "ajax_link.php?submit=set_group",
                        method: "POST",
                        data: { links: get_check(), group_id: group_id },
                        success: function (data) {
                            lightyear.loading('hide');
                            if (data.code == 200) {
                                lightyear.notify(data.msg, 'success', 1000);
                                listTable();
                            }
                            else {
                                lightyear.notify(data.msg, 'danger', 1000);
                            }
                            return true;
                        },
                        error: function (data) {
                            layer.msg('服务器错误');
                            return false;
                        }
                    });
                }
            },
            cancel: {
                text: '取消'
            },
        }
    });
};

//链接加密
function pwd_link(pwd_list) {
    if (get_check().length == 0) {
        $.alert("未选择链接");
        return false;
    }
    $.confirm({
        title: '加密链接',
        content: pwd_list,
        buttons: {
            formSubmit: {
                text: '加密',
                btnClass: 'btn-blue',
                action: function () {
                    var pwd_id = this.$content.find('.pwd_id').val();
                    if (!pwd_id) {
                        $.alert('请选择添加到的加密组');
                        return false;
                    }
                    lightyear.loading('show');
                    $.ajax({
                        url: "ajax_link.php?submit=pwd_link",
                        method: "POST",
                        data: { links: get_check(), pwd_id: pwd_id },
                        success: function (data) {

                            lightyear.loading('hide');
                            if (data.code == 200) {
                                lightyear.notify(data.msg, 'success', 1000);
                                listTable();
                            }
                            else {
                                lightyear.notify(data.msg, 'danger', 1000);
                            }
                            return true;
                        },
                        error: function (data) {
                            layer.msg('服务器错误');
                            return false;
                        }
                    });
                }
            },
            cancel: {
                text: '取消'
            },
        }
    });
};

function pwd_links() {
    $.alert({
        title: '分组已加密',
        content: '当前分组已设置为加密，若想单独设置链接加密，请先删除该分组的加密',
    });
}
$(document).on('click', '.tips', function () {
    $.alert({
        title: '提示',
        content: '<hr><h4>拖动排序</h4><li>在电脑端可以拖拽链接的<b>名称</b>进行排序，拖拽完成后点击“保存排序”即可生效</li><hr><h4>链接加密</h4><li>加密后的链接地址在本页面显示为<font color="#f96197">粉色</font>，以便标识</li><li>加密分组后该分组下的链接单独设置的加密将失效，删除分组的加密后即可恢复</li><li><b>加密后链接只能使用密码登录后方可查看</b></li>',
    });
});

//收集待检测链接
// scope = 'group' 当前分组（当前表格展示的分组行）
// scope = 'checked' 选中的链接（勾选的复选框）
// scope = 'all' 整站所有链接（allLinks 为服务端返回的全站链接数组）
function buildItems(scope, allLinks) {
    var items = [];
    if (scope === 'group' || scope === 'checked') {
        var checked = get_check();
        var onlyChecked = (scope === 'checked');
        var seen = {};
        $('#link tr').each(function () {
            var $td = $(this).find('.link-url');
            var u = $td.attr('data-url');
            if (u) {
                var id = $(this).find('input[name="link-check"]').val();
                if (seen[id]) return; //按 ID 去重，防止重复行被收集两次
                if (onlyChecked && checked.indexOf(id) == -1) return; //已选中时跳过未勾选的
                seen[id] = 1;
                items.push({
                    el: $td[0],
                    id: id,
                    url: u,
                    name: $.trim($(this).find('td.lylme').text())
                });
            }
        });
    } else if (scope === 'all') {
        (allLinks || []).forEach(function (lk) {
            items.push({
                el: null, //整站链接不在当前表格中，无对应行引用
                id: String(lk.id),
                url: lk.url,
                name: lk.name || ''
            });
        });
    }
    return items;
}

//失效检测：弹窗选择检测范围（当前分组 / 选中的链接 / 整站所有链接）
var check_group_links = function () {
    var checkedCount = get_check().length;
    // 当前分组（当前表格展示的分组行）链接数量
    var groupCount = 0;
    $('#link tr').each(function () {
        if ($(this).find('.link-url').attr('data-url')) groupCount++;
    });

    var scopeHtml = '' +
        '<div style="font-size:14px;font-weight:600;margin-bottom:10px">请选择检测范围：</div>' +
        '<div style="font-size:16px;line-height:2.3;font-weight:bold">' +
        '<label style="cursor:pointer;display:block"><input type="radio" name="check_scope" value="group" checked> 当前分组（<b>' + groupCount + '</b> 个链接）</label>' +
        '<label style="cursor:pointer;display:block"><input type="radio" name="check_scope" value="checked"' + (checkedCount == 0 ? ' disabled' : '') + '> 选中的链接（<b>' + checkedCount + '</b> 个' + (checkedCount == 0 ? '，未选择' : '') + '）</label>' +
        '<label style="cursor:pointer;display:block"><input type="radio" name="check_scope" value="all">  整站所有链接</label>' +
        '</div>' +
        '<hr style="margin:12px 0">' +
        '<div style="text-align:left;font-size:13px;line-height:2.1;padding:0 6px">' +
        '<div><i class="mdi mdi-check-circle text-success"></i> 优先由<b>服务器抓取</b>检测</div>' +
        '<div><i class="mdi mdi-check-circle text-success"></i>  服务器抓取失败的链接，再用<b>本地客户端</b>检测连通性(用于检测内网链接)</div>' +
        '<div><i class="mdi mdi-check-circle text-success"></i>  <b>可能误判失效：</b>反爬拦截、证书异常、HTTPS 页面检测 http 链接等</div>' +
        '<div><i class="mdi mdi-check-circle text-success"></i> 链接较多时耗时较长，请耐心等待</div>' +
        '</div>';

    $.alert({
        title: '<i class="mdi mdi-radar text-primary"></i> 链接失效检测',
        width: window.innerWidth < 768 ? '94%' : '600px',
        content: scopeHtml,
        buttons: {
            confirm: {
                text: '开始检测',
                btnClass: 'btn-primary',
                action: function () {
                    var scope = this.$content.find('input[name="check_scope"]:checked').val();
                    if (!scope) {
                        layer.msg('请选择检测范围');
                        return false; //保留弹窗
                    }
                    if (scope === 'checked') {
                        var items = buildItems('checked');
                        if (items.length == 0) { layer.msg('所选链接已失效或不存在'); return false; }
                        do_check(items, '选中的链接');
                    } else if (scope === 'group') {
                        var items = buildItems('group');
                        if (items.length == 0) { layer.msg('当前分组没有链接'); return false; }
                        do_check(items, '当前分组');
                    } else if (scope === 'all') {
                        // 整站所有链接：异步拉取全站数据后再检测
                        lightyear.loading('show');
                        getXhr('ajax_link.php?submit=all_links', 20000).then(function (data) {
                            lightyear.loading('hide');
                            if (!data || data.code != 200 || !data.links || data.links.length == 0) {
                                layer.msg('未获取到全站链接');
                                return;
                            }
                            do_check(buildItems('all', data.links), '整站所有链接');
                        });
                    }
                }
            },
            cancel: {
                text: '取消'
            }
        }
    });
    return false;
};

//执行检测（进度条 + 逐条状态，完成后打标记保留，不隐藏）
// scopeLabel 仅用于弹窗标题展示，标识本次检测范围
function do_check(items, scopeLabel) {
    var total = items.length;
    var ok = 0, fail = 0, done = 0;
    var results = [];
    var timeout = 10000; //10秒超时视为无法访问
    // 初始化检测控制对象：关闭窗口时置 stopped=true 停止后续检测并中断进行中的请求
    _checkCtrl = { stopped: false, pending: [] };

    // 移动端适配：窄屏使用百分比尺寸，避免弹窗超出屏幕
    var isMobile = window.innerWidth < 768;
    var winArea = isMobile ? ['96%', '88%'] : ['600px', '420px'];
    var listMaxH = isMobile ? 'calc(100vh - 200px)' : '250px';
    var itemFs = isMobile ? 13 : 12;

    // 检测弹窗：进度条 + 所有链接的状态列表（完成后打标记，不删除）
    var listHtml = '';
    items.forEach(function (it, i) {
        it._i = i;
        listHtml += '<div id="check_item_' + i + '" data-id="' + it.id + '" style="padding:4px 0;border-bottom:1px dashed #f0f2f5;font-size:' + itemFs + 'px;line-height:1.8;color:#495057;word-break:break-all">' +
            '<span class="text-muted"><i class="mdi mdi-timer-sand"></i> [等待检测]</span> <span class="text-muted">' + esc(it.url) + '</span></div>';
    });
    var checkWin = layer.open({
        type: 1,
        title: '正在检测链接' + (scopeLabel ? '（' + scopeLabel + '）' : ''),
        area: winArea,
        closeBtn: 0,
        shadeClose: false,
        btn: [],
        content: '<div style="padding:' + (isMobile ? '12px' : '18px 20px') + '">' +
            '<div class="progress" style="height:26px;margin-bottom:10px;background:#eef2f7">' +
            '<div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" style="width:0%;line-height:26px;font-size:13px">0 / ' + total + '</div>' +
            '</div>' +
            '<div id="check_list" style="max-height:' + listMaxH + ';overflow:auto;padding-right:4px;font-size:' + itemFs + 'px"></div>' +
            '<div id="check_footer" style="text-align:center;margin-top:12px;display:none"></div>' +
            '</div>'
    });
    _checkWin = checkWin;
    var $bar = $('#layui-layer' + checkWin + ' .progress-bar');
    var $list = $('#layui-layer' + checkWin + ' #check_list');
    $list.html(listHtml);
    // 关闭按钮：从检测开始即显示，可随时关闭窗口（二次确认，防止误点丢失检测结果）
    $('#layui-layer' + checkWin + ' #check_footer').html('<button class="btn btn-primary btn-sm" onclick="closeCheckWin(' + checkWin + ')"><i class="mdi mdi-close"></i> 关闭窗口</button>').show();

    var setItem = function (i, html) {
        $('#layui-layer' + checkWin + ' #check_item_' + i).html(html);
    };
    var updateBar = function () {
        if (done >= total) {
            $bar.css('width', '100%').text('检测完成：可访问 ' + ok + '  无法访问 ' + fail);
        } else {
            var pct = Math.round(done / total * 100);
            $bar.css('width', pct + '%').text(done + ' / ' + total + '  可访问 ' + ok + '  失效 ' + fail);
        }
    };
    // 单条检测完成：更新状态标记、统计并推进并发
    var doneOne = function (it, isOk, title) {
        if (_checkCtrl && _checkCtrl.stopped) return; // 窗口已关闭，丢弃结果
        active--;
        done++;
        if (isOk) {
            ok++;
            setItem(it._i, '<font color="green"><i class="mdi mdi-check-circle"></i> [完成]</font> ' +
                '<b>' + esc(title || it.name) + '</b> <span class="text-muted">' + esc(it.url) + '</span>' +
                // 正常链接也提供编辑按钮（无需删除）
                '<span style="margin-left:6px;white-space:nowrap">' +
                '<button class="btn btn-info btn-xs" onclick="edit_link(' + it.id + ')"><i class="mdi mdi-pencil"></i> 编辑</button>' +
                '</span>');
        } else {
            fail++;
            results.push(it);
            // 失效链接在弹窗内直接提供编辑/删除按钮，不弹新窗口
            setItem(it._i, '<font color="red"><i class="mdi mdi-close-circle"></i> [无法访问]</font> ' +
                '<b>' + esc(it.name) + '</b> <span class="text-muted">' + esc(it.url) + '</span>' +
                '<span style="margin-left:6px;white-space:nowrap">' +
                '<button class="btn btn-info btn-xs" onclick="edit_link(' + it.id + ')"><i class="mdi mdi-pencil"></i> 编辑</button> ' +
                '<button class="btn btn-danger btn-xs" onclick="del_failed_link(' + it.id + ')"><i class="mdi mdi-delete"></i> 删除</button>' +
                '</span>');
        }
        updateBar();
        if (done >= total) {
            // 完成后保留进度窗口：表格行打标记，弹窗标题/进度条更新，显示关闭按钮
            results.forEach(function (r) {
                if (r.el) { //整站链接无对应表格行，跳过行内标记
                    $(r.el).find('font').remove();
                    $(r.el).html('<font color="red">[无法访问] ' + $(r.el).text().trim() + '</font>');
                }
            });
            updateBar();
            layer.title('检测完成：共 ' + total + ' 个，可访问 ' + ok + ' 个，无法访问 ' + fail + ' 个', checkWin);
            $('#layui-layer' + checkWin + ' #check_footer').show();
        }
        startNext();
    };

    // 单个链接检测：先服务器抓取，失败再用本地客户端 fetch 兜底
    var checkOne = function (it) {
        if (_checkCtrl && _checkCtrl.stopped) return; // 已关闭，不再发起检测
        var testUrl = it.url;
        //HTTPS 页面请求 http 链接会被浏览器按混合内容拦截，自动升级为 https 重试
        if (location.protocol === 'https:' && /^http:\/\//i.test(testUrl)) {
            testUrl = testUrl.replace(/^http:\/\//i, 'https://');
        }
        setItem(it._i, '<font color="#ffc107"><i class="mdi mdi-sync mdi-spin"></i> [服务器检测]</font> <span class="text-muted">' + esc(testUrl) + '</span>');
        // 第一步：服务端抓取（同批量导入 geturl），能取到即判定可访问并显示标题
        getXhr('ajax_link.php?submit=geturl&url=' + encodeURIComponent(testUrl), timeout).then(function (data) {
            if (_checkCtrl && _checkCtrl.stopped) return;
            if (data && (data.code == 200 || data.title)) {
                doneOne(it, true, data.title);
                return;
            }
            // 第二步：服务器抓取失败（反爬/内网/超时等），客户端 fetch no-cors 兜底判断连通性
            setItem(it._i, '<font color="#ffc107"><i class="mdi mdi-sync mdi-spin"></i> [客户端检测]</font> <span class="text-muted">' + esc(testUrl) + '</span>');
            var controller = typeof AbortController !== 'undefined' ? new AbortController() : null;
            var timer = setTimeout(function () {
                if (controller) controller.abort();
            }, timeout);
            if (controller) _checkCtrl.pending.push({ abort: function () { try { controller.abort(); } catch (e) { } } });
            fetch(testUrl, { method: 'GET', mode: 'no-cors', cache: 'no-store', signal: controller ? controller.signal : undefined })
                .then(function () { clearTimeout(timer); if (!(_checkCtrl && _checkCtrl.stopped)) doneOne(it, true, ''); })
                .catch(function () { clearTimeout(timer); if (!(_checkCtrl && _checkCtrl.stopped)) doneOne(it, false, ''); });
        });
    };

    //限制并发数为 5，逐个推进直到全部完成
    var idx = 0, active = 0;
    var startNext = function () {
        if (_checkCtrl && _checkCtrl.stopped) return;
        while (active < 5 && idx < total) {
            var item = items[idx++];
            active++;
            checkOne(item);
        }
    };
    startNext();
}

//HTML 转义，防止名称/标题中的特殊字符破坏列表结构
function esc(s) {
    return String(s == null ? '' : s)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

//发起带超时的 GET 请求并解析 JSON（与批量导入 getXhr 一致），失败返回 null
//若处于检测中，将 XHR 登记到 _checkCtrl.pending，便于关闭窗口时统一中断
function getXhr(url, timeout) {
    return new Promise(function (resolve) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', url, true);
        xhr.timeout = timeout || 10000;
        xhr.onload = function () {
            try { resolve(JSON.parse(xhr.responseText)); }
            catch (e) { resolve(null); }
        };
        xhr.onerror = xhr.ontimeout = function () { resolve(null); };
        xhr.send();
        if (typeof _checkCtrl !== 'undefined' && _checkCtrl) {
            _checkCtrl.pending.push({ abort: function () { try { xhr.abort(); } catch (e) { } } });
        }
    });
}

//删除单个失效链接（删除后刷新列表；若检测弹窗仍打开则保留并同步移除该行）
function del_failed_link(id) {
    $.alert({
        title: '警告',
        content: '确定要删除该链接吗？删除后不可恢复',
        width: window.innerWidth < 768 ? '92%' : 'auto',
        buttons: {
            confirm: {
                text: '删除',
                btnClass: 'btn-danger',
                action: function () {
                    lightyear.loading('show');
                    $.ajax({
                        url: "ajax_link.php?submit=del",
                        method: "POST",
                        data: { links: [id] },
                        success: function (data) {
                            lightyear.loading('hide');
                            if (data.code == 200) {
                                lightyear.notify(data.msg, 'success', 1000);
                                // 检测弹窗仍打开时：保留弹窗，移除该行并更新统计，再静默刷新表格
                                if (_checkWin && $('#layui-layer' + _checkWin).length) {
                                    $('#layui-layer' + _checkWin + ' #check_list [data-id="' + id + '"]').remove();
                                    var $bar = $('#layui-layer' + _checkWin + ' .progress-bar');
                                    if ($bar.length) {
                                        $bar.text($bar.text().replace(/无法访问 (\d+)/, function (m, n) { return '无法访问 ' + (parseInt(n, 10) - 1); }));
                                    }
                                    var $t = $('#layui-layer' + _checkWin + ' .layui-layer-title');
                                    if ($t.length) {
                                        $t.text($t.text().replace(/共 (\d+) 个，可访问 (\d+) 个，无法访问 (\d+) 个/,
                                            function (m, a, b, c) { return '共 ' + (parseInt(a, 10) - 1) + ' 个，可访问 ' + b + ' 个，无法访问 ' + (parseInt(c, 10) - 1) + ' 个'; }));
                                    }
                                    listTable(undefined, true);
                                } else {
                                    listTable();
                                }
                            } else {
                                lightyear.notify(data.msg, 'danger', 1000);
                            }
                            return true;
                        },
                        error: function () {
                            layer.msg('服务器错误');
                            lightyear.loading('hide');
                            return false;
                        }
                    });
                }
            },
            cancel: {
                text: '取消'
            }
        }
    });
}

//关闭检测结果弹窗（二次确认，防止误点丢失检测结果）
//关闭时立即置 stopped=true 并中断所有进行中的请求，避免后台继续占用带宽
function closeCheckWin(checkWin) {
    $.alert({
        title: '关闭检测窗口',
        content: '确定要关闭检测窗口吗？关闭后将停止检测并丢失本次检测结果。',
        width: window.innerWidth < 768 ? '92%' : 'auto',
        buttons: {
            confirm: {
                text: '关闭',
                btnClass: 'btn-primary',
                action: function () {
                    if (_checkCtrl) {
                        _checkCtrl.stopped = true;
                        _checkCtrl.pending.forEach(function (p) { try { p.abort(); } catch (e) { } });
                        _checkCtrl.pending = [];
                    }
                    layer.close(checkWin);
                    _checkWin = 0;
                }
            },
            cancel: {
                text: '取消'
            }
        }
    });
}

//用弹窗打开链接编辑页（不整页跳转，避免丢失检测结果）
function edit_link(id) {
    var isMobile = window.innerWidth < 768;
    layer.open({
        type: 2,
        title: '编辑链接 #' + id,
        area: isMobile ? ['96%', '92%'] : ['620px', '86%'],
        shadeClose: false,
        content: 'ajax_link.php?submit=edit_form&id=' + id
    });
}

//编辑保存成功后由 iframe 回调：关闭编辑弹窗，刷新表格并保留检测弹窗
function editLinkSaved(id, msg) {
    layer.closeAll('iframe');
    lightyear.notify(msg, 'success', 1200);
    if (_checkWin && $('#layui-layer' + _checkWin).length) {
        // 弹窗内该行标记为已修改，并去掉行内编辑/删除按钮
        var $row = $('#layui-layer' + _checkWin + ' #check_list [data-id="' + id + '"]');
        if ($row.length) {
            var $bar = $('#layui-layer' + _checkWin + ' .progress-bar');
            if ($bar.length) {
                $bar.text($bar.text().replace(/无法访问 (\d+)/, function (m, n) { return '无法访问 ' + (parseInt(n, 10) - 1); }));
            }
            $row.html('<font color="#f39c12"><i class="mdi mdi-pencil"></i> [已修改]</font> <span class="text-muted">已更新，如需确认请重新检测</span>');
        }
        listTable(undefined, true);
    } else {
        listTable();
    }
}

function highlightRow($tr) {
    $('#listTable').find('tr.tr-highlight').removeClass('tr-highlight');
    $tr.addClass('tr-highlight');
    setTimeout(() => {
        $tr.removeClass('tr-highlight');
    }, 5000);
}
