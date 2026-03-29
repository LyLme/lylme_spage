#!/bin/bash
set -e

# 颜色输出
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

log_info() { echo -e "${GREEN}[INFO]${NC} $1"; }
log_warn() { echo -e "${YELLOW}[WARN]${NC} $1"; }
log_error() { echo -e "${RED}[ERROR]${NC} $1"; }
log_step() { echo -e "${BLUE}[STEP]${NC} $1"; }

# 检查是否已安装
is_installed() {
    [ -f "/var/www/html/install/install.lock" ]
}

# 等待 MariaDB 就绪
wait_for_mysql() {
    log_step "等待 MariaDB 启动..."
    
    local max_attempts=60
    local attempt=1
    
    while [ $attempt -le $max_attempts ]; do
        if mysqladmin ping --socket=/var/run/mysqld/mysqld.sock --skip-ssl --silent 2>/dev/null; then
            log_info "MariaDB 服务已就绪"
            return 0
        fi
        sleep 1
        attempt=$((attempt + 1))
    done
    
    log_error "MariaDB 启动超时"
    return 1
}

# 创建数据库和用户
setup_database() {
    log_step "配置数据库..."
    
    local db_user="${MYSQL_USER:-lylme}"
    local db_pass="${MYSQL_PASSWORD:-lylme123456}"
    local db_name="${MYSQL_DATABASE:-lylme_spage}"
    
    # 创建数据库
    mysql -u root --socket=/var/run/mysqld/mysqld.sock --skip-ssl -e "CREATE DATABASE IF NOT EXISTS \`${db_name}\` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null
    
    # 创建用户并授权
    mysql -u root --socket=/var/run/mysqld/mysqld.sock --skip-ssl -e "CREATE USER IF NOT EXISTS '${db_user}'@'localhost' IDENTIFIED BY '${db_pass}';" 2>/dev/null || true
    mysql -u root --socket=/var/run/mysqld/mysqld.sock --skip-ssl -e "GRANT ALL PRIVILEGES ON \`${db_name}\`.* TO '${db_user}'@'localhost';" 2>/dev/null || true
    mysql -u root --socket=/var/run/mysqld/mysqld.sock --skip-ssl -e "FLUSH PRIVILEGES;" 2>/dev/null
    
    log_info "数据库和用户已创建"
}

# 导入初始数据
import_database() {
    log_step "导入数据库结构..."
    
    local db_user="${MYSQL_USER:-lylme}"
    local db_pass="${MYSQL_PASSWORD:-lylme123456}"
    local db_name="${MYSQL_DATABASE:-lylme_spage}"
    
    if [ -f "/init/install.sql" ]; then
        mysql -u "${db_user}" -p"${db_pass}" --socket=/var/run/mysqld/mysqld.sock --skip-ssl "${db_name}" < /init/install.sql 2>/dev/null
        if [ $? -eq 0 ]; then
            log_info "数据库结构导入成功"
            return 0
        else
            log_error "数据库导入失败"
            return 1
        fi
    else
        log_error "找不到 install.sql 文件"
        return 1
    fi
}

# 更新配置文件
update_config() {
    log_step "更新配置文件..."
    
    local db_user="${MYSQL_USER:-lylme}"
    local db_pass="${MYSQL_PASSWORD:-lylme123456}"
    local db_name="${MYSQL_DATABASE:-lylme_spage}"
    
    # 生成配置文件
    cat > /var/www/html/config.php << EOF
<?php
/*数据库配置*/
\$dbconfig=array(
    "host" => "localhost",
    "port" => 3306,
    "user" => "${db_user}",
    "pwd" => "${db_pass}",
    "dbname" => "${db_name}",
    "socket" => "/var/run/mysqld/mysqld.sock",
);
?>
EOF
    
    log_info "配置文件已更新"
}

# 创建安装锁文件
create_lock_file() {
    log_step "创建安装锁文件..."
    
    mkdir -p /var/www/html/install
    
    cat > /var/www/html/install/install.lock << EOF
# LyLme Spage 安装锁文件
安装时间: $(date '+%Y-%m-%d %H:%M:%S')
数据库: ${MYSQL_DATABASE:-lylme_spage}
EOF
    
    chown www-data:www-data /var/www/html/install/install.lock 2>/dev/null || true
    chmod 444 /var/www/html/install/install.lock
    
    log_info "安装锁文件已创建"
}

# 显示安装信息
show_info() {
    echo ""
    echo -e "${GREEN}=========================================="
    echo -e "  🎉 LyLme Spage 安装完成！"
    echo -e "==========================================${NC}"
    echo ""
    echo -e "  ${BLUE}前台地址:${NC} http://localhost/"
    echo -e "  ${BLUE}后台地址:${NC} http://localhost/admin/"
    echo ""
    echo -e "  ${YELLOW}默认账号:${NC} admin"
    echo -e "  ${YELLOW}默认密码:${NC} 123456"
    echo ""
    echo -e "  ${RED}⚠️  请登录后台修改默认密码！${NC}"
    echo ""
}

# 初始化函数
do_init() {
    if is_installed; then
        log_info "检测到安装锁文件，跳过初始化"
        return 0
    fi
    
    log_info "首次运行，开始自动安装..."
    
    # 等待 MariaDB
    if ! wait_for_mysql; then
        return 1
    fi
    
    # 配置数据库
    setup_database
    
    # 导入数据
    if import_database; then
        # 更新配置
        update_config
        
        # 创建锁文件
        create_lock_file
        
        # 显示信息
        show_info
    fi
}

# 主函数
main() {
    log_info "=========================================="
    log_info "  LyLme Spage 启动中..."
    log_info "=========================================="
    
    # 初始化 MariaDB 数据目录
    mkdir -p /var/run/mysqld
    chown -R mysql:mysql /var/run/mysqld
    chmod 755 /var/run/mysqld
    
    # 检查是否需要初始化 MySQL 数据目录
    if [ ! -d "/var/lib/mysql/mysql" ]; then
        log_info "初始化 MySQL 数据目录..."
        mysql_install_db --user=mysql --datadir=/var/lib/mysql
        chown -R mysql:mysql /var/lib/mysql
    fi
    
    # 在后台启动初始化检查
    (
        sleep 5
        do_init
    ) &
    
    # 设置目录权限
    chown -R www-data:www-data /var/www/html 2>/dev/null || true
    
    # 启动 supervisord
    exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
}

main "$@"
