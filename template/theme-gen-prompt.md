# 六零导航页（lylme spage）主题生成提示词

> 用途：把本文件作为提示词，投喂给任意 AI 编程工具（如 WorkBuddy / ChatGPT / Claude / 通义灵码等），
> 让它基于官方开发包规范，生成一个**可运行、合规、且与官方 `dev-theme` 风格截然不同**的六零导航页新主题。
> **主题风格由使用本提示词的人自由指定**（见下方「一、风格输入区」）。

---

## 〇、任务

你是六零导航页（lylme spage，开源导航网站程序）的主题开发助手。请根据**「一、风格输入区」**中用户给出的风格描述，生成一个完整、可直接启用、零框架依赖的新主题。

### 核心要求（务必贯穿始终）

1. **只保留「契约」代码，其余全部从零原创。**
   你必须原样照搬的只有「二、必须保留的契约」里的内容：数据接口函数、主题信息、配置项、链接列表 `lists()` 接口、搜索区 DOM 锚点、系统配置白名单、输出安全规则、PHP/JS 兼容性硬约束。
   **除此之外的一切——`index.php` 的 HTML 结构、class 命名、整体布局、全部 CSS、动效——都要基于风格关键词大胆重构，禁止把官方 `dev-theme` 当模板去改色或微调。**

2. **禁止产出"dev-theme 换色版"。**
   官方 `dev-theme` 只是「契约样本 / 参考实现」，目的是告诉你接口怎么接，**不是给你套用的视觉模板**。它的 HTML 骨架、class 命名、CSS 变量体系、配色，一律不得复用（详见「三、视觉与布局：大胆原创」中的禁止清单）。

3. **大胆、具体、有辨识度。**
   优先做"一眼就能看出是什么风格"的设计，而不是安全的居中卡片网格。布局原型、配色体系、字体气质、点缀元素都要从关键词里长出来，而不是从 dev-theme 里改出来。

4. 输出下列 7 个文件（目录名用小写英文，例如 `moonlight`）。

---

## 一、风格输入区（★由使用本提示词的人填写★）

使用本提示词时，请把下面这段替换成你想要的风格。写得越具体、越有画面感，生成结果越贴合；**留白越多，AI 越要大胆发挥**（见第三节原型灵感）。

```
【主题名称（英文/拼音，作为目录名与 theme_name，小写）】：
【风格关键词】（如：极简、玻璃拟态、赛博朋克、复古、自然、暗黑、儿童、二次元、杂志、终端、像素…）：
【主色 / 配色方案】（可给具体色值，或只给感觉如"低饱和莫兰迪""高对比霓虹"）：
【整体氛围 / 情绪】（如：冷静专业、温暖治愈、科技未来、活泼可爱、疏离神秘）：
【布局偏好】（如：全屏居中 / 左侧固定栏 / 横向滑动 / 分屏双栏 / 瀑布流 / 杂志大字 / 终端风 / 便签墙 / 辐射圆 / 极简留白）：
【字体偏好】（如：圆润、纤细、等宽、衬线、手写、像素）：
【特殊视觉元素或动效】（如：毛玻璃、霓虹描边、悬浮放大、渐变流体背景、粒子、滚动视差、噪点质感、描边动画）：
【是否默认暗色 / 亮色 / 跟随系统】：
【其它想要的点】：
```

> 如果用户没有给具体值，则由你（AI）在合理范围内**自行设计一整套协调、有强风格倾向的方案**，并在 README 中说明你的默认设定与采用的布局原型。

---

## 二、必须保留的契约（★硬性，原样照搬，不要在里面"发挥"★）

> 这一节的内容是程序能跑起来的前提，**逐字保留即可**：不要改写函数逻辑，不要改 DOM 锚点，不要增删白名单键。
> 它们只负责"数据与接口"，与"视觉长什么样"完全无关——视觉在第三节另起炉灶。

