# --- Node.jsでViteビルド ---
FROM node:18 AS node_builder

WORKDIR /app
COPY package*.json ./
RUN npm install --omit=optional
COPY . .
ENV NODE_ENV=production
RUN npm run build

# --- PHP + ApacheでLaravelを実行 ---
FROM php:8.2-apache

RUN apt-get update && apt-get install -y unzip git libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

COPY --from=node_builder /app /var/www/html
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
RUN composer install --no-dev --optimize-autoloader && php artisan config:cache

RUN a2enmod rewrite
