# --- Node.js で Vite ビルド ---
FROM node:18 AS node_builder

WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# --- PHP CLI コンテナ（artisan serve 用）---
FROM php:8.2-cli

# 必要な拡張とツール
RUN apt-get update && apt-get install -y unzip git libzip-dev sqlite3 libsqlite3-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite zip

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# アプリケーションファイル
WORKDIR /app
COPY . .

# node ビルド成果物
COPY --from=node_builder /app/public/build /app/public/build

# Laravel セットアップ
RUN composer install --no-dev --optimize-autoloader \
    && php artisan config:clear \
    && php artisan key:generate \
    && php artisan view:clear

# ポート設定
EXPOSE 8000

# Laravel サーバ起動（本番環境で artisan serve を使うのは限定的だがRenderではOK）
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
