# LiquidGlass · 六零导航页主题

> 一眼可辨的 Apple 生态拟物主题：PC 端呈现 macOS 桌面（菜单栏 + 玻璃窗口 + 底部 Dock + 右侧桌面组件），
> 移动端呈现 iOS 主屏（Dynamic Island 状态栏 + 玻璃卡片时钟 + Spotlight 聚焦搜索 + 横向分页图标 + 底部玻璃 Tab Bar）。

## 风格设定一览

| 维度 | 选型 |
| --- | --- |
| 风格关键词 | 液态玻璃 · 深度分层 · 高保真拟物 · 毛玻璃 · 极简克制 · 空间计算感 · SF Symbols · 圆角矩形 · 无边框按钮 |
| 主色 / 配色 | iOS 系统蓝 `#007AFF`（可改）；背景薰衣草紫 + 天蓝 + 薄荷绿 极光渐变；提供四种壁纸方案 |
| 整体氛围 | 通透、轻盈、呼吸感、高级；亮色以半透明白玻璃 + 深空黑文字为主，深色自动切换为深空灰底 + 亮玻璃 + 反白文字 |
| 布局原型 | **「系统桌面拟物 × 液态玻璃」**——融合「macOS 桌面」（PC）与「iOS 主屏」（移动）两套版式，不属于 dev-theme 默认的「顶部条 + 居中容器 + 卡片网格」 |
| 字体 | 系统字体优先（SF Pro Display / PingFang SC），启用 `-webkit-font-smoothing: antialiased`，数字使用等宽 |
| 特殊视觉元素 | Liquid Glass（backdrop-filter blur 12/20/32px + saturate 180%， + 1px 玻璃描边）；柔光漂浮光斑；圆角矩形图标（22% 标准 iOS 近似曲率）；触摸按压缩小回弹；长按上下文菜单；抖动编辑模式；碎裂隐藏动画 |
| 外观模式 | 跟随系统（自动），可在设置或控制中心手动切换「始终浅色 / 始终深色」 |
| 其他亮点 | ① iOS Spotlight 聚焦搜索（全屏毛玻璃 + 实时过滤站内链接 + 键盘上下选 + Enter 打开，即站内搜索列表，方案 A 客户端过滤）<br>② 控制中心：从菜单栏右上角下拉，含第一个分组的快捷方式 + 深色模式开关 + 图标风格 / 列数分段 + 模糊滑块<br>③ 设置：iOS 分组表视图（Grouped Table View），iOS 绿色开关 + 分段控件 + 滑块<br>④ 图标风格可在「渐变 / 高保真拟物 / 扁平 / 官方纯色」四种之间切换<br>⑤ 减弱动效、玻璃模糊、列数、时钟/一言显示均可在设置/控制中心调节并持久化<br>⑥ 长按任意图标触发毛玻璃上下文菜单（打开 / 复制链接 / 隐藏图标 → 碎裂动画 / 取消）<br>⑦ Dock 鼠标悬停放大镜（macOS 风格）<br>⑧ 每页 = 一个分组，水平滑动切换 + Coverflow 邻近页淡出<br>⑨ 搜索框联想词：输入时调用百度联想接口（JSONP）下拉补全，仅用百度一种（优先级最高），可在配置关闭 |

## 布局原型 · PC（≥769px）

```
┌───────────────────────────────────────────────────────────────┐
│ ●●●            LiquidGlass 导航 · 申请收录 工具集            📶 📡 🔋 ⌕ ⚙ ─19:18│   ←  macOS 菜单栏（主题书签放在 Apple logo 之后）
├───────────────────────────────────────────────────────────────┤
│                                                       ┌──┐   │
│                                                       │时钟│   │ ← 桌面组件
│                                                       ├──┤   │
│                                                       │一言│   │
│   ┌───────────────────────────────────────────────┐   ├──┤   │
│   │ ● ● ●      LiquidGlass 导航            < > ⚙  │   │公告│   │
│   ├───────────────────────────────────────────────┤   └──┘   │
│   │ 🔍 搜索胶囊                       [搜索]      │          │
│   │ (百度)(必应)                                  │          │
│   ├──────────┬────────────────────────────────────┤          │
│   │ 个人收藏 │  常用站点                     4 个  │          │
│   │ ──────── │ ⬛ ⬛ ⬛ ⬛                         │          │
│   │ 常用站点 │ GitHub 苹果官网 Figma Dribbble    │          │
│   │ 设计资源 │ ── ○ ○                          │          │
│   │          │ 左右滑动 / 点击圆点切换分组       │          │
│   └──────────┴────────────────────────────────────┘          │
│                                                               │
│              ©  · 京ICP · 京公网安备                           │
│                  ┌─────────────────────────┐                  │
│                  │ 🔍  ⚙  ⚙  │   ←  Dock（仅 3 个系统项：聚焦搜索 / 控制中心 / 设置）
│                  └─────────────────────────┘                  │
└───────────────────────────────────────────────────────────────┘
```

