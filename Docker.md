# 六零导航页docker部署

**六零导航页** (LyLme Spage) 致力于简洁高效无广告的上网导航和搜索入口，支持后台添加链接、自定义搜索引擎，沉淀最具价值链接，全站无商业推广，简约而不简单。

##### 仅需一条命令即可完成六零导航页docker部署

> 开箱即用，无需安装，无需配置，自动导入数据库

## 🚀 快速开始

[安装Docker](https://www.runoob.com/docker/windows-docker-install.html)后选择下面的命令执行

### 方式一：快速体验（不推荐）

```bash
docker run -d -p 8080:80 lylme/lylme_spage
```

⚠️ 注意：此方式容器删除后数据会丢失

### 方式二：推荐方式（数据持久化）

```bash
docker run -d \
  -p 8080:80 \
  -v lylme_mysql:/var/lib/mysql \
  -v lylme_www:/var/www/html \
  --name lylme_spage \
  lylme/lylme_spage
```

✅ 使用命名卷，数据永久保存

## 🔐 访问信息

| 项目 | 地址 |
|------|------|
| 前台 | http://localhost:8080 |
| 后台 | http://localhost:8080/admin/ |
| 默认账号 | admin |
| 默认密码 | 123456 |

`localhost`可用`127.0.0.1`、`服务器内网IP`、`服务器公网IP`代替

⚠️ **请登录后台后立即修改默认密码！**

##### 去除端口号

将上面docker命令中的` -p 8080:80`改为`-p 80:80`

##### 通过域名访问(反向代理)

**宝塔面板：**添加站点绑定域名后，站点右侧的设置→反向代理→添加反向代理→代理名称(随便)→目标URL(填写`http://localhost:8080`)→其他默认保存即可

**Nginx：**参考以下配置，`server_name`改为你的域名

```nginx
server {
    listen 80;
    server_name daohang.example.com;

    location / {
        proxy_pass http://localhost:8080;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
    }
}
```

## 💾 数据持久化

### 持久化卷说明

| 卷路径 | 说明 |
|--------|------|
| `/var/lib/mysql` | MySQL 数据库文件 |
| `/var/www/html` | 网站文件（含配置、上传文件等） |

### 备份数据

```bash
# 备份数据库
docker exec lylme_spage mysqldump -u lylme -plylme123456 --socket=/var/run/mysqld/mysqld.sock lylme_spage > backup.sql

# 备份整个数据卷
docker run --rm -v lylme_mysql:/data -v $(pwd):/backup alpine tar czf /backup/mysql_backup.tar.gz /data
```

### 恢复数据

```bash
# 恢复数据库
docker exec -i lylme_spage mysql -u lylme -plylme123456 --socket=/var/run/mysqld/mysqld.sock lylme_spage < backup.sql
```

## 📋 环境变量

| 变量             | 默认值      | 说明         |
| ---------------- | ----------- | ------------ |
| `MYSQL_USER`     | lylme       | 数据库用户名 |
| `MYSQL_PASSWORD` | lylme123456 | 数据库密码   |
| `MYSQL_DATABASE` | lylme_spage | 数据库名称   |

✅ 数据库仅监听本地 socket（127.0.0.1）

## 🛡️ 安全配置

- ✅数据库仅监听 127.0.0.1
- ✅ 禁用 SSL（内部通信）
- ✅ 自动创建安装锁防止重复安装

## 🔧 常用命令

```bash
# 查看日志
docker logs -f lylme_spage

# 进入容器
docker exec -it lylme_spage bash

# 重启服务
docker restart lylme_spage

# 停止服务
docker stop lylme_spage

# 删除容器（保留数据卷）
docker rm lylme_spage

# 删除容器和数据
docker rm -v lylme_spage
docker volume rm lylme_mysql lylme_www
```

## 🔄 重新安装

```bash
# 方法1: 删除锁文件重启(只清空数据库)
docker exec lylme_spage rm /var/www/html/install/install.lock
docker restart lylme_spage

# 方法2: 完全重置（清空所有数据）
docker rm -v lylme_spage
docker volume rm lylme_mysql lylme_www
docker run -d -p 80:80 -v lylme_mysql:/var/lib/mysql -v lylme_www:/var/www/html --name lylme_spage lylme/lylme_spage
```

## 🏗️ 本地构建

```bash
cd E:\lylme_spage_docker
docker build -t lylme/lylme_spage .
docker run -d -p 80:80 -v lylme_mysql:/var/lib/mysql -v lylme_www:/var/www/html lylme/lylme_spage
```

## 📦 包含组件

| 组件 | 版本 |
|------|------|
| Apache | 2.4 |
| PHP | 8.2 |
| MariaDB | 10.x |
| Supervisor | latest |

### PHP 扩展

- mysqli, pdo_mysql
- gd (JPEG, PNG, FreeType)
- zip, curl, mbstring
- xml, bcmath, opcache

## 🐛 故障排除

### 容器启动失败

```bash
docker logs lylme_spage
```

### 数据库连接失败

```bash
# 检查 MariaDB 状态
docker exec lylme_spage ps aux | grep mysql

# 测试数据库连接
docker exec lylme_spage mysql -u lylme -plylme123456 --socket=/var/run/mysqld/mysqld.sock -e "SELECT 1;"
```

### 页面显示安装界面

说明初始化未完成，检查日志：
```bash
docker logs lylme_spage | grep -i error
```

## 📄 License

Apache-2.0

---

**项目地址**: https://github.com/LyLme/lylme_spage