### 2.1 程序环境与兼容性
- 目标程序：**六零导航页 v1.2.5 及以上**。
- **PHP 必须兼容 5.4+ / 7.x / 8.x**。强制规则：
  - 一律使用 `array()` 语法，**禁止短数组 `[]`**。
  - **禁止** PHP 7+ 专有语法：`??` 空合并、`<=>` 太空船、返回值/参数类型声明、`declare(strict_types=1)`、匿名类 `new class`、短 list 解构 `[$a,$b]`、箭头函数 `fn()=>`。
  - `htmlspecialchars()` **必须**显式传字符集：`htmlspecialchars($v, ENT_QUOTES, 'UTF-8')`。
  - 不要依赖 PHP 7+ 新增函数（如 `random_bytes()`），如必须用先 `function_exists()` 判断。
- **JS 必须 ES5**：用 `var` / `function`，**禁止** `let` / `const` / 箭头函数 / 模板字符串（反引号）。
- 新版 CSS 特性要有降级方案（如 `:has()` 需改为兄弟选择器）。

### 2.2 设计原则（仅约束"工程形态"，不约束视觉）
- **单文件入口、零框架、轻量**。页面结构全部写在 `index.php` 内，用注释分区，不必拆文件。
- 静态资源唯一入口：`css/style.css`、`js/script.js`。
- 换肤通过 `:root` CSS 变量实现（**变量名你自己定义，不要照抄 dev-theme**）；后台可配项用 `config.php` 暴露。

### 2.3 必须输出的文件与目录
```
你的主题目录名/                # 小写英文，与 theme_name 对应
├── index.php                 # 入口（必须）
├── theme.ini                 # 主题信息（必须，JSON）
├── config.php                # 主题自定义配置表单（建议）
├── functions.php             # 辅助函数库（必须，见 2.5）
├── README.md                 # 主题说明（建议）
├── css/style.css             # 样式（必须，唯一入口）
└── js/script.js              # 脚本（必须，唯一入口）
```

### 2.4 theme.ini 规范（JSON，前六字段必填）
```json
{
    "author_name": "作者名",
    "author_link": "https://你的主页",
    "theme_name": "目录名（如 Moonlight）",
    "theme_version": "1.0.0",
    "theme_explain": "主题一句话说明",
    "theme_demo": "https://演示地址",
    "requires": ">=1.2.5",
    "updated_at": "2026-01-01",
    "license": "MIT",
    "theme_course": "https://doc.lylme.com/spage/"
}
```
- `theme_version` 三段式（`1.0.0`），改动静态资源后 +1 即可刷新缓存（函数自动拼 `?v=`）。
- 不支持 HTML 标签。

### 2.5 functions.php（必须原样包含以下函数，可在此基础上扩展，新增函数一律 `theme_` 前缀）
```php
<?php
function theme_version() {
    static $version = null;
    if ($version === null) {
        $ini = @file_get_contents(__DIR__ . '/theme.ini');
        $info = is_string($ini) ? json_decode($ini, true) : array();
        $raw = (is_array($info) && isset($info['theme_version'])) ? $info['theme_version'] : '1.0.0';
        $version = preg_replace('/[^a-zA-Z0-9]/', '', $raw);
        if ($version === '') { $version = '100'; }
    }
    return $version;
}
function theme_css($path = 'css/style.css') {
    global $templatepath;
    $href = rtrim($templatepath, '/') . '/' . ltrim($path, '/') . '?v=' . theme_version();
    echo '<link rel="stylesheet" href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '">' . "\n";
}
function theme_js($path = 'js/script.js') {
    global $templatepath;
    $src = rtrim($templatepath, '/') . '/' . ltrim($path, '/') . '?v=' . theme_version();
    echo '<script defer src="' . htmlspecialchars($src, ENT_QUOTES, 'UTF-8') . '"></script>' . "\n";
}
function theme_e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
function theme_tags() {
    global $site, $DB;
    $tags = array();
    $result = $site->getTags();
    while ($row = $DB->fetch($result)) {
        $tags[] = array(
            'id'    => $row['tag_id'],
            'name'  => $row['tag_name'],
            'link'  => $row['tag_link'],
            'blank' => ((int) $row['tag_target'] === 1),
        );
    }
    return $tags;
}
function theme_sou() {
    global $site, $DB;
    $list = array();
    $result = $site->getSou();
    while ($row = $DB->fetch($result)) {
        if ((int) $row['sou_st'] !== 1) { continue; }
        $link = $row['sou_link'];
        if (checkmobile() && !empty($row['sou_waplink'])) { $link = $row['sou_waplink']; }
        $list[] = array(
            'alias' => $row['sou_alias'], 'name' => $row['sou_name'],
            'hint'  => $row['sou_hint'],  'icon' => $row['sou_icon'],
            'color' => $row['sou_color'], 'link' => $link,
        );
    }
    return $list;
}
function theme_icp() {
    $icp = isset($GLOBALS['conf']['icp']) ? trim((string) $GLOBALS['conf']['icp']) : '';
    if ($icp === '') { return; }
    echo '<a class="icp" href="https://beian.miit.gov.cn/" target="_blank" rel="nofollow noopener">'
        . theme_e($icp) . '</a>' . "\n";
}
function theme_security_filing($record = 'gonganbei') {
    $record = trim((string) theme_config($record, ''));
    if ($record === '') { return; }
    preg_match_all('/\d+/', $record, $gab);
    $code = isset($gab[0][0]) ? $gab[0][0] : '';
    echo '<a class="gonganbei" href="http://www.beian.gov.cn/portal/registerSystemInfo?recordcode='
        . theme_e($code) . '" target="_blank" rel="nofollow noopener">'
        . theme_e($record) . '</a>' . "\n";
}
```

