## 六零导航页Docker部署

​	**六零导航页** (LyLme Spage) 致力于简洁高效无广告的上网导航和搜索入口，支持后台添加链接、自定义搜索引擎，沉淀最具价值链接，全站无商业推广，简约而不简单。这里是六零导航页的Docker部署文档

### 一、文件说明

```bash

├─mysql			# MySQL
│  └─data 				#数据库文件目录
├─nginx			#Nginx
│  └─conf.d				#Nginx配置目录				
    │  ├─default.conf		#默认Nginx配置文件
    │  └─nginx.conf.bak		#脚本部署Nginx配置文件
├─php			#PHP
│  └─etc				#PH配置目录
    │  └─php*.ini			#PHP.ini配置文件
	└─Dockerfile		#PHP dockerfile文件
└─www			#网站目录
    ....
    └─  index.php
├─docker-compose.yaml	#Dockercompose文件
├─install.sh			#Linux下Docker部署脚本
├─php_file_write.sh		#PHP容器修复写入权限
```

### 二、Docker部署

1. 首先克隆本项目到网站要部署的目录

```shell
git clone -b docker https://github.com/LyLme/lylme_spage.git docker
cd docker && ls
```

2. 选择下面其中一种方式部署，**推荐使用Docker-compose方式部署**，shell脚本在不同版本系统可能会存在兼容性问题。

#### 1.  通过Docker-compose(推荐)

1. 安装Docker和Docker-compose ([查看教程](https://www.runoob.com/docker/centos-docker-install.html))

2. 编辑`docker-compose.yaml`文件52行`- MYSQL_ROOT_PASSWORD=123456 #MySQL root密码`修改123456为新的root密码。

3. 执行`docker-compose up -d`等待拉取镜像和构建容器完成。

#### 2. 通过shell脚本

1. 打开终端，进入本项目的目录

2. 执行以下命令

   ```shell
   chmod +x install.sh
   ./install.sh

3. 按提示操作至部署完成

### 三、开始使用

1. 访问[http://IP:8080/install](http://localhost:8080/install)，进入六零导航页安装程序（若提示环境检测不通过可尝试执行`docker-compose restart`重启容器再试）

2. 数据库配置

   - 数据库地址：`10.10.10.1`或(`spage-mysql`)

   - 数据库端口：`3306`

   -  数据库名：`spage`(或其他，若数据库名不存在安装程序会自动创建)

   - 数据库用户名：`root`

   - 数据库密码：第2步设置的密码，默认123456


3. 完成安装
   - 外网地址：`http://IP:8080`

   - 内网地址：`http://10.10.10.10`

4. 后台地址
   - `http://IP[:端口]/admin`
   - 后台账号：`admin`
   - 后台密码：`123456`

### 四、其他

若在使用Docker方式部署六零导航页遇到问题请附上截图和日志联系我
