layui.use(['form'], function() {
    var form = layui.form,
        layer = layui.layer,
        $ = layui.$;
    //监听提交
    form.on('submit(saveBtn)', function(data) {
        if (data.form.method == 'get') {

        } else if (data.form.method == 'post') {
            $.post(
                data.form.action,
                data.field,
                function(res) {
                    if (res.code != 200) {
                        layer.alert(res.msg || '失败', {
                            icon: 2
                        })
                    } else {
                        layer.msg(res.msg, {
                            icon: 1,
                        }, function() {
                            //do something
                            //parent.window.location.reload(); //打开注释可以重载页面
                        });
                    }
                },
                "json"
            )
            return false;
        }
    });

});

layui.use(['layer', 'upload', 'element'], function() {
    var layer = layui.layer,
        $ = layui.$;

    function generateRdStr() {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        for (var i = 0; i < 10; i++) {
            text += possible.charAt(Math.floor(Math.random() * possible.length));
        }
        return text;
    }

    var upload = layui.upload,
        element = layui.element;
    $("input[input_type='file']").parent().append("<ul class='upload_box' style='overflow:hidden;_zoom:1;padding-left:0px;'></ul>");
    $('ul.upload_box').each(function(i) {
        $('ul.upload_box').eq(i).append($('ul.upload_box').eq(i).siblings("input[input_type='file']"));
    });
    $("input[input_type='file']").wrap("<li  style='width: 150px;height: 150px;background: #EFEFEF;float:  left;overflow:hidden;border: 4px dashed #ddd;margin-right: 10px; position: relative;margin-bottom: 10px;'></li>")
    $('ul.upload_box li').each(function(i) {
        var upload_item = $('ul.upload_box li').eq(i),
            id_name = generateRdStr();
        upload_item.attr('id', id_name);
        upload_item.append("<div class='add' style='font-size: 80px; color: #CCCCCC;width: 100%;text-align: center;line-height: 150px;position: relative;z-index: 1'>+</div>")
        upload_item.append("<div class='preview' style='width: 100%;height: 100%;position: absolute;z-index: 2;top: 0px;'></div>")
        upload_item.append("<div class='layui-progress' lay-showPercent='yes' style='position: relative;z-index: 3;bottom: 16px;' lay-filter='" + id_name + "_process' >" +
            "<div class='layui-progress-bar' lay-percent='0%'></div>" +
            "</div>");
        upload_item.append("<div class='remove'  style='z-index:3;position: absolute;width: 14px;height: 14px;line-height:14px;text-align:center;background: #E9523F;color:#fff;overflow:hidden;border-radius:5px;right: 0px;top: 17px;'>X</div>");
        $('#' + id_name + ' .remove').hide();
        $('#' + id_name + ' .preview').hide();
        $('#' + id_name + ' .layui-progress').hide();
        $('#' + id_name + ' .remove').on('click', function() {
            $('#' + id_name + ' .remove').hide();
            $('#' + id_name + ' .preview').hide();
            $('#' + id_name + ' .layui-progress').hide();
            $('#' + id_name + ' .layui-progress').find('.layui-progress-bar').removeClass('layui-bg-red');
        })
        var init_val = $('#' + id_name).find("input[type='text']").hide().val() || '';
        if (init_val.length > 0) {
            $('#' + id_name + ' .remove').show();
            $('#' + id_name + ' .preview').css({
                'background': 'url(' + init_val + ')',
                'background-repeat': 'no-repeat',
                'background-size': '100% 100%',
            }).show();
        }
        var uploadIns = upload.render({
            elem: '#' + id_name + ' .add',
            url: '../include/file.php?crop=no',
            field: 'file',
            method: 'post',
            before: function(obj) {

            },
            choose: function(obj) {
                $('#' + id_name + ' .remove').show();
                $('#' + id_name + ' .layui-progress').show();
                obj.preview(function(index, file, result) {
                    $('#' + id_name + ' .preview').css({
                        'background': 'url(' + result + ')',
                        'background-repeat': 'no-repeat',
                        'background-size': '100% 100%',
                    }).show();
                });
            },
            progress: function(n, elem) {
                var percent = n + '%' //获取进度百分比
                element.progress(id_name + '_process', percent); //可配合 layui 进度条元素使用
            },
            done: function(res) {
                if (res.code != 200) {
                    layer.alert(res.msg || '接口出错')
                } else {
                    $('#' + id_name).find("input[type='text']").attr({
                        value: res.url || ''
                    });
                    layer.msg(res.msg || '上传成功', {
                        icon: 1,
                        time: 2000 //2秒关闭（如果不配置，默认是3秒）
                    });
                }
            },
            error: function() {
                layer.alert('接口出错')
                $('#' + id_name + ' .layui-progress').find('.layui-progress-bar').addClass('layui-bg-red');
            }
        });
    })
});