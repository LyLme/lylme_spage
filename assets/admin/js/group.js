function listTable(query) {
    var url = window.document.location.href.toString();
    var queryString = url.split("?")[1];
    query = query || queryString;
    layer.closeAll();
    var ii = layer.load(2, { shade: [0.1, '#fff'] });
    $.ajax({
        type: 'GET',
        url: 'table_group.php?' + query,
        dataType: 'html',
        cache: false,
        success: function (data) {
            layer.close(ii);
            $("#listTable").html(data);
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
$(document).on('click', '.sort-up', function () {
    //上移
    if ($(this).parents('tr').prevAll().length > 0) {
        $(this).parents('tr').prev().before($(this).parents('tr').prop('outerHTML'));
        $(this).parents('tr').remove();
        save_order();
    }
}).on('click', '.sort-down', function () {
    //下移
    if ($(this).parents('tr').nextAll().length > 0) {
        $(this).parents('tr').next().after($(this).parents('tr').prop('outerHTML'));
        $(this).parents('tr').remove();
        save_order();
    }
});
//保存排序
function save_order() {
    var groups = [];
    var $inputArr = $('input[name="group_id"]');
    $inputArr.each(function () {
        groups.push($(this).val());
    });

    lightyear.loading('show');
    $.ajax({
        url: "group.php?set=sort",
        method: "POST",
        data: { groups: groups },
        success: function (data) {
            lightyear.loading('hide');
            lightyear.notify('操作成功！', 'success', 1000);
            listTable();
            return true;
        },
        error: function (data) {
            layer.msg('服务器错误');
            lightyear.loading('hide');
            return false;
        }
    });
}

//启用分组
function on_group(id) {
    lightyear.loading('show');
    $.ajax({
        url: "group.php?set=on",
        method: "POST",
        data: { group_id: id },
        success: function (data) {
            lightyear.loading('hide');
            lightyear.notify('操作成功！', 'success', 1000);
            listTable();
            return true;
        },
        error: function (data) {
            layer.msg('服务器错误');
            lightyear.loading('hide');
            return false;
        }
    });
}
//禁用分组
function off_group(id) {
    lightyear.loading('show');
    $.ajax({
        url: "group.php?set=off",
        method: "POST",
        data: { group_id: id },
        success: function (data) {
            lightyear.loading('hide');
            lightyear.notify('操作成功！', 'success', 1000);
            listTable();
            return true;
        },
        error: function (data) {
            layer.msg('服务器错误');
            lightyear.loading('hide');
            return false;
        }
    });
}

//删除分组
function del_group(id) {
    $.confirm({
        title: '警告',
        content: '删除分组会<b>同时删除该分组下的所有链接</b>，该操作不可逆！<br><font color="red">是否继续？</font>',
        type: 'red',
        typeAnimated: true,
        buttons: {
            tryAgain: {
                text: '确定',
                btnClass: 'btn-red',
                action: function () {
                    lightyear.loading('show');
                    $.ajax({
                        url: "group.php?set=del",
                        method: "POST",
                        data: {
                            group_id: id
                        },
                        success: function (data) {
                            lightyear.loading('hide');
                            lightyear.notify('操作成功！', 'success', 1000);
                            listTable();
                            return true;
                        }
                    });
                }
            },
            close: {
                text: '取消'
            }
        }
    });
}