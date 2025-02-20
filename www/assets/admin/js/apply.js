//全选
function check_all() {
    var ischecked = $("#check_all").prop('checked');
    if (ischecked == true) {
        $('[name="link-check"]').prop('checked', true);
    } else {
        $('[name="link-check"]').prop('checked', false);
    }
}
//获取选中状态
function get_check() {
    var chk_value = [];
    $('input[name="link-check"]:checked').each(function () {
        chk_value.push($(this).val());
    });
    return chk_value;
}



// 审核
function checked_status(status) {
    if (get_check().length == 0) {
        layer.msg('未选择链接');
        return false;
    }
    lightyear.loading('show');
    $.ajax({
        url: "ajax_apply.php?set=status",
        method: "POST",
        data: {
            id: get_check(),
            status: status
        },
        dataType: "json",
        success: function (data) {
            lightyear.loading('hide');
            if (data.code == 200) {
                lightyear.notify(data.msg, 'success', 1000);
                $("#applylist").load(location.href + " #applylist>*", "");
            } else {
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

//选中删除
function checked_del(id) {
    var link_id = [];
    link_id.push(id);
    link_id = id ? link_id : get_check();
    if (link_id.length == 0) {
        layer.msg('未选择链接');
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
                        url: "ajax_apply.php?set=delete",
                        method: "POST",
                        data: {
                            id: link_id
                        },
                        dataType: "json",
                        success: function (data) {
                            lightyear.loading('hide');
                            if (data.code == 200) {
                                lightyear.notify(data.msg, 'success', 1000);
                                $("#applylist").load(location.href + " #applylist>*", "");
                            } else {
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
            },
            cancel: {
                text: '取消'
            }
        }
    });
}

//审核状态
function status(id, status) {
    $.ajax({
        url: "ajax_apply.php?set=status",
        type: "POST",
        dataType: "json",
        data: {
            id: id,
            status: status
        },
        dataType: "json",
        success: function (data) {
            lightyear.loading('hide');
            if (data.code == 200) {
                lightyear.notify(data.msg, 'success', 1000);
                $("#applylist").load(location.href + " #applylist>*", "");
            } else {
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

function deletes(id) {
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
                        url: "ajax_apply.php?set=delete",
                        method: "POST",
                        data: {
                            id: id
                        },
                        dataType: "json",
                        success: function (data) {
                            lightyear.loading('hide');
                            if (data.code == 200) {
                                lightyear.notify(data.msg, 'success', 1000);
                                $("#applylist").load(location.href + " #applylist>*", "");
                            } else {
                                lightyear.notify(data.msg, 'danger', 1000);
                            }
                            return true;
                        },
                        error: function (data) {

                            lightyear.loading('hide');
                            layer.msg('服务器错误');
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
$("img.lazy").lazyload({
    threshold: 100
});
$(document).ready(function () {
    $(".lazys").click(function () {
        $(this).attr('src', '/assets/admin/loading.gif');
        $(this).lazyload();
    });
});