### 2.6 index.php 入口契约（只管"接口与必引入项"，不管视觉骨架）
> 下面给出**最小必要骨架**：`require` 函数库、必引入的 css/js、统计代码、图标雪碧图必须原样保留。
> 中间的导航 / 搜索 / 列表区域，**结构、class、排布全部由你原创**（见第三节），不要在 dev-theme 的 `.site-header` / `.site-main` / `.links` 骨架上改。

```php
<?php
require_once __DIR__ . '/functions.php';

// 逻辑区（条件渲染集中处理，输出到变量）
$background_url = background();
$theme_color = theme_config('color', '#2f6fed');
$link_cols   = theme_config('link_cols', 4);
$notice      = trim((string) theme_config('notice', ''));
$modules     = theme_config('modules', array('clock'));
if (!is_array($modules)) { $modules = array($modules); }
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo theme_e($conf['title']); ?></title>
    <meta name="keywords" content="<?php echo theme_e($conf['keywords']); ?>">
    <meta name="description" content="<?php echo theme_e($conf['description']); ?>">
    <link rel="icon" href="<?php echo theme_e($conf['logo']); ?>">
    <?php theme_css('css/style.css'); ?>
    <?php theme_js('js/script.js'); ?>
</head>
<body class="<?php echo checkmobile() ? 'is-mobile' : 'is-pc'; ?>">
    <!-- 导航：foreach (theme_tags() as $tag) —— 结构与 class 自定 -->
    <!-- 模块：时钟(in_array('clock',$modules))、随机一言($conf['yan']=='true' 时 yan()) -->
    <!-- 搜索区：见 2.8 DOM 锚点契约 -->
    <!-- 链接列表：见 2.7 lists() 接口 -->
    <footer>
        <?php echo $conf['copyright']; ?>
        <?php theme_icp(); ?>
        <?php theme_security_filing(); ?>
    </footer>

    <?php echo $conf['wztj']; ?>   <!-- 必须：统计代码，原样输出在最底部 -->
    <script src="<?php echo $cdnpublic; ?>/assets/js/icon.js"></script>  <!-- 必须：图标雪碧图，删除后图标全空白 -->
    <script src="<?php echo $cdnpublic; ?>/assets/js/svg.js"></script>  <!-- 必须：旧版图标雪碧图，删除后图标全空白 -->
</body>
</html>
```

### 2.7 链接列表接口 `lists($html)`
- 仅以下键：`g1`分组开始 / `g2`分组内容 / `g3`分组结束 / `l1`链接开始 / `l2`链接内容 / `l3`链接结束。
- 可用占位符：`{group_id}` `{group_name}` `{group_icon}`；`{link_id}` `{link_name}`(可能含`<font>`色标，原样输出不转义) `{link_name_text}`(纯文本) `{link_url}` `{link_icon}`(缺失自动补默认) `{link_desc}`。
- **注意**：下面 `l1/l2/g2` 里的 class 名（如 `link-item` `link-icon`）只是占位示例，**你必须替换成自己的原创命名**，并在自己的 `style.css` 里写对应的原创样式。不要原样照抄这一套 class。

