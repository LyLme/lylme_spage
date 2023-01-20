/*
* 六零
* 2022年12月10日
*/ 
$(document).ready(function(){
    //设置为上次使用的搜索引擎
    var index_id = localStorage.getItem("index_id")?localStorage.getItem("index_id"):0;
    sethint(index_id);
    $(".lylme").children().eq(index_id).show();
});
$(".lg").click(function () {
    //切换搜索引擎
    var count = $(".lylme").children(".ss").length;
    var index = $(".lg").index(this);
    if(count <= index+1){var index = -1;}
    $(".lylme").children(".ss").hide();
    $(".lylme").children().eq(index+1).show();
    sethint(index+1);
    localStorage.setItem("index_id",index+1); 
});
function sethint(id){
    //设置搜索框提示
     $(".soinput").attr("placeholder",solist()[id][1]);
}
function go(input_text){
    // 跳转搜索结果
    var index_id = localStorage.getItem("index_id");
    var sourl = solist()[index_id][2];
    if(input_text==""){var input_text = $(".soinput").val();}
        var url = sourl+input_text;
    if(navigator.userAgent.match(/mobile/i)){
        window.location.href=url;
    }else{
        window.open(url, "_blank");
    }
}

/*
作者:D.Young
主页：https://yyv.me/
github：https://github.com/5iux/sou
日期：2020/11/18
版权所有，请勿删除
*/

$(document).ready(function() {
    //判断窗口大小，添加输入框自动完成
    var wid = $("body").width();
    if (wid < 640) {
        //$(".wd").attr('autocomplete', 'off');
    } else {
        $(".wd").focus();
    }
    //菜单点击
    $("#menu").click(function(event) {
        $(this).toggleClass('on');
        $(".list").toggleClass('closed');
        $(".mywth").toggleClass('hidden');
    });
    $("#content").click(function(event) {
        $(".on").removeClass('on');
        $(".list").addClass('closed');
        $(".mywth").removeClass('hidden');
        $('#word').hide();
    });
    $(".mywth").click(function(event) {
        var wt = $("body").width();
        if (wt < 750 || wt == 750) {
            //window.location.href = "https://tianqi.qq.com/";
            window.location.href = "/weather/";
        }
    });
});

//关键词sug
$(function() {
    //当键盘键被松开时发送Ajax获取数据
    $('.wd').keyup(function() {
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
                    //$('#word').append('<div class="error">Not find  "' + keywords + '"</div>');
                    $('#word').hide();
                }
                $.each(data.s, function() {
                    $('#word').append('<li><svg class="icon" style=" width: 15px; height: 15px; opacity: 0.5;" aria-hidden="true"><use xlink:href="#icon-sousuo"></use></svg> ' + this + '</li>');
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
        $('.wd').val(word);
        $('#word').hide();
       go(word);
        // $('#texe').trigger('click');触发搜索事件
    })
$(".wd").keypress(function(event){
  if(event.which === 13) { 
      var word = $(".soinput").val();
       go(word);
   }
})
})