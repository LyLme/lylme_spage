# 六零导航页（lylme spage）主题生成提示词

> 用途：把本文件作为提示词，投喂给任意 AI 编程工具（如 WorkBuddy / ChatGPT / Claude / 通义灵码等），
> 让它基于官方 `dev-theme` 开发包，生成一个**可运行、合规**的六零导航页新主题。
> **主题风格由使用本提示词的人自由指定**（见下方「一、风格输入区」）。

---

## 〇、任务

你是六零导航页（lylme spage，开源导航网站程序）的主题开发助手。请根据**「一、风格输入区」**中用户给出的风格描述，基于官方 `dev-theme` 开发包规范，生成一个完整、可直接启用、零框架依赖的新主题。

要求：
- 严格遵循「二、技术约束」中的所有硬性规则（兼容性、安全性、契约）。
- 默认在官方开发包基础上做**风格化改造**（配色、布局、视觉语言、动效），不要改动数据接口与核心契约。
- 输出下列 7 个文件（目录名用小写英文，例如 `moonlight`）。

---

## 一、风格输入区（★由使用本提示词的人填写★）

使用本提示词时，请把下面这段替换成你想要的风格。写得越具体，生成结果越贴合。

```
【主题名称（英文/拼音，作为目录名与 theme_name，小写）】：
【风格关键词】（如：极简、玻璃拟态、赛博朋克、复古、自然、暗黑、儿童、二次元…）：
【主色 / 配色方案】（可给具体色值，如 #2f6fed + 浅灰 + 白）：
【整体氛围 / 情绪】（如：冷静专业、温暖治愈、科技未来、活泼可爱）：
【布局偏好】（如：卡片网格 / 瀑布流 / 全屏居中 / 左侧栏 / 圆形图标）：
【字体偏好】（如：圆润、纤细、等宽、衬线）：
【特殊视觉元素或动效】（如：毛玻璃、霓虹描边、悬浮放大、渐变背景、粒子、滚动视差）：
【是否默认暗色 / 亮色 / 跟随系统】：
【其它想要的点】：
```

> 如果用户没有给具体值，则由你（AI）在合理范围内自行补全一套完整、协调的设计，并在 README 中说明你的默认设定。

---

## 二、技术约束（★硬性，不可违反★）

### 2.1 程序环境与兼容性
- 目标程序：**六零导航页 v1.2.5 及以上**。
- **PHP 必须兼容 5.4+ / 7.x / 8.x**。强制规则：
  - 一律使用 `array()` 语法，**禁止短数组 `[]`**。
  - **禁止** PHP 7+ 专有语法：`??` 空合并、`<=>` 太空船、返回值/参数类型声明、`declare(strict_types=1)`、匿名类 `new class`、短 list 解构 `[$a,$b]`、箭头函数 `fn()=>`。
  - `htmlspecialchars()` **必须**显式传字符集：`htmlspecialchars($v, ENT_QUOTES, 'UTF-8')`。
  - 不要依赖 PHP 7+ 新增函数（如 `random_bytes()`），如必须用先 `function_exists()` 判断。
- **JS 必须 ES5**：用 `var` / `function`，**禁止** `let` / `const` / 箭头函数 / 模板字符串（反引号）。
- CSS 使用新特性时要有降级方案（如 `:has()` 需改为兄弟选择器）。

### 2.2 设计原则
- **单文件入口、零框架、轻量**。页面结构全部写在 `index.php` 内，用注释分区，不必拆文件。
- 静态资源唯一入口：`css/style.css`、`js/script.js`。
- 换肤通过 `:root` CSS 变量实现；可后台配置的项用 `config.php` 暴露。

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