```php
<?php
$html = array(
  'g1' => '<YOUR_OWN_GROUP_WRAPPER>',
  'g2' => '<YOUR_OWN_GROUP_TITLE>{group_icon}<span>{group_name}</span></YOUR_OWN_GROUP_TITLE>',
  'g3' => '</YOUR_OWN_GROUP_WRAPPER>',
  'l1' => '<a class="YOUR_OWN_LINK" href="{link_url}" target="_blank" rel="nofollow noopener" title="{link_name_text}">',
  'l2' => '<span class="YOUR_OWN_ICON">{link_icon}</span><span class="YOUR_OWN_NAME">{link_name}</span>',
  'l3' => '</a>',
);
lists($html);
?>
```

### 2.8 搜索区 DOM 锚点契约（JS 依赖，只保这些"锚点"，外观与排布随意）
> JS 只读下面这些 **id / name / data-属性**，class 名、包裹结构、视觉全由你定。
> 务必保留：`form` 的 `id="search-form"`、`input` 的 `id="search-input"`、`radio` 的 `name="sou"`、`value=接口地址`、`data-alias=别名`、`data-hint=提示`；首个 `checked`。

```html
<form id="search-form" action="#">
    <input type="text" id="search-input" placeholder="请输入搜索内容" autocomplete="off">
    <button type="submit">搜索</button>
</form>
<div class="YOUR_OWN_ENGINE_LIST">
  <!-- 每个引擎：radio name="sou"，value=接口地址，data-alias=别名，data-hint=提示；首个 checked -->
  <label style="--engine-color: <?php echo theme_e($sou['color']); ?>;">
    <input type="radio" name="sou" value="<?php echo theme_e($sou['link']); ?>"
           data-alias="<?php echo theme_e($sou['alias']); ?>" data-hint="<?php echo theme_e($sou['hint']); ?>"
           <?php echo $index === 0 ? 'checked' : ''; ?>>
    <?php echo $sou['icon'] . "\n"; ?>
    <span><?php echo theme_e($sou['name']); ?></span>
  </label>
</div>
```
- 提交由 JS 拦截：拼接 `接口地址 + encodeURIComponent(关键词)` 新窗口打开（各引擎参数名不统一）。
- 选择记忆写入 `localStorage['theme_sou']`（键全站统一，换主题保留）。
- JS 用 ES5 实现：切换引擎更新 placeholder + 记忆；载入时恢复；提交时拼接跳转；时间显示模块需做「节点不存在则跳过」的兼容。

### 2.9 系统配置白名单（只可用这些 `$conf` 键）
`title` `keywords` `description` `logo` `background`/`wap_background`(用 `background()` 自适应) `copyright`(HTML原样) `wztj`(HTML/JS，原样) `icp` `yan`(判断用 `=='true'`) `tq`(天气开关，主题自决) `template`/`version`/`cdnpublic`(系统项)。
**不要依赖**：`apply` `about` `home-title` `admin_user` `admin_pwd` `wxplus` 等。
> 注意：`$conf['version']` 形如 `v2.6.0`（带 v），**不能**用作缓存版本；缓存版本取 `theme.ini` 的 `theme_version`。

### 2.10 安全与输出规则
- **所有文本输出经 `theme_e()`**；例外：`{link_name}`、`sou_icon`（官方 HTML，原样输出）。
- 多选项（checkbox）读取做类型兜底（全不选时核心给空数组，主题侧仍 `if(!is_array($modules)) $modules=array($modules);`）。
- 导航/引擎/图标等循环输出必须正确转义。

### 2.11 config.php 配置表单
- 返回 PHP 数组 `$theme_config`，元素含：`type`(text/textarea/select/checkbox/radio/color，开关建议用 radio 替代 switch)、`name`(主题内唯一，无需前缀)、`title`、`description`、`value`、`enum`(键值对)、`verify`(required/url/number/email)。
- **必须保留**通用配置（可改默认值以贴合风格）：主色 `color`(color)、列表列数 `link_cols`(select 3~6)、首页模块 `modules`(checkbox，含 clock)、首页公告 `notice`(textarea，支持HTML)、公安备案号 `gonganbei`(text)。
- 可按风格增删配置（如：圆角大小、卡片透明度、是否毛玻璃、强调色、背景模糊度、字体族等），增强可玩性。

