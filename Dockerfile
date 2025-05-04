# --- Node.js でフロントエンドをビルド ---
FROM node:18 AS node_builder

WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# --- PHP + Apache で Laravel を実行 ---
FROM php:8.2-apache

# PHP拡張 & Apache設定
RUN apt-get update && apt-get install -y unzip git libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Apacheの公開ディレクトリを Laravel の public に変更
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

# Composer をインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Laravel アプリを配置
WORKDIR /var/www/html
COPY . .

# フロントエンドビルド成果物を配置
COPY --from=node_builder /app/public/build /var/www/html/public/build

# .env がなければ example をコピー（Render 環境でも artisan コマンドを通すため）
RUN if [ ! -f .env ]; then cp .env.example .env; fi

# Laravel セットアップ
RUN composer install --no-dev --optimize-autoloader \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && php artisan key:generate --force \
    && php artisan config:cache

# mod_rewrite を有効化（ルーティング用）
RUN a2enmod rewrite

# ポート80（Apache）をExpose
EXPOSE 80

# Apache をフォアグラウンド起動
CMD ["apache2-foreground"]
