FROM php:8.2-apache

RUN apt-get update && apt-get install -y unzip git libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

WORKDIR /var/www/html
COPY . .

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader && php artisan config:cache

RUN a2enmod rewrite
