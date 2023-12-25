# 使用官方的Alpine Linux基础镜像
FROM php:7.4-fpm-alpine3.10

# 安装依赖和Composer

RUN sed -i 's/dl-cdn.alpinelinux.org/mirrors.aliyun.com/g' /etc/apk/repositories \
    &&  apk --no-cache add \
        build-base \
        libzip-dev \
        freetype-dev \
        libjpeg-turbo-dev \
        libpng-dev \
        libwebp-dev \
        oniguruma-dev \
    && wget https://mirrors.aliyun.com/composer/composer.phar \
    && mv composer.phar /usr/bin/composer \
    && chmod +x /usr/bin/composer

# 安装PHP扩展
RUN docker-php-ext-configure gd --with-webp=/usr/include/webp --with-jpeg=/usr/include --with-freetype=/usr/include/freetype2/ \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql mysqli bcmath zip \
    && docker-php-ext-enable pdo_mysql gd \
    && chown -R www-data:www-data /var/www/html