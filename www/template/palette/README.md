# 六零导航页 Palette（调色板）主题

六零导航页 (LyLme Spage) 的 Palette 主题，毛玻璃质感 + 6 种主题色彩自由切换，一键换色，简约现代风格。

🔗 **在线预览**：[https://nav.litx.cn](https://nav.litx.cn)

## 注意

**该项目为主题模板文件，不支持直接使用，请前往 [LyLme_Spage](https://github.com/LyLme/lylme_spage) 下载安装完整项目源码搭建。**

## 特点

- **🎨 6 色自由切换** — 顶部独立的调色板按钮，极光紫 / 板岩蓝 / 森林绿 / 玫瑰粉 / 日落橙 / 海洋青，一键换色，偏好自动记忆
- **🌙 日间/夜间模式** — 每种色彩都有对应的深色版本，夜间模式与色彩独立运作
- **🪟 毛玻璃质感** — 卡片和导航栏使用 CSS `backdrop-filter` 实现通透的磨砂玻璃效果
- **🔍 搜索引擎切换** — 点击搜索框左侧图标弹出引擎列表选择，支持百度搜索建议下拉
- **🖼️ 背景自由切换** — 支持图片背景、视频背景、清除背景，偏好自动记忆
- **📱 全响应式适配** — 网格布局自动适配桌面 / 平板 / 手机（8→6→4→3 列）
- **✨ 卡片入场动画** — 链接卡片交错淡入上浮，视觉体验流畅
- **🕐 时钟与问候语** — 根据当前时间显示不同问候语
- **🎯 分类筛选** — 顶部分类导航，点击即可筛选显示对应分类链接
- **🔗 智能链接过滤** — 自动过滤已禁用和密码保护的链接，与后端行为一致

## 色彩方案

| 名称 | 强调色 | 预览 | 风格 |
|------|--------|------|------|
| 极光紫 (Purple) | `#7c5cfc` | 🟣 | 紫蓝渐变，优雅梦幻 |
| 板岩蓝 (Blue) | `#5b7fbd` | 🔵 | 蓝灰暖金，沉稳精致 |
| 森林绿 (Green) | `#4a9c5d` | 🟢 | 自然清新，舒适护眼 |
| 玫瑰粉 (Pink) | `#d4608b` | 🩷 | 温柔浪漫，甜美柔和 |
| 日落橙 (Orange) | `#e08840` | 🟠 | 温暖活力，热情明亮 |
| 海洋青 (Teal) | `#2ea8a0` | 🩵 | 沉稳知性，清爽冷静 |

## 预览图片

### 六种主题色彩



| 极光紫 | 板岩蓝 | 森林绿 |
|--------|--------|--------|
| ![极光紫](https://raw.githubusercontent.com/litxiaoxi/LyLme-Spage-Palette/refs/heads/main/preview/1.png) | ![板岩蓝](https://raw.githubusercontent.com/litxiaoxi/LyLme-Spage-Palette/refs/heads/main/preview/2.png) | ![森林绿](https://raw.githubusercontent.com/litxiaoxi/LyLme-Spage-Palette/refs/heads/main/preview/3.png) |

| 玫瑰粉 | 日落橙 | 海洋青 |
|--------|--------|--------|
| ![玫瑰粉](https://raw.githubusercontent.com/litxiaoxi/LyLme-Spage-Palette/refs/heads/main/preview/4.png) | ![日落橙](https://raw.githubusercontent.com/litxiaoxi/LyLme-Spage-Palette/refs/heads/main/preview/5.png) | ![海洋青](https://raw.githubusercontent.com/litxiaoxi/LyLme-Spage-Palette/refs/heads/main/preview/6.png) |



### 更多预览



| 夜间-极光紫 | 夜间-板岩蓝 | 背景-极光紫 | 背景-板岩蓝 |
|----------|------------|------------|----------|
| ![夜间模式](https://raw.githubusercontent.com/litxiaoxi/LyLme-Spage-Palette/refs/heads/main/preview/7.png) | ![调色板切换](https://raw.githubusercontent.com/litxiaoxi/LyLme-Spage-Palette/refs/heads/main/preview/8.png) | ![响应式布局](https://raw.githubusercontent.com/litxiaoxi/LyLme-Spage-Palette/refs/heads/main/preview/9.png) | ![背景视频](https://raw.githubusercontent.com/litxiaoxi/LyLme-Spage-Palette/refs/heads/main/preview/10.png) |



## 安装说明

> 当前版本：**1.3.7**（版本号见 `theme.ini` 的 `theme_version`）

### 从 Release 安装（推荐）

1. 打开 [Releases](https://github.com/litxiaoxi/LyLme-Spage-Palette/releases) 下载最新版 `Palette-x.y.z.zip`
2. 解压得到 `palette/` 文件夹，整个放入 `lylme_spage/template/` 目录
3. 登录后台 → 主题设置 → 选择 "Palette" → 点击使用

### 手动部署

将本仓库根目录下的主题文件（`theme.ini`、`index.php`、`head.php`、`list.php`、`foot.php`、`functions.php`、`config.php`、`go.php`、`css/`、`js/`）复制到 `lylme_spage/template/palette/`。`https://raw.githubusercontent.com/litxiaoxi/LyLme-Spage-Palette/refs/heads/main/preview/`、`.git/`、`.gitignore` 仅为仓库展示与开发用途，部署时无需包含。

### 更新主题

1. 备份 `lylme_spage/template/palette/`（主题配置存于数据库，覆盖文件不会丢失）
2. 用新版文件覆盖该目录
3. 主题静态资源自带 `?v=主题版本` 缓存参数，正常刷新即可生效；仍看到旧样式时强制刷新一次

## 使用方法

### 切换色彩

页面顶部栏右侧有一个 **调色板图标**（🌐 地球仪图标），悬停或点击即可展开 6 个彩色圆点，点击色块切换。选择会自动保存到浏览器，刷新不丢失；键盘可用 `Tab` 聚焦、`Enter` 选择。

### 切换日间/夜间模式

点击调色板右侧的 **太阳/月亮图标** 即可切换。夜间模式为每种色彩提供了对应的深色版本。

### 切换背景

点击 **图片图标** 切换背景，仅在手动点击时生效：配置了视频地址时在 **视频 / 清除** 之间切换，未配置时在 **图片 / 清除** 之间切换。图片取自后台「背景设置」（PC / 手机自适应），视频地址在主题设置中配置；首次访问默认不显示图片或视频背景（仅主题渐变），切换后会按浏览器记忆恢复。

### 切换搜索引擎

点击搜索框左侧的引擎图标会弹出搜索引擎列表，点击任意引擎即可切换并自动收起；点击空白处、按 `Esc` 或再次点击按钮可关闭列表。选择会写入 `localStorage['theme_sou']`，刷新或更换主题后仍会保留。输入时仅对百度显示搜索建议。

### 键盘操作

顶部所有按钮（背景、调色板、夜间模式、更多）与分类导航均为原生 `button`，可通过 `Tab` 聚焦、`Enter` / `Space` 触发；`Esc` 可关闭已展开的调色板、更多菜单与搜索引擎列表。

## 主题设置

后台 → **主题设置 → 主题自定义设置**：

| 配置项 | 类型 | 说明 |
|--------|------|------|
| `background_video` | text | 背景视频 URL，支持 MP4；留空则背景切换时跳过视频，仅使用图片背景 |
| `gonganbei` | text | 公安备案号，显示在页脚；留空不显示 |
| `go_enable` | switch | 外链安全提醒页：开启后点击站外链接先经过 `go.php` 倒计时确认页，默认关闭 |

主题配置按 `theme_config_palette` 独立存储，不会与其他主题冲突。

## 自定义配色

编辑 `css/style.css`，在对应的 `[data-theme="..."]` 选择器中修改 CSS 变量即可调整配色。

例如，修改极光紫的强调色：

```css
[data-theme="purple"] {
    --accent: #7c5cfc;         /* 主色调 */
    --accent-light: #a78bfa;   /* 浅色调 */
    --card-bg: rgba(...);      /* 卡片背景 */
    ...
}
```

夜间模式配色在 `html.night-mode[data-theme="purple"]` 选择器中对应修改。

### 添加自定义色彩

1. 在 `css/style.css` 中添加新的 `[data-theme="yourcolor"]` 变量块
2. 在 `head.php` 的 `.palette-popover` 中按同样格式添加一个色块按钮（必须是 `button`，`span` 无法键盘聚焦）：
   ```html
   <button type="button" class="color-opt" data-color="yourcolor" title="你的色彩" aria-label="你的色彩"></button>
   ```
3. 在 `js/script.js` 的 `colors` 数组中添加 `'yourcolor'`

## 文件结构

```
palette/
├── theme.ini          # 主题元信息（名称、版本、作者等，JSON）
├── index.php          # 入口文件，加载 functions.php 与三个 partial
├── head.php           # 页面头部、顶栏、搜索区、时钟
├── list.php           # 分类导航条 + 链接列表
├── foot.php           # 页脚、背景变量、图标脚本、自定义统计代码
├── functions.php      # 官方辅助函数库（转义、标签、搜索引擎、备案）
├── config.php         # 主题自定义设置（background_video / gonganbei）
├── go.php             # 外部链接安全跳转页
├── css/
│   └── style.css      # 完整样式表（6 色彩 × 2 模式 = 12 套变量）
├── js/
│   └── script.js      # 唯一 JS 入口：配色、夜间、时钟、背景、搜索、分类
└── README.md
```

> `go.php` 是外链安全提醒页（站内直跳、站外 10 秒倒计时确认）。后台主题设置中的「外链安全提醒页」开关开启后，站外链接点击会经此页确认；默认关闭，关闭时删除该文件不影响现有功能。

## 开发规范

- 主题入口为 `index.php`，内部按 `head.php` / `list.php` / `foot.php` 拆分；官方允许作者自行拆分文件
- PHP 代码按 **PHP 5.4+** 语法编写：统一使用 `array()`，不使用 `??`、箭头函数、类型声明
- JS 统一使用 ES5 语法（`var` / `function`），不依赖 jQuery
- 文本输出统一经过 `theme_e()`；链接名称、图标等官方 HTML 字段除外
- 静态资源通过 `theme_css()` / `theme_js()` 加载，缓存版本取自 `theme.ini` 的 `theme_version`
- 图标脚本：按规范固定输出 `assets/js/icon.js` 与 `assets/js/svg.js` 两行（均带 `defer`，不阻塞解析），不做本地文件探测，`$cdnpublic` 指向 CDN 时同样按 CDN 路径加载
- 新特性降级：`inset` 前保留 `top/right/bottom/left` 四向声明；`backdrop-filter` 不支持时由 `@supports not` 分支提高底色不透明度，保证文字可读
- 交互控件使用原生 `button`，支持键盘聚焦与 `Enter` / `Space` / `Esc` 操作

## 浏览器兼容性

- Chrome / Edge 90+
- Firefox 90+
- Safari 15+
- 需要浏览器支持 CSS `backdrop-filter` 和 CSS Grid

## 许可

Apache License 2.0，与六零导航页主项目一致。

## 相关链接

- [六零导航页主项目](https://github.com/LyLme/lylme_spage)
- [Gitee 镜像](https://gitee.com/LyLme/lylme_spage)
- [主题开发文档](https://doc.lylme.com/spage/#/dev)