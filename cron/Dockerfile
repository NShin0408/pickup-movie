FROM php:8.2-cli

# 必要な拡張とcomposerのインストール
RUN apt-get update && apt-get install -y unzip git libzip-dev zip \
    && docker-php-ext-install pdo pdo_mysql zip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

COPY . .

RUN composer install --no-dev --optimize-autoloader

# 実行するArtisanコマンド（cronから呼ばれる）
CMD ["php", "artisan", "cache:movies"]
