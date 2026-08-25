#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
六零导航页 Docker 镜像多架构构建推送

【交互模式】
    python build-push.py

【命令行模式】
    python build-push.py --proxy                  # 用系统代理环境变量
    python build-push.py --proxy-url http://127.0.0.1:7890  # 指定代理地址
    python build-push.py --no-latest             # 只推版本标签

说明:
    - 版本号从 www/include/version.php 自动读取 (define('VERSION', 'x.y.z'))
    - 自动打 v<版本号> 标签, 默认同时打 latest
    - 使用 docker buildx 构建 linux/amd64, linux/arm/v7, linux/arm64 三种架构
    - 镜像仓库: lylme/lylme_spage
"""

import argparse
import os
import re
import subprocess
import sys

# ---------- 配置 ----------
IMAGE = "lylme/lylme_spage"
PLATFORMS = "linux/amd64,linux/arm/v7,linux/arm64"
VERSION_FILE = os.path.join("www", "include", "version.php")
BUILDER = "lylme-multiarch"

PROXY_ENV_KEYS = [
    "HTTP_PROXY", "http_proxy",
    "HTTPS_PROXY", "https_proxy",
    "NO_PROXY", "no_proxy",
    "ALL_PROXY", "all_proxy",
]


def log(msg):
    print(f"[信息] {msg}")


def err(msg):
    print(f"[错误] {msg}", file=sys.stderr)


def hr():
    print("-" * 56)


def read_version():
    """从 version.php 读取版本号，返回 ('vX.Y.Z', 'X.Y.Z')。"""
    if not os.path.isfile(VERSION_FILE):
        sys.exit(f"找不到版本文件: {VERSION_FILE}")
    with open(VERSION_FILE, "r", encoding="utf-8") as f:
        content = f.read()
    m = re.search(
        r"define\(\s*['\"]VERSION['\"]\s*,\s*['\"]([^'\"]+)['\"]\s*\)", content
    )
    if not m:
        sys.exit(f"在 {VERSION_FILE} 中未找到 VERSION 定义")
    version = m.group(1).strip()
    if not re.fullmatch(r"\d+\.\d+(\.\d+)?", version):
        err(f"版本号格式异常: '{version}' (期望如 2.6.0)")
        sys.exit(1)
    return f"v{version}", version


def run(cmd, env=None, check=True):
    """执行命令。check=False 时不因失败退出。"""
    printable = " ".join(str(c) for c in cmd)
    print(f"\n$ {printable}")
    try:
        r = subprocess.run(cmd, env=env)
    except FileNotFoundError:
        err("未找到 docker 命令，请确认已安装 Docker 且在本机可用。")
        sys.exit(1)
    if check and r.returncode != 0:
        err(f"命令执行失败 (exit={r.returncode}): {printable}")
        sys.exit(r.returncode)
    return r.returncode


def check_login():
    """检查是否已登录 DockerHub，未登录则退出。"""
    r = subprocess.run(
        ["docker", "info", "--format", "{{.RegistryConfig}}"],
        capture_output=True, text=True,
    )
    if r.returncode != 0:
        err("无法连接 Docker daemon，请确认 Docker 已启动。")
        sys.exit(1)
    cfg = os.path.join(os.path.expanduser("~"), ".docker", "config.json")
    has_auth = False
    if os.path.isfile(cfg):
        with open(cfg, "r", encoding="utf-8", errors="ignore") as f:
            txt = f.read()
        has_auth = '"auths"' in txt and "https://index.docker.io/v1/" in txt
    if not has_auth:
        err("尚未登录 DockerHub，请先执行: docker login")
        sys.exit(1)
    log("DockerHub 登录状态检查通过。")


def ensure_builder():
    """确保 buildx builder 实例存在且已启动。"""
    out = subprocess.run(
        ["docker", "buildx", "ls"], capture_output=True, text=True
    )
    if BUILDER not in out.stdout:
        log(f"创建 buildx builder: {BUILDER}")
        run(["docker", "buildx", "create", "--name", BUILDER,
             "--driver", "docker-container", "--use"])
    else:
        run(["docker", "buildx", "use", BUILDER])
    run(["docker", "buildx", "inspect", "--bootstrap"])


def apply_proxy(env, proxy_url=None):
    """根据是否启用代理配置环境变量。返回是否启用代理。"""
    if proxy_url is not None:
        for k in ("HTTP_PROXY", "HTTPS_PROXY", "http_proxy", "https_proxy", "ALL_PROXY", "all_proxy"):
            env[k] = proxy_url
        log(f"已启用代理(指定地址): {proxy_url}")
        return True
    has_proxy = any(k in os.environ for k in ("HTTPS_PROXY", "https_proxy", "HTTP_PROXY", "http_proxy"))
    if has_proxy:
        log("已启用代理模式(沿用系统代理环境变量)。")
        return True
    for k in PROXY_ENV_KEYS:
        env.pop(k, None)
    log("未启用代理模式，使用直连。")
    return False


def ask(question, default="", choices=None, hint=None, default_label=None):
    """通用交互输入。choices 为可选项列表(大小写均可)，空输入返回 default。"""
    if choices:
        # 展示用选项(保持原始大小写)
        opts = " / ".join(choices)
        # 实际接受的值(转小写便于比较)
        lowered = {c.lower(): c for c in choices}
        # 默认值展示: 优先用 default_label, 否则默认选项大写显示
        show_default = default_label if default_label else (default.upper() if default else "")
        prompt = f"{question} [{opts}]"
    else:
        show_default = default
        prompt = f"{question}" + (f" (默认: {default})" if default else "")
    if hint:
        print(f"    {hint}")
    while True:
        suffix = f" (默认 {show_default})" if show_default else ""
        val = input(f"  > {prompt}{suffix}: ").strip()
        if not val:
            return default
        if choices:
            key = val.lower()
            if key not in lowered:
                print(f"    ✗ 请输入其中之一: {opts}")
                continue
            return lowered[key]
        return val


def interactive_prompt():
    """交互式收集参数，返回命名空间对象。"""
    print("\n" + "=" * 56)
    print("  六零导航页 镜像构建推送")
    print("=" * 56)

    # 1. 显示当前版本并允许覆盖
    tag_version, raw_version = read_version()
    hr()
    print(f"  检测到版本号: {raw_version}  (来自 {VERSION_FILE})")
    print("  将以此版本号打标签 v" + raw_version)
    use_def = ask("是否沿用该版本号", default="Y", choices=["Y", "n"],
                  default_label="Y 沿用")
    version_tag = tag_version
    if use_def == "n":
        new_ver = ask("请输入新的版本号 (不含 v 前缀, 如 2.6.0)",
                      hint="格式: 数字.数字[.数字]")
        if not re.fullmatch(r"\d+\.\d+(\.\d+)?", new_ver):
            err(f"版本号格式异常: '{new_ver}'")
            sys.exit(1)
        version_tag = f"v{new_ver}"
        print(f"  将使用版本号: {version_tag}")
    else:
        version_tag = tag_version

    # 2. 是否打 latest 标签
    hr()
    print("  latest 标签始终指向最新稳定版，方便用户 `docker pull lylme/lylme_spage` 直接获取。")
    latest = ask("是否同时推送 latest 标签", default="Y", choices=["Y", "n"],
                 default_label="Y 推送")
    no_latest = (latest == "n")

    # 3. 代理模式
    hr()
    print("  请选择网络代理方式 (影响镜像拉取与推送速度):")
    print("    1) 直连        - 不使用任何代理")
    print("    2) 系统代理    - 沿用当前环境的 HTTP(S)_PROXY 变量")
    print("    3) 自定义代理  - 手动输入代理地址")
    proxy_choice = ask("选择代理方式", default="1", choices=["1", "2", "3"],
                       default_label="1 直连")
    proxy_url = None
    use_proxy = False
    if proxy_choice == "2":
        use_proxy = True
        print("  将沿用系统代理环境变量。")
    elif proxy_choice == "3":
        proxy_url = ask("请输入代理地址", hint="例如 http://127.0.0.1:7890")
        use_proxy = True

    # 4. 确认 (打印将要执行的命令, 选择是否执行)
    hr()
    tags = [f"{IMAGE}:{version_tag}"]
    if not no_latest:
        tags.append(f"{IMAGE}:latest")
    print("  构建配置确认:")
    print(f"    镜像标签 : {', '.join(tags)}")
    print(f"    目标架构 : {PLATFORMS}")
    print(f"    代理模式 : {'直连' if not use_proxy else ('系统代理' if proxy_choice=='2' else proxy_url)}")
    print("  将执行的命令:")
    cmd_preview = ["docker", "buildx", "build", "--platform", PLATFORMS]
    for t in tags:
        cmd_preview += ["-t", t]
    cmd_preview += ["--push", "."]
    print("    " + " ".join(cmd_preview))
    hr()
    confirm = ask("确认开始构建并推送", default="Y", choices=["Y", "n"],
                  default_label="Y 开始")
    if confirm == "n":
        print("已取消。")
        sys.exit(0)

    # 组装为类 argparse 的命名空间
    class Args:
        pass
    a = Args()
    a.version_tag = version_tag
    a.no_latest = no_latest
    a.proxy = (proxy_choice == "2")
    a.proxy_url = proxy_url
    a.interactive = True
    return a


def main():
    parser = argparse.ArgumentParser(
        description="六零导航页 Docker 镜像多架构构建并推送脚本"
    )
    parser.add_argument("-p", "--proxy", action="store_true",
                        help="使用系统代理环境变量构建并推送")
    parser.add_argument("--proxy-url", metavar="URL", default=None,
                        help="显式指定代理地址, 如 http://127.0.0.1:7890")
    parser.add_argument("--no-latest", action="store_true",
                        help="不打 latest 标签, 只推送版本标签")
    parser.add_argument("--yes", "-y", action="store_true",
                        help="命令行模式下跳过确认直接执行")
    # 无参数 -> 进入交互模式
    if len(sys.argv) == 1:
        args = interactive_prompt()
    else:
        args = parser.parse_args()
        # 命令行模式: 补全交互字段
        args.interactive = False
        # 命令行模式版本从文件读取
        tag_version, _ = read_version()
        args.version_tag = tag_version

    use_proxy = getattr(args, "proxy", False) or args.proxy_url is not None

    # 读取版本 (交互模式已在提示时读取, 这里复用)
    tag_version = args.version_tag
    log(f"目标标签: {tag_version}" + ("" if args.no_latest else ", latest"))

    check_login()

    env = os.environ.copy()
    apply_proxy(env, proxy_url=args.proxy_url)
    if use_proxy and args.proxy_url is None and not any(
        k in env for k in ("HTTPS_PROXY", "https_proxy")
    ):
        log("提示: 未检测到系统代理环境变量, 将使用直连。")

    ensure_builder()

    tags = [f"{IMAGE}:{tag_version}"]
    if not args.no_latest:
        tags += [f"{IMAGE}:latest"]

    cmd = ["docker", "buildx", "build", "--platform", PLATFORMS]
    for t in tags:
        cmd += ["-t", t]
    cmd += ["--push", "."]

    # 交互模式已在最后一步展示命令并确认, 这里仅处理命令行模式的二次确认
    if not args.interactive and not getattr(args, "yes", False):
        print("\n即将执行以下构建推送:")
        print(f"  {' '.join(cmd)}")
        c = input("  确认执行? [Y/n]: ").strip().lower()
        if c not in ("", "y", "yes"):
            print("已取消。")
            sys.exit(0)

    run(cmd, env=env)

    print("\n[完成] 已构建并推送以下镜像:")
    for t in tags:
        print(f"  - {t}")
    print(f"  - 架构: {PLATFORMS}")


if __name__ == "__main__":
    main()
