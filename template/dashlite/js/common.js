const request = (url, data, params) => {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: url,
            type: params && params.type || 'post',
            dataType: params && params.dataType || 'json',
            data: data,
            cache: params && params.cache || false,
            success: function (res) {
                resolve(res)
            },
            error: function () {
                reject()
            }
        });
    });
}

const httpGet = (url, callback, back) => {
    var ii = layer.load();
    return request(url, {}, {
        type: 'GET',
    }).then(res => {
        layer.close(ii);
        if(back){
            callback(res);
        }else{
            if(res.status == 'ok'){
                callback(res.data)
            }else{
                layer.alert(res.message, {icon: 7});
            }
        }
    },() => {
        layer.close(ii);
        layer.msg('服务器错误', {icon: 5});
    })
}

const httpPost = (url, data, callback, back) => {
    var ii = layer.load();
    return request(url, data, {
        type: 'POST',
    }).then(res => {
        layer.close(ii);
        if(back){
            callback(res);
        }else{
            if(res.status == 'ok'){
                callback(res.data)
            }else{
                layer.alert(res.message, {icon: 7});
            }
        }
    },() => {
        layer.close(ii);
        layer.msg('服务器错误', {icon: 5});
    })
}

function star_plugin(){
    var op = plugin_is_star?'del':'add';
    httpPost("/stars", {do: op, id: plugin_id}, function(data){
        if(data.status == 'ok'){
            plugin_is_star = !plugin_is_star;
            $("#star-btn-text").text(plugin_is_star?'取消收藏':'添加收藏');
            layer.msg(data.message, {icon:1, time:600});
        }else{
            layer.msg(data.message, {icon:7});
        }
    }, true);
}

const copy = (text) => {
    let oInput = document.createElement('textarea');
    oInput.value = text;
    document.body.appendChild(oInput);
    oInput.select();
    document.execCommand("Copy");
    oInput.className = 'oInput';
    oInput.style.display = 'none';
    oInput.remove();
}

const getQueryString = (name) => {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]); return null;
}

window.appendChildOrg = Element.prototype.appendChild;
Element.prototype.appendChild = function() {
    if(arguments[0].tagName == 'SCRIPT'){
        arguments[0].setAttribute('referrerpolicy', 'no-referrer');
    }
    return window.appendChildOrg.apply(this, arguments);
};