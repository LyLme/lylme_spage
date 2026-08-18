
//输入框获取焦点
window.onload = function () {
    var text = document.getElementById('search-text');
    text.focus();

}

const searchInput = document.getElementById('search-text');
const wordList = document.getElementById('word').getElementsByTagName('li');
let selectedWordIndex = -1;
let ignoreKeyEvents = false; // 新增标志用于判断是否忽略键盘事件

// 监听键盘事件
searchInput.addEventListener('keydown', function (event) {
    switch (event.key) {
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
        case 'Enter':
            // 选中联想项时回车：本站链接直接打开，网页词用当前引擎搜索
            if (selectedWordIndex >= 0 && wordList[selectedWordIndex]) {
                event.preventDefault();
                chooseSugItem(wordList[selectedWordIndex]);
            }
            break;
        default:
            ignoreKeyEvents = false; // 其他键盘事件恢复正常
            break;
    }
});

// 选中上一个选项（循环）
function selectPreviousWord() {
    if (wordList.length === 0) return;
    if (selectedWordIndex >= 0) {
        wordList[selectedWordIndex].classList.remove('selected');
    }
    selectedWordIndex = (selectedWordIndex - 1 + wordList.length) % wordList.length;
    wordList[selectedWordIndex].classList.add('selected');
}

// 选中下一个选项（循环）
function selectNextWord() {
    if (wordList.length === 0) return;
    if (selectedWordIndex >= 0) {
        wordList[selectedWordIndex].classList.remove('selected');
    }
    selectedWordIndex = (selectedWordIndex + 1) % wordList.length;
    wordList[selectedWordIndex].classList.add('selected');
}

// 将选中的选项填入输入框
function fillInputWithSelectedWord() {
    if (selectedWordIndex !== -1) {
        searchInput.value = wordList[selectedWordIndex].innerText;
        ignoreKeyEvents = true; // 标记忽略键盘事件
    }
}

// 统一处理联想项选中（点击 / 键盘回车）：本站链接直接打开，网页词用当前引擎搜索
function chooseSugItem(li) {
    if (!li) return;
    var type = li.getAttribute('data-type');
    if (type === 'site') {
        var href = li.getAttribute('data-href');
        if (href) window.open(href, '_blank');
    } else {
        var word = li.getAttribute('data-text') || li.textContent;
        $('#search-text').val(word);
        $('.submit').trigger('click'); // 触发搜索事件
    }
    $('#word').empty();
    $('#word').hide();
    selectedWordIndex = -1;
}

//关键词sug + 站内搜索
$(function () {
    var sugSeq = 0; // 请求序号，防止异步联想乱序覆盖

    // 获取本站卡片匹配（{text, href, type:'site'}）
    function getSiteMatches(keyword) {
        keyword = (keyword || '').trim().toLowerCase();
        if (!keyword) return [];
        var items = [];
        $('.mylist a[href][title]').each(function () {
            var name = $(this).attr('title') || '';
            var href = $(this).attr('href') || '';
            if (name && href && name.toLowerCase().indexOf(keyword) !== -1) {
                items.push({ text: name, href: href, type: 'site' });
            }
        });
        return items.slice(0, 5);
    }

    // 追加一条联想项（本站链接 type:'site' / 网页词 type:'web'）
    function appendSugItem(item) {
        if (!item || !item.text) return;

        var li = document.createElement('li');

        if (item.type === 'site') {
            li.className = 'sug-site';

            var icon = document.createElement('span');
            icon.className = 'sug-icon';
            icon.innerHTML = '<svg viewBox="0 0 1024 1024" width="14" height="14"><path d="M909.6 854.5L649.5 594.4C690.9 542 714 475.8 714 405.1 714 217.7 562.3 66 375 66S36 217.7 36 405.1 187.7 744.1 375 744.1c70.7 0 136.9-23.1 189.3-64.4l260.1 260.1c9.2 9.2 24.1 9.2 33.3 0l28.3-28.3c9.2-9.2 9.2-24.1-.1-33.3zM375 666c-143.9 0-261-117.1-261-261s117.1-261 261-261 261 117.1 261 261-117.1 261-261 261z"/></svg>';
            li.appendChild(icon);

            li.setAttribute('data-href', item.href || '');
        }

        // 主文本
        li.appendChild(document.createTextNode(item.text));

        if (item.type === 'site') {
            var badge = document.createElement('span');
            badge.className = 'sug-badge-inner';
            badge.textContent = '站内';
            li.appendChild(badge);
        }

        li.setAttribute('data-type', item.type);
        li.setAttribute('data-text', item.text);

        $('#word').append(li);
    }

    // 联想主流程：先显示本站匹配链接（同步），再追加百度联想词（异步）
    function runSuggest(keyword) {
        $('#word').empty();
        selectedWordIndex = -1;
        var seq = ++sugSeq;
        // 1. 本站匹配链接
        getSiteMatches(keyword).forEach(function (item) {
            appendSugItem(item);
        });
        // 2. 百度联想词
        $.ajax({
            url: 'https://suggestion.baidu.com/su?wd=' + encodeURIComponent(keyword),
            dataType: 'jsonp',
            jsonp: 'cb',
            success: function (data) {
                if (seq !== sugSeq) return; // 结果已过期，丢弃
                var list = data.s || [];
                list.slice(0, 8).forEach(function (word) {
                    var dup = false;
                    $('#word li').each(function () {
                        if ($(this).attr('data-text') === word) dup = true;
                    });
                    if (!dup) appendSugItem({ text: word, type: 'web' });
                });
                if ($('#word li').length) {
                    $('#word').show();
                } else {
                    $('#word').empty();
                    $('#word').hide();
                }
                selectedWordIndex = -1;
            },
            error: function () {
                if (seq !== sugSeq) return;
                if (!$('#word li').length) {
                    $('#word').empty();
                    $('#word').hide();
                }
            }
        });
        if ($('#word li').length) $('#word').show();
    }

    //当键盘键被松开时发送Ajax获取数据
    $('#search-text').on('keyup', function () {
        if (!ignoreKeyEvents) { // 只有在标志为false时才执行
            var keywords = $(this).val();
            if (keywords == '') { $('#word').hide(); return; }
            runSuggest(keywords);
        }
    });
    //点击联想项：本站链接直接打开，网页词复制给搜索框并搜索
    $(document).on('click', '#word li', function () {
        chooseSugItem(this);
    })
    $(document).on('click', '.container,.banner-video,nav', function () {
        $('#word').empty();
        $('#word').hide();
    })
})