### 2.6 index.php 骨架（必须保留的契约）
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
    <!-- 导航：foreach (theme_tags() as $tag) -->
    <!-- 模块：时钟(in_array('clock',$modules))、随机一言($conf['yan']=='true' 时 yan()) -->
    <!-- 搜索区：见 2.8 DOM 契约 -->
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
```php
<?php
$html = array(
  'g1' => '<div class="link-group">',
  'g2' => '<h2 class="group-title">{group_icon}<span>{group_name}</span></h2>',
  'g3' => '</div>',
  'l1' => '<a class="link-item" href="{link_url}" target="_blank" rel="nofollow noopener" title="{link_name_text}">',
  'l2' => '<span class="link-icon">{link_icon}</span><span class="link-name">{link_name}</span>',
  'l3' => '</a>',
);
lists($html);
?>
```

### 2.8 搜索区 DOM 契约（js 依赖，必须保持）
```html
<form class="search-form" id="search-form" action="#">
    <input type="text" class="search-input" id="search-input" placeholder="请输入搜索内容" autocomplete="off">
    <button type="submit" class="search-btn">搜索</button>
</form>
<div class="search-engines">
  <!-- 每个引擎：radio name="sou"，value=接口地址，data-alias=别名，data-hint=提示；首个 checked -->
  <label class="engine" style="--engine-color: <?php echo theme_e($sou['color']); ?>;">
    <input type="radio" name="sou" value="<?php echo theme_e($sou['link']); ?>"
           data-alias="<?php echo theme_e($sou['alias']); ?>" data-hint="<?php echo theme_e($sou['hint']); ?>"
           <?php echo $index === 0 ? 'checked' : ''; ?>>
    <?php echo $sou['icon'] . "\n"; ?>
    <span class="engine-name"><?php echo theme_e($sou['name']); ?></span>
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
- 可按风格增删配置（如：圆角大小、卡片透明度、是否毛玻璃、强调色等），增强可玩性。

---

## 三、生成步骤（AI 执行顺序）

1. 解析「一、风格输入区」→ 形成一套完整视觉设计规范（配色、间距、圆角、字体、动效）。
2. 确定目录名（小写英文）与 `theme_name`，写 `theme.ini`。
3. 复制 2.5 的 `functions.php`，按需扩展（保持 `theme_` 前缀与 `function_exists()` 守卫，**不要重复定义**核心已有函数）。
4. 写 `config.php`：保留 2.11 通用项，按风格增删。
5. 写 `index.php`：遵循 2.6 骨架 + 2.7/2.8 契约，把风格转成 DOM 结构与 class。
6. 写 `css/style.css`：`:root` 变量化（含主题色、文字色、卡片背景/圆角/阴影、间距、列数），实现风格视觉，**保留 768px 移动端断点**（手机端链接列表降为 2 列）。
7. 写 `js/script.js`：ES5，实现搜索引擎切换/记忆/提交 + 时钟，风格化动效。
8. 写 `README.md`：含风格说明、配置项说明、演示地址、适用版本、自检清单。
9. 自查（见第四节）。

---

## 四、发布自检清单（生成后逐条核对）

- [ ] `index.php` / `theme.ini` 存在；theme.ini 前六字段齐全、JSON 合法、版本三段式。
- [ ] 目录名与 `theme_name` 对应。
- [ ] PHP 无 7+ 专有语法（PHP 5.4 可解析）；统一 `array()`。
- [ ] 所有文本输出经 `theme_e()`（`link_name`/`sou_icon` 除外）。
- [ ] 保留 `$conf['wztj']` 与 `icon.js`和`svg.js` 引入。
- [ ] 未依赖白名单外配置键。
- [ ] 多选项读取已做非数组兜底。
- [ ] 768px 断点下排版正常（链接列表 2 列）。
- [ ] JS 为 ES5，无 `let/const/箭头/模板字符串`。
- [ ] `lists()` 接口结构完整；搜索区 DOM 契约保持。
- [ ] README 含配置说明与演示地址。

---

## 五、输出格式要求

请一次性给出全部 7 个文件的**完整代码**（不要省略、不要写「此处省略」），并在开头用 3-5 句话概述你基于用户输入所做的风格设定与默认补全项。若用户输入的风格信息不足，明确说明你采用的默认设定。
