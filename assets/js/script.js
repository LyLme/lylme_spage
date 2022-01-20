//天气插件
WIDGET={"CONFIG":{"modules":"12043","background":"5","tmpColor":"FFFFFF","tmpSize":"16","cityColor":"FFFFFF","citySize":"18","aqiColor":"FFFFFF","aqiSize":"16","weatherIconSize":"24","alertIconSize":"18","padding":"10px 10px 10px 10px","shadow":"0","language":"auto","fixed":"true","vertical":"center","horizontal":"center","right":"0","top":"50","key":"9d714f8dd6b94c7696f9cea8dc3ed1c5"}}


//输入框获取焦点
function FocusOnInput() {
	document.getElementById("search-text").focus();
} 

//关键词sug
$(function() {
    //当键盘键被松开时发送Ajax获取数据
    $('#search-text').keyup(function() {
        var keywords = $(this).val();
        if (keywords == '') { $('#word').hide(); return };
        $.ajax({
            url: 'https://suggestion.baidu.com/su?wd=' + keywords,
            dataType: 'jsonp',
            jsonp: 'cb', //回调函数的参数名(键值)key
            // jsonpCallback: 'fun', //回调函数名(值) value
            beforeSend: function() {
                // $('#word').append('<li>正在加载。。。</li>');
            },
            success: function(data) {
                $('#word').empty().show();
                if (data.s == '') {
                    // $('#word').append('<div class="error">暂无  ' + keywords + ' 相关索引</div>');
                    $('#word').empty();
                    $('#word').hide();
                }
                $.each(data.s, function() {
                    $('#word').append('<li>' + this + '</li>');
                })
            },
            error: function() {
                $('#word').empty().show();
                //$('#word').append('<div class="click_work">Fail "' + keywords + '"</div>');
                $('#word').hide();
            }
        })
    })
    //点击搜索数据复制给搜索框
    $(document).on('click', '#word li', function() {
        var word = $(this).text();
        $('#search-text').val(word);
        $('#word').empty();
        $('#word').hide();
        //$("form").submit();
         $('.submit').trigger('click');//触发搜索事件
    })
    $(document).on('click', '.container,.banner-video,nav', function() {
        $('#word').empty();
        $('#word').hide();
    })

})

//显示时间
setInterval(function() {
	var date = new Date();
	var format = [
		("0" + date.getHours()).substr(-2), ("0" + date.getMinutes()).substr(-2), ("0" + date
		.getSeconds()).substr(-2)
	].join(":");

	document.getElementById("show_time").innerHTML = format;
}, 500);
 
//显示日期和星期
/*
    setInterval("fun(show_date)",1);
    function fun(timeID){ 
    var date = new Date();  //创建对象  
    var y = date.getFullYear();     //获取年份  
    var m =date.getMonth()+1;   //获取月份  返回0-11  
    var d = date.getDate(); // 获取日  
    var w = date.getDay();   //获取星期几  返回0-6   (0=星期天) 
    var ww = ' 星期'+'日一二三四五六'.charAt(new Date().getDay()) ;//星期几
     
    document.getElementById(timeID.id).innerHTML =  y+"年"+m+"月"+d+"日 "+ww; 
      }
      */