$(function () {
    $('.type-right').click(function (e) {
        $('#type-left').toggleClass('showListType');
        e.stopPropagation();  //阻止冒泡
    });

    $(document).click(function (e) {
        var con = $('.type-left');
        if (!con.is(e.target)) {
            con.toggleClass('showListType', false);
        }
    });
    $(document).click(function (e) {
        var con = $('.collapse');
        if (!con.is(e.target)) {
            con.toggleClass('show', false);
        }
    });
    $('.type-left ul li').click(function () {
        $(this).addClass('active').siblings('li').removeClass('active');
        $('.type-left').toggleClass('showListType');
        var lylme_tag = '#' + $(this).attr("data-lylme");
        $('html,body').animate({ scrollTop: $(lylme_tag).offset().top }, 500);

    })
})

//点击空白处关闭导航

//显示日期和时间
function show() {
    var date = new Date();
    var y = date.getFullYear();     //获取年份  
    var m = date.getMonth() + 1;   //获取月份  返回0-11  
    var d = date.getDate(); // 获取日  
    var w = date.getDay();   //获取星期几  返回0-6   (0=星期天) 
    var ww = ' 星期' + '日一二三四五六'.charAt(new Date().getDay());//星期几
    var format = [
        ("0" + date.getHours()).substr(-2), ("0" + date.getMinutes()).substr(-2)
    ].join(":");

    document.getElementById("show_date").innerHTML = y + "年" + m + "月" + d + "日 " + ww;
    document.getElementById("show_time").innerHTML = format;
    return show;
}
setInterval(show(), 1000);


!
    function () {
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
(function (a) { a.fn.scrollToTop = function (c) { var d = { speed: 800 }; c && a.extend(d, { speed: c }); return this.each(function () { var b = a(this); a(window).scroll(function () { 100 < a(this).scrollTop() ? b.fadeIn() : b.fadeOut() }); b.click(function (b) { b.preventDefault(); a("body, html").animate({ scrollTop: 0 }, d.speed) }) }) } })(jQuery); $(function () { ahtml = '<a href="javascript:void(0)" id="toTop" style="display:none;position:fixed;bottom:66px;right:10px;width:48px;height:48px;background-image:url(\'data:image/svg+xml;base64,PHN2ZyB0PSIxNjU0OTM5MTkxNTY0IiBjbGFzcz0iaWNvbiIgdmlld0JveD0iMCAwIDEwMjQgMTAyNCIgdmVyc2lvbj0iMS4xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHAtaWQ9IjEyMTgiIHdpZHRoPSI0OCIgaGVpZ2h0PSI0OCI+PHBhdGggZD0iTTUxMyAxMDMuN2MtMjI2LjEgMC00MDkuNCAxODMuMy00MDkuNCA0MDkuNFMyODYuOSA5MjIuNiA1MTMgOTIyLjZzNDA5LjQtMTgzLjMgNDA5LjQtNDA5LjRTNzM5LjEgMTAzLjcgNTEzIDEwMy43eiBtMTUzLjUgMzY0LjdjLTUuMiA1LjMtMTIuMSA3LjktMTkgNy45cy0xMy44LTIuNi0xOS03LjlMNTQ1LjEgMzg1YzAgMC40IDAuMSAwLjcgMC4xIDEuMVY3MDVjMCAxMS4xLTUuNyAyMC45LTE0LjQgMjYuNi00LjcgNC4yLTEwLjkgNi43LTE3LjcgNi43LTYuOCAwLTEzLTIuNS0xNy43LTYuNy04LjctNS43LTE0LjQtMTUuNS0xNC40LTI2LjZWMzg2LjFjMC0wLjQgMC0wLjcgMC4xLTEuMWwtODMuNCA4My40Yy0xMC41IDEwLjUtMjcuNSAxMC41LTM4IDBzLTEwLjUtMjcuNSAwLTM4TDQ5NCAyOTUuOWMxMC41LTEwLjUgMjcuNS0xMC41IDM4IDBsMTM0LjUgMTM0LjVjMTAuNSAxMC40IDEwLjUgMjcuNSAwIDM4eiIgZmlsbD0iIzE1NzJlZiIgcC1pZD0iMTIxOSI+PC9wYXRoPjwvc3ZnPg==\');z-index:999;opacity:0.9;"></a>'; $("body").append(ahtml); $("#toTop").scrollToTop(300); });