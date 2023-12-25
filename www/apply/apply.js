//抓取网页(旧版)
function geturl(){
	var url = $("input[name=\'url\']").val();
	if(!url){
		layer.msg('链接地址不能为空');
		return false;
	}
	$('#loading').css("display","flex");
    if (!/^http[s]?:\/\/+/.test(url)&&url!="") {
		var url =  "http://"+url;
		$("input[name=\'url\']").val(url);
	}
	
    $.ajax({
        url:"index.php",
        type:"GET",
        dataType:"json",
        data:{url:url},
        success:function(data){
            var head = eval(data);
            $("input[name=\'name\']").val(head.title);
            if(!head.title && !head.icon){
                layer.msg('获取失败，请手动填写');
            }
            else if(!head.icon){
                layer.msg('未获取到网站图标');
            }
            $("textarea[name=\'icon\']").val(head.icon);
            $('#loading').css("display","none");
            return true;
        },
        error:function(data){
        	layer.msg('获取失败，目标网站无法访问或防火墙限制！');
        	$('#loading').css("display","none");
        	return false;
        }
    });
}  
//补全URL
function gurl(){
    var url = $("input[name=\'url\']").val();
    if (!/^http[s]?:\/\/+/.test(url)&&url!="") {
		var url =  "http://"+url;
		$("input[name=\'url\']").val(url);
		return false;
	}
	return true;
}

//生成二维码
function recode(){
    $('#captcha_img').attr('src','../include/validatecode.php?r='+Math.random());
    $("input[name=\'authcode\']").val('');
}
//微信相关推送
function wxPlus(){
    var url = $("input[name=\'url\']").val();
    var name = $("input[name=\'name\']").val();
    if(!url || !name){
        return false;
    }
    $.ajax({
        url:"./wxplus.php?wx=plus",
        type:"POST",
        data:{
            wx_name: name,
            wx_url: url
        },success:function(data){
            console.log(data.data);
        }
        });
}

//提交
function submit(){
    var url = $("input[name=\'url\']");
    var name = $("input[name=\'name\']");
    var group_id = $("select[name=\'group_id\']");
    var icon = $("input[name=\'icon\']") ? $("input[name=\'icon\']") : $("textarea[name=\'icon\']");
    var authcode = $("input[name=\'authcode\']");
    if(!url.val() || !name.val() || !group_id.val() || !authcode.val()){
        layer.msg('必填项不能为空');
        return false;
    }
    $.ajax({
        url:"index.php?submit=post",
        type:"POST",
        dataType:"json",
        data:{
            url: url.val(),
            name: name.val(),
            group_id: group_id.val(),
            icon: icon.val(),
            authcode: authcode.val()
        },
        success:function(data){
            if(data.code == '200'){
                wxPlus(name,url);
                swal({
                    title: "成功",
                    text: data.msg,
                    icon: "success",
                    allowOutsideClick:false,
                    button : "确定"
                }).then(function () {
                recode();
                window.location.replace("./");
                return true;
                });
            }
            else{
                 swal({
                    title: "失败",
                    text: data.msg,
                    icon: "error",
                    allowOutsideClick:false,
                    button : "确定"
                }).then(function () {
                    recode();
                    return false;
                });
            }
            
            
        },
        error:function(data){
        	layer.msg('服务器错误');
        	return false;
        }
    });
}  


//抓取网页(新版)
function get_url(){
	var url = $("input[name=\'url\']").val();
	if(!url){
		layer.msg('链接地址不能为空');
		return false;
	}
	$('#loading').css("display","flex");
    if (!/^http[s]?:\/\/+/.test(url)&&url!="") {
		var url =  "http://"+url;
		$("input[name=\'url\']").val(url);
	}
	
    $.ajax({
        url:"index.php",
        type:"GET",
        dataType:"json",
        data:{url:url},
        success:function(data){
            $("input[name=\'name\']").val(data.title);
            if(!data.title && !data.icon){
                layer.msg('获取失败，请手动填写');
            }
            else if(!data.icon){
                layer.msg('未获取到网站图标');
            }
             layer.msg('正则抓取目标网站图标...');
             downloadimg(data.icon,url);
            $('#loading').css("display","none");
            return true;
        },
        error:function(data){
        	layer.msg('获取失败，目标网站无法访问或防火墙限制！');
        	$('#loading').css("display","none");
        	return false;
        }
    });
}
//抓取图标
function downloadimg(url,referer){
    $.ajax({
        url:"/include/file.php",
        type:"POST",
        dataType:"json",
        data:{url:url,referer:referer},
         success:function(data){
            if(data.code == '200'){
                layer.msg(data.msg);
                $("input[name=\'icon\']").val(data.url);
                $("#review").attr("src",data.url); 
                $("#review").show(); 
                return true;
            }
            else{
                 layer.msg(data.msg);
                 return false;
            }
        },
        error:function(data){
            layer.msg('服务器错误');
            return false;
        }
    });
}    
//生成图标
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
        dataType:"JSON",
        success:function(data){
            if(data.code == '200'){
                layer.msg(data.msg);
                $("input[name=\'icon\']").val(data.url);
                $("#review").attr("src",data.url); 
                $("#review").show(); 
                return true;
            }
            else{
                 layer.msg(data.msg);
                 return false;
            }
        },
        error:function(data){
            layer.msg('服务器错误');
            return false;
        }
    });
}