## 布局原型 · 移动端（≤768px）

```
┌─────────────────────────────────┐
│ 19:18       ▀▀▀▀       📶 📡 🔋 ⚙ │   ←  iOS 状态栏 + Dynamic Island
│                                 │
│ ┌─────────────────────────────┐ │
│ │ 今天                       │ │   ←  玻璃卡片 · 时钟组件
│ │ 19:18                       │ │
│ │ 8月31日 星期一               │ │
│ └─────────────────────────────┘ │
│                                 │
│ ┌─────────────────────────────┐ │
│ │ 🔍 百度一下            搜索 │ │   ←  Spotlight 搜索胶囊
│ │ (百度) (必应)                │ │
│ └─────────────────────────────┘ │
│                                 │
│ [🔖 申请收录] [🔖 工具集]         │   ←  快捷导航条（theme_tags）
│                                 │
│ (常用站点) (设计资源)            │   ←  玻璃胶囊分组切换
│                                 │
│ ┌─────────────────────────────┐ │
│ │ ⬛ ⬛ ⬛ ⬛                   │ │   ←  图标网格（4 列自适应）
│ │ GitHub 苹果官网 Figma ...    │ │
│ └─────────────────────────────┘ │
│ ── ○ ○                          │
│                                 │
│  © · 京ICP · 京公网安备          │
│                                 │
│ ┌─────────────────────────────┐ │
│ │ 🔍  ⚙  ⚙  │   ←  iOS 玻璃 Tab Bar（仅 3 个系统项）
│ │ 搜索 控制 设置│
│ └─────────────────────────────┘ │
└─────────────────────────────────┘
```

## 配置项（config.php）

通用项（契约保留）：

| key | type | 默认 | 说明 |
| --- | --- | --- | --- |
| `color` | color | `#007AFF` | 系统强调色 / 选中态 / 开关 |
| `link_cols` | select | `4` | PC 窗口内图标网格列数（3/4/5/6） |
| `modules` | checkbox | `clock` | 桌面组件开关：`clock` 时钟 · `yan` 一言 |
| `notice` | textarea | `""` | 桌面公告卡片，支持 HTML |
| `gonganbei` | text | `""` | 公安备案号 |

风格扩展项：

| key | type | 默认 | 可选值 |
| --- | --- | --- | --- |
| `wall` | select | `aurora` | `aurora` 极光（紫+蓝+绿） · `dusk` 暮色（粉+橙+紫） · `mint` 薄荷（青+蓝+白） · `graphite` 石墨（中性灰） |
| `blur` | select | `medium` | `soft` 12px · `medium` 20px · `strong` 32px |
| `icons` | select | `gradient` | `gradient` 渐变 · `skew` 高保真拟物 · `flat` 扁平 · `official` 官方纯色 |
| `scheme` | radio | `auto` | `auto` 跟随系统 · `light` 始终浅色 · `dark` 始终深色 |
| `icon_radius` | select | `22` | `16` `22` `28` `50` 百分比圆角 |
| `suggestion` | radio | `on` | `on` 开启搜索联想词（百度 JSONP） · `off` 关闭 |

访客还可在运行时通过「设置」或「控制中心」覆盖外观模式、图标风格、列数、玻璃模糊、减弱动效、时钟/一言显示等偏好，存储于 `localStorage['lg_prefs']`。

## 文件清单

```
liquidglass/
├── index.php        # 入口：菜单栏 / 状态栏 / 组件 / 主窗口 / Dock / Spotlight / 控制中心 / 设置 / 上下文菜单
├── theme.ini        # 主题信息（JSON）
├── config.php       # 配置表单（含 5 项通用 + 5 项风格扩展）
├── functions.php    # 契约函数 + theme_lg_glyph / theme_lg_battery / theme_lg_enum
├── css/style.css    # 自创令牌（--lg-*）与全部样式，含 1640px / 768px 断点
├── js/script.js     # ES5 脚本：搜索引擎、时钟、分组分页、Dock 放大镜、聚焦搜索、控制中心、设置、上下文菜单、抖动/碎裂、偏好持久化
└── README.md        # 本文件
```

## 兼容性 / 自检

