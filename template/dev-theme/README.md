# 开发包devTheme —— 六零导航页主题开发包

单文件入口 + 零框架依赖的规范主题骨架。复制本目录、改名、改 CSS 即可做出自己的主题。

适用版本：六零导航页 v1.2.5 及以上

PHP 兼容：5.4+ / 7.x / 8.x（代码统一使用 `array()` 语法，不使用 `??`、箭头函数、类型声明等 PHP 7+ 专有语法）

## 目录结构

```
dev-theme/
├── index.php          # 入口（必须）：单文件，按注释分区
├── theme.ini          # 主题信息（必须）：缓存版本号从这里读
├── config.php         # 主题配置：后台「主题设置」的表单
├── functions.php      # 辅助函数库：封装官方底层接口
├── README.md          # 本文件
├── css/style.css      # 样式：改 :root 变量即可换肤
└── js/script.js       # 脚本：搜索引擎切换 + 时间显示
```

## 快速开始

1. 复制 `dev-theme` 目录，改名为你的主题目录（小写英文，如 `mynav`）
2. 修改 `theme.ini`：`theme_name` 与目录名对应，`theme_version` 用三段式（如 `1.0.0`）
3. 按需增删 `config.php` 中的配置项（`name` 主题内唯一即可，配置按主题独立存储，不会与其它主题冲突）
4. 在后台「系统设置 - 网站主题」中切换到你的主题

> 改动 CSS / JS 后不需要手动改缓存版本号——`theme_css()` / `theme_js()` 会读取 `theme.ini`
> 的 `theme_version`，改动后只需把版本号 +1，缓存自动失效。

## 可用的辅助函数（functions.php）

| 函数 | 作用 |
| ---- | ---- |
| `theme_css('css/style.css')` | 输出 `<link>` 标签，自动附加 `?v=版本号` |
| `theme_js('js/script.js')` | 输出 `<script defer>` 标签，自动附加版本号 |
| `theme_version()` | 读取 theme.ini 的版本号（去除非字母数字） |
| `theme_e($value)` | HTML 转义，所有文本输出都应经过它 |
| `theme_tags()` | 导航菜单数组：`foreach (theme_tags() as $tag)` |
| `theme_sou()` | 搜索引擎数组：`link` 已按 PC / 手机自动解析 |
| `theme_icp()` | 输出 ICP 备案链接（读 `$conf['icp']`），留空不输出 |
| `theme_security_filing()` | 输出公安备案链接（读主题配置 `gonganbei`），自动提取数字拼查询地址，留空不输出 |

> 取数类函数返回数组，输出类函数（`theme_css/theme_js/theme_icp/theme_security_filing`）直接 echo 完整标签。

列表数据仍使用官方 `lists($html)` 接口，结构未做任何改动。

## 搜索区的 DOM 契约

`js/script.js` 依赖以下结构，**修改搜索区时请保持这些约定**：

```html
<form id="search-form" action="#">
    <input type="text" id="search-input">
</form>
<label class="engine">
    <input type="radio" name="sou" value="接口地址" data-alias="引擎别名" data-hint="提示文字">
    ...
</label>
```

- 切换引擎 → 更新输入框提示 → 写入 `localStorage['theme_sou']`（键全站统一，换主题也保留）
- 提交搜索 → JS 拼接「接口地址 + encodeURIComponent(关键词)」后新窗口打开
  （各引擎参数名不统一：`wd=` / `q=` / `word=`，因此不走表单原生 GET）

## 系统配置项（主题可用白名单）

| 键 | 说明 |
| ---- | ---- |
| `title` / `keywords` / `description` | SEO 与标题 |
| `logo` | LOGO / favicon |
| `background` / `wap_background` | 背景图（用 `background()`，已自适应 PC/手机） |
| `copyright` / `wztj` | 底部版权、自定义统计代码（HTML，原样输出） |
| `icp` | ICP 备案号，留空不显示 |
| `yan` | 随机一言开关，判断用 `== 'true'` |
| `tq` | 天气开关（核心不消费，主题自行决定是否展示） |
| `template` / `version` / `cdnpublic` | 系统项 |

其他键（`apply`、`about`、`home-title` 等）主题不应依赖。

## 必须保留的三处代码

```php
<?php echo $conf['wztj']; ?>                              <!-- 统计代码，放在最底部 -->
<script src="<?php echo $cdnpublic; ?>/assets/js/icon.js"></script>  <!-- 图标雪碧图 -->
<script src="<?php echo $cdnpublic; ?>/assets/js/svg.js"></script>  <!-- 旧版图标雪碧图 -->
```

`svg.js`和`icon.js` 注入了数据库所有 `#lyicon-*` 图标的 SVG symbol，删除后站点图标将全部空白。

## theme.ini 字段说明

必填六项：`author_name`、`author_link`、`theme_name`、`theme_version`、`theme_explain`、`theme_demo`

建议补充：`requires`（最低程序版本）、`updated_at`、`license`（核心忽略未识别字段，向后安全）

可选：`theme_course`（主题教程链接，配置后后台显示"主题教程"按钮）

> `theme_version` 会被 `theme_css()` / `theme_js()` 用作缓存版本参数，改动静态资源后把版本号 +1 即可。

## 自检清单

- [ ] `theme.ini` 六字段齐全、JSON 合法，版本号三段式
- [ ] 目录名与 `theme_name` 对应
- [ ] 所有文本输出都经过 `theme_e()`（`link_name`、`sou_icon` 为官方 HTML，除外）
- [ ] 保留 `wztj` 输出与 `icon.js`和`svg.js` 引入
- [ ] 移动端 768px 断点下排版正常
- [ ] 搜索引擎切换后刷新页面，选择被正确记忆