---

## 三、视觉与布局：大胆原创（★本节是本次生成的重点，禁止照搬 dev-theme★）

> 这一节决定主题"像不像 dev-theme"。请把它当成硬约束来执行。

### 3.1 禁止清单（出现任意一条即视为不合格）
**禁止复用 dev-theme 的下列 class 名**（即使你改了颜色也不行）：
`site-header` `site-logo` `site-nav` `site-main` `clock` `clock-time` `clock-date` `yan` `search` `search-form` `search-input` `search-btn` `search-engines` `engine` `engine-name` `notice` `links` `link-group` `group-title` `link-item` `link-icon` `link-name` `site-footer` `icp` `gonganbei`

**禁止照搬 dev-theme 的 CSS 变量体系**：不要原样使用 `--theme-color / --text-color / --text-muted / --card-bg / --card-radius / --card-shadow / --gap / --link-cols` 这一套命名与数值。你应当**定义自己的一套语义变量**（命名体现你的风格，例如 `--ink` `--paper` `--accent` `--glow` `--depth` 等，随风格自定）。

**禁止套用 dev-theme 的布局骨架**：即"顶部一条横向导航 → 中间 max-width:1200px 居中容器 → 搜索框居中 → 下方卡片网格（CSS grid + repeat(var(--link-cols),1fr)）"这种组合。这是最容易被复制的默认版式，务必避开。

### 3.2 布局原型灵感（至少从里面挑一个，或融合，或自己发明；不要退回默认版式）
- **全屏英雄居中**：时钟/一言作视觉主体占满首屏，搜索框悬浮中央，链接区在下方可滚。
- **左侧固定侧栏**：导航与品牌固定在左侧竖向栏，右侧为可滚动的内容画布（移动端折叠为顶部条）。
- **横向滑动分类**：每个分组是一行可横向滑动的卡片，整页纵向排布"轨道"。
- **分屏双栏**：左半是信息/时钟/搜索的"控制台"，右半是链接瀑布；或大图背景 + 前景浮层。
- **不规则网格 / 瀑布流**：卡片尺寸、跨列、错位随内容变化，拒绝整齐方格。
- **杂志 / 编辑风**：超大衬线标题、分栏排版、首字下沉、留白即设计。
- **终端 / CLI 风**：等宽字体、`$`、`>` 提示符、块光标闪烁、纯文本质感。
- **便签 / 卡片墙**：链接像便利贴、拍立得、笔记本页随意散布（用旋转/偏移制造手感）。
- **辐射 / 同心圆**：链接以中心为原点环形或射线排布，悬停展开。
- **极简留白**：巨量负空间、单一焦点、近乎无边框，靠排版与字重建立层级。
- **材质拟态**：新拟物（soft UI）、黏土（claymorphism）、玻璃（glassmorphism）、噪点纸纹、金属拉丝等其中一种明确质感。
- **暗黑霓虹 / 赛博朋克**：深色底 + 高饱和描边/辉光/扫描线/网格地平线。
- **像素 / 复古**：像素质感边框、CRT 扫描线、8-bit 配色、复古操作系统窗口。
- **日系 / 自然**：和纸底纹、低饱和、手写感、留白与季节意象。

> 鼓励：把上述多个原型融合，或提出文档没列的原型。目标是**风格一眼可辨**。

### 3.3 配色与视觉语言
- 配色必须**从关键词推导**，形成有逻辑的色板（主色 / 背景 / 文字 / 强调 / 阴影或辉光），而不是只改一个 `--theme-color`。
- 暗色 / 亮色 / 跟随系统由风格决定；如做双主题，用自己命名的变量组切换。
- 字体气质要与风格一致（圆润→儿童/治愈；纤细无衬线→极简/科技；衬线→杂志/复古；等宽→终端；像素字体→像素风）。优先用系统字体栈与 `background()` 背景图，**不要引入外部字体 CDN 依赖**（保持零框架、可离线）。

