FROM php:8.0-apache

# 코드 복사
COPY . /var/www/html

# 포트 열기기
EXPOSE 80

# MySQL 확장 설치
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Apache 모듈 활성화
RUN a2enmod rewrite

# 시스템 전체 Apache 응답 인코딩 UTF-8로 고정
RUN echo "AddDefaultCharset UTF-8" >> /etc/apache2/apache2.conf