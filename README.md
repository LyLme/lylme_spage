# 六零导航页

#### 项目介绍
​		**六零导航页**（LyLme Spage）基于D.Young开发的 <u>5IUX搜索</u> 开发，包含多种搜索引擎，致力于简洁高效无广告的上网导航和搜索入口，沉淀最具价值链接，全站无商业推广，简约而不简单。

 **项目名称：** 六零导航页

 **项目地址：**[https://github.com/LyLme/lylme_spage](https://github.com/LyLme/lylme_spage)

 **演示站点：**[https://hao.lylme.com](https://hao.lylme.com/)

 **演示截图：**

![截图1](https://cdn.lylme.com/img/lylme_spage/lylme_spage1.png)

![截图2](https://cdn.lylme.com/img/lylme_spage/lylme_spage2.png)

#### 项目说明
​		首先感谢**D.Young**的开发，虽然原项目的基本上满足了我理想中导航网站的需求，但这还不够，为了更方便的使用，针对我的需求，我在原项目上添加和修改了一些内容，如下：

1.  增加一些常用的搜索引擎（如知乎搜索、哔哩哔哩搜索、在线翻译等）
2.  为了让添加数据更方便，并且满足一些功能，我使用了PHP+MySql
3.  修改了网站的大部分样式
4.  增加和优化了一些的内容，比如：返回顶部、获取输入框焦点等


#### 安装教程

1.  前往[Releases](https://github.com/LyLme/lylme_spage/releases/) 下载最新版本源码压缩包，上传到网站根目录解压
2.  访问http://域名/install目录，按提示进行安装

#### 修改背景

- **每日一图背景：**六零导航页默认使用`assets/img/bing.php`（Bing每日一图）作为背景，但因使用PHP调用的图片不是静态文件，没有浏览器缓存，每次加载都会重新请求会导致加载速度慢，解决方案：添加一个每天执行的CRON任务：`GET http://xxx.com/assets/img/cron.php` 执行后会将Bing每日一图保存到`assets/img/background.jpg`，首次请求后
- **自定义背景：**将背景图片改名为`background.jpg`上传至`assets/img`即可
- **CDN&背景接口：**在`index.php`查找`./assets/img/bing.php`替换为你的背景文件地址

以上三种方案任选一种，也可以忽略使用默认背景接口

#### 鸣谢

**D.Young**

-   原版： [5IUX搜索](https://sou.5iux.cn) 使用HTML+CSS+JS由**D.Young**开发
-   博客地址：[https://blog.5iux.cn/](https://blog.5iux.cn/4679.html)
-   GitHub地址：https://github.com/5iux/5iux.github.io
