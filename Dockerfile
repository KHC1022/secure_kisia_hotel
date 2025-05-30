FROM php:8.0-apache

# 앱 복사
COPY ./src /var/www/html

# Apache + PHP 확장 + SSL 도구 설치
RUN apt-get update && apt-get install -y \
    openssl \
    vim

RUN docker-php-ext-install mysqli pdo pdo_mysql

# Apache 모듈 활성화
RUN a2enmod ssl rewrite headers

# 인증서 및 설정 복사
COPY ssl/server.crt /etc/apache2/ssl/server.crt
COPY ssl/server.key /etc/apache2/ssl/server.key
COPY apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY apache/ssl.conf /etc/apache2/sites-available/ssl.conf

# 기존 default 사이트 비활성화
RUN a2dissite 000-default.conf

# 새 사이트 설정 활성화
RUN a2ensite ssl.conf
RUN a2ensite 000-default.conf

# 로그 디렉토리 생성
RUN mkdir -p /var/www/html/logs

# php.ini 보안 (버퍼오버플로우 차단) 및 로그 설정
RUN echo "AddDefaultCharset UTF-8" >> /etc/apache2/apache2.conf && \
    echo "post_max_size = 8M" >> /usr/local/etc/php/php.ini && \
    echo "upload_max_filesize = 5M" >> /usr/local/etc/php/php.ini && \
    echo "max_input_vars = 1000" >> /usr/local/etc/php/php.ini && \
    echo "max_input_time = 60" >> /usr/local/etc/php/php.ini && \
    echo "memory_limit = 128M" >> /usr/local/etc/php/php.ini && \
    echo "log_errors = On" >> /usr/local/etc/php/php.ini && \
    echo "error_log = /var/www/html/logs/php_errors.log" >> /usr/local/etc/php/php.ini

# Apache 보안 설정 (서버 정보 숨김)
RUN echo "ServerSignature Off" >> /etc/apache2/conf-available/security.conf && \
    echo "ServerTokens Prod" >> /etc/apache2/conf-available/security.conf

# 포트 노출
EXPOSE 80 443