### 3.4 CSS 必须原创
- 自己写 `:root` 变量体系、自己命名 class、自己写每一处样式。**不要从 dev-theme 的 style.css 复制任何规则再调参数。**
- 允许使用现代 CSS（`grid` / `flex` / `clamp()` / `min()` / `gradient` / `transform` / `filter` / `mask`），但需给出老旧浏览器的可用降级（如 `:has()` 改兄弟选择器）。
- 动效要有风格：悬停、入场、加载、焦点态都服务于整体气质（如赛博用辉光脉冲，便签用轻微旋转回正，玻璃用模糊与位移）。

### 3.5 移动端（保留 768px 断点，但版式自定）
- 必须有 `@media (max-width: 768px)` 适配，保证手机可用、不溢出、可点。
- 不一定"降为 2 列"——如果你的原型是侧栏，就折叠成顶部导航；是横向轨道，就保持滑动；按你的布局自然收敛即可。

---

## 四、生成步骤（AI 执行顺序）

1. 解析「一、风格输入区」→ 选定语义布局原型（来自第三节），形成一整套**原创**视觉规范（配色、间距、圆角/质感、字体、动效）。
2. 确定目录名（小写英文）与 `theme_name`，写 `theme.ini`（用 2.4）。
3. 复制 2.5 的 `functions.php`，按需扩展（保持 `theme_` 前缀与 `function_exists()` 守卫，**不要重复定义**核心已有函数）。
4. 写 `config.php`：保留 2.11 通用项，按风格增删（变量命名贴合你的主题）。
5. **写 `index.php`：抛弃 dev-theme 骨架，按你选的布局原型原创 DOM 结构与 class；只保留 2.6 的入口契约与必引入项、2.7/2.8 的接口锚点。**
6. **写 `css/style.css`：从零原创，定义自己的变量体系与全部样式，实现你选的布局原型与风格视觉；含 768px 移动端适配（版式自定）。**
7. 写 `js/script.js`：ES5，保留搜索切换/记忆/提交 + 时钟（DOM 锚点同 2.8），可叠加风格化动效；做"节点不存在则跳过"兼容。
8. 写 `README.md`：含风格说明、**采用的布局原型**、配置项说明、演示地址、适用版本、自检清单。
9. 自查（见第五节），特别核对「是否仍长得像 dev-theme」。

---

## 五、发布自检清单（生成后逐条核对）

- [ ] `index.php` / `theme.ini` 存在；theme.ini 前六字段齐全、JSON 合法、版本三段式。
- [ ] 目录名与 `theme_name` 对应。
- [ ] PHP 无 7+ 专有语法（PHP 5.4 可解析）；统一 `array()`。
- [ ] 所有文本输出经 `theme_e()`（`link_name`/`sou_icon` 除外）。
- [ ] 保留 `$conf['wztj']` 与 `icon.js`/`svg.js` 引入。
- [ ] 未依赖白名单外配置键。
- [ ] 多选项读取已做非数组兜底。
- [ ] JS 为 ES5，无 `let/const/箭头/模板字符串`；搜索 DOM 锚点（id/form/input/name="sou"/data-alias/data-hint）完整。
- [ ] `lists()` 接口结构完整；占位符使用正确。
- [ ] 768px 断点下排版正常、不溢出。
- [ ] **【创新校验】未使用第三节 3.1 禁止清单里的任何 dev-theme class 名。**
- [ ] **【创新校验】CSS 变量体系为自创，未原样照搬 `--theme-color/--card-bg/--link-cols` 等。**
- [ ] **【创新校验】布局不是"顶部导航条 + 居中 1200px 容器 + 卡片网格"的 dev-theme 默认版式，而是明确采用了第三节的某一种（或融合的）原创原型。**
- [ ] README 含配置说明、演示地址、**采用的布局原型**。

---

## 六、输出格式要求

请一次性给出全部 7 个文件的**完整代码**（不要省略、不要写「此处省略」），并在开头用 3-5 句话概述你基于用户输入所做的风格设定、采用的**布局原型**、以及默认补全项。若用户输入的风格信息不足，明确说明你采用的默认设定与选型理由。
