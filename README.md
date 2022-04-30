# 六零导航页

#### 项目介绍
**六零导航页** (LyLme Spage) 致力于简洁高效无广告的上网导航和搜索入口，支持后台添加链接、自定义搜索引擎，沉淀最具价值链接，全站无商业推广，简约而不简单。

 **项目名称：** 六零导航页（HTML版）

**版本说明：** HTML版本无后台 [查看PHP版](https://gitee.com/LyLme/lylme_spage/tree/master)

 **演示站点：**[https://hao.lylme.com/html](https://hao.lylme.com/html)

 **纯html版截图：**

<img src="https://cdn.lylme.com/img/lylme_spage/lylme_spage1.png" alt="截图1" style="zoom:80%;" />



![截图2](https://cdn.lylme.com/img/lylme_spage/lylme_spage2.png)






#### 分支说明	

 html分支是**六零导航页**  (LyLme Spage) 的HTML版本，无后台功能，需自己修改源码定制，或着使用[PHP版](https://gitee.com/LyLme/lylme_spage/tree/master)


#### 安装教程

前往[Gitee html分支](https://gitee.com/LyLme/lylme_spage/tree/html/) 或[Github html分支](https://github.com/LyLme/lylme_spage/tree/html/) 下载源码zip压缩包，上传到网站根目录解压



#### 自定义链接

 修改 `index.html`文件约170行（代码内有注释）

**自定义链接格式：**

```html
<li class="col-3 col-sm-3 col-md-3 col-lg-1"><a rel="nofollow" href="链接地址" target="_blank"><img src="图标地址" / ><span>链接名称</span></a></li>
```

**自定义分组格式：**

```html
<ul class="mylist row">
	<li class="title"><img src="图标地址" / ><sapn>分组名称</sapn></li>
		分类下的链接.....
</ul>
```
注：img标签可以使用SVG图标代替，教程：https://gitee.com/LyLme/lylme_spage/wikis/pages?sort_id=5472271&doc_id=2453225

```html
<img src="图标地址" / >
替换为
<svg>svg path代码</svg>
```




#### 修改说明

**LOGO修改：** 替换`img/logo.png`文件，或修改 `index.html`文件第10行

**背景修改：** 替换`img/background.jpg`文件，或修改 `index.html`文件第27行

**顶部导航菜单：** 修改 `index.html`文件约40行（代码内有注释）

**备案和版权：** 修改 `index.html`文件末尾（代码内有注释）

**网站统计：** 修改 `index.html`文件末尾（代码内有注释）

#### 自定义搜索引擎

 修改 `index.html`文件约60行（代码内有注释）

**注意：** `input`的`id`值和`label`的`for`值不能和其他搜索引擎重复