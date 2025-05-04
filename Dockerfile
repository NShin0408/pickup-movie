# --- Node.jsでViteビルド ---
FROM node:18 AS node_builder

WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# --- PHP + ApacheでLaravelを実行 ---
FROM php:8.2-apache

# 必要なPHP拡張とcomposer
RUN apt-get update && apt-get install -y unzip git libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Apacheの公開ディレクトリをLaravelの public に変更
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

# Node.js側のビルド結果とLaravelファイルをコピー
COPY --from=node_builder /app /var/www/html

# Composer追加
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Laravel依存インストールとキャッシュ
WORKDIR /var/www/html
RUN composer install --no-dev --optimize-autoloader && php artisan config:cache

# Apacheのmod_rewrite有効化（ルーティング対応）
RUN a2enmod rewrite
