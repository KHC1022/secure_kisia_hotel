#!/bin/bash
set -e

SSL_DIR="/etc/apache2/ssl"
CERT_FILE="$SSL_DIR/server.crt"
KEY_FILE="$SSL_DIR/server.key"

# SSL 폴더가 없으면 생성
mkdir -p "$SSL_DIR"

# 인증서가 없으면 생성 (없을 때만)
if [ ! -f "$CERT_FILE" ] || [ ! -f "$KEY_FILE" ]; then
  echo "Generating self-signed SSL certificate..."
  openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout "$KEY_FILE" \
    -out "$CERT_FILE" \
    -subj "/CN=localhost" \
    -addext "subjectAltName=DNS:localhost,IP:127.0.0.1" \
    -extensions v3_ca \
    -config <(cat /etc/ssl/openssl.cnf <(printf "[v3_ca]\nbasicConstraints=CA:FALSE"))
else
  echo "SSL certificate already exists. Skipping generation."
fi

# Apache 실행
apache2-foreground