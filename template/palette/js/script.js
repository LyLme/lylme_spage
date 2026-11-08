/**
 * Palette Theme - UI Interactions
 * 配色、夜间模式、时钟、背景切换、搜索引擎、搜索建议、分类筛选
 *
 * 原生 ES5，无 jQuery / 无框架依赖。
 * 搜索区 DOM 契约：form#search-form / input#search-input /
 * input[name="sou"][data-alias][data-hint] / localStorage['theme_sou']
 */
(function () {
    'use strict';

    var COLOR_KEY = 'palette_color';
    var NIGHT_KEY = 'palette_nightMode';
    var BG_KEY = 'palette_bgSetting';
    var SOU_KEY = 'theme_sou';

    var colors = ['purple', 'blue', 'green', 'pink', 'orange', 'teal'];

    // ============ 通用工具 ============
    function storageGet(key) {
        try {
            return window.localStorage.getItem(key);
        } catch (e) {
            return null;
        }
    }

    function storageSet(key, value) {
        try {
            window.localStorage.setItem(key, value);
        } catch (e) {
            // 隐私模式等场景写入失败，静默降级
        }
    }

    function trimText(value) {
        return String(value == null ? '' : value).replace(/^\s+|\s+$/g, '');
    }

    function pad2(value) {
        return value < 10 ? '0' + value : '' + value;
    }

    function containsColor(color) {
        for (var i = 0; i < colors.length; i++) {
            if (colors[i] === color) {
                return true;
            }
        }
        return false;
    }

    // ============ 主题色彩 ============
    function updateColorActive(color) {
        var items = document.querySelectorAll('.color-opt');
        for (var i = 0; i < items.length; i++) {
            if (items[i].getAttribute('data-color') === color) {
                items[i].classList.add('active');
            } else {
                items[i].classList.remove('active');
            }
        }
    }

    function setColor(color) {
        if (!containsColor(color)) {
            color = 'purple';
        }
        document.documentElement.setAttribute('data-theme', color);
        storageSet(COLOR_KEY, color);
        updateColorActive(color);
    }

    // ============ 夜间模式 ============
    function updateNightModeIcon() {
        var isNight = document.documentElement.classList.contains('night-mode');
        var sunIcon = document.querySelector('#nightBtn .icon-sun');
        var moonIcon = document.querySelector('#nightBtn .icon-moon');
        if (sunIcon && moonIcon) {
            sunIcon.style.display = isNight ? 'none' : '';
            moonIcon.style.display = isNight ? '' : 'none';
        }
        var nightBtn = document.getElementById('nightBtn');
        if (nightBtn) {
            nightBtn.setAttribute('aria-pressed', isNight ? 'true' : 'false');
        }
    }

    // ============ 时钟与问候 ============
    function updateClock() {
        var now = new Date();
        var timeEl = document.getElementById('clockTime');
        var dateEl = document.getElementById('clockDate');
        var greetingEl = document.getElementById('clockGreeting');

        if (timeEl) {
            timeEl.textContent = pad2(now.getHours()) + ':' + pad2(now.getMinutes()) + ':' + pad2(now.getSeconds());
        }

        if (dateEl) {
            var weekdays = ['星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'];
            dateEl.textContent = now.getFullYear() + '年' + (now.getMonth() + 1) + '月' + now.getDate() + '日 ' + weekdays[now.getDay()];
        }

        if (greetingEl) {
            var h = now.getHours();
            var greeting;
            if (h < 6) {
                greeting = '夜深了，早点休息吧';
            } else if (h < 9) {
                greeting = '早上好！新的一天开始了';
            } else if (h < 12) {
                greeting = '上午好！愿你精力充沛';
            } else if (h < 14) {
                greeting = '中午好！记得按时吃饭';
            } else if (h < 18) {
                greeting = '下午好！工作辛苦了';
            } else if (h < 22) {
                greeting = '晚上好！放松一下吧';
            } else {
                greeting = '夜深了，晚安';
            }
            greetingEl.textContent = greeting;
        }
    }

    // ============ 背景切换 ============
    var bgLayer = document.querySelector('.bg-layer');

    function removeVideoBackground() {
        var videos = document.querySelectorAll('body > video');
        for (var i = 0; i < videos.length; i++) {
            if (videos[i].parentNode) {
                videos[i].parentNode.removeChild(videos[i]);
            }
        }
    }

    function setImageBackground() {
        var url = window.background_url ? String(window.background_url) : '';
        if (!bgLayer || !url) {
            return false;
        }
        storageSet(BG_KEY, 'image');
        bgLayer.classList.add('image-bg');
        bgLayer.style.backgroundImage = 'url("' + url.replace(/"/g, '%22') + '")';
        removeVideoBackground();
        return true;
    }

    function setVideoBackground() {
        var src = window.background_video_url ? String(window.background_video_url) : '';
        if (!src) {
            return false;
        }
        storageSet(BG_KEY, 'video');
        if (bgLayer) {
            bgLayer.classList.remove('image-bg');
            bgLayer.style.backgroundImage = 'none';
        }
        removeVideoBackground();

        var video = document.createElement('video');
        video.src = src;
        video.loop = true;
        video.muted = true;
        video.autoplay = true;
        video.playsInline = true;
        video.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;object-fit:cover;z-index:-3;pointer-events:none;';
        // 视频地址失效、跨域被拒或浏览器不支持编码时回退到无背景，
        // 同时把偏好写回 none，否则每次刷新都会重试失败视频、切换按钮也看似无效
        video.addEventListener('error', function () {
            clearBackground();
        });
        document.body.appendChild(video);
        return true;
    }

    function clearBackground() {
        storageSet(BG_KEY, 'none');
        if (bgLayer) {
            bgLayer.classList.remove('image-bg');
            bgLayer.style.backgroundImage = 'none';
        }
        removeVideoBackground();
    }

    // 后台是否配置了视频背景地址
    function hasVideoBackground() {
        return !!(window.background_video_url && String(window.background_video_url));
    }

    function restoreBackground() {
        var saved = storageGet(BG_KEY);
        // 首次访问（无保存值）不显示图片/视频背景，仅保留主题渐变
        if (saved === 'video') {
            if (!setVideoBackground()) {
                clearBackground();
            }
        } else if (saved === 'image') {
            // 配置了视频地址后图片背景不再参与切换，历史偏好清零
            if (!hasVideoBackground() && setImageBackground()) {
                return;
            }
            clearBackground();
        }
    }

    function cycleBackground() {
        var current = storageGet(BG_KEY);
        // 配置了视频地址：只切换 视频 / 无背景；未配置：只切换 图片 / 无背景
        if (hasVideoBackground()) {
            if (current === 'video') {
                clearBackground();
            } else {
                setVideoBackground();
            }
            return;
        }
        if (current === 'image') {
            clearBackground();
        } else {
            setImageBackground();
        }
    }

    // ============ 搜索引擎 ============
    var searchForm = document.getElementById('search-form');
    var searchInput = document.getElementById('search-input');
    var searchSwitcher = document.getElementById('engineSwitcher');
    var engineIcon = document.getElementById('engineIcon');
    var engineOptions = document.getElementById('engineOptions');
    var suggestionList = document.getElementById('searchSuggestions');
    var engines = document.querySelectorAll('input[name="sou"]');

    var suggestTimer = null;
    var suggestScript = null;
    var suggestCallbackName = null;
    var suggestActiveIndex = -1;

    function currentEngine() {
        for (var i = 0; i < engines.length; i++) {
            if (engines[i].checked) {
                return engines[i];
            }
        }
        return engines.length ? engines[0] : null;
    }

    function currentAlias() {
        var engine = currentEngine();
        return engine ? (engine.getAttribute('data-alias') || '') : '';
    }

    function hideSuggestions() {
        if (!suggestionList) {
            return;
        }
        suggestionList.className = 'search-suggestions';
        while (suggestionList.firstChild) {
            suggestionList.removeChild(suggestionList.firstChild);
        }
    }

    function cleanupSuggestion() {
        if (suggestTimer) {
            clearTimeout(suggestTimer);
            suggestTimer = null;
        }
        if (suggestScript && suggestScript.parentNode) {
            suggestScript.parentNode.removeChild(suggestScript);
        }
        suggestScript = null;
        if (suggestCallbackName) {
            var name = suggestCallbackName;
            suggestCallbackName = null;
            // 已发起但尚未执行的 JSONP 仍可能回调，先替换为空函数再延迟删除，
            // 避免控制台出现 “paletteSuggest... is not defined”
            window[name] = function () {};
            setTimeout(function () {
                try {
                    delete window[name];
                } catch (e) {
                    window[name] = undefined;
                }
            }, 10000);
        }
    }

    function clearSuggestions() {
        cleanupSuggestion();
        hideSuggestions();
    }

    function updateEngineUI() {
        var engine = currentEngine();
        if (!engine) {
            return;
        }

        var label = engine.parentNode;
        var iconSpan = label && label.querySelector ? label.querySelector('.engine-option-icon') : null;
        if (engineIcon && iconSpan) {
            engineIcon.innerHTML = iconSpan.innerHTML;
        }

        var labels = document.querySelectorAll('.engine-option');
        for (var i = 0; i < labels.length; i++) {
            var input = labels[i].querySelector ? labels[i].querySelector('input[name="sou"]') : null;
            if (input === engine) {
                labels[i].classList.add('active');
            } else {
                labels[i].classList.remove('active');
            }
        }

        if (searchInput) {
            searchInput.placeholder = engine.getAttribute('data-hint') || '请输入搜索内容';
        }
        clearSuggestions();
    }

    function isEngineMenuOpen() {
        return !!(engineOptions && engineOptions.classList.contains('show'));
    }

    function setEngineMenu(open) {
        if (engineOptions) {
            if (open) {
                engineOptions.classList.add('show');
            } else {
                engineOptions.classList.remove('show');
            }
        }
        if (searchSwitcher) {
            if (open) {
                searchSwitcher.classList.add('open');
            } else {
                searchSwitcher.classList.remove('open');
            }
            searchSwitcher.setAttribute('aria-expanded', open ? 'true' : 'false');
        }
        if (open) {
            clearSuggestions();
            var engine = currentEngine();
            if (engine && engine.focus) {
                try {
                    engine.focus();
                } catch (e) {
                    // 忽略聚焦失败
                }
            }
        }
    }

    function toggleEngineMenu() {
        setEngineMenu(!isEngineMenuOpen());
    }

    function restoreEngine() {
        var saved = storageGet(SOU_KEY);
        if (saved) {
            for (var i = 0; i < engines.length; i++) {
                if (engines[i].getAttribute('data-alias') === saved) {
                    engines[i].checked = true;
                    break;
                }
            }
        }
        updateEngineUI();
    }

    function doSearch(keyword) {
        var engine = currentEngine();
        keyword = trimText(keyword);
        var engineUrl = engine ? trimText(engine.value) : '';
        // 搜索地址为空时不做任何跳转，避免拼出站内相对 URL
        if (!engineUrl || !keyword) {
            return;
        }
        var win = window.open(engineUrl + encodeURIComponent(keyword), '_blank');
        if (win) {
            win.opener = null;
        }
    }

    function renderSuggestions(data) {
        if (!suggestionList) {
            return;
        }
        while (suggestionList.firstChild) {
            suggestionList.removeChild(suggestionList.firstChild);
        }
        if (!data || !data.s || !data.s.length) {
            suggestionList.className = 'search-suggestions';
            suggestActiveIndex = -1;
            return;
        }
        for (var i = 0; i < data.s.length; i++) {
            var item = document.createElement('li');
            item.innerHTML = '<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>';
            item.appendChild(document.createTextNode(String(data.s[i])));
            suggestionList.appendChild(item);
        }
        suggestionList.className = 'search-suggestions show';
        // 新结果渲染后取消高亮，等待方向键选择
        suggestActiveIndex = -1;
    }

    // 设置搜索建议的高亮项（循环移动），并滚动到可见区域
    function setSuggestActive(index) {
        if (!suggestionList) {
            return;
        }
        var items = suggestionList.querySelectorAll('li');
        if (!items.length) {
            suggestActiveIndex = -1;
            return;
        }
        if (index < 0) {
            index = items.length - 1;
        }
        if (index >= items.length) {
            index = 0;
        }
        for (var i = 0; i < items.length; i++) {
            if (i === index) {
                items[i].classList.add('active');
            } else {
                items[i].classList.remove('active');
            }
        }
        suggestActiveIndex = index;
        if (items[index].scrollIntoView) {
            items[index].scrollIntoView({ block: 'nearest' });
        }
    }

    function fetchSuggestions(keyword) {
        if (currentAlias() !== 'baidu' || !suggestionList) {
            hideSuggestions();
            return;
        }
        cleanupSuggestion();

        var callbackName = 'paletteSuggest' + (new Date().getTime()) + Math.floor(Math.random() * 1000);
        var script = document.createElement('script');

        window[callbackName] = function (data) {
            // 已被后续请求取代时丢弃迟到的结果
            if (suggestCallbackName !== callbackName) {
                return;
            }
            cleanupSuggestion();
            renderSuggestions(data);
        };

        script.src = 'https://suggestion.baidu.com/su?wd=' + encodeURIComponent(keyword) + '&cb=' + callbackName;
        script.onerror = function () {
            // 只清理本次请求，避免误删已被后续请求替换的 script
            if (suggestScript !== script) {
                return;
            }
            cleanupSuggestion();
            hideSuggestions();
        };

        suggestCallbackName = callbackName;
        suggestScript = script;
        document.body.appendChild(script);
    }

    function bindSearch() {
        if (searchSwitcher) {
            searchSwitcher.addEventListener('click', function (e) {
                e.stopPropagation();
                toggleEngineMenu();
            });
            searchSwitcher.addEventListener('keydown', function (e) {
                var key = e.key || e.keyCode;
                if (key === 'Enter' || key === ' ' || key === 13 || key === 32) {
                    e.preventDefault();
                    toggleEngineMenu();
                } else if (key === 'ArrowDown' || key === 40) {
                    e.preventDefault();
                    setEngineMenu(true);
                } else if (key === 'Escape' || key === 27) {
                    setEngineMenu(false);
                }
            });
        }

        if (engineOptions) {
            engineOptions.addEventListener('click', function (e) {
                // 点击选项时不触发 document 的“点击外部关闭”
                e.stopPropagation();

                if (!e.target.closest('.engine-option')) {
                    return;
                }
                // 选中状态、偏好写入与 UI 刷新统一交给 radio 的 change 事件，
                // 这里只负责关闭菜单（含重复点击已选中项的场景）
                setEngineMenu(false);
            });

            engineOptions.addEventListener('keydown', function (e) {
                var key = e.key || e.keyCode;
                var count = engines.length;
                if (!count) {
                    return;
                }

                if (key === 'ArrowDown' || key === 'ArrowUp' || key === 40 || key === 38) {
                    e.preventDefault();
                    var index = -1;
                    for (var k = 0; k < count; k++) {
                        if (engines[k].checked) {
                            index = k;
                            break;
                        }
                    }
                    if (index < 0) {
                        index = 0;
                    } else {
                        index = (index + ((key === 'ArrowDown' || key === 40) ? 1 : count - 1)) % count;
                    }
                    engines[index].checked = true;
                    storageSet(SOU_KEY, engines[index].getAttribute('data-alias') || '');
                    updateEngineUI();
                    if (engines[index].focus) {
                        engines[index].focus();
                    }
                } else if (key === 'Enter' || key === ' ' || key === 13 || key === 32) {
                    e.preventDefault();
                    var current = currentEngine();
                    if (current) {
                        storageSet(SOU_KEY, current.getAttribute('data-alias') || '');
                        updateEngineUI();
                    }
                    setEngineMenu(false);
                    if (searchInput) {
                        searchInput.focus();
                    }
                } else if (key === 'Escape' || key === 27) {
                    e.preventDefault();
                    setEngineMenu(false);
                    if (searchSwitcher) {
                        searchSwitcher.focus();
                    }
                }
            });
        }

        for (var i = 0; i < engines.length; i++) {
            engines[i].addEventListener('change', function () {
                storageSet(SOU_KEY, this.getAttribute('data-alias') || '');
                updateEngineUI();
                setEngineMenu(false);
            });
        }

        if (searchForm) {
            searchForm.addEventListener('submit', function (e) {
                e.preventDefault();
                var keyword = searchInput ? trimText(searchInput.value) : '';
                if (!keyword) {
                    if (searchInput) {
                        searchInput.focus();
                    }
                    return;
                }
                doSearch(keyword);
                clearSuggestions();
            });
        }

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                var keyword = trimText(this.value);
                if (suggestTimer) {
                    clearTimeout(suggestTimer);
                    suggestTimer = null;
                }
                if (!keyword) {
                    clearSuggestions();
                    return;
                }
                suggestTimer = setTimeout(function () {
                    fetchSuggestions(keyword);
                }, 250);
            });

            searchInput.addEventListener('keydown', function (e) {
                var key = e.key || e.keyCode;
                var isDown = (key === 'ArrowDown' || key === 40);
                var isUp = (key === 'ArrowUp' || key === 38);
                var isEnter = (key === 'Enter' || key === 13);
                var isEsc = (key === 'Escape' || key === 27);
                var visible = suggestionList && suggestionList.className.indexOf('show') !== -1;

                if (isEsc) {
                    clearSuggestions();
                    return;
                }
                if (!visible) {
                    return;
                }

                if (isDown || isUp) {
                    e.preventDefault();
                    var items = suggestionList.querySelectorAll('li');
                    if (!items.length) {
                        return;
                    }
                    var next = suggestActiveIndex;
                    if (isDown) {
                        next = (next + 1) % items.length;
                    } else {
                        next = (next - 1 + items.length) % items.length;
                    }
                    setSuggestActive(next);
                } else if (isEnter && suggestActiveIndex >= 0) {
                    var selItems = suggestionList.querySelectorAll('li');
                    if (!selItems.length) {
                        return;
                    }
                    var word = trimText(selItems[suggestActiveIndex].textContent || selItems[suggestActiveIndex].innerText || '');
                    if (word) {
                        e.preventDefault();
                        searchInput.value = word;
                        clearSuggestions();
                        doSearch(word);
                    }
                }
            });
        }

        if (suggestionList) {
            suggestionList.addEventListener('click', function (e) {
                var node = e.target;
                while (node && node !== suggestionList && (!node.tagName || node.tagName.toLowerCase() !== 'li')) {
                    node = node.parentNode;
                }
                if (!node || node === suggestionList) {
                    return;
                }
                var word = trimText(node.textContent || node.innerText || '');
                if (word && searchInput) {
                    searchInput.value = word;
                    clearSuggestions();
                    doSearch(word);
                }
            });
        }
    }

    // ============ 分类筛选 ============
    function filterCategory(categoryId) {
        var groups = document.querySelectorAll('.link-group');
        for (var i = 0; i < groups.length; i++) {
            var groupId = groups[i].getAttribute('data-group-id');
            groups[i].style.display = (categoryId === '0' || groupId === categoryId) ? '' : 'none';
        }
    }

    function bindCategories() {
        var items = document.querySelectorAll('.category-item');
        for (var i = 0; i < items.length; i++) {
            items[i].addEventListener('click', function () {
                var categoryId = this.getAttribute('data-cat') || '0';
                var allItems = document.querySelectorAll('.category-item');
                for (var j = 0; j < allItems.length; j++) {
                    allItems[j].classList.remove('active');
                    allItems[j].setAttribute('aria-pressed', 'false');
                }
                this.classList.add('active');
                this.setAttribute('aria-pressed', 'true');
                filterCategory(categoryId);
            });
        }
    }

    // ============ 下拉菜单（键盘 / 触摸可用） ============
    // 调色板与「更多」菜单原本只靠 :hover 展开，触屏和键盘都无法操作。
    // 这里统一由触发按钮切换 .open，CSS 同时保留 :hover 以维持桌面悬停体验。
    function closeAllDropdowns() {
        var opened = document.querySelectorAll('.dropdown.open, .palette-dropdown.open');
        for (var i = 0; i < opened.length; i++) {
            opened[i].classList.remove('open');
            var trigger = opened[i].querySelector('.icon-btn');
            if (!trigger) {
                continue;
            }
            trigger.setAttribute('aria-expanded', 'false');
            if (opened[i].contains && opened[i].contains(document.activeElement) && document.activeElement !== trigger) {
                try {
                    trigger.focus();
                } catch (e) {
                    // 忽略聚焦失败
                }
            }
        }
    }

    function bindDropdowns() {
        var dropdowns = document.querySelectorAll('.palette-dropdown, .dropdown');
        for (var i = 0; i < dropdowns.length; i++) {
            (function (dropdown) {
                var trigger = dropdown.querySelector('.icon-btn');
                if (!trigger) {
                    return;
                }
                trigger.addEventListener('click', function (e) {
                    // 阻止冒泡，避免刚展开就被 document 的关闭逻辑收起
                    e.stopPropagation();
                    var willOpen = !dropdown.classList.contains('open');
                    closeAllDropdowns();
                    if (willOpen) {
                        dropdown.classList.add('open');
                        trigger.setAttribute('aria-expanded', 'true');
                    }
                });
            })(dropdowns[i]);
        }
        document.addEventListener('click', closeAllDropdowns);
    }

    // ============ 初始化 ============
    function init() {
        setColor(storageGet(COLOR_KEY) || 'purple');
        updateNightModeIcon();
        updateClock();
        setInterval(updateClock, 1000);
        restoreBackground();
        restoreEngine();
        bindSearch();
        bindCategories();
        bindDropdowns();

        var nightBtn = document.getElementById('nightBtn');
        if (nightBtn) {
            nightBtn.addEventListener('click', function () {
                var isNight = document.documentElement.classList.toggle('night-mode');
                storageSet(NIGHT_KEY, isNight ? 'true' : 'false');
                updateNightModeIcon();
            });
        }

        var bgBtn = document.getElementById('bgBtn');
        if (bgBtn) {
            bgBtn.addEventListener('click', cycleBackground);
        }

        document.addEventListener('click', function (e) {
            var target = e.target;

            // 外链安全提醒页（go.php）：开启后，点击站外链接先经过倒计时确认页
            if (window.palette_go_enabled && target.closest) {
                var linkCard = target.closest('.link-card');
                if (linkCard) {
                    var href = linkCard.getAttribute('href');
                    // 仅对带协议（http/https 等）的绝对链接生效，跳过锚点 / 相对路径
                    if (href && /^[a-z][a-z0-9+.\-]*:/i.test(href)) {
                        var tmp = document.createElement('a');
                        tmp.href = href;
                        if (tmp.hostname && tmp.hostname !== location.hostname) {
                            e.preventDefault();
                            var goUrl = (window.palette_go_url || '') + '?url=' + encodeURIComponent(href);
                            window.open(goUrl, '_blank', 'noopener');
                            return;
                        }
                    }
                }
            }

            if (!target.closest('.search-engine-switcher') && !target.closest('.engine-options')) {
                setEngineMenu(false);
            }

            var colorOpt = target.closest('.color-opt');
            if (colorOpt) {
                setColor(colorOpt.getAttribute('data-color'));
                return;
            }

            if (!target.closest('.search-area')) {
                clearSuggestions();
            }
        });

        document.addEventListener('keydown', function (e) {
            var key = e.key || e.keyCode;
            if (key === 'Escape' || key === 27) {
                setEngineMenu(false);
                closeAllDropdowns();
            }
        });

        if (window.innerWidth >= 768 && searchInput) {
            setTimeout(function () {
                try {
                    searchInput.focus();
                } catch (e) {
                    // 忽略聚焦失败
                }
            }, 300);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
