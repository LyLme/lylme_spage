// ltab Theme JS
(function() {
    // 时钟
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const timeEl = document.getElementById('clock-time');
        if (timeEl) timeEl.textContent = hours + ':' + minutes;

        const month = now.getMonth() + 1;
        const day = now.getDate();
        const weekDays = ['日','一','二','三','四','五','六'];
        const week = weekDays[now.getDay()];
        const dateEl = document.getElementById('clock-date');
        if (dateEl) dateEl.textContent = month + '月' + day + '日 星期' + week;
    }
    updateClock();
    setInterval(updateClock, 1000);

    const ENGINE_STORAGE_KEY = 'ltab_last_engine';
    let currentEngineId = null; // 当前选中的搜索引擎标识
    let engineBaseAction = ''; // 搜索引擎原始链接(不含搜索词)，作为每次拼接的基准

    // 搜索引擎下拉
    window.toggleEngine = function() {
        const dropdown = document.getElementById('engine-dropdown');
        const btn = document.getElementById('search-engine');
        if (!dropdown || !btn) return;
        if (dropdown.style.display === 'none') {
            dropdown.style.display = 'block';
            btn.classList.add('open');
        } else {
            dropdown.style.display = 'none';
            btn.classList.remove('open');
        }
    };

    // 关闭搜索引擎下拉
    function closeEngine() {
        const dropdown = document.getElementById('engine-dropdown');
        const btn = document.getElementById('search-engine');
        if (!dropdown || !btn) return;
        dropdown.style.display = 'none';
        btn.classList.remove('open');
    }

    // 选择搜索引擎
    window.selectEngine = function(el) {
        const link = el.getAttribute('data-link');
        const placeholder = el.getAttribute('data-placeholder');
        const form = document.getElementById('search-form');
        const input = document.getElementById('search-input');
        const iconWrap = document.getElementById('engine-icon');
        engineBaseAction = link || '';
        if (form) form.action = engineBaseAction;
        if (input) input.placeholder = placeholder || '输入搜索内容';
        if (iconWrap) {
            const icon = el.querySelector('svg, img, i');
            if (icon) iconWrap.innerHTML = icon.outerHTML;
        }
        // 记忆所选搜索引擎
        const id = el.getAttribute('data-id') || '';
        currentEngineId = id;
        if (currentEngineId) {
            try { localStorage.setItem(ENGINE_STORAGE_KEY, currentEngineId); } catch (e) {}
        }
        // 选择后收起下拉
        closeEngine();
        if (input) input.focus();
    };

    // 组装搜索引擎地址（基于原始引擎链接，避免重复拼接）
    function buildSearchUrl(val) {
        let base = engineBaseAction;
        if (!base) base = 'https://www.bing.com/search?q=';
        return base + encodeURIComponent(val);
    }

    // 搜索提交
    window.doSearch = function() {
        const form = document.getElementById('search-form');
        const input = document.getElementById('search-input');
        if (!form || !input) return false;
        const val = input.value.trim();
        if (!val) return false;
        window.open(buildSearchUrl(val), '_blank');
        return false;
    };

    // 网页搜索
    function submitWebSearch() {
        const form = document.getElementById('search-form');
        const input = document.getElementById('search-input');
        if (!form || !input) return false;
        const val = input.value.trim();
        if (!val) return false;
        window.open(buildSearchUrl(val), '_blank');
        return false;
    }

    // ---------- 本站链接匹配（用于联想混入） ----------
    function getCards() {
        return Array.prototype.slice.call(document.querySelectorAll('.ltab-card'));
    }

    // 按关键词匹配本站卡片标题，返回 {text, href, type:'site'}
    function getSiteMatches(keyword) {
        keyword = (keyword || '').trim().toLowerCase();
        if (!keyword) return [];
        const items = [];
        getCards().forEach(function(card) {
            const name = (card.getAttribute('title') || '').trim();
            const href = card.getAttribute('href') || '';
            if (name && href && name.toLowerCase().indexOf(keyword) !== -1) {
                items.push({ text: name, href: href, type: 'site' });
            }
        });
        return items.slice(0, 5);
    }

    // ---------- 搜索词提示 ----------
    const suggestEl = document.getElementById('search-suggest');
    const inputEl = document.getElementById('search-input');
    let selectedIndex = -1;
    let sugItems = [];

    function showSuggest() {
        if (suggestEl) suggestEl.style.display = 'block';
    }
    function hideSuggest() {
        if (suggestEl) suggestEl.style.display = 'none';
        selectedIndex = -1;
        sugItems = [];
    }
    function clearSuggest() {
        if (suggestEl) suggestEl.innerHTML = '';
    }
    function highlightSelected() {
        if (!suggestEl) return;
        const lis = suggestEl.querySelectorAll('li');
        lis.forEach(function(li, i) {
            li.classList.toggle('sug-active', i === selectedIndex);
        });
    }

    // 追加一条联想项（本站链接 type:'site' / 网页词 type:'web'）
    function appendSugItem(item) {
        if (!item || !item.text) return;
        const li = document.createElement('li');
        if (item.type === 'site') {
            li.classList.add('sug-site');
            const icon = document.createElement('span');
            icon.className = 'sug-icon';
            icon.innerHTML = '<svg viewBox="0 0 1024 1024" width="14" height="14"><path d="M909.6 854.5L649.5 594.4C690.9 542 714 475.8 714 405.1 714 217.7 562.3 66 375 66S36 217.7 36 405.1 187.7 744.1 375 744.1c70.7 0 136.9-23.1 189.3-64.4l260.1 260.1c9.2 9.2 24.1 9.2 33.3 0l28.3-28.3c9.2-9.2 9.2-24.1-.1-33.3zM375 666c-143.9 0-261-117.1-261-261s117.1-261 261-261 261 117.1 261 261-117.1 261-261 261z"/></svg>';
            li.appendChild(icon);
        }
        li.appendChild(document.createTextNode(item.text));
        li.setAttribute('data-type', item.type);
        if (item.href) li.setAttribute('data-href', item.href);
        li.addEventListener('click', function() {
            chooseSugItem(item);
        });
        suggestEl.appendChild(li);
        sugItems.push(item);
        showSuggest();
    }

    // 统一处理联想项选中（点击 / 键盘回车）：本站链接直接打开，网页词用当前引擎搜索
    function chooseSugItem(item) {
        if (!item) return;
        if (item.type === 'site') {
            if (item.href) window.open(item.href, '_blank');
        } else {
            if (inputEl) inputEl.value = item.text;
            submitWebSearch();
        }
        hideSuggest();
    }

    // 联想主流程：先显示本站匹配链接（同步），再追加百度联想词（异步）
    let sugTimer = null;
    let sugSeq = 0; // 请求序号，防止异步联想乱序覆盖
    function runSuggest(keyword) {
        clearSuggest();
        sugItems = [];
        const seq = ++sugSeq;
        // 1. 本站匹配链接
        getSiteMatches(keyword).forEach(function(item) {
            appendSugItem(item);
        });
        // 2. 百度联想词
        if (typeof jQuery !== 'undefined') {
            jQuery.ajax({
                url: 'https://suggestion.baidu.com/su?wd=' + encodeURIComponent(keyword),
                dataType: 'jsonp',
                jsonp: 'cb',
                success: function(data) {
                    if (seq !== sugSeq) return; // 结果已过期，丢弃
                    const list = data.s || [];
                    list.slice(0, 8).forEach(function(word) {
                        const dup = sugItems.some(function(i) { return i.text === word; });
                        if (!dup) appendSugItem({ text: word, type: 'web' });
                    });
                },
                error: function() {}
            });
        }
        selectedIndex = -1;
        if (!sugItems.length) hideSuggest();
    }

    if (inputEl && suggestEl) {
        inputEl.addEventListener('input', function() {
            const val = this.value.trim();
            if (sugTimer) clearTimeout(sugTimer);
            if (!val) {
                hideSuggest();
                return;
            }
            sugTimer = setTimeout(function() { runSuggest(val); }, 60);
        });

        // 键盘上下选择、回车确认
        inputEl.addEventListener('keydown', function(e) {
            if (suggestEl.style.display === 'none' || !sugItems.length) return;
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                selectedIndex = (selectedIndex + 1) % sugItems.length;
                highlightSelected();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                selectedIndex = (selectedIndex - 1 + sugItems.length) % sugItems.length;
                highlightSelected();
            } else if (e.key === 'Enter') {
                if (selectedIndex >= 0 && selectedIndex < sugItems.length) {
                    e.preventDefault();
                    chooseSugItem(sugItems[selectedIndex]);
                }
            }
        });
    }

    // 点击外部关闭下拉与提示
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('engine-dropdown');
        const btn = document.getElementById('search-engine');
        if (!dropdown || !btn) return;
        if (!btn.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
            btn.classList.remove('open');
        }
        if (!e.target.closest('#search-suggest') && !e.target.closest('.search-box')) {
            hideSuggest();
        }
    });

    // 默认选中搜索引擎（优先读取记忆，否则第一个）
    const savedEngineId = (function() {
        try { return localStorage.getItem(ENGINE_STORAGE_KEY) || ''; } catch (e) { return ''; }
    })();
    let defaultEngine = null;
    if (savedEngineId) {
        defaultEngine = document.querySelector('.engine-item[data-id="' + savedEngineId + '"]');
    }
    if (!defaultEngine) {
        defaultEngine = document.querySelector('.engine-item');
    }
    if (defaultEngine) {
        selectEngine(defaultEngine);
    }

    // 导航高亮 + 平滑滚动
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(function(item) {
        item.addEventListener('click', function(e) {
            navItems.forEach(function(n) { n.classList.remove('active'); });
            item.classList.add('active');
            const target = item.getAttribute('data-target');
            if (target && target !== 'home') {
                const el = document.getElementById(target);
                if (el) {
                    e.preventDefault();
                    el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            } else if (target === 'home') {
                e.preventDefault();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
    });
})();
