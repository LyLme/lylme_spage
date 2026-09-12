/**
 * 开发包devTheme 主题脚本
 *
 * 只做两件事，全部为原生 JS，无任何依赖：
 *   1. 搜索引擎切换：更新输入框提示，并把选择记忆到 localStorage
 *   2. 时间显示（若后台关闭该模块，对应节点不存在，脚本自动跳过）
 *
 * 搜索区的 DOM 契约（index.php 必须遵守）：
 *   <form id="search-form">   <input id="search-input">
 *   <input type="radio" name="sou" value="接口地址" data-alias="引擎别名" data-hint="提示文字">
 *
 * @version 1.0.0
 */
(function () {
    'use strict';

    var SOU_KEY = 'theme_sou'; // 全站统一，换主题后用户选择依然保留

    // ---------- 搜索引擎切换 ----------
    var form = document.getElementById('search-form');
    var input = document.getElementById('search-input');
    var engines = document.querySelectorAll('input[name="sou"]');

    // 获取当前选中的引擎
    function currentEngine() {
        for (var i = 0; i < engines.length; i++) {
            if (engines[i].checked) {
                return engines[i];
            }
        }
        return null;
    }

    // 同步输入框的提示文字
    function syncPlaceholder() {
        var engine = currentEngine();
        if (engine && input) {
            input.placeholder = engine.getAttribute('data-hint') || '请输入搜索内容';
        }
    }

    // 恢复上次选择的引擎（localStorage 可能不可用，故 try/catch 包裹）
    function restoreEngine() {
        var saved = null;
        try {
            saved = localStorage.getItem(SOU_KEY);
        } catch (e) {
            return;
        }
        if (!saved) {
            return;
        }
        for (var i = 0; i < engines.length; i++) {
            if (engines[i].getAttribute('data-alias') === saved) {
                engines[i].checked = true;
                break;
            }
        }
    }

    // 切换引擎时记忆选择
    function bindEngineChange() {
        for (var i = 0; i < engines.length; i++) {
            engines[i].addEventListener('change', function () {
                try {
                    localStorage.setItem(SOU_KEY, this.getAttribute('data-alias'));
                } catch (e) {
                    // 隐私模式等场景写入失败，静默降级即可
                }
                syncPlaceholder();
            });
        }
    }

    // 提交搜索：各引擎参数名不统一（wd= / q= / word=），
    // 因此不依赖表单原生 GET，而是拼接「接口地址 + 关键词」后跳转
    function bindSubmit() {
        if (!form) {
            return;
        }
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var engine = currentEngine();
            var keyword = input ? input.value.replace(/^\s+|\s+$/g, '') : '';
            if (!engine || keyword === '') {
                if (input) {
                    input.focus();
                }
                return;
            }
            window.open(engine.value + encodeURIComponent(keyword), '_blank');
        });
    }

    restoreEngine();
    syncPlaceholder();
    bindEngineChange();
    bindSubmit();

    // ---------- 时间显示 ----------
    var clockTime = document.getElementById('clock-time');
    var clockDate = document.getElementById('clock-date');

    function tick() {
        if (!clockTime) {
            return;
        }
        var now = new Date();
        var pad = function (n) {
            return n < 10 ? '0' + n : '' + n;
        };
        var week = ['星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'];
        clockTime.textContent = pad(now.getHours()) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds());
        if (clockDate) {
            clockDate.textContent = now.getFullYear() + '年' + (now.getMonth() + 1) + '月' + now.getDate() + '日 ' + week[now.getDay()];
        }
    }

    tick();
    setInterval(tick, 1000);
})();
