FROM composer:2 as vendor
WORKDIR /app
COPY database/ ./database/
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

FROM node:18-alpine as frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm install
COPY . .
RUN npm run build

FROM php:8.2-fpm-alpine
WORKDIR /var/www/html

RUN apk add --no-cache libzip-dev oniguruma-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql gd zip bcmath ctype fileinfo mbstring tokenizer xml

COPY --chown=www-data:www-data . .

COPY --chown=www-data:www-data --from=vendor /app/vendor/ ./vendor/
COPY --chown=www-data:www-data --from=frontend /app/public/build/ ./public/build/

RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 8000
CMD php artisan serve --host=0.0.0.0 --port=8000
