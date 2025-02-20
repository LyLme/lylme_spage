//天气插件
WIDGET={"CONFIG":{"modules":"12043","background":"5","tmpColor":"FFFFFF","tmpSize":"16","cityColor":"FFFFFF","citySize":"18","aqiColor":"FFFFFF","aqiSize":"16","weatherIconSize":"24","alertIconSize":"18","padding":"0px 0px 0px 0px","shadow":"0","language":"auto","fixed":"false","vertical":"center","horizontal":"center","right":"0","top":"0","key":"9d714f8dd6b94c7696f9cea8dc3ed1c5"}}


//输入框获取焦点
window.onload= function () {
    var text=document.getElementById('search-text');
	text.focus();

} 
const searchInput = document.getElementById('search-text');
const wordList = document.getElementById('word').getElementsByTagName('li');
let selectedWordIndex = -1;
let ignoreKeyEvents = false; // 新增标志用于判断是否忽略键盘事件

// 监听键盘事件
searchInput.addEventListener('keydown', function(event) {
    switch(event.key) {
        case 'ArrowUp':
            event.preventDefault();
            selectPreviousWord();
            fillInputWithSelectedWord();
            break;
        case 'ArrowDown':
            event.preventDefault();
            selectNextWord();
            fillInputWithSelectedWord();
            break;
        default:
            ignoreKeyEvents = false; // 其他键盘事件恢复正常
            break;
    }
});

// 选中上一个选项
function selectPreviousWord() {
    if (selectedWordIndex > 0) {
        wordList[selectedWordIndex].classList.remove('selected');
        selectedWordIndex--;
        wordList[selectedWordIndex].classList.add('selected');
    }
}

// 选中下一个选项
function selectNextWord() {
    if (selectedWordIndex < wordList.length - 1) {
        if (selectedWordIndex >= 0) {
            wordList[selectedWordIndex].classList.remove('selected');
        }
        selectedWordIndex++;
        wordList[selectedWordIndex].classList.add('selected');
    }
}

// 将选中的选项填入输入框
function fillInputWithSelectedWord() {
    if (selectedWordIndex !== -1) {
        searchInput.value = wordList[selectedWordIndex].innerText;
        ignoreKeyEvents = true; // 标记忽略键盘事件
    }
}
function lylme(){
    var up = document.getElementById('lylme-up');
    var down = document.getElementById('lylme-down');
    var lylme = document.getElementById('chso');
    var upsw = up.style.display;
    var downsw = down.style.display;
    if(upsw=='none'){
        up.style.display='inline';
         down.style.display='none';
        lylme.style.display='none';
        
      
    }
    else{
        up.style.display='none';
        down.style.display='inline';  
        lylme.style.display='contents';
    }
    
}
$(document).on('click', '.search-type li', function(){
    var icon=$(this).find('svg').html() //|| $(this).find('img')[0].outerHTML;
    $(".lylme").html(icon);
});
 $(document).click(function(e){
    var con = $('.collapse');
    if(!con.is(e.target)){ 
        con.toggleClass('show',false);
    }
    });

//关键词sug
$(function() {
    //当键盘键被松开时发送Ajax获取数据
    $('#search-text').on('keyup', function() {
        if (!ignoreKeyEvents) { // 只有在标志为false时才执行
            var keywords = $(this).val();
            if (keywords == '') { $('#word').hide(); return };
            $.ajax({
                url: 'https://suggestion.baidu.com/su?wd=' + keywords,
                dataType: 'jsonp',
                jsonp: 'cb',
                beforeSend: function() {
                    // $('#word').append('<li>正在加载。。。</li>');
                },
                success: function(data) {
                    $('#word').empty().show();
                    if (data.s == '') {
                        $('#word').empty();
                        $('#word').hide();
                    }
                    $.each(data.s, function() {
                        $('#word').append('<li>' + this + '</li>');
                    })
                    // 重新设置选中词汇的索引
                    selectedWordIndex = -1;
                },
                error: function() {
                    $('#word').empty().show();
                    $('#word').hide();
                }
            })
        }
    });
    //点击搜索数据复制给搜索框
    $(document).on('click', '#word li', function() {
        var word = $(this).text();
        $('#search-text').val(word);
        $('#word').empty();
        $('#word').hide();
		$('.submit').trigger('click');//触发搜索事件
    })
    $(document).on('click', '.container,.banner-video,nav', function() {
        $('#word').empty();
        $('#word').hide();
    });
});




