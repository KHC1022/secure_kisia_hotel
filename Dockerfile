FROM php:8.0-apache

# 앱 복사
COPY ./src /var/www/html

# uploads 폴더 권한 설정
RUN chmod -R 777 /var/www/html/uploads

# Apache + PHP 확장 + SSL 도구 설치
RUN apt-get update && apt-get install -y openssl
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Apache 모듈 활성화
RUN a2enmod ssl rewrite headers

# 기존 인증서 복사 제거 (자동 생성 예정)
# COPY ssl/server.crt /etc/apache2/ssl/server.crt
# COPY ssl/server.key /etc/apache2/ssl/server.key

# Apache 설정 복사
COPY apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY apache/ssl.conf /etc/apache2/sites-available/ssl.conf

# 기본 사이트 비활성화
RUN a2dissite 000-default.conf

# 새 사이트 활성화
RUN a2ensite ssl.conf
RUN a2ensite 000-default.conf

# UTF-8 기본 설정
RUN echo "AddDefaultCharset UTF-8" >> /etc/apache2/apache2.conf

# entrypoint 스크립트 복사 및 권한 부여
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# 포트 노출
EXPOSE 80 443

# entrypoint 실행
ENTRYPOINT ["/entrypoint.sh"]