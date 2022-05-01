var bodyH = $('.index-main').height();
var winH = $(window).height() - 100;
if(bodyH > winH) {
	$('footer').addClass('show');
};

// layui.use(['dropdown', 'layer', 'form'], function() {
// 	var dropdown = layui.dropdown,
// 		layer = layui.layer,
// 		form = layui.form,
// 		$ = layui.jquery;
// })
//搜索引擎切换
function searchChange() {
	$(".search-change").click(function() {
		$('.search-lists').toggleClass('hide');
// 		console.log('1')
	});
	$(".search-lists .list").click(function() {
		var souurl = $(this).data('url');
		var text = $(this).html();
		$('.search-btn').html(text);
		$('.search-btn').attr('data-url', souurl);
		$('.search-lists').addClass('hide');
    	console.log(souurl);

	});
	$(".search-btn").click(function() {
		var url = $(this).attr('data-url');
		var kw = $('#search').val();
		if(kw !== "") {
			window.open(url + kw);
		} else {
			layer.msg('未输入搜索框关键词！', {
				time: 1000,
			});
		}
	});
}
searchChange();
//回车键、本地搜索
function keyClick() {
	$('body').keyup(function(e) {
		if(e.keyCode === 13) {
			var isFocus = $("#search").is(":focus");
			if(true == isFocus) {
				// console.log(isFocus);
				var url = $('.search-btn').attr('data-url');
				var kw = $('#search').val();
				if(kw !== "") {
					window.open(url + kw);
				} else {
					layer.msg('未输入搜索框关键词！', {
						time: 1000,
					});
				}
			}
		}
	});
	$("#search").focus(function(data, status) {
		$('.search-lists').addClass('hide');
	});
	$("#search").blur(function(data, status) {
		if($("#search").val() == '') {
			$(".site-name").removeClass("hidden");
		};
	});
	var h = holmes({
		input: '#search',
		find: '.urllist',
		placeholder: '<div class="empty">未搜索到匹配结果！</div>',
		mark: false,
		hiddenAttr: true,
		class: {
			visible: 'visible',
				hidden: 'hidden'
		},
		onFound(el) {
			$(".site-name").addClass("hidden");
		},
		onInput(el) {
			$(".site-name").addClass("hidden");
		},
		onVisible(el) {
			$(".site-name").removeClass("hidden");
		},
		onEmpty(el) {
			$(".site-name").removeClass("hidden");
		},
	});

}
keyClick();

//锚点、返回顶部
$("a.catlist").click(function() {
	$("html, body").animate({
		scrollTop: $($(this).attr("href")).offset().top - 5 + "px"
	}, 500);
	return false;
});
$('.scroll_top').click(function() {
	$('html,body').animate({
		scrollTop: '0px'
	}, 500);
});
$(window).scroll(function() {
	if($(window).scrollTop() >= 100) {
		$(".scroll_top").fadeIn(1000);
	} else {
		$(".scroll_top").stop(true, true).fadeOut(1000);
	}
});

//时间
function getNow(Mytime) {
	return Mytime < 10 ? '0' + Mytime : Mytime;
}

function CurrentTime() {
	var myDate = new Date();
	//获取当前小时数(0-23)
	var h = myDate.getHours();
	//获取当前分钟数(0-59)
	var m = myDate.getMinutes();
	//获取当前秒数(0-59)
	var s = myDate.getSeconds();
	var nowTime = getNow(h) + ':' + getNow(m) + ":" + getNow(s);
	$('#nowTime').text(nowTime);
	setTimeout("CurrentTime()", 1000); //设定定时器，循环运行     
}
CurrentTime();

var myDate = new Date();
//获取当前年份
var year = myDate.getFullYear();
//获取当前月份
var month = myDate.getMonth() + 1;
//获取当前日期
var date = myDate.getDate();
var nowDate = year + ' 年 ' + getNow(month) + " 月 " + getNow(date) + " 日";
$('#nowYmd').text(nowDate);

$('.date-main').click(function() {
	window.open('https://wannianli.tianqi.com/');
});
//获取农历
var lunarD = Lunar.fromDate(myDate);
// console.log(lunarD);
var lunarNowDate = lunarD.getYearInGanZhi() + '年' + lunarD.getMonthInChinese() + "月" + lunarD.getDayInChinese();
$('#nowLunar').text(lunarNowDate);

//获取星期
var nowWeek = lunarD.getWeekInChinese();
$('#nowWeek').text('星期' + nowWeek);

//手机端
$(".navbar").click(function() {
	$(".m-navlist-w").slideToggle();
	$(this).toggleClass("hover");
});
$(".m-navlist a.list").click(function() {
	$(".m-navlist-w").slideUp();
});


	$(function() {
    //当键盘键被松开时发送Ajax获取数据
    $('#search').keyup(function() {
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
        $('#search').val(word);
        $('#word').empty();
        $('#word').hide();
        //$("form").submit();
         $('.search-btn').trigger('click');//触发搜索事件
    })
    $(document).on('click', '.container,.banner-video,nav', function() {
        $('#word').empty();
        $('#word').hide();
    })

}) 

 $(function () { 
  
   //点击空白处隐藏弹出层。
	 $(document).click(function(event){
		 var _con = $('#word');  // 设置目标区域
		 if(!_con.is(event.target) && _con.has(event.target).length === 0){ // Mark 1
			//$('#word').slideUp('slow');  //滑动消失
			$('#word').hide(300);     //淡出消失
		 }
	});
 })