//显示日期和时间
function show() {
	var date = new Date();
	var y = date.getFullYear();     //获取年份  
    var m =date.getMonth()+1;   //获取月份  返回0-11  
    var d = date.getDate(); // 获取日  
    var w = date.getDay();   //获取星期几  返回0-6   (0=星期天) 
    var ww = ' 星期'+'日一二三四五六'.charAt(new Date().getDay()) ;//星期几
	var format = [
		("0" + date.getHours()).substr(-2), ("0" + date.getMinutes()).substr(-2)
	].join(":");
	
    document.getElementById("show_date").innerHTML =  y+"年"+m+"月"+d+"日 "+ww; 
	document.getElementById("show_time").innerHTML = format;
	return show;
}
setInterval(show(), 500);
 

!
function() {
	function g() {
		h(), i(), j(), k()
	}
	function h() {
		d.checked = s()
	}
	function i() {
		var a = document.querySelector('input[name="type"][value="' + p() + '"]');
		a && (a.checked = !0, l(a))
	}
	function j() {
		v(u())
	}
	function k() {
		w(t())
	}
	function l(a) {
		for (var b = 0; b < e.length; b++) e[b].classList.remove("s-current");
		a.parentNode.parentNode.parentNode.classList.add("s-current")
	}
	function m(a, b) {
		window.localStorage.setItem("superSearch" + a, b)
	}
	function n(a) {
		return window.localStorage.getItem("superSearch" + a)
	}
	function o(a) {
		f = a.target, v(u()), w(a.target.value), m("type", a.target.value), c.focus(), l(a.target)
	}
	function p() {
		var b = n("type");
		return b || a[0].value
	}
	function q(a) {
		m("newWindow", a.target.checked ? 1 : -1), x(a.target.checked)
	}
	function r(a) {
		return a.preventDefault(), "" == c.value ? (c.focus(), !1) : (w(t() + c.value), x(s()), s() ? window.open(b.action, +new Date) : location.href = b.action, void 0)
	}
	function s() {
		var a = n("newWindow");
		return a ? 1 == a : !0
	}
	function t() {
		return document.querySelector('input[name="type"]:checked').value
	}
	function u() {
		return document.querySelector('input[name="type"]:checked').getAttribute("data-placeholder")
	}
	function v(a) {
		c.setAttribute("placeholder", a);
		document.getElementById('chso').style.display='none';
        document.getElementById('lylme-up').style.display='inline';
        document.getElementById('lylme-down').style.display='none';
    
	}
	function w(a) {
		b.action = a
	}
	function x(a) {
		a ? b.target = "_blank" : b.removeAttribute("target")
	}
	var y, a = document.querySelectorAll('input[name="type"]'),
		b = document.querySelector("#super-search-fm"),
		c = document.querySelector("#search-text"),
		c = document.querySelector("#search-text"),
		d = document.querySelector("#set-search-blank"),
		e = document.querySelectorAll(".search-group"),
		f = a[0];
	for (g(), y = 0; y < a.length; y++) a[y].addEventListener("change", o);
	d.addEventListener("change", q), b.addEventListener("submit", r)
	 
}();

