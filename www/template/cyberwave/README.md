# CyberWave · 赛博朋克主题（六零导航页 lylme spage）

> 暗黑霓虹 + 网格地平线 + CRT 扫描线 + 辉光描边的赛博朋克风格导航主题。基于 `theme-gen-prompt.md` 规范从零原创，**未复用官方 `dev-theme` 的任何视觉片段**。

## 一、风格设定（用户输入留白时的默认补全）

| 项目 | 设定 |
| --- | --- |
| 主题名称 | `CyberWave`（目录 `cyberwave`） |
| 风格关键词 | 赛博朋克 / 暗黑霓虹 / 故障美学 / 终端控制台 |
| 配色 | 深空黑底 `#05060a` + 霓虹青 `#00f0ff`（主）+ 霓虹品红 `#ff2e97`（强调）+ 微光绿点缀 |
| 氛围 | 科技未来、冷峻、疏离而充满能量感 |
| 布局原型 | **左侧固定侧栏 + 右侧可滚画布**（融合「暗黑霓虹 / 赛博朋克」原型），非顶部导航条 + 居中卡片网格 |
| 字体 | 界面用系统无衬线栈；时钟 / 提示符 / 编号用等宽字体（Consolas / Courier New） |
| 特殊元素 | 网格地平线、CRT 扫描线、品牌故障字（glitch）、霓虹辉光脉冲、链接悬浮上移 + 左侧扫光 |
| 暗/亮 | 默认暗色（赛博朋克基调，无亮色模式） |
| 零依赖 | 无外部字体 CDN，全部用系统字体栈与 `background()` 背景图，可离线 |

## 二、采用的布局原型

**左侧固定侧栏（sticky rail）+ 右侧内容画布（scroll stage）**，属于规范第三节「左侧固定侧栏」与「暗黑霓虹 / 赛博朋克」的融合：

- 左侧 `cw-rail`：品牌（故障字）+ 分类导航 + 控制台（霓虹时钟 / 随机一言 / 公告）。
- 右侧 `cw-stage`：英雄区（搜索控制台 + 引擎胶囊）+ 链接区（每个分组是一条网格轨道）。
- 全屏环境层：`cw-grid` 透视网格地平线、`cw-scan` CRT 扫描线（可关）、可选 `cw-bg` 自定义背景。
- 移动端（≤768px）：侧栏折叠为顶部条，链接区降为 2 列；≤480px 再降为 1 列。

## 三、配置项（后台 `config.php`）

| 配置名 | 类型 | 说明 | 默认 |
| --- | --- | --- | --- |
| `color` | color | 主题主色（霓虹青），驱动辉光与描边 | `#00f0ff` |
| `link_cols` | select | 链接区每行列数（3~6） | `3` |
| `modules` | checkbox | 首页模块：霓虹时钟 `clock` / 随机一言 `yan` | `clock` |
| `notice` | textarea | 首页公告，支持 HTML | 空 |
| `gonganbei` | text | 公安备案号 | 空 |
| `accent` | color | 强调色（霓虹品红），用于分隔线与品牌标识 | `#ff2e97` |
| `scanline` | radio | CRT 扫描线开关 | `on` |
| `glow` | radio | 辉光强度：弱 / 中 / 强 | `mid` |

## 四、文件结构

```
cyberwave/
├── index.php          # 入口（原创 DOM 结构，保留契约锚点）
├── theme.ini          # 主题信息（JSON）
├── config.php         # 主题配置表单
├── functions.php      # 辅助函数（原样保留契约函数）
├── README.md          # 本说明
├── css/style.css      # 原创样式（自创变量体系）
└── js/script.js       # ES5 脚本（搜索 + 时钟）
```

## 五、适用版本与演示

- 适用：六零导航页 `v1.2.5` 及以上
- 演示地址：https://doc.lylme.com/spage/

## 六、自检清单（发布前核对）

- [x] `index.php` / `theme.ini` 存在；theme.ini 前六字段齐全、JSON 合法、版本三段式。
- [x] 目录名 `cyberwave` 与 `theme_name` 对应。
- [x] PHP 无 7+ 专有语法（统一 `array()`，`htmlspecialchars` 显式传 `UTF-8`）。
- [x] 文本输出经 `theme_e()`（`link_name` / `sou_icon` 除外）。
- [x] 保留 `$conf['wztj']` 与 `icon.js` / `svg.js` 引入。
- [x] 未依赖白名单外配置键；多选项读取已做非数组兜底。
- [x] JS 为 ES5，无 `let/const/箭头/模板字符串`；搜索 DOM 锚点完整（`id="search-form"` / `id="search-input"` / `name="sou"` / `data-alias` / `data-hint` / 首个 `checked` / `localStorage['theme_sou']`）。
- [x] `lists()` 接口结构完整，占位符使用正确。
- [x] 768px / 480px 断点下排版正常、不溢出。
- [x] 未使用 `site-header` / `site-nav` / `clock` / `search` / `link-item` / `link-group` 等禁止清单 class。
- [x] CSS 变量体系为自创（`--cy-*`），未照搬 `--theme-color / --card-bg / --link-cols` 等。
- [x] 布局采用「左侧固定侧栏 + 暗黑霓虹」原创原型，非 dev-theme 默认版式。
