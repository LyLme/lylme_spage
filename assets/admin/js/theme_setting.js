$(function() {
    // 简单的 Toast 提示（Bootstrap 风格）
    function showToast(msg, type) {
        var type = type || 'success';
        var $container = $('body > .toast-container');
        if (!$container.length) {
            $container = $('<div class="toast-container position-fixed top-0 end-0 p-3"></div>');
            $('body').append($container);
        }
        var $toast = $('<div class="toast align-items-center border-0 text-bg-' + type + ' show" role="alert">' +
            '<div class="d-flex"><div class="toast-body">' + msg + '</div>' +
            '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>');
        $container.append($toast);
        setTimeout(function() {
            $toast.remove();
        }, 3000);
    }

    // 监听表单提交（AJAX 保存）
    $(document).on('submit', 'form.form-horizontal, form[action*="ajax_theme.php"]', function(e) {
        e.preventDefault();
        var $form = $(this);
        $.post($form.attr('action'), $form.serialize(), function(res) {
            if (res.code != 200) {
                showToast(res.msg || '保存失败', 'danger');
            } else {
                showToast(res.msg || '保存成功', 'success');
            }
        }, 'json').fail(function() {
            showToast('保存失败', 'danger');
        });
        return false;
    });

    function generateRdStr() {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        for (var i = 0; i < 10; i++) {
            text += possible.charAt(Math.floor(Math.random() * possible.length));
        }
        return text;
    }

    // 图片上传控件（Bootstrap 进度条）
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
        upload_item.append("<div class='progress' style='position: relative;z-index: 3;bottom: 16px;height: 16px;'>" +
            "<div class='progress-bar progress-bar-striped progress-bar-animated' style='width: 0%'></div>" +
            "</div>");
        upload_item.append("<div class='remove'  style='z-index:3;position: absolute;width: 14px;height: 14px;line-height:14px;text-align:center;background: #E9523F;color:#fff;overflow:hidden;border-radius:5px;right: 0px;top: 17px;cursor:pointer;'>X</div>");
        $('#' + id_name + ' .remove').hide();
        $('#' + id_name + ' .preview').hide();
        $('#' + id_name + ' .progress').hide();
        $('#' + id_name + ' .remove').on('click', function() {
            $('#' + id_name + ' .remove').hide();
            $('#' + id_name + ' .preview').hide();
            $('#' + id_name + ' .progress').hide();
            $('#' + id_name).find("input[type='text']").val('');
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
        $('#' + id_name + ' .add').on('click', function() {
            var fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.accept = 'image/*';
            fileInput.onchange = function() {
                var file = fileInput.files[0];
                if (!file) {
                    return;
                }
                $('#' + id_name + ' .remove').show();
                $('#' + id_name + ' .progress').show();
                var $bar = $('#' + id_name + ' .progress-bar');
                $bar.css('width', '0%').removeClass('bg-danger');
                // 本地预览
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#' + id_name + ' .preview').css({
                        'background': 'url(' + e.target.result + ')',
                        'background-repeat': 'no-repeat',
                        'background-size': '100% 100%',
                    }).show();
                };
                reader.readAsDataURL(file);
                // 上传
                var fd = new FormData();
                fd.append('file', file);
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '../include/file.php?crop=no', true);
                xhr.upload.onprogress = function(evt) {
                    if (evt.lengthComputable) {
                        var pct = Math.round(evt.loaded / evt.total * 100) + '%';
                        $bar.css('width', pct);
                    }
                };
                xhr.onload = function() {
                    try {
                        var res = JSON.parse(xhr.responseText);
                        if (res.code != 200) {
                            $bar.addClass('bg-danger');
                            showToast(res.msg || '接口出错', 'danger');
                        } else {
                            $('#' + id_name).find("input[type='text']").val(res.url || '');
                            showToast(res.msg || '上传成功', 'success');
                        }
                    } catch (err) {
                        $bar.addClass('bg-danger');
                        showToast('接口出错', 'danger');
                    }
                };
                xhr.onerror = function() {
                    $bar.addClass('bg-danger');
                    showToast('接口出错', 'danger');
                };
                xhr.send(fd);
            };
            fileInput.click();
        });
    })
});