//返回顶部
(function(a){a.fn.scrollToTop=function(c){var d={speed:800};c&&a.extend(d,{speed:c});return this.each(function(){var b=a(this);a(window).scroll(function(){100<a(this).scrollTop()?b.fadeIn():b.fadeOut()});b.click(function(b){b.preventDefault();a("body, html").animate({scrollTop:0},d.speed)})})}})(jQuery);$(function(){ahtml='<a href="javascript:void(0)" id="toTop" style="display:none;position:fixed;bottom:66px;right:10px;width:45px;height:45px;border-radius:50%;overflow:hidden;background-image:url(\'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAD30lEQVRYR8VXTU7bQBSeMcIO3RB33UVygiZSl5VKbpCcAOgFgF13wAmAE5CqarfQTbfJtiu4QXKCxlSqiI3w6/fG88zEdigOrRopJIxn5n3v+95ftHrOa0ZNFeroOVfo5xwObpITRaoTN/3eqvesDCCYzfeV1odsmLS+TDb93VVArATAn8V9rdUFkRp4Ko1IeyNN6e483BjWBVEfAHQPdDKB98N40z9gg43Z7Q5AnABEFyCmdUDUBuDPbo+09rZj8rtuAAZRfAUprutKUQ+A9R6eHhTpFlnwrF2HhVoAbODtifegfgt0T8UgWJgQpR+TcOPoqTLUAxDFF9B+ytpnUqy9Qw40sTbmtSwtaSduBuG/AjABxcdMP3srTJjvzaAtMmA9fGqBWsoA6G0hsq9gcACDY/YIhohTLwmDS/8mOec1XBDB6xYADOyZSUq6exf61ywR6bVDTfecopXZUQ3ABNvdBdMLb3rGmywAZwDUywGhGBH2eEoN2UARgAV9xZ8A2K2SpRKATbW9Yl4zAy6A4oWZx6Yo5ZkgoJCiw6oULQEQ6kHrcRw2TsWIzYATkYAZ8dV8XysdyT5bkM6LVVHOVqVoCYCN5L5baNZnScfTNHKrn0Q8ADZd7xxjuVRWign2jYsslAEgomHoUspsdjgZIR4iDjTDiJRjok9a0y9S3gfXO0iF+OEuGbSFQSnXxRRdAGA95cjP0Vetidbw6IsmegEjW262SMDmcj2A5iBeqJQLABwN802W0m0TxfC8oeYdbjxMPZIQqUU8kHTwjnD5mZRo0xso/epWRawxgDytbRo/xLLoB2M5sHzooPUB0pBzv29PwLC+zr5TC3/4za9rTl3fu0N3RBqLbEZKyEt05gb3AgOSfq5OAsB62uJGxKmW2dJjFwBLZ9nh5SkkitygYwBSScXtAoBs0HBLqchiKTb93ngCjzkG8JnA0x3juZUJTDHADiQ4LkiQV9JKABJcUkrNpocKmE88eaxo9Rl7Y63ovZv7UnzcgJNgXribORQk8smBUipCSMMYMVAYQCaUpt+1563h7JvigMK9YoF+O0M+moYMgg+C0v6fWurGj9u3vP9eeT/BwKv4ZeNb0Rn3f1NLNGSyY1ylBLzo0Ld8yLSysMYmFDGi2doxrQIhbbpIvzlbeUBYIL9d2dctAFg+pfT+hgHg8gG34NJ92d7RsnnxkXaccBvl8ltuo2UAr+2cuMhAto9rR6cYI0slkAdOCS5LUQCwbAaUJgZ2epXsLJMgp5F/+/Gr4vef0dXT71iCOkNoUaJaQ2kpZTGE/lcAnDEMqs7vgL/KwGN5/9RnvwELn+g/DNVkAQAAAABJRU5ErkJggg==\');background-repeat:no-repeat;background-position:center;z-index:999;cursor:pointer;border:1px solid rgb(0, 243, 255);box-sizing:border-box;opacity:0.9;"></a>';$("body").append(ahtml);$("#toTop").scrollToTop(300);});