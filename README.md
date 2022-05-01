# 六零导航页

#### 项目介绍
​		**六零导航页** (LyLme Spage) 致力于简洁高效无广告的上网导航和搜索入口，支持后台添加链接、自定义搜索引擎，沉淀最具价值链接，全站无商业推广，简约而不简单。

 **项目名称：** 六零导航页

 **演示站点：**[https://hao.lylme.com](https://hao.lylme.com/)

 **前端截图：**
![截图1](https://cdn.lylme.com/img/lylme_spage/lylme_spage1.png)

![六零导航页baisuTwo主题PC端截图](https://cdn.lylme.com/img/lylme_spage/image-20220501192454699.png)

![截图6](https://cdn.lylme.com/img/lylme_spage/lylme_spage6.png)

![截图2](https://cdn.lylme.com/img/lylme_spage/lylme_spage2.png)

 **后台截图：**
![截图3](https://cdn.lylme.com/img/lylme_spage/lylme_spage3.png)

![截图4](https://cdn.lylme.com/img/lylme_spage/lylme_spage4.png)

![截图5](https://cdn.lylme.com/img/lylme_spage/lylme_spage5.png)


#### 项目说明	

 **六零导航页**  (LyLme Spage) 整合了一些优秀的导航页，为了让使用和网站管理更方便，增加了后台管理。并修改和优化了部分内容：

1.   使用PHP+MySql，增加后台管理
2.   多模板选择，支持在后台切换模板

2.  增加常用搜索引擎，如：知乎、哔哩哔哩、在线翻译等（支持自定义）
3.  支持用户提交收录申请，地址：`http://域名/apply`
4.  部分模板优化和增加部分功能，如返回顶部、获取输入框焦点、时间日期显示等
5.  另外，如果你有更好的建议或者反馈问题欢迎提交Issue！


#### 安装教程

1.  前往[Gitee Releases](https://gitee.com/LyLme/lylme_spage/releases/) 或[Github Releases](https://github.com/LyLme/lylme_spage/releases/) 下载最新版本源码压缩包，上传到网站根目录解压
2.  访问`http://域名/install`
3.  按提示配置数据库进行安装
4.  后台地址：`http://域名/admin`
5.  账号密码：`admin`/`123456`

#### 背景设置

-  **每日一图背景：** 六零导航页支持使用Bing每日一图作为背景，接口地址：`/assets/img/bing.php`，但直接调用的`bing.php`返回的图片并不是静态文件，不会产生缓存，会导致后续加载速度慢。

    解决方案：添加一个每天执行的CRON任务：`GET http://域名/assets/img/cron.php` ，执行后会将Bing每日一图保存到`assets/img/background.jpg`，然后在后台修改背景地址为：`./assets/img/background.jpg`即可

-  **其他背景：** 前往后台设置

#### 鸣谢

 **D.Young**

-   前端：5iux主题和部分前端代码
-   GitHub地址：https://github.com/5iux

**笔下光年**

-   后台模板：Light Year Admin
-   Gitee地址：https://gitee.com/yinqi/Light-Year-Admin-Template

**BaiSu** 

-   前端：baisuTwo模板
-   原项目：[baisuTwo: onenav主题 (gitee.com)](https://gitee.com/baisucode/baisu-two)
-   Fork仓库：[六零导航页(LyLme Spage)baisu主题，基于baisuTwo开发 (gitee.com)](https://gitee.com/LyLme/baisu)
