/*
* 六零
* 2022年12月10日
*/ 
$(document).ready(function(){
    //设置为上次使用的搜索引擎
    var index_id = localStorage.getItem("index_id")?localStorage.getItem("index_id"):0;
    localStorage.setItem("index_id",index_id); 
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

});

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