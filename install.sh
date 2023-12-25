#!/bin/bash

if ! command -v docker &> /dev/null || ! command -v docker-compose &> /dev/null; then  
    curl -fsSL https://get.docker.com | bash -s docker --mirror Aliyun
fi

read -p "请输入数据库名（默认为spage）：" db_database
db_database=${db_database:-spage}  
  
read -p "请设置数据库的root密码（默认为123456）：" db_password
db_password=${db_password:-123456}

echo "#### 外网端口和域名配置 ####"
echo "提示：域名需要解析后才能访问，若无外网访问需求可按下回车键保持默认"


while true; do

    read -p "请输入外网端口号80到65535（默认为 8080）：" port
    port=${port:-8080}
    # 检查端口是否被占用
    result=$(netstat -tuln | grep ":$port ")
    if [[ -n "$result" ]]; then
        echo "端口 $port 已被占用"
    else
        break
    fi
done



sed -i "s/<port>/$port/" nginx/conf.d/nginx.conf.bak
read -p "请输入网站域名(可不填)：" domain
if [ -n "$domain" ]; then  
   sed -i "s/<domain>/$domain/" nginx/conf.d/nginx.conf.bak
else  
   sed -i "s/<domain>/_/" nginx/conf.d/nginx.conf.bak

fi
domain=${domain:-ip}


mv nginx/conf.d/nginx.conf.bak nginx/conf.d/nginx.conf
mv nginx/conf.d/default.conf nginx/conf.d/default.conf.bak

# 创建docker-compose.yml文件，并写入配置信息
cat << EOF > docker-compose-sh.yml
version: '3'
services:
  php:
    container_name: "spage-php74"
    build: ./php
    image: php74-fpm-alpine 
    #ports: 
    #  - "9000:9000"  #fpm 端口
    restart: always
    volumes:
      - ./php/etc:/usr/local/etc/php/conf.d #映射配置文件
      - ./www:/var/www/html
    stdin_open: true
    tty: true
    links:
      - "mysql" #链接mysql服务
    networks:
      spage:
        ipv4_address: 10.10.10.2
    environment:
        - TZ=Asia/Shanghai # 设置时区
  nginx:
    container_name: "spage-nginx"
    image: nginx:latest
    restart: always
    ports:
      - "$port:80"
    environment:
      - TZ=Asia/Shanghai
    depends_on:
      - "php"
    links:
      - "php"
    volumes:
      - ./nginx/conf.d:/etc/nginx/conf.d
      #- ./nginx/conf/nginx.conf:/etc/nginx/nginx.conf
      - ./nginx/log:/var/log/nginx/
      - ./www:/var/www/html
    networks:
      spage:
        ipv4_address: 10.10.10.10 #分配ip
  mysql:
    container_name: "spage-mysql"
    image: mysql:5.7
    #ports:
    #  - ":3306"
    volumes:
      - ./mysql/data:/var/lib/mysql #数据目录
    restart: always
    environment:
      - TZ=Asia/Shanghai
      - MYSQL_ROOT_PASSWORD=$db_password #MySQL root密码
      - MYSQL_DATABASE=$db_database  #MySQL 数据库名
    networks:
      spage:
        ipv4_address: 10.10.10.1 #MySQL ip地址
networks:
  spage:
    driver: bridge
    driver_opts:
      com.docker.network.enable_ipv6: "true"
    ipam:
      config:
        - subnet: 10.10.0.0/16

EOF
# 启动Docker Compose服务  
docker-compose -f docker-compose-sh.yml up -d

# 显示安装完成信息  

echo "外网地址：http://$domain:$port(外网访问需确保端口已放行)"
echo "内网地址：http://10.10.10.10"
echo "数据库地址：10.10.10.1(或spage-mysql)"
echo "数据库端口：3306"
echo "数据库名：$db_database"
echo "数据库用户名：root"
echo "数据库密码：$db_password"
echo "网站部署完成！"

