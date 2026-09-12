/* ==========================================================================
   LiquidGlass · 主题脚本
   纯 ES5：无 let / const / 箭头函数 / 模板字符串
   模块：搜索引擎 · 时钟 · 分组分页 · 聚焦搜索 · 控制中心 · 设置 · 长按菜单
   ========================================================================== */
(function () {
    'use strict';

    /* ---------------- 基础工具 ---------------- */
    function $(sel, ctx) { return (ctx || document).querySelector(sel); }
    function $$(sel, ctx) {
        var list = (ctx || document).querySelectorAll(sel);
        var out = [], i;
        for (i = 0; i < list.length; i++) { out.push(list[i]); }
        return out;
    }
    /* 注意：SVG 元素的 className 是 SVGAnimatedString 对象而非字符串，
       一律走 getAttribute('class') / setAttribute，避免 .indexOf is not a function */
    function clsOf(el) {
        if (!el || !el.getAttribute) { return ''; }
        return el.getAttribute('class') || '';
    }
    function has(el, cls) {
        return (' ' + clsOf(el) + ' ').indexOf(' ' + cls + ' ') > -1;
    }
    function add(el, cls) {
        if (!el || !el.setAttribute) { return; }
        if (has(el, cls)) { return; }
        var cur = clsOf(el);
        el.setAttribute('class', cur ? (cur + ' ' + cls) : cls);
    }
    function del(el, cls) {
        if (!el || !el.setAttribute) { return; }
        var next = (' ' + clsOf(el) + ' ').replace(' ' + cls + ' ', ' ').replace(/^\s+|\s+$/g, '');
        el.setAttribute('class', next);
    }
    function toggle(el, cls) {
        if (!el) { return; }
        if (has(el, cls)) { del(el, cls); } else { add(el, cls); }
    }
    /* 从 el 向上（含自身、止于 stop）寻找带指定 class 的祖先 */
    function closest(el, cls, stop) {
        var node = el;
        while (node && node !== stop) {
            if (has(node, cls)) { return node; }
            node = node.parentNode;
        }
        return null;
    }
    function on(el, type, fn) { if (el && el.addEventListener) { el.addEventListener(type, fn, false); } }
    function pad(n) { return n < 10 ? '0' + n : '' + n; }

    var STORE_PREFS = 'lg_prefs';
    var STORE_HIDDEN = 'lg_hidden';
    var STORE_SOU = 'theme_sou';           /* 契约：全站统一，换主题保留 */

    function load(key, fallback) {
        try {
            var raw = window.localStorage.getItem(key);
            if (raw === null || raw === '') { return fallback; }
            var val = JSON.parse(raw);
            return (val === null || val === undefined) ? fallback : val;
        } catch (e) { return fallback; }
    }
    function save(key, val) {
        try { window.localStorage.setItem(key, JSON.stringify(val)); } catch (e) { /* 隐私模式忽略 */ }
    }

    var root = document.documentElement;
    var body = document.body;

    /* ---------------- 偏好设置 ---------------- */
    function cssNum(name, fallback) {
        var v = '';
        if (window.getComputedStyle) {
            try { v = window.getComputedStyle(root).getPropertyValue(name); } catch (e) { v = ''; }
        }
        if (!v) { v = root.style.getPropertyValue(name); }
        var n = parseInt(v, 10);
        return isNaN(n) ? fallback : n;
    }
    var defaultPrefs = {
        scheme: body.getAttribute('data-lg-scheme') || 'auto',
        icons: body.getAttribute('data-lg-icons') || 'gradient',
        cols: cssNum('--lg-cols', 4),
        blur: cssNum('--lg-blur', 20),
        motion: false,
        clock: true,
        yan: true,
        note: true,
        notes: true,
        todo: true,
        statusbar: false
    };
    var prefs = load(STORE_PREFS, null);
    if (!prefs || typeof prefs !== 'object') { prefs = {}; }
    for (var pk in defaultPrefs) {
        if (!Object.prototype.hasOwnProperty.call(defaultPrefs, pk)) { continue; }
        if (prefs[pk] === undefined || prefs[pk] === null) { prefs[pk] = defaultPrefs[pk]; }
    }

    function isDark() {
        if (prefs.scheme === 'dark') { return true; }
        if (prefs.scheme === 'light') { return false; }
        return !!(window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
    }

    function applyPrefs() {
        /* 外观 */
        if (prefs.scheme === 'auto') { root.removeAttribute('data-lg-scheme'); }
        else { root.setAttribute('data-lg-scheme', prefs.scheme); }
        /* 图标风格 */
        body.setAttribute('data-lg-icons', prefs.icons);
        /* 列数与模糊 */
        root.style.setProperty('--lg-cols', String(prefs.cols));
        root.style.setProperty('--lg-blur', prefs.blur + 'px');
        /* 动效 */
        if (prefs.motion) { root.setAttribute('data-lg-motion', 'reduce'); }
        else { root.removeAttribute('data-lg-motion'); }
        /* 组件显隐 */
        var wClock = document.getElementById('lg-widget-clock');
        var wYan = document.getElementById('lg-widget-yan');
        var wNote = document.getElementById('lg-widget-note');
        var wNotes = document.getElementById('lg-widget-notes');
        var wTodo = document.getElementById('lg-widget-todo');
        if (wClock) { wClock.style.display = prefs.clock ? '' : 'none'; }
        if (wYan) { wYan.style.display = prefs.yan ? '' : 'none'; }
        if (wNote) { wNote.style.display = prefs.note ? '' : 'none'; }
        if (wNotes) { wNotes.style.display = prefs.notes ? '' : 'none'; }
        if (wTodo) { wTodo.style.display = prefs.todo ? '' : 'none'; }
        if (wStatus) {
            var sbOn = (prefs.statusbar && has(body, 'is-mobile'));
            wStatus.style.display = sbOn ? 'flex' : 'none';
            if (sbOn) { add(body, 'lg-statusbar-on'); } else { del(body, 'lg-statusbar-on'); }
        }
        /* 恢复（重新打开）时清除最小化态，确保内容完整显示 */
        if (wYan) { del(wYan, 'is-min'); }
        if (wNote) { del(wNote, 'is-min'); }
        if (wNotes) { del(wNotes, 'is-min'); }
        if (wTodo) { del(wTodo, 'is-min'); }
        /* 合并卡片：仅当内部仍有可见组件时才显示，避免空卡片 */
        var wCombo = document.getElementById('lg-combo');
        if (wCombo) {
            var comboVisible = (wClock && wClock.style.display !== 'none')
                             || (wYan && wYan.style.display !== 'none')
                             || (wNote && wNote.style.display !== 'none');
            wCombo.style.display = comboVisible ? '' : 'none';
        }
        save(STORE_PREFS, prefs);
        syncControls();
        layoutPager(true);
    }

    /* ---------------- 控件回显 ---------------- */
    function syncControls() {
        var i, j;

        /* 分段控件（控制中心与设置页可能各有一组） */
        var segs = $$('[data-lg-seg]');
        for (i = 0; i < segs.length; i++) {
            var key = segs[i].getAttribute('data-lg-seg');
            var val = String(prefs[key]);
            var btns = segs[i].getElementsByTagName('button');
            for (j = 0; j < btns.length; j++) {
                if (btns[j].getAttribute('data-value') === val) { add(btns[j], 'is-on'); }
                else { del(btns[j], 'is-on'); }
            }
        }

        /* 开关 */
        var toggles = $$('[data-lg-toggle]');
        for (i = 0; i < toggles.length; i++) {
            var t = toggles[i].getAttribute('data-lg-toggle');
            var state = false;
            if (t === 'dark') { state = isDark(); }
            else if (t === 'motion') { state = !!prefs.motion; }
            else if (t === 'clock') { state = !!prefs.clock; }
            else if (t === 'yan') { state = !!prefs.yan; }
            else if (t === 'note') { state = !!prefs.note; }
            else if (t === 'notes') { state = !!prefs.notes; }
            else if (t === 'todo') { state = !!prefs.todo; }
            else if (t === 'statusbar') { state = !!prefs.statusbar; }
            if (state) { add(toggles[i], 'is-on'); } else { del(toggles[i], 'is-on'); }
        }

        /* 滑块 */
        var ranges = $$('.lg-range');
        for (i = 0; i < ranges.length; i++) { ranges[i].value = String(prefs.blur); }
        var bv1 = document.getElementById('lg-blur-val');
        var bv2 = document.getElementById('lg-blur-val-2');
        if (bv1) { bv1.textContent = prefs.blur + 'px'; }
        if (bv2) { bv2.textContent = prefs.blur + 'px'; }
    }

    /* ---------------- 搜索引擎（契约） ---------------- */
    var form = document.getElementById('search-form');
    var input = document.getElementById('search-input');
    var souRadios = $$('input[name="sou"]');
    var caretBox = form ? form : null;

    function getActive() {
        for (var i = 0; i < souRadios.length; i++) {
            if (souRadios[i].checked) { return souRadios[i]; }
        }
        return souRadios.length ? souRadios[0] : null;
    }
    function activeEngineName() {
        var a = getActive();
        if (!a) { return ''; }
        var lab = a.parentNode;
        var nm = lab ? $('.lg-engine-name', lab) : null;
        return nm ? nm.textContent : (a.getAttribute('data-alias') || '');
    }
    function applySou(el) {
        if (!el || !input) { return; }
        var hint = el.getAttribute('data-hint');
        input.placeholder = hint ? hint : '聚焦搜索';
        var spotEngine = document.getElementById('lg-spot-engine');
        if (spotEngine) { spotEngine.textContent = activeEngineName(); }
    }
    function markActive(r) {
        for (var i = 0; i < souRadios.length; i++) {
            var lab = souRadios[i].parentNode;
            if (!lab) { continue; }
            if (souRadios[i] === r) { add(lab, 'is-active'); } else { del(lab, 'is-active'); }
        }
    }

    /* 构造搜索 URL：兼容 {keyword} / {q} / %s 占位符，否则直接拼接 */
    function buildSearchUrl(template, keyword) {
        var kw = encodeURIComponent(keyword);
        if (!template) { return ''; }
        if (template.indexOf('{keyword}') > -1) { return template.split('{keyword}').join(kw); }
        if (template.indexOf('{q}') > -1)        { return template.split('{q}').join(kw); }
        if (template.indexOf('%s') > -1)         { return template.split('%s').join(kw); }
        if (template.indexOf('?') > -1 || template.indexOf('=') > -1) { return template + kw; }
        return template + kw;
    }

    /* 真正打开搜索结果：先 window.open，被拦截则用隐藏 form target=_blank 兜底 */
    function openInNewTab(url) {
        if (!url) { return false; }
        var win = null;
        try { win = window.open(url, '_blank', 'noopener,noreferrer'); } catch (e) { win = null; }
        if (win) { return true; }
        /* 兜底：动态表单提交，新窗口打开 */
        try {
            var form = document.createElement('form');
            form.method = 'GET';
            form.action = url;
            form.target = '_blank';
            form.rel = 'noopener noreferrer';
            form.style.cssText = 'position:fixed;left:-9999px;top:-9999px;width:1px;height:1px;opacity:0;';
            document.body.appendChild(form);
            form.submit();
            window.setTimeout(function () {
                if (form.parentNode) { form.parentNode.removeChild(form); }
            }, 200);
            return true;
        } catch (e2) {
            /* 最后兜底：当前页跳转 */
            try { window.location.href = url; } catch (e3) { /* ignore */ }
            return false;
        }
    }

    function engineSearch(keyword) {
        var a = getActive();
        if (!a) { return; }
        var url = buildSearchUrl(a.value, keyword);
        if (!url) { return; }
        try { window.localStorage.setItem(STORE_SOU, a.value); } catch (e) { /* ignore */ }
        openInNewTab(url);
    }

    var savedSou = null;
    try { savedSou = window.localStorage.getItem(STORE_SOU); } catch (e) { savedSou = null; }
    if (savedSou && souRadios.length) {
        for (var s = 0; s < souRadios.length; s++) {
            if (souRadios[s].value === savedSou) { souRadios[s].checked = true; break; }
        }
    }
    markActive(getActive());
    applySou(getActive());

    for (var ri = 0; ri < souRadios.length; ri++) {
        (function (radio) {
            on(radio, 'change', function () {
                markActive(radio);
                applySou(radio);
                syncEngMenu();
                try { window.localStorage.setItem(STORE_SOU, radio.value); } catch (e) { /* ignore */ }
            });
        })(souRadios[ri]);
    }
    /* 点击 label 时也确保立即写记忆（change 在 radio 切到相同值时不会触发） */
    var engineLabels = $$('.lg-engine');
    for (var eli = 0; eli < engineLabels.length; eli++) {
        (function (lab) {
            on(lab, 'click', function () {
                var radio = $('input[name="sou"]', lab);
                if (radio) {
                    markActive(radio);
                    applySou(radio);
                    syncEngMenu();
                    try { window.localStorage.setItem(STORE_SOU, radio.value); } catch (e) { /* ignore */ }
                }
            });
        })(engineLabels[eli]);
    }

    if (form) {
        on(form, 'submit', function (e) {
            if (e && e.preventDefault) { e.preventDefault(); }
            closeEngMenu();
            if (sug && sug.hide) { sug.hide(); }
            if (!input) { return; }
            var q = input.value.replace(/^\s+|\s+$/g, '');
            if (q === '') { input.focus(); return; }
            engineSearch(q);
            input.value = '';
            input.focus();
        });
    }

    /* ---------------- 搜索引擎联想词（百度 JSONP，默认优先级最高） ----------------
     * 默认仅用百度一种联想源（与所选引擎无关，仅作输入补全，提交仍走引擎 value）。
     * 若用户明确指定其它源，可在此改为对应 API；此处按 §3.8 默认百度。 */
    var SUG_CB = '_lgSugCb_';
    var sugSeq = 0;
    function Sug(inputEl, boxEl, onPick) {
        if (!inputEl || !boxEl) { return null; }
        var items = [];      /* 混合：{type:'site',url,name,desc,glyph,hue} | {type:'suggest',text} */
        var cursor = -1;
        var lastKw = '';
        var timer = null;
        var scriptTag = null;
        var cbName = '';
        var reqId = 0;
        var siteIndex = null;

        /* —— 站内搜索：复用书签瓦片（与 Spotlight 同源，客户端过滤，零后端） —— */
        function buildSiteIndex() {
            siteIndex = [];
            var ts = $$('.lg-tile');
            for (var i = 0; i < ts.length; i++) {
                var nm = $('.lg-tile-name', ts[i]);
                var ds = $('.lg-tile-desc', ts[i]);
                var gl = $('.lg-tile-glyph', ts[i]);
                siteIndex.push({
                    name: nm ? (nm.textContent || '') : '',
                    desc: ds ? (ds.textContent || '') : '',
                    url: ts[i].getAttribute('href') || '',
                    glyph: gl ? gl.innerHTML : '',
                    hue: ts[i].style ? (ts[i].style.getPropertyValue('--lg-h') || '212') : '212'
                });
            }
        }
        function matchSite(kw) {
            if (!siteIndex) { buildSiteIndex(); }
            if (kw === '') { return []; }
            var k = kw.toLowerCase();
            var out = [];
            for (var i = 0; i < siteIndex.length; i++) {
                var it = siteIndex[i];
                if (it.name.toLowerCase().indexOf(k) > -1
                    || it.desc.toLowerCase().indexOf(k) > -1
                    || it.url.toLowerCase().indexOf(k) > -1) {
                    out.push(it);
                    if (out.length >= 5) { break; }   /* 站内结果最多 5 条，避免挤压联想词 */
                }
            }
            return out;
        }

        function clearBox() { items = []; cursor = -1; boxEl.innerHTML = ''; }
        function hideBox() { boxEl.hidden = true; clearBox(); }
        function showBox() { boxEl.hidden = !items.length; }

        function setCursor(idx) {
            var rows = $$('.lg-sug-item', boxEl);
            cursor = idx;
            for (var i = 0; i < rows.length; i++) {
                var ridx = parseInt(rows[i].getAttribute('data-idx'), 10);
                if (ridx === idx) { add(rows[i], 'is-cursor'); } else { del(rows[i], 'is-cursor'); }
            }
        }
        function render() {
            boxEl.innerHTML = '';   /* 仅清空 DOM，保留 items（由 finish 设置） */
            cursor = -1;
            var prevType = '';
            for (var i = 0; i < items.length; i++) {
                (function (it, idx) {
                    if (it.type !== prevType) {
                        var head = document.createElement('div');
                        head.className = 'lg-sug-head';
                        head.textContent = it.type === 'site' ? '站内搜索' : '搜索建议';
                        boxEl.appendChild(head);
                        prevType = it.type;
                    }
                    var row = document.createElement('div');
                    row.className = 'lg-sug-item' + (it.type === 'site' ? ' lg-sug-site' : '');
                    row.setAttribute('role', 'option');
                    row.setAttribute('data-idx', String(idx));
                    if (it.type === 'site') {
                        if (row.style && row.style.setProperty) { row.style.setProperty('--lg-h', it.hue); }
                        var g = document.createElement('span');
                        g.className = 'lg-sug-site-glyph';
                        g.innerHTML = it.glyph;
                        var b = document.createElement('span');
                        b.className = 'lg-sug-site-body';
                        var n = document.createElement('span');
                        n.className = 'lg-sug-site-name';
                        n.textContent = it.name;
                        var m = document.createElement('span');
                        m.className = 'lg-sug-site-meta';
                        m.textContent = it.desc || it.url;
                        b.appendChild(n);
                        b.appendChild(m);
                        row.appendChild(g);
                        row.appendChild(b);
                    } else {
                        row.textContent = it.text || '';
                    }
                    on(row, 'mousedown', function (e) {
                        if (e && e.preventDefault) { e.preventDefault(); }  /* 防输入框失焦 */
                        pick(it);
                    });
                    on(row, 'mouseenter', function () { setCursor(idx); });
                    boxEl.appendChild(row);
                })(items[i], i);
            }
            showBox();
        }
        function pick(it) {
            if (!it) { return; }
            if (it.type === 'site') {
                hideBox();
                if (it.url) { window.open(it.url, '_blank'); }
                inputEl.focus();
                return;
            }
            inputEl.value = it.text;
            hideBox();
            if (onPick) { onPick(it.text); }
            inputEl.focus();
        }
        function cleanupScript() {
            if (scriptTag && scriptTag.parentNode) { scriptTag.parentNode.removeChild(scriptTag); }
            scriptTag = null;
            /* 关键修复：此处【不可】删除 window[cbName]。
               否则被本请求替换、但仍可能在途（HTTP 缓存 / 慢网络）的旧脚本响应执行时，
               会抛 ReferenceError: _lgSugCb_N is not defined。
               旧回调由其自身的 finish() 在触发后自行清除（见 fetchSug）。 */
        }
        function fetchSug(kw) {
            reqId++;
            var myId = reqId;
            cleanupScript();
            cbName = SUG_CB + (sugSeq++);
            var s = document.createElement('script');
            var done = false;
            function finish(arr) {
                if (done) { return; }
                done = true;
                if (s && s.parentNode) { s.parentNode.removeChild(s); }  /* 仅移除本次请求的脚本节点 */
                if (myId !== reqId) { return; }   /* 仅采用最新一次请求 */
                var list = [];
                var sites = matchSite(kw);
                for (var i = 0; i < sites.length; i++) {
                    list.push({ type: 'site', url: sites[i].url, name: sites[i].name, desc: sites[i].desc, glyph: sites[i].glyph, hue: sites[i].hue });
                }
                var sug = (arr && arr.length) ? arr.slice(0, 10) : [];
                for (var j = 0; j < sug.length; j++) {
                    list.push({ type: 'suggest', text: sug[j] });
                }
                items = list;
                render();
            }
            window[cbName] = function (data) {
                var arr = (data && data.s) ? data.s : [];
                try { delete window[cbName]; } catch (e) {}   /* 仅在真实响应触发时清除自身回调 */
                finish(arr);
            };
            s.src = 'https://suggestion.baidu.com/su?wd=' + encodeURIComponent(kw) + '&cb=' + cbName;
            s.onerror = function () { finish([]); };
            scriptTag = s;
            document.body.appendChild(s);
            window.setTimeout(function () { finish([]); }, 1600);  /* 超时静默降级，不阻塞主搜索 */
        }
        function onChange() {
            var kw = (inputEl.value || '').replace(/^\s+|\s+$/g, '');
            if (kw === '') { hideBox(); lastKw = ''; return; }
            if (kw === lastKw) { return; }
            lastKw = kw;
            if (timer) { window.clearTimeout(timer); }
            timer = window.setTimeout(function () { fetchSug(kw); }, 250);  /* 输入防抖 */
        }
        function scrollCursor() {
            var rows = boxEl.getElementsByTagName('div');
            for (var i = 0; i < rows.length; i++) {
                if (has(rows[i], 'lg-sug-item') && i === cursor) {
                    if (rows[i].scrollIntoView) { try { rows[i].scrollIntoView({ block: 'nearest' }); } catch (e2) {} }
                    break;
                }
            }
        }
        on(inputEl, 'input', onChange);
        on(inputEl, 'focus', function () { if (lastKw !== '') { showBox(); } });
        on(inputEl, 'keydown', function (e) {
            if (e.keyCode === 40) { /* ↓ */
                if (items.length) { e.preventDefault(); setCursor(cursor < 0 ? 0 : Math.min(cursor + 1, items.length - 1)); scrollCursor(); }
            } else if (e.keyCode === 38) { /* ↑ */
                if (items.length) { e.preventDefault(); setCursor(cursor < 0 ? 0 : Math.max(cursor - 1, 0)); scrollCursor(); }
            } else if (e.keyCode === 27) { /* Esc */
                if (!boxEl.hidden) { e.preventDefault(); hideBox(); }
            } else if (e.keyCode === 13) { /* Enter：有高亮项则拾取，否则交给外层表单提交 */
                if (!boxEl.hidden && cursor > -1 && items[cursor]) { e.preventDefault(); pick(items[cursor]); }
            }
        });
        on(inputEl, 'blur', function () {
            window.setTimeout(function () {
                if (document.activeElement !== inputEl) { hideBox(); }
            }, 150);  /* 延迟以便 mousedown 拾取 */
        });
        on(document, 'click', function (e) {
            if (boxEl.hidden) { return; }
            var t = e.target;
            while (t && t !== boxEl && t !== inputEl) { t = t.parentNode; }
            if (t === boxEl || t === inputEl) { return; }
            hideBox();
        });
        return { hide: hideBox };
    }
    var sug = null;
    var sugBox = document.getElementById('lg-sug');
    if (sugBox) {
        sug = Sug(input, sugBox, function (text) {
            engineSearch(text);
            if (input) { input.value = ''; }
        });
    }

    /* 闪烁光标：聚焦且为空时显示 */
    if (input && caretBox) {
        function syncCaret() {
            if (input.value === '') { add(caretBox, 'is-empty'); } else { del(caretBox, 'is-empty'); }
        }
        on(input, 'focus', function () { add(caretBox, 'is-focus'); syncCaret(); });
        on(input, 'blur', function () { del(caretBox, 'is-focus'); });
        on(input, 'input', syncCaret);
        syncCaret();
    }

    /* ---------------- 移动端搜索引擎下拉（放大镜触发） ---------------- */
    var engToggle = document.getElementById('lg-eng-toggle');
    var engMenu = document.getElementById('lg-eng-menu');
    var CHECK_SVG = '<svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>';

    function getActiveIdx() {
        for (var i = 0; i < souRadios.length; i++) { if (souRadios[i].checked) { return i; } }
        return 0;
    }
    function syncEngMenu() {
        if (!engMenu) { return; }
        var items = engMenu.getElementsByTagName('button');
        var act = String(getActiveIdx());
        for (var i = 0; i < items.length; i++) {
            if (items[i].getAttribute('data-idx') === act) { add(items[i], 'is-on'); } else { del(items[i], 'is-on'); }
        }
    }
    function buildEngMenu() {
        if (!engMenu || !souRadios.length) { return; }
        engMenu.innerHTML = '';
        for (var i = 0; i < souRadios.length; i++) {
            (function (radio, idx) {
                var lab = radio.parentNode;
                var ico = lab ? $('.lg-engine-ico', lab) : null;
                var nm = lab ? $('.lg-engine-name', lab) : null;
                var item = document.createElement('button');
                item.type = 'button';
                item.className = 'lg-eng-item';
                item.setAttribute('role', 'option');
                item.setAttribute('data-idx', String(idx));
                var ic = document.createElement('span'); ic.className = 'lg-eng-item-ico';
                if (ico) { ic.innerHTML = ico.innerHTML; }
                item.appendChild(ic);
                var name = document.createElement('span'); name.className = 'lg-eng-item-name';
                name.textContent = nm ? nm.textContent : (radio.getAttribute('data-alias') || '');
                item.appendChild(name);
                var tick = document.createElement('span'); tick.className = 'lg-eng-item-tick'; tick.innerHTML = CHECK_SVG;
                item.appendChild(tick);
                if (radio.checked) { add(item, 'is-on'); }
                on(item, 'click', function () {
                    radio.checked = true;
                    markActive(radio);
                    applySou(radio);
                    try { window.localStorage.setItem(STORE_SOU, radio.value); } catch (e2) { /* ignore */ }
                    closeEngMenu();
                });
                engMenu.appendChild(item);
            })(souRadios[i], i);
        }
    }
    function openEngMenu() {
        if (!engMenu || !engToggle) { return; }
        syncEngMenu();
        add(engMenu, 'is-open'); add(engToggle, 'is-open');
        engToggle.setAttribute('aria-expanded', 'true');
    }
    function closeEngMenu() {
        if (!engMenu || !engToggle) { return; }
        del(engMenu, 'is-open'); del(engToggle, 'is-open');
        engToggle.setAttribute('aria-expanded', 'false');
    }
    function toggleEngMenu() {
        if (!has(body, 'is-mobile')) { return; }   /* 仅移动端启用 */
        if (engMenu && has(engMenu, 'is-open')) { closeEngMenu(); } else { openEngMenu(); }
    }
    if (engToggle) {
        on(engToggle, 'click', function (e) { if (e && e.preventDefault) { e.preventDefault(); } toggleEngMenu(); });
    }
    if (engMenu) {
        on(document, 'click', function (e) {
            if (!engMenu || !has(engMenu, 'is-open')) { return; }
            if (closest(e.target, 'lg-eng-menu')) { return; }
            /* 排除放大镜按钮本身及其内部子元素（按钮只设了 id，closest 按 class 查不到） */
            if (engToggle && (e.target === engToggle || engToggle.contains(e.target))) { return; }
            closeEngMenu();
        });
    }
    buildEngMenu();
    syncEngMenu();

    /* ---------------- 移动端把记事本/待办搬到底部 ---------------- */
    var wNotes = document.getElementById('lg-widget-notes');
    var wTodo = document.getElementById('lg-widget-todo');
    var wStatus = document.getElementById('lg-statusbar');
    var wAside = document.getElementById('lg-widgets');
    var wBottom = document.getElementById('lg-bottomwidgets');
    function placeWidgets() {
        if (!wNotes || !wTodo || !wAside || !wBottom) { return; }
        if (has(body, 'is-mobile')) {
            if (wNotes.parentNode !== wBottom) { wBottom.appendChild(wNotes); }
            if (wTodo.parentNode !== wBottom) { wBottom.appendChild(wTodo); }
        } else {
            if (wNotes.parentNode !== wAside) { wAside.appendChild(wNotes); }
            if (wTodo.parentNode !== wAside) { wAside.appendChild(wTodo); }
        }
    }

    /* ---------------- 时钟与电池 ---------------- */
    var cMenu = document.getElementById('lg-clock-menu');
    var cStatus = document.getElementById('lg-clock-status');
    var cWidget = document.getElementById('lg-clock-widget');
    var dWidget = document.getElementById('lg-date-widget');
    var WEEK = ['日', '一', '二', '三', '四', '五', '六'];

    function tick() {
        var d = new Date();
        var h = d.getHours(), m = d.getMinutes();
        if (cMenu) {
            cMenu.textContent = (d.getMonth() + 1) + '月' + d.getDate() + '日 周' + WEEK[d.getDay()] + ' ' + pad(h) + ':' + pad(m);
        }
        if (cStatus) { cStatus.textContent = h + ':' + pad(m); }
        if (cWidget) { cWidget.textContent = h + ':' + pad(m); }
        if (dWidget) {
            dWidget.textContent = (d.getMonth() + 1) + '月' + d.getDate() + '日 星期' + WEEK[d.getDay()];
        }
    }
    if (cMenu || cStatus || cWidget || dWidget) { tick(); window.setInterval(tick, 1000); }

    function setBattery(pct) {
        var levels = $$('[data-lg-batt]');
        for (var i = 0; i < levels.length; i++) {
            levels[i].style.width = Math.max(8, Math.min(100, pct)) + '%';
        }
    }
    setBattery(78);
    if (navigator && typeof navigator.getBattery === 'function') {
        try {
            navigator.getBattery().then(function (b) {
                setBattery(Math.round(b.level * 100));
            });
        } catch (e) { /* 忽略 */ }
    }

    /* ---------------- 图标：色相散列 + 按压反馈 ---------------- */
    var tiles = $$('.lg-tile');

    function hueOf(str) {
        var h = 7, i;
        for (i = 0; i < str.length; i++) { h = (h * 31 + str.charCodeAt(i)) % 360; }
        return h;
    }
    function paintTiles() {
        for (var i = 0; i < tiles.length; i++) {
            var nm = $('.lg-tile-name', tiles[i]);
            var text = nm ? (nm.textContent || '') : '';
            var hue = hueOf(text + '#' + i);
            if (tiles[i].style && typeof tiles[i].style.setProperty === 'function') {
                tiles[i].style.setProperty('--lg-h', String(hue));
            }
        }
    }
    paintTiles();

    /* ---------------- 分组分页 ---------------- */
    var pager = document.getElementById('lg-pager');
    var track = document.getElementById('lg-track');
    var pages = track ? $$('.lg-page', track) : [];
    var railList = document.getElementById('lg-rail-list');
    var pills = document.getElementById('lg-pills');
    var dots = document.getElementById('lg-dots');
    var current = 0;

    function pageName(page, i) {
        var nm = $('.lg-page-name', page);
        return (nm && nm.textContent) ? nm.textContent : ('分组 ' + (i + 1));
    }

    function buildNav() {
        var i, li, btn, sp, dot;
        for (i = 0; i < pages.length; i++) {
            var name = pageName(pages[i], i);
            var count = $$('.lg-tile', pages[i]).length;
            var cnt = $('.lg-page-count', pages[i]);
            if (cnt) { cnt.textContent = count + ' 个'; }

            if (railList) {
                li = document.createElement('li');
                btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'lg-rail-btn';
                btn.setAttribute('data-go', String(i));
                btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
                    + '<circle cx="12" cy="12" r="8.4"/><path d="M15 9l-1.7 4.4L9 15l1.7-4.4z"/></svg>';
                sp = document.createElement('span');
                sp.textContent = name;
                btn.appendChild(sp);
                li.appendChild(btn);
                railList.appendChild(li);
            }
            if (pills) {
                btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'lg-pill';
                btn.setAttribute('data-go', String(i));
                btn.textContent = name;
                pills.appendChild(btn);
            }
            if (dots) {
                dot = document.createElement('button');
                dot.type = 'button';
                dot.className = 'lg-dot';
                dot.setAttribute('data-go', String(i));
                dot.setAttribute('aria-label', name);
                dots.appendChild(dot);
            }
        }
    }

    function layoutPager(instant) {
        if (!pager || !track || !pages.length) { return; }
        var page = pages[current];
        if (!page) { return; }
        var h = page.offsetHeight;
        if (h > 0) {
            if (instant) {
                var old = track.style.transition;
                track.style.transition = 'none';
                track.style.height = h + 'px';
                /* 强制重排后再恢复过渡，避免首帧动画 */
                void track.offsetHeight;
                track.style.transition = old;
            } else {
                track.style.height = h + 'px';
            }
        }
    }

    function go(i, silent) {
        if (!pages.length) { return; }
        if (i < 0) { i = 0; }
        if (i > pages.length - 1) { i = pages.length - 1; }
        current = i;
        if (track) {
            track.style.transition = '';
            track.style.transform = 'translateX(' + (-i * 100) + '%)';
        }
        var k;
        for (k = 0; k < pages.length; k++) {
            if (k === i) { add(pages[k], 'is-current'); } else { del(pages[k], 'is-current'); }
        }
        var dotsAll = $$('.lg-dot', dots || document);
        for (k = 0; k < dotsAll.length; k++) {
            if (k === i) { add(dotsAll[k], 'is-active'); } else { del(dotsAll[k], 'is-active'); }
        }
        var pillsAll = $$('.lg-pill', pills || document);
        for (k = 0; k < pillsAll.length; k++) {
            if (k === i) { add(pillsAll[k], 'is-active'); } else { del(pillsAll[k], 'is-active'); }
        }
        var railAll = $$('.lg-rail-btn', railList || document);
        for (k = 0; k < railAll.length; k++) {
            if (k === i) { add(railAll[k], 'is-active'); } else { del(railAll[k], 'is-active'); }
        }
        layoutPager(false);
        if (!silent && pills) {
            var act = pillsAll[i];
            if (act && act.scrollIntoView) {
                try { act.scrollIntoView({ block: 'nearest', inline: 'center' }); } catch (e) { /* 旧浏览器忽略 */ }
            }
        }
    }

    buildNav();
    if (pages.length) {
        go(0, true);
        /* 图标注入 / 图片加载后重新量高 */
        window.setTimeout(function () { layoutPager(true); }, 260);
        window.setTimeout(function () { layoutPager(true); }, 1200);
        on(window, 'load', function () { layoutPager(true); });
        on(window, 'resize', function () { layoutPager(true); });
    }

    /* 导航点击 */
    var goBtns = $$('[data-go]');
    for (var gi = 0; gi < goBtns.length; gi++) {
        (function (btn) {
            on(btn, 'click', function () { go(parseInt(btn.getAttribute('data-go'), 10)); });
        })(goBtns[gi]);
    }

    /* 触摸滑动 */
    if (pager && track) {
        var sx = 0, sy = 0, dx = 0, dragging = false, w = 0, decided = false;
        on(pager, 'touchstart', function (e) {
            if (!e.touches || e.touches.length !== 1) { return; }
            sx = e.touches[0].clientX;
            sy = e.touches[0].clientY;
            dx = 0; dragging = true; decided = false;
            w = track.clientWidth || 1;
        }, false);
        on(pager, 'touchmove', function (e) {
            if (!dragging || !e.touches) { return; }
            var mx = e.touches[0].clientX - sx;
            var my = e.touches[0].clientY - sy;
            if (!decided) {
                if (Math.abs(mx) < 6 && Math.abs(my) < 6) { return; }
                decided = true;
                if (Math.abs(my) > Math.abs(mx)) { dragging = false; return; }
            }
            dx = mx;
            if (e.cancelable) { e.preventDefault(); }
            track.style.transition = 'none';
            track.style.transform = 'translateX(' + (-current * w + dx) + 'px)';
        }, false);
        on(pager, 'touchend', function () {
            if (!dragging) { return; }
            dragging = false;
            track.style.transition = '';
            if (Math.abs(dx) > Math.max(46, w * 0.14)) {
                go(current + (dx < 0 ? 1 : -1));
            } else {
                go(current);
            }
            dx = 0;
        }, false);
    }

    /* 键盘翻页 */
    on(document, 'keydown', function (e) {
        var tag = (e.target && e.target.tagName) ? e.target.tagName.toLowerCase() : '';
        if (tag === 'input' || tag === 'textarea') { return; }
        if (e.keyCode === 37) { go(current - 1); }
        else if (e.keyCode === 39) { go(current + 1); }
    });

    /* ---------------- Dock 放大镜 ---------------- */
    var dockInner = document.getElementById('lg-dock-inner');
    if (dockInner && !has(body, 'is-mobile')) {
        var dockItems = $$('.lg-dock-item', dockInner);
        function clearMag() {
            for (var i = 0; i < dockItems.length; i++) {
                del(dockItems[i], 'is-mag');
                del(dockItems[i], 'is-near');
            }
        }
        for (var di = 0; di < dockItems.length; di++) {
            (function (item, idx) {
                on(item, 'mouseenter', function () {
                    clearMag();
                    add(item, 'is-mag');
                    if (dockItems[idx - 1]) { add(dockItems[idx - 1], 'is-near'); }
                    if (dockItems[idx + 1]) { add(dockItems[idx + 1], 'is-near'); }
                });
            })(dockItems[di], di);
        }
        on(dockInner, 'mouseleave', clearMag);
    }

    /* ---------------- 覆盖层调度 ---------------- */
    var spotlight = document.getElementById('lg-spotlight');
    var control = document.getElementById('lg-control');
    var settings = document.getElementById('lg-settings');
    var spotInput = document.getElementById('lg-spot-input');
    var spotResults = document.getElementById('lg-spot-results');
    var ctxMenu = document.getElementById('lg-ctx');

    function closeAll(except) {
        if (except !== 'spotlight' && spotlight) { del(spotlight, 'is-open'); }
        if (except !== 'control' && control) { del(control, 'is-open'); }
        if (except !== 'settings' && settings) { del(settings, 'is-open'); }
        if (ctxMenu) { del(ctxMenu, 'is-open'); }
        stopJiggle();
    }

    /* ---------------- 聚焦搜索 ---------------- */
    var index = null;
    function buildIndex() {
        index = [];
        for (var i = 0; i < tiles.length; i++) {
            var nm = $('.lg-tile-name', tiles[i]);
            var ds = $('.lg-tile-desc', tiles[i]);
            var gl = $('.lg-tile-glyph', tiles[i]);
            index.push({
                el: tiles[i],
                name: nm ? (nm.textContent || '') : '',
                desc: ds ? (ds.textContent || '') : '',
                url: tiles[i].getAttribute('href') || '',
                glyph: gl ? gl.innerHTML : '',
                hue: tiles[i].style ? (tiles[i].style.getPropertyValue('--lg-h') || '212') : '212'
            });
        }
    }

    function renderResults(list, keyword) {
        if (!spotResults) { return; }
        spotResults.innerHTML = '';
        if (!list.length) {
            var empty = document.createElement('div');
            empty.className = 'lg-spot-empty';
            empty.textContent = keyword
                ? '没有找到「' + keyword + '」，按 Enter 使用' + activeEngineName() + '搜索'
                : '输入关键词以过滤站点，或按 Enter 使用' + activeEngineName() + '搜索';
            spotResults.appendChild(empty);
            return;
        }
        for (var i = 0; i < list.length; i++) {
            var it = list[i];
            var row = document.createElement('a');
            row.className = 'lg-spot-item';
            row.href = it.url;
            row.target = '_blank';
            row.rel = 'nofollow noopener';
            row.setAttribute('data-idx', String(i));
            row.style.setProperty('--lg-h', it.hue);
            var g = document.createElement('span');
            g.className = 'lg-spot-item-glyph';
            g.innerHTML = it.glyph;
            var b = document.createElement('span');
            b.className = 'lg-spot-item-body';
            var n = document.createElement('span');
            n.className = 'lg-spot-item-name';
            n.textContent = it.name;
            var m = document.createElement('span');
            m.className = 'lg-spot-item-meta';
            m.textContent = it.desc || it.url;
            b.appendChild(n);
            b.appendChild(m);
            row.appendChild(g);
            row.appendChild(b);
            spotResults.appendChild(row);
        }
    }

    function filterSpot() {
        if (!index) { buildIndex(); }
        var kw = (spotInput.value || '').replace(/^\s+|\s+$/g, '').toLowerCase();
        var list = [];
        if (kw === '') {
            list = index.slice(0, 30);
        } else {
            for (var i = 0; i < index.length; i++) {
                var it = index[i];
                if (it.name.toLowerCase().indexOf(kw) > -1
                    || it.desc.toLowerCase().indexOf(kw) > -1
                    || it.url.toLowerCase().indexOf(kw) > -1) {
                    list.push(it);
                    if (list.length >= 60) { break; }
                }
            }
        }
        renderResults(list, kw);
        spotCursor = -1;
    }

    var spotCursor = -1;
    function moveCursor(step) {
        if (!spotResults) { return; }
        var rows = $$('.lg-spot-item', spotResults);
        if (!rows.length) { return; }
        spotCursor += step;
        if (spotCursor < 0) { spotCursor = rows.length - 1; }
        if (spotCursor > rows.length - 1) { spotCursor = 0; }
        for (var i = 0; i < rows.length; i++) {
            if (i === spotCursor) { add(rows[i], 'is-cursor'); } else { del(rows[i], 'is-cursor'); }
        }
        if (rows[spotCursor] && rows[spotCursor].scrollIntoView) {
            try { rows[spotCursor].scrollIntoView({ block: 'nearest' }); } catch (e) { /* ignore */ }
        }
    }

    function openSpot() {
        if (!spotlight) { return; }
        closeAll('spotlight');
        add(spotlight, 'is-open');
        if (!index) { buildIndex(); }
        filterSpot();
        if (spotInput) {
            window.setTimeout(function () { try { spotInput.focus(); } catch (e) { /* ignore */ } }, 40);
        }
    }
    function closeSpot() { if (spotlight) { del(spotlight, 'is-open'); } }

    if (spotInput) {
        on(spotInput, 'input', filterSpot);
        on(spotInput, 'keydown', function (e) {
            if (e.keyCode === 40) { e.preventDefault(); moveCursor(1); }
            else if (e.keyCode === 38) { e.preventDefault(); moveCursor(-1); }
            else if (e.keyCode === 27) { closeSpot(); }
        });
    }
    var spotForm = document.getElementById('lg-spot-form');
    if (spotForm) {
        on(spotForm, 'submit', function (e) {
            if (e.preventDefault) { e.preventDefault(); }
            var rows = $$('.lg-spot-item', spotResults || document);
            if (rows.length && spotCursor > -1) {
                window.open(rows[spotCursor].getAttribute('href'), '_blank');
                closeSpot();
                return;
            }
            var kw = spotInput ? spotInput.value.replace(/^\s+|\s+$/g, '') : '';
            if (kw !== '') { engineSearch(kw); }
            closeSpot();
        });
    }
    var spotClear = document.getElementById('lg-spot-clear');
    if (spotClear) {
        on(spotClear, 'click', function () {
            if (spotInput) { spotInput.value = ''; filterSpot(); spotInput.focus(); }
        });
    }
    if (spotResults) {
        on(spotResults, 'click', function (e) {
            var t = e.target;
            while (t && t !== spotResults && !has(t, 'lg-spot-item')) { t = t.parentNode; }
            if (t && has(t, 'lg-spot-item')) {
                if (e.preventDefault) { e.preventDefault(); }
                window.open(t.getAttribute('href'), '_blank');
                closeSpot();
            }
        });
    }

    /* ---------------- 控制中心 ---------------- */
    var ccQuick = document.getElementById('lg-cc-quick');
    function buildControlQuick() {
        if (!ccQuick || !pages.length) { return; }
        var first = pages[0];
        var items = $$('.lg-tile', first);
        ccQuick.innerHTML = '';
        for (var i = 0; i < items.length && i < 8; i++) {
            var nm = $('.lg-tile-name', items[i]);
            var gl = $('.lg-tile-glyph', items[i]);
            var a = document.createElement('a');
            a.className = 'lg-cc-tile';
            a.href = items[i].getAttribute('href') || '#';
            a.target = '_blank';
            a.rel = 'nofollow noopener';
            var g = document.createElement('span');
            g.className = 'lg-cc-tile-ico';
            if (gl) { g.innerHTML = gl.innerHTML; }
            var s = document.createElement('span');
            s.className = 'lg-cc-tile-name';
            s.textContent = nm ? nm.textContent : '';
            a.appendChild(g);
            a.appendChild(s);
            ccQuick.appendChild(a);
        }
    }
    buildControlQuick();

    function toggleControl() {
        if (!control) { return; }
        if (has(control, 'is-open')) { del(control, 'is-open'); }
        else { closeAll('control'); add(control, 'is-open'); }
    }

    /* ---------------- 设置面板 ---------------- */
    function openSettings() {
        if (!settings) { return; }
        closeAll('settings');
        add(settings, 'is-open');
        syncControls();
        refreshHiddenCount();
    }

    /* ---------------- 全站动作按钮 ---------------- */
    var actions = $$('[data-lg-action]');
    for (var ai = 0; ai < actions.length; ai++) {
        (function (btn) {
            on(btn, 'click', function (e) {
                if (e.preventDefault) { e.preventDefault(); }
                var act = btn.getAttribute('data-lg-action');
                if (act === 'spotlight') { openSpot(); }
                else if (act === 'control') { toggleControl(); }
                else if (act === 'settings') { openSettings(); }
                else if (act === 'prev') { go(current - 1); }
                else if (act === 'next') { go(current + 1); }
            });
        })(actions[ai]);
    }
    var closers = $$('[data-lg-close]');
    for (var ci = 0; ci < closers.length; ci++) {
        (function (btn) {
            on(btn, 'click', function (e) {
                if (e.preventDefault) { e.preventDefault(); }
                var which = btn.getAttribute('data-lg-close');
                if (which === 'spotlight') { closeSpot(); }
                else if (which === 'settings' && settings) { del(settings, 'is-open'); }
            });
        })(closers[ci]);
    }

    on(document, 'keydown', function (e) {
        if (e.keyCode === 27) { closeAll(null); closeEngMenu(); }
        /* Cmd/Ctrl + K 唤起聚焦搜索 */
        if ((e.metaKey || e.ctrlKey) && e.keyCode === 75) {
            if (e.preventDefault) { e.preventDefault(); }
            openSpot();
        }
    });

    /* 点击空白处收起控制中心 */
    on(document, 'click', function (e) {
        if (!control || !has(control, 'is-open')) { return; }
        var t = e.target;
        while (t) {
            if (t === control) { return; }
            if (t.getAttribute && t.getAttribute('data-lg-action') === 'control') { return; }
            t = t.parentNode;
        }
        del(control, 'is-open');
    });

    /* ---------------- 偏好控件绑定 ---------------- */
    var segGroups = $$('[data-lg-seg]');
    for (var sg = 0; sg < segGroups.length; sg++) {
        (function (group) {
            var key = group.getAttribute('data-lg-seg');
            var btns = group.getElementsByTagName('button');
            for (var b = 0; b < btns.length; b++) {
                (function (btn) {
                    on(btn, 'click', function () {
                        var v = btn.getAttribute('data-value');
                        if (key === 'cols') {
                            var c = parseInt(v, 10) || 4;
                            prefs.cols = Math.max(3, Math.min(6, c));
                        } else {
                            prefs[key] = v;
                        }
                        applyPrefs();
                    });
                })(btns[b]);
            }
        })(segGroups[sg]);
    }

    var togglesAll = $$('[data-lg-toggle]');
    for (var ti = 0; ti < togglesAll.length; ti++) {
        (function (tg) {
            function flip() {
                var t = tg.getAttribute('data-lg-toggle');
                if (t === 'dark') { prefs.scheme = isDark() ? 'light' : 'dark'; }
                else if (t === 'motion') { prefs.motion = !prefs.motion; }
                else if (t === 'clock') { prefs.clock = !prefs.clock; }
                else if (t === 'yan') { prefs.yan = !prefs.yan; }
                else if (t === 'note') { prefs.note = !prefs.note; }
                else if (t === 'notes') { prefs.notes = !prefs.notes; }
                else if (t === 'todo') { prefs.todo = !prefs.todo; }
                else if (t === 'statusbar') { prefs.statusbar = !prefs.statusbar; }
                applyPrefs();
            }
            on(tg, 'click', flip);
            on(tg, 'keydown', function (e) {
                if (e.keyCode === 13 || e.keyCode === 32) { if (e.preventDefault) { e.preventDefault(); } flip(); }
            });
        })(togglesAll[ti]);
    }

    /* 桌面组件 macOS 窗口控制：红=关闭 黄=最小化 绿=展开（移动端隐藏） */
    function wKeyToPref(id) {
        if (id === 'lg-widget-clock') { return 'clock'; }
        if (id === 'lg-widget-yan') { return 'yan'; }
        if (id === 'lg-widget-note') { return 'note'; }
        if (id === 'lg-widget-notes') { return 'notes'; }
        if (id === 'lg-widget-todo') { return 'todo'; }
        return null;
    }
    var wCloseBtns = $$('[data-wclose]');
    for (var wci = 0; wci < wCloseBtns.length; wci++) {
        (function (b) {
            on(b, 'click', function (e) {
                if (e.preventDefault) { e.preventDefault(); }
                if (e.stopPropagation) { e.stopPropagation(); }
                var id = b.getAttribute('data-wclose');
                var el = document.getElementById(id);
                if (!el) { return; }
                var pk = wKeyToPref(id);
                if (pk && prefs[pk] !== undefined) { prefs[pk] = false; applyPrefs(); }
                else { el.style.display = 'none'; }
            });
        })(wCloseBtns[wci]);
    }
    var wMinBtns = $$('[data-wmin]');
    for (var wmi = 0; wmi < wMinBtns.length; wmi++) {
        (function (b) {
            on(b, 'click', function (e) {
                if (e.stopPropagation) { e.stopPropagation(); }
                var el = document.getElementById(b.getAttribute('data-wmin'));
                if (el) { toggle(el, 'is-min'); }
            });
        })(wMinBtns[wmi]);
    }
    var wMaxBtns = $$('[data-wmax]');
    for (var wxi = 0; wxi < wMaxBtns.length; wxi++) {
        (function (b) {
            on(b, 'click', function (e) {
                if (e.stopPropagation) { e.stopPropagation(); }
                var el = document.getElementById(b.getAttribute('data-wmax'));
                if (el) { del(el, 'is-min'); }
            });
        })(wMaxBtns[wxi]);
    }

    var rangeAll = $$('.lg-range');
    for (var ri2 = 0; ri2 < rangeAll.length; ri2++) {
        (function (rg) {
            on(rg, 'input', function () {
                prefs.blur = parseInt(rg.value, 10) || 20;
                root.style.setProperty('--lg-blur', prefs.blur + 'px');
                var bv1 = document.getElementById('lg-blur-val');
                var bv2 = document.getElementById('lg-blur-val-2');
                if (bv1) { bv1.textContent = prefs.blur + 'px'; }
                if (bv2) { bv2.textContent = prefs.blur + 'px'; }
                for (var z = 0; z < rangeAll.length; z++) { rangeAll[z].value = String(prefs.blur); }
                save(STORE_PREFS, prefs);
            });
            on(rg, 'change', function () { save(STORE_PREFS, prefs); });
        })(rangeAll[ri2]);
    }

    /* 系统外观变化时刷新回显 */
    if (window.matchMedia) {
        try {
            var mq = window.matchMedia('(prefers-color-scheme: dark)');
            if (mq.addListener) { mq.addListener(syncControls); }
            else if (mq.addEventListener) { mq.addEventListener('change', syncControls); }
        } catch (e) { /* ignore */ }
    }

    /* ---------------- 记事本 ---------------- */
    var STORE_NOTES = 'lg_notes';
    var STORE_TODOS = 'lg_todos';
    var notesArea = document.getElementById('lg-notes');
    var notesTimer = null;
    function saveNotes() { save(STORE_NOTES, notesArea.value || ''); }
    function loadNotes() {
        if (!notesArea) { return; }
        var v = load(STORE_NOTES, '');
        notesArea.value = typeof v === 'string' ? v : '';
    }
    if (notesArea) {
        loadNotes();
        on(notesArea, 'input', function () {
            if (notesTimer) { window.clearTimeout(notesTimer); }
            notesTimer = window.setTimeout(saveNotes, 300);
        });
        on(notesArea, 'blur', saveNotes);
    }

    /* ---------------- 代办事项 ---------------- */
    var todoForm = document.getElementById('lg-todo-form');
    var todoInput = document.getElementById('lg-todo-input');
    var todoList = document.getElementById('lg-todo-list');
    var todoMeta = document.getElementById('lg-todo-meta');

    function loadTodos() {
        var t = load(STORE_TODOS, null);
        if (Object.prototype.toString.call(t) !== '[object Array]') { t = []; }
        return t;
    }
    var todos = loadTodos();
    function saveTodos() { save(STORE_TODOS, todos); }

    function renderTodos() {
        if (!todoList) { return; }
        todoList.innerHTML = '';
        if (!todos.length) {
            var empty = document.createElement('li');
            empty.className = 'lg-todo-empty';
            empty.textContent = '暂无任务，输入文字回车添加';
            todoList.appendChild(empty);
        } else {
            for (var i = 0; i < todos.length; i++) {
                (function (item, idx) {
                    var li = document.createElement('li');
                    li.className = 'lg-todo-item' + (item.done ? ' is-done' : '');
                    li.setAttribute('data-idx', String(idx));
                    var chk = document.createElement('span');
                    chk.className = 'lg-todo-check';
                    var txt = document.createElement('span');
                    txt.className = 'lg-todo-text';
                    txt.textContent = item.text;
                    var del = document.createElement('button');
                    del.type = 'button';
                    del.className = 'lg-todo-del';
                    del.setAttribute('aria-label', '删除任务');
                    del.innerHTML = theme_lg_glyph_html('minus');
                    li.appendChild(chk);
                    li.appendChild(txt);
                    li.appendChild(del);
                    todoList.appendChild(li);
                })(todos[i], i);
            }
        }
        if (todoMeta) {
            var left = todos.length;
            var done = 0;
            for (var k = 0; k < todos.length; k++) { if (todos[k].done) { done++; } }
            if (left === 0) {
                todoMeta.innerHTML = '';
            } else {
                todoMeta.innerHTML = '<span>剩余 <em style="color:var(--lg-accent);font-weight:600">' + (left - done) + '</em> · 已完成 ' + done + '</span>'
                    + '<button type="button" class="lg-todo-clear" data-lg-action="todo-clear">清除已完成</button>';
            }
        }
    }
    function addTodo(text) {
        var t = (text || '').replace(/^\s+|\s+$/g, '');
        if (t === '') { return; }
        if (t.length > 200) { t = t.slice(0, 200); }
        todos.unshift({ text: t, done: false, t: Date.now() });
        if (todos.length > 200) { todos = todos.slice(0, 200); }
        saveTodos();
        renderTodos();
    }
    function toggleTodo(idx) {
        if (!todos[idx]) { return; }
        todos[idx].done = !todos[idx].done;
        saveTodos();
        renderTodos();
    }
    function delTodo(idx) {
        todos.splice(idx, 1);
        saveTodos();
        renderTodos();
    }
    function clearDoneTodos() {
        var keep = [];
        for (var i = 0; i < todos.length; i++) { if (!todos[i].done) { keep.push(todos[i]); } }
        todos = keep;
        saveTodos();
        renderTodos();
    }
    if (todoForm) {
        on(todoForm, 'submit', function (e) {
            if (e && e.preventDefault) { e.preventDefault(); }
            if (!todoInput) { return; }
            var v = todoInput.value;
            addTodo(v);
            todoInput.value = '';
            todoInput.focus();
        });
    }
    if (todoList) {
        on(todoList, 'click', function (e) {
            var t = e.target;
            /* 先向上找到所在的 li（可能是 li / span / svg / path 任一） */
            var li = t;
            while (li && li.parentNode !== todoList) { li = li.parentNode; }
            if (!li || !li.getAttribute) { return; }
            var idx = parseInt(li.getAttribute('data-idx'), 10);
            if (isNaN(idx)) { return; }
            /* 判断是否落在删除按钮上：向上找 .lg-todo-del，止于 li */
            if (closest(t, 'lg-todo-del', li)) { delTodo(idx); }
            else { toggleTodo(idx); }
        });
    }
    if (todoMeta) {
        on(todoMeta, 'click', function (e) {
            var btn = e.target;
            if (btn && btn.getAttribute && btn.getAttribute('data-lg-action') === 'todo-clear') {
                clearDoneTodos();
            }
        });
    }
    renderTodos();

    /* 主题内嵌 SVG 字形 glyph → HTML 字符串（仅代办删除按钮需要，引一个轻量内联） */
    function theme_lg_glyph_html(name) {
        var attr = ' viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"';
        return '<svg' + attr + '><circle cx="12" cy="12" r="8.4"/><path d="M8.6 12h6.8"/></svg>';
    }

    /* ---------------- 数据导入导出 ---------------- */
    function buildExport() {
        var engine = '';
        try { engine = window.localStorage.getItem(STORE_SOU) || ''; } catch (e) { engine = ''; }
        return {
            schema: 'liquidglass.v1',
            exportedAt: new Date().toISOString(),
            keys: {
                prefs: STORE_PREFS,
                notes: STORE_NOTES,
                todos: STORE_TODOS,
                hidden: STORE_HIDDEN,
                engine: STORE_SOU
            },
            prefs: prefs,
            notes: notesArea ? (notesArea.value || '') : '',
            todos: todos,
            hidden: hidden,
            engine: engine
        };
    }
    function exportAll() {
        try {
            var data = buildExport();
            var json = JSON.stringify(data, null, 2);
            var blob = new Blob([json], { type: 'application/json;charset=utf-8' });
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            var stamp = new Date().toISOString().slice(0, 10);
            a.href = url;
            a.download = 'liquidglass-' + stamp + '.json';
            a.style.cssText = 'position:fixed;left:-9999px;top:-9999px;width:1px;height:1px;opacity:0;';
            document.body.appendChild(a);
            a.click();
            window.setTimeout(function () {
                if (a.parentNode) { a.parentNode.removeChild(a); }
                URL.revokeObjectURL(url);
            }, 200);
        } catch (e) {
            window.alert('导出失败：' + e.message);
        }
    }
    function importAll(file) {
        if (!file) { return; }
        var reader = new FileReader();
        reader.onload = function (ev) {
            try {
                var raw = ev.target.result;
                var data = JSON.parse(raw);
                if (data && data.prefs && typeof data.prefs === 'object') {
                    prefs = data.prefs;
                    for (var pk in defaultPrefs) {
                        if (!Object.prototype.hasOwnProperty.call(defaultPrefs, pk)) { continue; }
                        if (prefs[pk] === undefined || prefs[pk] === null) { prefs[pk] = defaultPrefs[pk]; }
                    }
                    save(STORE_PREFS, prefs);
                }
                if (notesArea) {
                    notesArea.value = (typeof data.notes === 'string') ? data.notes : '';
                    saveNotes();
                }
                if (Object.prototype.toString.call(data.todos) === '[object Array]') {
                    todos = data.todos;
                    saveTodos();
                    renderTodos();
                }
                if (Object.prototype.toString.call(data.hidden) === '[object Array]') {
                    hidden = data.hidden;
                    save(STORE_HIDDEN, hidden);
                    applyHidden();
                }
                if (data.engine && typeof data.engine === 'string') {
                    try { window.localStorage.setItem(STORE_SOU, data.engine); } catch (e) { /* ignore */ }
                }
                applyPrefs();
                layoutPager(true);
                window.alert('导入成功');
            } catch (err) {
                window.alert('导入失败：' + (err && err.message ? err.message : err));
            }
        };
        reader.onerror = function () { window.alert('文件读取失败'); };
        reader.readAsText(file, 'utf-8');
    }
    function resetAll() {
        if (!window.confirm('将清除：偏好、记事本、待办、隐藏图标、搜索引擎记忆。本机不可恢复，是否继续？')) { return; }
        try {
            var keys = [STORE_PREFS, STORE_NOTES, STORE_TODOS, STORE_HIDDEN, STORE_SOU];
            for (var i = 0; i < keys.length; i++) { window.localStorage.removeItem(keys[i]); }
        } catch (e) { /* ignore */ }
        window.location.reload();
    }

    var exportRow = document.getElementById('lg-export-row');
    if (exportRow) { on(exportRow, 'click', exportAll); }
    var importRow = document.getElementById('lg-import-row');
    var importFile = document.getElementById('lg-import-file');
    if (importRow && importFile) {
        on(importRow, 'click', function () { importFile.click(); });
        on(importFile, 'change', function () {
            if (importFile.files && importFile.files[0]) { importAll(importFile.files[0]); }
            importFile.value = '';
        });
    }
    var resetRow = document.getElementById('lg-reset-row');
    if (resetRow) { on(resetRow, 'click', resetAll); }

    /* ---------------- 隐藏图标 / 恢复 ---------------- */
    var hidden = load(STORE_HIDDEN, null);
    if (!hidden || Object.prototype.toString.call(hidden) !== '[object Array]') { hidden = []; }

    function tileKey(tile) { return tile.getAttribute('href') || ''; }
    function saveHidden() { save(STORE_HIDDEN, hidden); refreshHiddenCount(); }
    function refreshHiddenCount() {
        var el = document.getElementById('lg-hidden-count');
        if (el) { el.textContent = String(hidden.length); }
    }
    function applyHidden() {
        for (var i = 0; i < tiles.length; i++) {
            var k = tileKey(tiles[i]);
            if (k !== '' && hidden.indexOf(k) > -1) { add(tiles[i], 'is-hidden'); }
        }
        refreshHiddenCount();
    }
    applyHidden();

    var restoreRow = document.getElementById('lg-restore-row');
    if (restoreRow) {
        on(restoreRow, 'click', function () {
            hidden = [];
            for (var i = 0; i < tiles.length; i++) { del(tiles[i], 'is-hidden'); }
            saveHidden();
            layoutPager(true);
        });
    }
    var editRow = document.getElementById('lg-edit-row');
    if (editRow) {
        on(editRow, 'click', function () {
            startJiggle();
            if (settings) { del(settings, 'is-open'); }
        });
    }

    /* ---------------- 抖动 / 碎裂 ---------------- */
    var grids = $$('.lg-grid');
    function startJiggle() {
        for (var i = 0; i < grids.length; i++) { add(grids[i], 'is-jiggle'); }
    }
    function stopJiggle() {
        for (var i = 0; i < grids.length; i++) { del(grids[i], 'is-jiggle'); }
    }

    function shatter(tile) {
        var gl = $('.lg-tile-glyph', tile);
        if (!gl) { return; }
        var r = gl.getBoundingClientRect();
        var shard = document.createElement('div');
        shard.className = 'lg-shard';
        shard.style.left = r.left + 'px';
        shard.style.top = r.top + 'px';
        shard.style.width = r.width + 'px';
        shard.style.height = r.height + 'px';
        shard.style.borderRadius = getComputedStyle(gl).borderRadius || '22%';
        shard.style.backgroundImage = getComputedStyle(gl).backgroundImage || '';
        shard.style.boxShadow = getComputedStyle(gl).boxShadow || '';
        shard.innerHTML = gl.innerHTML;
        document.body.appendChild(shard);
        window.setTimeout(function () {
            if (shard.parentNode) { shard.parentNode.removeChild(shard); }
        }, 700);
    }

    /* ---------------- 长按上下文菜单 ---------------- */
    var menuTarget = null;
    var longTimer = null;
    var longFired = false;

    function openCtx(tile, x, y) {
        if (!ctxMenu) { return; }
        menuTarget = tile;
        add(ctxMenu, 'is-open');
        var w = ctxMenu.offsetWidth || 190;
        var h = ctxMenu.offsetHeight || 180;
        var left = Math.min(x, window.innerWidth - w - 12);
        var top = Math.min(y, window.innerHeight - h - 12);
        ctxMenu.style.left = Math.max(12, left) + 'px';
        ctxMenu.style.top = Math.max(12, top) + 'px';
        startJiggle();
    }
    function closeCtx() {
        if (ctxMenu) { del(ctxMenu, 'is-open'); }
        menuTarget = null;
        stopJiggle();
    }

    var ctxItems = $$('[data-lg-ctx]');
    for (var ci2 = 0; ci2 < ctxItems.length; ci2++) {
        (function (item) {
            on(item, 'click', function () {
                var act = item.getAttribute('data-lg-ctx');
                var t = menuTarget;
                if (act === 'open' && t) { window.open(t.getAttribute('href'), '_blank'); }
                else if (act === 'copy' && t) { copyText(t.getAttribute('href') || ''); }
                else if (act === 'hide' && t) {
                    var k = tileKey(t);
                    if (k !== '' && hidden.indexOf(k) < 0) { hidden.push(k); }
                    shatter(t);
                    add(t, 'is-hidden');
                    saveHidden();
                    if (index) {
                        for (var q = 0; q < index.length; q++) {
                            if (index[q].el === t) { index.splice(q, 1); break; }
                        }
                    }
                    layoutPager(true);
                }
                closeCtx();
            });
        })(ctxItems[ci2]);
    }

    function copyText(text) {
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.left = '-9999px';
        document.body.appendChild(ta);
        ta.select();
        try { document.execCommand('copy'); } catch (e) { /* ignore */ }
        document.body.removeChild(ta);
    }

    /* 图标按压反馈 + 长按 */
    for (var ti2 = 0; ti2 < tiles.length; ti2++) {
        (function (tile) {
            function pressStart(e) {
                longFired = false;
                add(tile, 'is-press');
                window.setTimeout(function () { del(tile, 'is-press'); }, 460);
                var point = (e.touches && e.touches[0]) ? e.touches[0] : e;
                var x = point.clientX || 0;
                var y = point.clientY || 0;
                longTimer = window.setTimeout(function () {
                    longFired = true;
                    openCtx(tile, x, y);
                }, 520);
            }
            function pressEnd() {
                if (longTimer) { window.clearTimeout(longTimer); longTimer = null; }
            }
            function pressMove(e) {
                if (!longTimer) { return; }
                var point = (e.touches && e.touches[0]) ? e.touches[0] : e;
                if (Math.abs((point.clientX || 0) - (pressStart._x || 0)) > 10) { pressEnd(); }
            }
            on(tile, 'touchstart', function (e) {
                var p = e.touches && e.touches[0] ? e.touches[0] : e;
                pressStart._x = p.clientX;
                pressStart(e);
            }, false);
            on(tile, 'touchmove', pressMove, false);
            on(tile, 'touchend', pressEnd, false);
            on(tile, 'touchcancel', pressEnd, false);
            on(tile, 'mousedown', function (e) {
                pressStart._x = e.clientX;
                pressStart(e);
            });
            on(tile, 'mouseup', pressEnd);
            on(tile, 'mouseleave', pressEnd);
            on(tile, 'click', function (e) {
                if (longFired) {
                    if (e.preventDefault) { e.preventDefault(); }
                    longFired = false;
                }
            });
        })(tiles[ti2]);
    }

    on(document, 'scroll', closeCtx, false);
    on(window, 'resize', function () { closeCtx(); placeWidgets(); if (wStatus) { var sbOn = (prefs.statusbar && has(body, 'is-mobile')); wStatus.style.display = sbOn ? 'flex' : 'none'; if (sbOn) { add(body, 'lg-statusbar-on'); } else { del(body, 'lg-statusbar-on'); } } });

    /* ---------------- 启动 ---------------- */
    applyPrefs();
    placeWidgets();
    window.setTimeout(function () { layoutPager(true); }, 400);
})();
