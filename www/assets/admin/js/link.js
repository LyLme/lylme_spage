//请求页面
function listTable(query) {
    var url = window.document.location.href.toString();
    var queryString = url.split("?")[1];
    query = query || queryString;
    layer.closeAll();
    var ii = layer.load(2, { shade: [0.1, '#fff'] });
    $.ajax({
        type: 'GET',
        url: 'table_link.php?' + query,
        dataType: 'html',
        cache: false,
        success: function (data) {
            layer.close(ii);
            $("#listTable").html(data);
            $("#link").dragsort({
                dragBetween: true,
                dragSelector: "td.lylme",
                dragEnd: showbutton,
                placeHolderTemplate: "<tr></tr>",
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
function on_link() {
    if (get_check().length == 0) {
        $.alert("未选择链接");
        return false;
    }
    lightyear.loading('show');
    $.ajax({
        url: "ajax_link.php?submit=on",
        method: "POST",
        data: { links: get_check() },
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
function off_link() {
    if (get_check().length == 0) {
        $.alert("未选择链接");
        return false;
    }
    lightyear.loading('show');
    $.ajax({
        url: "ajax_link.php?submit=off",
        method: "POST",
        data: { links: get_check() },
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
            $("input[name=\'name\']").val(data.title);
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
function uploadimg(e) {
    var formData = new FormData();
    formData.append("file", $("#file")[0].files[0]);
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
        dragEnd: showbutton,
        dragBetween: true, dragSelector: "tr", placeHolderTemplate: "<tr></tr>"
    });
});

//显示保存
function showbutton() {
    $("#save_order").show();
}

//保存拖拽排序
function save_order() {
    var link_array = [];
    var $inputArr = $('input[name="link-check"]');
    $inputArr.each(function () {
        link_array.push($(this).val());
    });

    lightyear.loading('show');
    $.ajax({
        url: "ajax_link.php?submit=allorder",
        method: "POST",
        data: { link_array: link_array },
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

//点击排序
$(document).on('click', '.sort-up', function () {
    //上移一行
    if ($(this).parents('tr').prevAll().length > 0) {
        $(this).parents('tr').prev().before($(this).parents('tr').prop('outerHTML'));
        $(this).parents('tr').remove();
        save_order();
    }
}).on('click', '.sort-down', function () {
    //下移一行
    if ($(this).parents('tr').nextAll().length > 0) {
        $(this).parents('tr').next().after($(this).parents('tr').prop('outerHTML'));
        $(this).parents('tr').remove();
        save_order();
    }
}).on('click', '.sort-goup', function () {
    //移到顶部
    if ($(this).parents('tr').prevAll().length > 0) {
        $(this).parents('tbody').children("tr:first-child").before($(this).parents('tr').prop('outerHTML'));
        $(this).parents('tr').remove();
        save_order();
    }
}).on('click', '.sort-godown', function () {
    //移到底部
    if ($(this).parents('tr').nextAll().length > 0) {
        $(this).parents('tbody').children("tr:last-child").after($(this).parents('tr').prop('outerHTML'));
        $(this).parents('tr').remove();
        save_order();
    }
})

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