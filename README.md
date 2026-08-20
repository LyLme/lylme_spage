# 六零导航页 (LyLme Spage)

**六零导航页**致力于简洁高效无广告的上网导航和搜索入口，支持后台添加链接、自定义搜索引擎，沉淀最具价值链接，全站无商业推广，简约而不简单。

> 演示站点：<https://hao.lylme.com>

## 功能特性

- 简洁高效的上网导航与搜索入口
- 后台可视化添加链接、自定义搜索引擎
- 全站无广告、无商业推广
- 一键 Docker 部署，开箱即用，自动导入数据库
- 默认使用北京时间（CST），时区可配置

## 快速开始

安装 [Docker](https://www.runoob.com/docker/windows-docker-install.html) 后，任选以下一种方式部署。

### 方式一：快速体验（不推荐）

```bash
docker run -d -p 8080:80 lylme/lylme_spage
```

> 注意：此方式容器删除后数据会丢失，仅用于体验。

### 方式二：数据持久化（推荐）

```bash
docker run -d -p 8080:80 \
  -v lylme_mysql:/var/lib/mysql \
  -v lylme_www:/var/www/html \
  --name lylme_spage \
  lylme/lylme_spage
```

> 使用命名卷，数据永久保存，容器删除/重建不丢失。

### 方式三：国内镜像加速

```bash
docker run -d -p 8080:80 \
  -v lylme_mysql:/var/lib/mysql \
  -v lylme_www:/var/www/html \
  --name lylme_spage \
  docker.1ms.run/lylme/lylme_spage:latest
```

> 效果与方式二完全一致，Docker 官方仓库访问受限时使用。

### 方式四：Docker Compose 部署（推荐）

项目根目录已内置 `docker-compose.yml`（含健康检查、自动重启、内部网络），克隆或下载本项目后执行：

```bash
docker compose up -d
```

> 等效于方式二，自动启用容器健康检查与 `unless-stopped` 重启策略。
> 注意：Compose 方式使用的卷名为 `lylme_mysql_data`、`lylme_web_data`，与方式二/三的 `lylme_mysql`、`lylme_www` 相互独立。

## 访问信息

| 项目 | 地址 |
|------|------|
| 前台 | http://localhost:8080 |
| 后台 | http://localhost:8080/admin/ |
| 默认账号 | `admin` |
| 默认密码 | `123456` |

`localhost` 可用 `127.0.0.1`、服务器内网 IP、服务器公网 IP 代替。

> 请登录后台后立即修改默认密码！

## 域名访问

### 去除端口号

将 docker 命令中的 `-p 8080:80` 改为 `-p 80:80` 即可。

### 宝塔面板反向代理

添加站点绑定域名后，进入 **站点设置 → 反向代理 → 添加反向代理**：

- 代理名称：任意
- 目标 URL：`http://localhost:8080`

其余保持默认并保存。

### Nginx 反向代理

参考以下配置，将 `server_name` 改为你的域名：

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

## 数据持久化

### 持久化卷说明

| 卷路径 | 说明 |
|--------|------|
| `/var/lib/mysql` | 数据库文件 |
| `/var/www/html` | 网站文件（含配置、上传文件等） |

### 备份数据

```bash
# 备份数据库
docker exec lylme_spage mysqldump -u lylme -plylme123456 \
  --socket=/var/run/mysqld/mysqld.sock lylme_spage > backup.sql

# 备份整个数据卷（以 mysql 卷为例）
docker run --rm -v lylme_mysql:/data -v $(pwd):/backup \
  alpine tar czf /backup/mysql_backup.tar.gz /data
```

### 恢复数据

```bash
# 恢复数据库
docker exec -i lylme_spage mysql -u lylme -plylme123456 \
  --socket=/var/run/mysqld/mysqld.sock lylme_spage < backup.sql
```

## 环境变量

| 变量 | 默认值 | 说明 |
|------|--------|------|
| `MYSQL_USER` | `lylme` | 数据库用户名 |
| `MYSQL_PASSWORD` | `lylme123456` | 数据库密码 |
| `MYSQL_DATABASE` | `lylme_spage` | 数据库名称 |
| `TZ` | `Asia/Shanghai` | 容器时区（北京时间） |

通过 `docker run -e` 或 `docker-compose.yml` 的 `environment` 覆盖，例如：

```bash
docker run -d -p 8080:80 -e TZ=Asia/Tokyo \
  -v lylme_mysql:/var/lib/mysql -v lylme_www:/var/www/html \
  --name lylme_spage lylme/lylme_spage
```

## 时区说明

- 镜像默认时区为 **Asia/Shanghai（北京时间，UTC+8）**，数据库、日志、安装锁时间均使用该时区。
- 如需其他时区，设置环境变量 `TZ` 即可（如 `TZ=Asia/Tokyo`）。
- 容器启动时会自动校验时区文件，若指定的时区不存在则回退到 `Asia/Shanghai`。

## 安全配置

- 数据库仅监听本机 socket（127.0.0.1），不对外暴露端口
- 内部通信禁用 SSL
- 首次启动自动创建安装锁，防止重复安装
- 内置健康检查，异常时自动重启（Compose 方式）

## 常用命令

### Docker 方式

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
docker rm -f lylme_spage
docker volume rm lylme_mysql lylme_www
```

### Docker Compose 方式

```bash
# 构建并启动
docker compose up -d --build

# 查看日志
docker compose logs -f web

# 停止 / 重启 / 删除
docker compose stop
docker compose restart
docker compose down
```

## 重新安装

```bash
# 方法 1：仅重置数据库（删除安装锁后重启自动重装）
docker stop lylme_spage
docker exec lylme_spage rm -f /var/www/html/install/install.lock
docker restart lylme_spage

# 方法 2：完全重置（清空所有数据）
docker stop lylme_spage
docker rm -f lylme_spage
docker volume rm lylme_mysql lylme_www
# 重新启动
docker run -d -p 8080:80 \
  -v lylme_mysql:/var/lib/mysql -v lylme_www:/var/www/html \
  --name lylme_spage lylme/lylme_spage
```

> 网站文件丢失时无需重装：启动脚本会自动从镜像内置备份 `/app/www_bak` 恢复。

## 包含组件

| 组件 | 版本 |
|------|------|
| Apache | 2.4 |
| PHP | 8.2 |
| MariaDB | 10.x |
| Supervisor | latest |

### PHP 扩展

- `mysqli`、`pdo_mysql`
- `gd`（JPEG、PNG、FreeType）
- `zip`、`curl`、`mbstring`
- `xml`、`bcmath`、`opcache`
- `redis`（可选，安装失败自动跳过）

## 从源码构建

```bash
# 构建镜像
docker build -t lylme/lylme_spage:latest .

# 或使用 Compose 构建
docker compose build web
```

## 故障排除

### 容器启动失败

```bash
docker logs lylme_spage
```

### 数据库连接失败

```bash
# 检查 MariaDB 状态
docker exec lylme_spage ps aux | grep mysql

# 测试数据库连接
docker exec lylme_spage mysql -u lylme -plylme123456 \
  --socket=/var/run/mysqld/mysqld.sock -e "SELECT 1;"
```

### 页面一直显示安装界面

说明初始化未完成，检查日志：

```bash
docker logs lylme_spage | grep -i error
```

### 容器内时间不是北京时间

```bash
# 检查当前时区
docker exec lylme_spage date

# 若为 UTC，重建容器并确认 TZ 环境变量为 Asia/Shanghai
docker compose up -d --build
```

## License

本项目使用 **Apache-2.0** 协议开源。

---

**项目地址**：<https://github.com/LyLme/lylme_spage>
