# 六零导航页 LyLme Spage

<p align="center">
  <a href="https://github.com/LyLme/lylme_spage/releases"><img src="https://img.shields.io/github/v/release/LyLme/lylme_spage?label=version&cacheSeconds=3600" alt="Version"></a>
  <a href="./LICENSE"><img src="https://img.shields.io/badge/license-Apache--2.0-green" alt="License"></a>
  <a href="https://gitee.com/LyLme/lylme_spage"><img src="https://img.shields.io/badge/Gitee-LyLme%2Flylme__spage-red" alt="Gitee"></a>
  <a href="https://github.com/LyLme/lylme_spage"><img src="https://img.shields.io/badge/GitHub-LyLme%2Flylme__spage-black" alt="GitHub"></a>
  <img src="https://img.shields.io/badge/PHP-%3E%3D5.6-purple" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-%3E%3D5.6-orange" alt="MySQL">
</p>

> 致力于简洁高效无广告的上网导航和搜索入口，支持后台管理、多模板切换与自定义搜索引擎，全站无商业推广，简约而不简单。

**演示站点**：<https://hao.lylme.com> &nbsp;|&nbsp; **项目文档**：<https://doc.lylme.com/spage>

## 快速开始

### 常规安装（推荐）

1. 前往 [Gitee Releases](https://gitee.com/LyLme/lylme_spage/releases) 或 [GitHub Releases](https://github.com/LyLme/lylme_spage/releases) 下载最新版本源码压缩包，上传至网站根目录解压
2. 访问 `http://域名/install`，按提示配置数据库完成安装
3. 后台地址：`http://域名/admin`，默认账号密码：`admin` / `123456`

### Docker 部署

一条命令完成部署，开箱即用，自动配置并导入数据库：

```bash
docker run -d -p 8080:80 -v lylme_mysql:/var/lib/mysql -v lylme_www:/var/www/html --name lylme_spage lylme/lylme_spage
```
#### 国内镜像加速
```bash
docker run -d -p 8080:80 -v lylme_mysql:/var/lib/mysql -v lylme_www:/var/www/html --name lylme_spage docker.1ms.run/lylme/lylme_spage:latest
```
| 项目     | 地址                          |
| -------- | ----------------------------- |
| 前台     | <http://localhost:8080>       |
| 后台     | <http://localhost:8080/admin> |
| 默认账号 | `admin`                       |
| 默认密码 | `123456`                      |



详细的 Docker 部署、数据持久化、备份恢复、反向代理等内容请参阅 [Docker.md](Docker.md)。

### 环境要求

| 组件 | 要求 |
|------|------|
| PHP | >= 5.6（支持 5.x / 7.x / 8.x） |
| MySQL | >= 5.6（推荐 5.7+） |
| Web 服务器 | Apache / Nginx |

**PHP 扩展**：mysqli、pdo_mysql、gd、curl、mbstring、xml、zip

## 功能特性

### 前台

- **多搜索引擎切换** — 内置多个常用搜索引擎，后台可自定义增删与排序
- **分组导航** — 链接按分组展示，支持分组排序、加密访问
- **收录申请** — 用户可在线提交网站收录申请，支持验证码与限流防护
- **详情页模式** — 支持直接跳转与详情页两种运行模式，详情页自动采集站点信息
- **响应式设计** — 适配 PC 与移动端，支持独立手机端背景
- **Bing 每日壁纸** — 支持通过 CRON 定时抓取 Bing 每日一图作为背景
- **随机一言** — 可选的随机一言展示

### 后台

- **网站设置** — 标题、Logo、背景、SEO 关键词/描述、备案号、版权信息、自定义 Footer 等
- **链接管理** — 增删改查、批量导入、批量操作（启用/禁用/移动/加密/删除）、失效检测
- **分组管理** — 分组增删改查、拖拽排序、分组加密
- **搜索引擎管理** — 搜索引擎的增删改查与排序
- **主题管理** — 内置多套主题模板，后台一键切换，支持主题自定义设置
- **收录审核** — 查看用户提交的收录申请，支持通过/拒绝/删除
- **导航菜单** — 顶部导航菜单的自定义管理
- **加密管理** — 链接/分组密码保护，支持多密码组
- **文件清理** — 图片快速清理
- **账号安全** — 修改管理员账号密码、后台目录自定义、调试模式开关

### 安全防护

| 防护层 | 说明 |
|--------|------|
| WAF 防护 | SQL 注入、XSS、命令注入等常见攻击检测 |
| CSRF 防护 | 表单 CSRF Token 生成与验证 |
| SSRF 防护 | 禁止访问内网 IP 等不安全资源 |
| 限流机制 | API 请求频率限制，防止滥用 |
| 验证码 | 收录申请等场景的图形验证码 |
| 文件校验 | 核心文件完整性校验，检测篡改 |
| 密码加密 | 管理员密码 MD5 加密存储，链接/分组密码访问 |

## 前台截图

<table>
  <tr>
    <td><img src="https://cdn.lylme.com/img/lylme_spage/default主题.png" alt="default主题"></td>
    <td><img src="https://cdn.lylme.com/img/lylme_spage/LTAB主题.png" alt="LTAB主题"></td>
    <td><img src="https://cdn.lylme.com/img/lylme_spage/详情页.png" alt="详情页"></td>
  </tr>
</table>

<details>
<summary>更多前台截图</summary>

<table>
  <tr>
    <td><img src="https://cdn.lylme.com/img/lylme_spage/baisu主题.png" alt="baisu主题"></td>
    <td><img src="https://cdn.lylme.com/img/lylme_spage/DashLite主题.png" alt="DashLite主题"></td>
    <td><img src="https://cdn.lylme.com/img/lylme_spage/申请收录页面.png" alt="申请收录页面"></td>
  </tr>
</table>
</details>

## 后台截图

<table>
  <tr>
    <td><img src="https://cdn.lylme.com/img/lylme_spage/后台首页.png" alt="后台首页"></td>
    <td><img src="https://cdn.lylme.com/img/lylme_spage/后台链接管理页.png" alt="后台链接管理页"></td>
    <td><img src="https://cdn.lylme.com/img/lylme_spage/后台多主题选择.png" alt="后台多主题选择"></td>
  </tr>
</table>

<details>
<summary>更多后台截图</summary>

<table>
  <tr>
    <td><img src="https://cdn.lylme.com/img/lylme_spage/后台搜素引擎自定义.png" alt="后台搜素引擎自定义"></td>
    <td><img src="https://cdn.lylme.com/img/lylme_spage/后台批量导入链接和收藏夹导入.png" alt="后台批量导入链接和收藏夹导入"></td>
  </tr>
</table>
</details>

## 背景设置

六零导航页支持使用 Bing 每日一图作为背景，接口地址为 `/assets/img/bing.php`。但直接调用 `bing.php` 不会产生缓存，影响后续加载速度。

**缓存方案**：修改 `/assets/img/cron.php` 配置密钥，添加每天执行的 CRON 任务：

```
GET http://域名/assets/img/cron.php
```

执行后 Bing 每日一图会保存到 `assets/img/background.jpg`，在后台将背景地址设为 `./assets/img/background.jpg` 即可。

## 项目结构

```
lylme_spage/
├── index.php                # 前台入口
├── config.php              # 数据库配置
├── .htaccess               # Apache 伪静态规则
├── nginx.htaccess          # Nginx 伪静态规则
├── include/                # 核心代码
│   ├── common.php          #   系统初始化
│   ├── include.php         #   模板渲染与公共元素
│   ├── function.php        #   公共函数库
│   ├── lists.php           #   列表渲染
│   ├── site.php            #   SITE 类（数据操作 + WAF）
│   ├── db.class.php        #   数据库类
│   ├── member.php          #   管理员鉴权
│   ├── go.php              #   链接跳转与密码验证
│   ├── validatecode.php   #   验证码生成
│   ├── file.php            #   文件上传处理
│   ├── tj.php              #   访问统计
│   ├── updbase.php         #   数据库升级
│   ├── qrcode.php          #   二维码生成
│   └── version.php         #   版本号定义
├── admin/                  # 后台管理
│   ├── index.php           #   仪表盘
│   ├── set.php             #   网站设置
│   ├── link.php            #   链接管理
│   ├── group.php           #   分组管理
│   ├── sou.php             #   搜索引擎管理
│   ├── apply.php           #   收录审核
│   ├── theme.php           #   主题管理
│   ├── tag.php             #   导航菜单
│   ├── pwd.php             #   加密组管理
│   ├── user.php            #   账号安全
│   ├── batch_add.php       #   批量导入
│   ├── ajax_link.php       #   链接 AJAX 接口
│   ├── ajax_apply.php     #   收录 AJAX 接口
│   ├── ajax_theme.php      #   主题 AJAX 接口
│   ├── filecheck.php       #   文件完整性校验
│   ├── cleanimg.php        #   图片清理
│   ├── cache.php           #   缓存管理
│   ├── update.php          #   检查更新
│   ├── wxplus.php          #   微信推送
│   ├── license.php         #   授权管理
│   ├── about.php           #   关于页面设置
│   └── help.php            #   帮助文档
├── apply/                  # 收录申请（前台）
│   ├── index.php           #   申请页面
│   ├── apply.js            #   前端交互脚本
│   └── wxplus.php          #   微信推送通知
├── site/                   # 站点服务
│   ├── index.php           #   详情页入口
│   ├── common.php          #   详情页公共逻辑
│   ├── sitemap.php         #   XML 站点地图
│   └── baidu_api.php       #   百度主动推送
├── template/               # 主题模板
│   ├── default/            #   默认主题
│   ├── ***/                #   其他主题
├── assets/                 # 静态资源
│   ├── css/                #   前端样式
│   ├── js/                #   前端脚本
│   ├── img/               #   图片资源（含 Bing 每日一图接口）
│   └── data/              #   随机一言数据
├── install/                # 安装程序
│   ├── index.php           #   安装入口
│   ├── data/install_struct.sql  # 数据库结构
│   └── templates/          #   安装向导模板
├── files/                  # 上传文件
├── about/                  # 关于页面
├── pwd/                    # 密码访问入口
└── logs/                   # 日志目录
```

## 数据表结构

| 数据表 | 说明 |
|--------|------|
| `lylme_config` | 网站配置（键值对存储所有可配置项） |
| `lylme_links` | 导航链接 |
| `lylme_groups` | 链接分组 |
| `lylme_sou` | 搜索引擎配置 |
| `lylme_apply` | 用户收录申请 |
| `lylme_tags` | 顶部导航菜单 |
| `lylme_pwd` | 加密访问密码组 |

## 相关链接

| 项目 | 地址 |
|------|------|
| Gitee 仓库 | <https://gitee.com/LyLme/lylme_spage> |
| GitHub 仓库 | <https://github.com/LyLme/lylme_spage> |
| 演示站点 | <https://hao.lylme.com> |
| 项目文档 | <https://doc.lylme.com/spage> |
| 主题开发文档 | <http://doc.lylme.com/spage/#/dev> |
| Docker 部署文档 | [Docker.md](Docker.md) |
| 捐赠支持 | <https://www.lylme.com/support> |

## 鸣谢

本项目的开发离不开以下开源项目的支持：

- **[Bootstrap](https://getbootstrap.com/)** — 前端 UI 框架
- **[jQuery](https://jquery.com/)** — JavaScript 库
- **[Font Awesome](https://fontawesome.com/)** — 图标库
- **[Viewer.js](https://github.com/fengyuanchen/viewerjs)** — 图片查看器
- **[Layer](https://github.com/sentsin/layer)** — 弹层组件

感谢所有为项目提交 Issue、PR 及反馈建议的用户。

## License

[Apache License 2.0](LICENSE)

---

Copyright &copy; 2022-2026 LyLme Spage. All Rights Reserved.
