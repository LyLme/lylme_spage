/* CyberWave 主题脚本 · 纯 ES5，无 let/const/箭头/模板字符串 */
(function () {
    'use strict';

    var form = document.getElementById('search-form');
    var input = document.getElementById('search-input');
    var souRadios = document.querySelectorAll('input[name="sou"]');
    var saved = null;
    try { saved = localStorage.getItem('theme_sou'); } catch (e) { saved = null; }

    function getActive() {
        for (var i = 0; i < souRadios.length; i++) {
            if (souRadios[i].checked) { return souRadios[i]; }
        }
        return souRadios.length ? souRadios[0] : null;
    }

    function applySou(el) {
        if (!el || !input) { return; }
        var hint = el.getAttribute('data-hint');
        input.placeholder = hint ? hint : '请输入搜索内容';
    }

    function setActive(r) {
        for (var i = 0; i < souRadios.length; i++) {
            var lab = souRadios[i].parentNode;
            if (!lab) { continue; }
            if (souRadios[i] === r) {
                if (lab.classList) { lab.classList.add('is-active'); }
                else if ((' ' + lab.className + ' ').indexOf(' is-active ') < 0) { lab.className += ' is-active'; }
            } else {
                if (lab.classList) { lab.classList.remove('is-active'); }
                else { lab.className = (' ' + lab.className + ' ').replace(' is-active ', ' ').trim(); }
            }
        }
    }

    /* 载入时恢复记忆 */
    if (saved && souRadios.length) {
        for (var k = 0; k < souRadios.length; k++) {
            if (souRadios[k].value === saved) {
                souRadios[k].checked = true;
                break;
            }
        }
    }
    setActive(getActive());
    applySou(getActive());

    /* 切换引擎：更新占位符 + 记忆 + 高亮 */
    for (var j = 0; j < souRadios.length; j++) {
        (function (r) {
            r.addEventListener('change', function () {
                setActive(r);
                applySou(r);
            });
        })(souRadios[j]);
    }

    /* 提交：拼接 接口地址 + 关键词，新窗口打开 */
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var active = getActive();
            if (!active || !input) { return; }
            var q = input.value.replace(/^\s+|\s+$/g, '');
            if (q === '') { input.focus(); return; }
            var url = active.value + encodeURIComponent(q);
            try { localStorage.setItem('theme_sou', active.value); } catch (err) { /* 隐私模式忽略 */ }
            window.open(url, '_blank');
            input.value = '';
        });
    }

    /* 霓虹时钟（节点不存在则跳过） */
    var timeEl = document.getElementById('cw-time');
    var dateEl = document.getElementById('cw-date');

    function pad(n) { return n < 10 ? '0' + n : '' + n; }

    function tick() {
        if (!timeEl && !dateEl) { return; }
        var d = new Date();
        if (timeEl) {
            timeEl.textContent = pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds());
        }
        if (dateEl) {
            var wk = ['日', '一', '二', '三', '四', '五', '六'];
            dateEl.textContent = d.getFullYear() + '/' + pad(d.getMonth() + 1) + '/' + pad(d.getDate()) + ' 周' + wk[d.getDay()];
        }
    }

    if (timeEl || dateEl) {
        tick();
        setInterval(tick, 1000);
    }
})();
