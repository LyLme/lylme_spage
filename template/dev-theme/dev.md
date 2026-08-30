### 前言

>   主题开发仅支持 **六零导航页 v1.2.5** 及以上版本

>   文档最后更新：2026 年 8 月 29 日

#### 主题投稿方式

- QQ群：933434961
- 邮件：admin@lylme.com
- PR：Gitee、Github

### 开始

#### 开发包

[下载开发包-dev-theme.zip](https://cdn.lylme.com/lylme_spage/themes/dev-theme.zip)

官方开发包即 `template/dev-theme` 脚手架主题：**复制整个目录 → 改名 → 改 CSS**，即可开始开发。

```
复制 template/dev-theme  →  template/你的主题目录名（小写英文）
修改 theme.ini          →  theme_name 与目录名对应，theme_version 用三段式
修改 config.php         →  增删主题自己的配置项
在后台「系统设置 - 网站主题」切换启用
```

### 目录结构

```
/template								# 主题目录（主题上传目录）
└── dev-theme							# 主题文件夹（目录名 = theme_name 的小写形式）
        ├── index.php					# 主题入口文件 (必须)
        ├── theme.ini					# 主题说明 (必须)
        ├── config.php					# 主题自定义设置配置文件 (可选)
        ├── functions.php				# 主题辅助函数库 (建议，见「辅助函数库」)
        ├── README.md					# 主题说明文档 (建议)
        ├── css							# css目录
        │   └── style.css				# 样式文件（唯一入口）
        ├── js							# js目录
        │   └── script.js				# 脚本文件（唯一入口）
        └── img							# 图片目录 (可选)
```

#### 文件说明

| 文件名        | 作用           | 必须         |
| ------------- | -------------- | ------------ |
| index.php     | 主题入口文件   | 是           |
| theme.ini     | 主题信息文件   | 是           |
| config.php    | 主题配置文件   | 否           |
| functions.php | 主题辅助函数库 | 否(建议包含) |
| css/style.css | 主题样式文件   | 否           |
| js/script.js  | 主题脚本文件   | 否           |
| README.md     | 主题说明文档   | 否           |

**设计原则：轻量、单文件、零框架。** 页面结构全部写在 `index.php` 内，用注释分区即可，不必拆成 `head.php`/`list.php` 等多个文件（核心不感知主题内部文件，拆分与否由作者决定）。

### 主题信息

**文件：theme.ini**

**格式：json**

**说明：** 前六个字段必填，其余可选；不支持 HTML 标签

```json
{
    "author_name": "六零",
    "author_link": "https://gitee.com/lylme",
    "theme_name": "DevTheme",
    "theme_version": "1.0.0",
    "theme_explain": "基于官方开发包开发",
    "theme_demo": "https://hao.lylme.com/theme/dev-theme",
    "requires": ">=1.2.5",
    "updated_at": "2026-08-29",
    "license": "MIT",
    "theme_course": "https://doc.lylme.com/spage/"
}
```

| 参数          | 说明                                     | 必填 |
| ------------- | ---------------------------------------- | ---- |
| author_name   | 作者名称                                 | 是   |
| author_link   | 作者主页                                 | 是   |
| theme_name    | 主题名称（与目录名对应）                 | 是   |
| theme_version | 主题版本，**三段式**如 `1.0.0`           | 是   |
| theme_explain | 主题说明                                 | 是   |
| theme_demo    | 演示站点                                 | 是   |
| requires      | 最低程序版本                             | 否   |
| updated_at    | 更新日期                                 | 否   |
| license       | 开源协议                                 | 否   |
| theme_course  | 主题教程链接（后台显示「主题教程」按钮） | 否   |

> `theme_version` 会被辅助函数用作 css/js 的缓存版本参数（去除非字母数字后拼接），
> 改动静态资源后把版本号 +1 即可自动刷新缓存，无需手写 `?v=20260826`。

### 主题自定义配置

**文件：config.php**

**格式：PHP Array**

```php
<?php
    /**配置示例 **/
$theme_config = array(
  [
    'type' => 'color',
    'name' => 'color',
    'title' => '主题主色',
    'description' => '用于搜索按钮、链接悬停等主色调，留空则使用默认蓝色',
    'value' => '#2f6fed',
  ],
  [
    'type' => 'select',
    'name' => 'link_cols',
    'title' => '列表列数',
    'description' => '每行显示的链接数量，手机端会自动变为 2 列',
    'value' => 4,
    'enum' => [
      3 => "3 列",
      4 => "4 列",
      5 => "5 列",
      6 => "6 列",
    ],
  ],
  [
    'type' => 'checkbox',
    'name' => 'modules',
    'title' => '首页显示模块',
    'description' => '可多选，取消勾选后对应模块不再显示',
    'value' => ['clock'],
    'enum' => [
      'clock' => "时间显示",
    ],
  ],
  [
    'type' => 'textarea',
    'name' => 'notice',
    'title' => '首页公告',
    'description' => '显示在搜索框下方，<code>支持HTML代码</code>，留空不显示',
    'value' => '',
  ],
  [
    'type' => 'text',
    'name' => 'security_filing',
    'title' => '公安备案号',
    'description' => '公安备案号，留空不显示',
    'value' => '',
    'placeholder' => "京公安网备xxxxxxxxxx号",
  ],
);
```

#### @param array `$theme_config` 表单配置参数说明

- type：表单类型，支持 [text：单行文本, textarea：多行文本, select：下拉菜单, checkbox：多选框, radio：单选, color：颜色]，**开关型建议使用 radio 替代 switch**
- name：表单参数的键，主题内需唯一
- title：配置项标题文字
- description：配置项提示文字
- value：默认值
- enum：枚举值，数组类型，键为参数值、值为显示文本（select / checkbox / radio 可用）
- verify：表单验证方式，支持 [required：必填, url：网址, number：数字, email：邮箱等]

> 主题配置按主题独立存储（数据表键 `theme_config_{主题目录名}`），键名不会与其它主题冲突，
> 因此 `name` **无需加主题前缀**，保持语义清晰即可。

#### 读取配置

使用函数：`theme_config('参数名称', '默认值')`，**建议始终传入第二参默认值**。

```php
// 多选项读取需做类型兜底（全不选时为空数组）
$modules = theme_config('modules', ['clock']);
if (!is_array($modules)) {
    $modules = array($modules);
}
if (in_array('clock', $modules)) {
    // 显示时间模块
}
```

> 多选项全部取消勾选时浏览器不会提交该字段，**核心已做兼容处理**（缺失即视为空数组），
> 主题侧仍需按上面示例做类型兜底。

### 辅助函数库（functions.php）

核心不会自动加载主题内文件，需在 `index.php` 顶部引入：

```php
require_once __DIR__ . '/functions.php';
```

开发包已内置下列函数，复制即可用，也可按需扩展（新增函数请沿用 `theme_` 前缀）：

| 函数 | 作用 |
| ---- | ---- |
| `theme_css('css/style.css')` | 输出 `<link>` 标签，自动附加 `?v=主题版本` |
| `theme_js('js/script.js')` | 输出 `<script defer>` 标签，自动附加版本参数 |
| `theme_version()` | 读取 theme.ini 的版本号（去除非字母数字） |
| `theme_e($value)` | HTML 转义，所有文本输出都应经过它 |
| `theme_tags()` | 导航菜单数组：`foreach (theme_tags() as $tag)` |
| `theme_sou()` | 搜索引擎数组：`link` 已按 PC / 手机自动解析 |
| `theme_icp()` | 输出 ICP 备案链接，留空则不输出 |
| `theme_security_filing()` | 输出公安备案链接（自动提取数字拼查询地址），留空则不输出 |

取数类函数只返回数据，输出类函数（`theme_css/theme_js/theme_icp/theme_security_filing`）直接 echo 完整标签。

#### 核心实现（可直接复制到主题中使用）[functions.php文件]

```php
<?php
/** 读取 theme.ini 的版本号，去除非字母数字字符（1.0.0 → 100），静态缓存 */
function theme_version()
{
    static $version = null;
    if ($version === null) {
        $ini = @file_get_contents(__DIR__ . '/theme.ini');
        $info = is_string($ini) ? json_decode($ini, true) : array();
        $raw = (is_array($info) && isset($info['theme_version'])) ? $info['theme_version'] : '1.0.0';
        $version = preg_replace('/[^a-zA-Z0-9]/', '', $raw);
        if ($version === '') {
            $version = '100';
        }
    }
    return $version;
}

/** 输出样式标签：<link rel="stylesheet" href=".../css/style.css?v=100"> */
function theme_css($path = 'css/style.css')
{
    global $templatepath;
    $href = rtrim($templatepath, '/') . '/' . ltrim($path, '/') . '?v=' . theme_version();
    echo '<link rel="stylesheet" href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '">' . "\n";
}

/** 输出脚本标签（defer） */
function theme_js($path = 'js/script.js')
{
    global $templatepath;
    $src = rtrim($templatepath, '/') . '/' . ltrim($path, '/') . '?v=' . theme_version();
    echo '<script defer src="' . htmlspecialchars($src, ENT_QUOTES, 'UTF-8') . '"></script>' . "\n";
}

/** HTML 转义 */
function theme_e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** 导航菜单：每项含 id / name / link / blank(是否新窗口) */
function theme_tags()
{
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

/** 搜索引擎（已过滤未启用项，link 已按当前设备解析） */
function theme_sou()
{
    global $site, $DB;
    $list = array();
    $result = $site->getSou();
    while ($row = $DB->fetch($result)) {
        if ((int) $row['sou_st'] !== 1) {
            continue;
        }
        $link = $row['sou_link'];
        if (checkmobile() && !empty($row['sou_waplink'])) {
            $link = $row['sou_waplink'];
        }
        $list[] = array(
            'alias' => $row['sou_alias'],
            'name'  => $row['sou_name'],
            'hint'  => $row['sou_hint'],
            'icon'  => $row['sou_icon'],
            'color' => $row['sou_color'],
            'link'  => $link,
        );
    }
    return $list;
}
```

### 系统配置（主题可用白名单）

主题应使用 `$conf` 中的下列配置项，其余键属于后台业务或程序内部，请勿依赖：

| 键 | 说明 | 注意 |
| ---- | ---- | ---- |
| `title` | 网站标题 | 输出需转义 |
| `keywords` / `description` | SEO 关键词 / 描述 | 输出需转义 |
| `logo` | 网站 LOGO / favicon | 输出需转义 |
| `background` / `wap_background` | PC / 手机背景图 | 用 `background()`，已做设备自适应 |
| `copyright` | 底部版权 | HTML 代码，原样输出 |
| `wztj` | 自定义 footer（统计代码） | HTML/JS/CSS 代码，原样输出在 `</body>` 前 |
| `icp` | ICP 备案号 | 留空不显示，输出需转义 |
| `yan` | 随机一言开关 | 字符串 `'true'`/`'false'`，判断用 `== 'true'` |
| `tq` | 天气开关 | 核心不消费，由主题决定是否展示天气 |
| `template` / `version` / `cdnpublic` | 当前主题名 / 程序版本 / CDN 地址 | 系统项 |

**不要依赖**：`apply`、`apply_gg`、`about`、`about_content`（分别由 `/apply`、`/about` 页面消费）、
`home-title`（已废弃，后台该位置现为「运行模式 `mode`」）、`admin_user`、`admin_pwd`、`wxplus` 等。

> 注意：`$conf['version']` 的值形如 `v2.6.0`（带 `v` 前缀），不能直接用作缓存版本参数；
> 缓存版本请取 `theme.ini` 的 `theme_version`。

#### 常用函数

| 函数 | 说明 |
| ---- | ---- |
| `background()` | 返回当前设备的背景图地址（PC / 手机自适应） |
| `theme_config($name, $default)` | 读取主题自定义配置 |
| `checkmobile()` | 是否为手机访问 |
| `yan()` | 随机一言内容（`$conf['yan'] == 'true'` 时使用） |

### 数据输出

#### 分组和链接

##### 标签说明

| 分组标签 | 说明         | 链接标签 | 说明         |
| -------- | ------------ | -------- | ------------ |
| g1       | 分组开始标签 | l1       | 链接开始标签 |
| g2       | 分组内容     | l2       | 链接内容     |
| g3       | 分组结束标签 | l3       | 链接结束标签 |

##### 参数说明

| 分组参数         | 说明                 |
| ---------------- | -------------------- |
| **{group_id}**   | **分组ID(唯一标识)** |
| **{group_name}** | **分组名称**         |
| **{group_icon}** | **分组图标**         |

| 链接参数         | 说明                               |
| ---------------- | ---------------------------------- |
| {link_id}        | 链接ID(唯一标识)                   |
| {link_name}      | 链接名称(若设字体颜色则带HTML标签) |
| {link_name_text} | 链接名称(纯文本)                   |
| {link_url}       | 链接地址（已处理详情页模式）       |
| {link_icon}      | 链接图标（缺失时自动补默认图标）   |
| {link_desc}      | 链接描述                           |

##### 实例

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

**HTML输出：**

```html
<div class="link-group">
    <h2 class="group-title"><svg ...></svg><span>常用导航</span></h2>
    <a class="link-item" href="https://www.lylme.com" target="_blank" rel="nofollow noopener" title="六零主页">
        <span class="link-icon"><svg ...></svg></span>
        <span class="link-name"><font color="#ff0000">六零主页</font></span>
    </a>
</div>
```

> `{link_name}` 可能携带 `<font>` 颜色标签（官方数据格式），因此**不转义**原样输出；
> 需要纯文本的场合（如 `title` 属性）请使用 `{link_name_text}`。

#### 导航菜单

描述：常用于显示在网站顶部的导航菜单

##### 参数

| 参数           | 说明                           |
| -------------- | ------------------------------ |
| `tag_id`       | 导航菜单唯一ID                 |
| `tag_name`     | 导航菜单名称                   |
| `tag_link`     | 导航菜单链接                   |
| `tag_target`   | 打开方式（1 新窗口，0 当前窗口） |

**推荐写法（使用辅助函数[functions.php文件]）：**

```php
<ul>
<?php foreach (theme_tags() as $tag): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo theme_e($tag['link']); ?>"<?php echo $tag['blank'] ? ' target="_blank"' : ''; ?>>
            <?php echo theme_e($tag['name']); ?>
        </a>
    </li>
<?php endforeach; ?>
</ul>
```

**原始写法（未使用辅助函数时）：**

```php
<ul>
<?php
$tagslists = $site->getTags();
while ($taglist = $DB->fetch($tagslists)) {
    echo '<li class="nav-item"><a class="nav-link" href="' . theme_e($taglist["tag_link"]) . '"';
    if ($taglist["tag_target"] == 1) {
        echo ' target="_blank"';
    }
    echo '>' . theme_e($taglist["tag_name"]) . '</a></li>';
}
?>
</ul>
```

#### 搜索引擎

##### 参数

| 参数         | 说明                   | 备注                              |
| ------------ | ---------------------- | --------------------------------- |
| `sou_id`     | 搜索引擎ID             | 唯一ID                            |
| `sou_alias`  | 搜索引擎别名           | 英文别名（唯一）                  |
| `sou_name`   | 搜索引擎名称           | 如：百度                          |
| `sou_hint`   | 输入框提示文字         | 如：请输入搜索内容                |
| `sou_link`   | 搜索引擎接口地址       | 如：https://www.baidu.com/s?word= |
| `sou_waplink`| 搜索引擎手机端接口地址 | 如：https://m.baidu.com/s?word=   |
| `sou_icon`   | 搜索引擎图标           | 图标代码，svg 或 img（HTML 原样输出） |
| `sou_color`  | 搜索引擎字体颜色       | 如 #0c498c                        |
| `sou_st`     | 搜索引擎状态           | 是否启用                          |

**推荐写法（使用辅助函数，`link` 已按设备类型解析、`sou_st` 已过滤）：**

```php
<div class="search-engines">
    <?php foreach (theme_sou() as $index => $sou): ?>
    <label class="engine" style="--engine-color: <?php echo theme_e($sou['color']); ?>;">
        <input type="radio" name="sou"
               value="<?php echo theme_e($sou['link']); ?>"
               data-alias="<?php echo theme_e($sou['alias']); ?>"
               data-hint="<?php echo theme_e($sou['hint']); ?>"
               <?php echo $index === 0 ? 'checked' : ''; ?>>
        <?php echo $sou['icon'] . "\n"; ?>
        <span class="engine-name"><?php echo theme_e($sou['name']); ?></span>
    </label>
    <?php endforeach; ?>
</div>
```

### 页面结构标准

#### HTML 骨架

```html
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
    <header class="site-header"><!-- 导航 --></header>
    <main class="site-main"><!-- 搜索区 + 链接列表 --></main>
    <footer class="site-footer"><!-- 版权 / 备案 --></footer>

    <?php echo $conf['wztj']; ?>
    <script src="<?php echo $cdnpublic; ?>/assets/js/icon.js"></script>
    <script src="<?php echo $cdnpublic; ?>/assets/js/svg.js"></script>
</body>
</html>
```

**必须保留的三处：**

1. `<?php echo $conf['wztj']; ?>` —— 后台自定义的统计代码，放在 `</body>` 前
2. `<script src="<?php echo $cdnpublic; ?>/assets/js/icon.js"></script>` —— 图标雪碧图
3. `<script src="<?php echo $cdnpublic; ?>/assets/js/svg.js"></script>` —— 旧版图标雪碧图

> 数据库存储的全部分组 / 链接 / 搜索引擎图标都是 `<use xlink:href="#lyicon-*">` 形式，
> 其 SVG symbol 由 `assets/js/icon.js`和 `assets/js/svg.js`注入。**不加载该脚本，站点图标将全部空白。**

`<body>` 的 class 建议保留 `is-pc` / `is-mobile` 设备类，便于 CSS / JS 做设备差异处理；
主题自有的状态类（如 `bg-fixed`）追加在后面即可：`class="is-pc bg-fixed"`。

#### 搜索区 DOM 契约

统一结构，便于脚本与样式复用：

```html
<form class="search-form" id="search-form" action="#">
    <input type="text" class="search-input" id="search-input" placeholder="请输入搜索内容" autocomplete="off">
    <button type="submit" class="search-btn">搜索</button>
</form>
```

| 约定 | 说明 |
| ---- | ---- |
| `input[name="sou"]` | 引擎选择器，radio；`value` = 接口地址、`data-alias` = 引擎别名、`data-hint` = 输入提示 |
| 首个引擎 `checked` | 无本地记录时的默认选中项 |
| 提交由 JS 拦截 | 各引擎参数名不统一（`wd=` / `q=` / `word=`），原生表单 GET 无法适配，统一拼接 `接口地址 + encodeURIComponent(关键词)` |
| 选择记忆 | 写入 `localStorage['theme_sou']`（键名全站统一，换主题后用户选择依然保留） |

```js
// 切换：更新 placeholder + localStorage.setItem('theme_sou', data-alias)
// 载入：读取 localStorage，按 data-alias 找回对应 radio 并触发 change
// 提交：preventDefault 后 window.open(选中项.value + encodeURIComponent(关键词), '_blank')
```

### 兼容性要求

主题代码需同时兼容 **PHP 5.4+ / 7.x / 8.x**（与安装程序的版本检查一致）。主题代码统一使用 `array()` 语法，
不使用短数组字面量与高版本专有语法，因此实际可运行于 PHP 5.2 及以上。

**禁止使用（PHP 7.0+ 专有，会导致 PHP 5.x 直接解析失败）：**

| 语法 | 说明 | 替代写法 |
| ---- | ---- | -------- |
| `$a ?? $b` | 空合并运算符（7.0+） | `isset($a) ? $a : $b` |
| `$a <=> $b` | 太空船运算符（7.0+） | 普通比较表达式 |
| `declare(strict_types=1)` | 严格模式（7.0+） | 不使用 |
| `function f(): string` | 返回值类型声明（7.0+） | 省略类型声明 |
| `function f(int $a)` | 标量参数类型（7.0+） | 省略类型声明 |
| `new class { }` | 匿名类（7.0+） | 具名类 |
| `[$a, $b] = $arr` | 短 list 解构（7.1+） | `list($a, $b) = $arr` |
| `fn() =>` | 箭头函数（7.4+） | `function () { }` |

**建议避免（PHP 5.4+ 语法，为兼容 PHP 5.2/5.3 建议使用传统写法）：**

| 语法 | 传统写法 |
| ---- | -------- |
| `$a = []`、`$a = ['x']` | `array()`、`array('x')` |

**其它注意事项：**

- `htmlspecialchars()` 必须显式传字符集：`htmlspecialchars($v, ENT_QUOTES, 'UTF-8')`
- 不要依赖 PHP 7+ 新增函数（如 `random_bytes()`、`intdiv()`），若必须使用请先 `function_exists()` 判断
- JS 统一 ES5 语法：`var` 声明、`function` 函数，不用 `let/const/箭头函数/模板字符串`
- CSS 选择性使用新特性时要有降级方案（如 `:has()` 需改为兄弟选择器）

### 发布自检清单

- [ ] `index.php` / `theme.ini` 存在，theme.ini 前六字段齐全、JSON 合法，版本号三段式
- [ ] 目录名与 `theme_name` 对应
- [ ] PHP 代码未使用上表中的 PHP 7+ 专有语法（PHP 5.4 可解析）
- [ ] 所有文本输出经过 `theme_e()`（`link_name`、`sou_icon` 为官方 HTML，除外）
- [ ] 所有文本输出经过 `theme_e()`（`link_name`、`sou_icon` 为官方 HTML，除外）
- [ ] 已保留 `$conf['wztj']` 输出，并引入 `$cdnpublic/assets/js/icon.js`和 `$cdnpublic/assets/js/svg.js`
- [ ] 未依赖白名单之外的系统配置键
- [ ] 多选项读取已做非数组类型兜底
- [ ] 移动端 768px 断点下排版正常
- [ ] README 含配置项说明与演示地址

### 排查须知

- `include/include.php` 为加密后的代码，部分核心函数（如 `theme()`）定义在其中，
  **文本搜索不到不等于函数不存在**；判断请用 `function_exists()` 或 `ReflectionFunction::getFileName()`
- 切勿在明文文件中重复定义加密代码里已有的函数，否则会触发 `Cannot redeclare` 致命错误
- 新增核心辅助函数请使用全新函数名并加 `function_exists()` 守卫
