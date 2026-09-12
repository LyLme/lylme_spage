# GitHub Actions 工作流说明

针对 [LyLme/lylme_spage](https://github.com/LyLme/lylme_spage)（六零导航页）配置的自动化工作流。
仓库现状：`master` 为 PHP 源码（v2.7.0），`docker` 分支含 Dockerfile 与 `www/` 源码副本，
目前没有 `.github` 目录 —— 把本目录整体拷到仓库根目录即可生效。

## 工作流一览

| 文件 | 触发时机 | 作用 |
|---|---|---|
| `docker-build.yml` | 推送 `v*.*.*` 标签 / docker 分支变更 / 手动 | buildx 构建 amd64、armv7、arm64 三架构并推送镜像 |
| `release.yml` | 推送 `v*.*.*` 标签 / 手动 | 打包安装包 + 更新包；tag 触发时创建 Release 并上传资产，手动触发只产出构建产物 |
| `php-lint.yml` | PHP 文件变更 / PR | PHP 5.6~8.4 多版本语法检查 + 安全扫描 |
| `sync-docker-branch.yml` | master 分支变更 | 把 master 源码自动同步到 docker 分支的 `www/` |
| `docker-smoke-test.yml` | 每 10 天 06:00（北京时间）/ 手动 | 拉起 latest 镜像，验证首页与后台可访问 |

## 需要的 Secrets / Variables

进入仓库 **Settings → Secrets and variables → Actions** 配置：

| 名称 | 类型 | 必填 | 说明 |
|---|---|---|---|
| `DOCKERHUB_USERNAME` | Secret | 推送 Docker Hub 时必填 | Docker Hub 用户名 |
| `DOCKERHUB_TOKEN` | Secret | 推送 Docker Hub 时必填 | Docker Hub **Access Token**（不是登录密码） |
| `SYNC_PAT` | Secret | 否 | 有 `repo` 权限的 PAT。配了它，`sync-docker-branch.yml` 推送后才能自动触发 `docker-build.yml`；不配则只能手动触发构建 |
| `ENABLE_GHCR` | Variable | 否 | 设为 `true` 时额外推送到 `ghcr.io/<owner>/lylme_spage`，用内置 `GITHUB_TOKEN`，无需额外配置 |

没有配任何推送目标（`DOCKERHUB_USERNAME` 为空且 `ENABLE_GHCR` 不为 `true`）时，
`docker-build.yml` 会跳过登录与推送，只执行构建验证（不会再因未认证而失败）。
仅启用 GHCR 时，也只生成 GHCR 标签，不会去推 Docker Hub。

> 版本一致性保护：tag 触发构建时，若 tag 版本号（如 `v2.7.1`）与 docker 分支
> `www/include/version.php` 不一致，工作流会直接报错退出，提示先跑同步。因此
> **务必先同步源码、再打 tag**。

## 安装步骤

```bash
# 在仓库根目录执行
mkdir -p .github/workflows
cp /path/to/lylme-workflows/.github/workflows/*.yml .github/workflows/
git add .github && git commit -m "ci: 添加 GitHub Actions 工作流" && git push
```

工作流文件建议**同时放到 master 和 docker 分支**。原因是：

- `docker-build.yml` 默认 checkout `docker` 分支（Dockerfile 在那里），所以放在 master 上也能正常构建；
- `php-lint.yml` 与 `release.yml` 主要面向 master 的源码。
- 若启用了 `sync-docker-branch.yml`，master 的 `.github` 会被排除在同步之外（脚本里已 `--exclude='.github/'`），
  因此 docker 分支的 `.github` 需要手动同步一次。

## 发版流程（推荐）

1. 在 master 上改代码，把 `include/version.php` 里的 `VERSION` 改成新版本号；
2. 提交后 `sync-docker-branch.yml` 自动把源码同步到 docker 分支的 `www/`；
3. 构建镜像：
   - 配了 `SYNC_PAT` —— 同步推送会自动触发 `docker-build.yml`；
   - 没配 —— 手动运行 `docker-build.yml`，ref 保持默认 `docker`；
4. 确认镜像正常后，打标签并推送：

```bash
git tag -a v2.7.0 -m "六零导航页 v2.7.0"
git push origin v2.7.0
```

5. `release.yml` 自动生成两个 zip 并创建 Release；`docker-build.yml` 推送 `v2.7.0` / `2.7.0` / `v2.7.0-<sha>` / `latest` 四组标签。

## 注意事项

- **PHP 5.6 检查仅作提醒**：matrix 中标了 `experimental: true`，即使失败也不会阻断合并。
- **`docker-smoke-test.yml` 只测 `latest`**，且依赖外网拉取镜像；Docker Hub 限流时可能偶发失败。
- 工作流里读取版本号的命令依赖 GNU grep 的 `-oP`，GitHub 托管的 Ubuntu runner 默认支持；自建 runner 若报 `--enable-perl-regexp` 相关错误，需换成 `sed` 写法。