- **PHP**：兼容 5.4+ / 7.x / 8.x；统一 `array()`；`htmlspecialchars()` 显式传 `ENT_QUOTES, 'UTF-8'`；无 `??` / `<=>` / 类型声明 / 箭头函数 / 短数组。
- **JS**：纯 ES5；无 `let`/`const`/箭头函数/模板字符串；带 localStorage try/catch；时钟、Spotlight、控制中心、设置均有「节点不存在则跳过」兼容。
- **CSS**：使用 `backdrop-filter` + `saturate`；提供无 backdrop-filter 时的 `var(--lg-glass-solid)` 兜底（页面无 `@supports` 警告但已为不支持的环境加深玻璃底色）。
- **图标尺寸**：`.lg-tile-glyph` 与 `.lg-engine-ico`、`.lg-page-ico`、`.lg-spot-item-glyph`、`.lg-cc-tile-ico` 同时对 `svg` 与 `img` 约束尺寸；缓存版本号取 `theme.ini.theme_version`，CSS/JS 通过 `?v=100` 缓存破坏。
- **契约锚点**：`#search-form` / `#search-input` / `name="sou"` / `data-alias` / `data-hint` / 首个 `checked`；`lists($html)` 使用 7 个键；`#lg-clock-widget`、`#lg-clock-status`、`#lg-clock-menu`、`#lg-date-widget`、`#lg-yan`、`#lg-spot-input`、`#lg-spot-form`、`#lg-spot-results`、`#lg-blur-range`、`[data-lg-action]` 等运行时锚点。

## 创新校验（自检）

- [x] **未使用 dev-theme 禁止清单 class**：`site-header / site-logo / site-nav / site-main / clock / clock-time / clock-date / yan / search / search-form / search-input / search-btn / search-engines / engine / engine-name / notice / links / link-group / group-title / link-item / link-icon / link-name / site-footer / icp / gonganbei` 均未出现。
- [x] **自创 CSS 变量体系**：使用 `lg-*` 前缀 + 语义化命名（`--lg-glass`、`--lg-hairline`、`--lg-specular`、`--lg-blur`、`--lg-sat`、`--lg-icon-radius`、`--lg-cols`、`--lg-h` 等）。
- [x] **非默认版式**：明确采用「macOS 桌面窗口 + Dock + 菜单栏」（PC）/「iOS 主屏 + Tab Bar + Dynamic Island」（移动），并融合液态玻璃材质与 Spotlight 聚焦搜索。
- [x] **图标尺寸**：`.lg-tile-glyph svg, .lg-tile-glyph img`、`.lg-engine-ico svg, .lg-engine-ico img`、`.lg-page-ico svg, .lg-page-ico img`、`.lg-spot-item-glyph svg, .lg-spot-item-glyph img`、`.lg-cc-tile-ico svg, .lg-cc-tile-ico img` 全部约束。
- [x] **图标渲染清晰**：使用 `-webkit-font-smoothing: antialiased` + `-moz-osx-font-smoothing: grayscale` + `font-variant-numeric: tabular-nums`。

## 键盘 / 触控

| 操作 | 触发 |
| --- | --- |
| 唤起聚焦搜索 | 点击 Dock「聚焦搜索」/ 点击菜单栏放大镜 / `Ctrl + K` / `⌘ + K` |
| Spotlight 选择 | `↑` `↓` / 点击 / Enter 打开 |
| 关闭覆盖层 | `Esc` / 点击空白处 |
| 切换分组 | Dock/Tab Bar 标签点击 / 侧栏（PC）/ 分组胶囊（移动）/ 左右方向键 / 触屏左右滑动 |
| 长按图标 | 触发毛玻璃上下文菜单：打开 · 复制链接 · 隐藏图标（碎裂动画） · 取消 |
| 编辑模式 | 设置 →「进入编辑模式」→ 所有图标进入抖动状态 |

## 演示 / 适用版本

- 适用六零导航页：**v1.2.5 及以上**。
- 演示地址：https://spage.lylme.com/liquidglass
- 主题版本：**1.0.4** · 2026-08-31
## localStorage 键位

| 键 | 内容 |
| --- | --- |
| `lg_prefs` | 偏好：外观模式 / 图标风格 / 列数 / 模糊 / 减弱动效 / 四个组件开关 |
| `lg_notes` | 记事本文本 |
| `lg_todos` | 待办数组 `[{text, done, t}]` |
| `lg_hidden` | 被隐藏的图标链接数组 |
| `theme_sou` | 上次使用的搜索引擎（**契约键名，全站统一，换主题保留**） |