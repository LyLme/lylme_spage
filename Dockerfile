# LyLme Spage Docker Image - All-in-One
# Apache + PHP 8.2 + MariaDB (内置)

FROM php:8.2-apache

LABEL maintainer="lylme(六零)"
LABEL description="六零导航页-简洁高效的上网导航"

# 设置环境变量
ENV DEBIAN_FRONTEND=noninteractive
ENV MYSQL_HOST=localhost
ENV MYSQL_USER=lylme
ENV MYSQL_PASSWORD=lylme123456
ENV MYSQL_DATABASE=lylme_spage

# 安装 MariaDB 和 PHP 扩展
RUN apt-get update && apt-get install -y --no-install-recommends \
    mariadb-server \
    mariadb-client \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    libonig-dev \
    supervisor \
    curl \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        mysqli \
        pdo \
        pdo_mysql \
        zip \
        curl \
        mbstring \
        xml \
        bcmath \
        opcache \
    && pecl install redis && docker-php-ext-enable redis || true

# 配置 MariaDB 只监听本地 socket
RUN mkdir -p /var/run/mysqld && \
    chown mysql:mysql /var/run/mysqld && \
    chmod 755 /var/run/mysqld && \
    echo "[mysqld]" > /etc/mysql/mariadb.conf.d/99-local-only.cnf && \
    echo "bind-address = 127.0.0.1" >> /etc/mysql/mariadb.conf.d/99-local-only.cnf && \
    echo "skip-ssl" >> /etc/mysql/mariadb.conf.d/99-local-only.cnf

# 配置 PHP
RUN { \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.interned_strings_buffer=8'; \
        echo 'opcache.max_accelerated_files=10000'; \
        echo 'opcache.revalidate_freq=2'; \
        echo 'upload_max_filesize = 50M'; \
        echo 'post_max_size = 50M'; \
        echo 'memory_limit = 256M'; \
        echo 'max_execution_time = 300'; \
    } > /usr/local/etc/php/conf.d/custom.ini

# 启用 Apache 模块
RUN a2enmod rewrite headers expires deflate

# 设置工作目录
WORKDIR /var/www/html

# 复制应用文件
COPY www/ /var/www/html/

# 复制初始化文件
COPY install.sql /init/install.sql

# 复制启动脚本
COPY docker-entrypoint.sh /docker-entrypoint.sh
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# 设置权限
RUN chmod +x /docker-entrypoint.sh \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# 数据目录
VOLUME ["/var/lib/mysql", "/var/www/html"]

# 暴露端口
EXPOSE 80

# 健康检查
HEALTHCHECK --interval=30s --timeout=10s --start-period=30s --retries=3 \
    CMD curl -f http://localhost/ || exit 1

# 入口点
ENTRYPOINT ["/docker-entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
