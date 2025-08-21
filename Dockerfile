FROM composer:2 as vendor
WORKDIR /app
COPY . .
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

FROM node:20-alpine as frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN NODE_OPTIONS=--openssl-legacy-provider npm run build

FROM php:8.2-fpm-alpine
WORKDIR /var/www/html

RUN set -eux; \
    apk add --no-cache \
        freetype libjpeg-turbo libpng libzip zlib oniguruma libxml2; \
    apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        freetype-dev libjpeg-turbo-dev libpng-dev libzip-dev zlib-dev oniguruma-dev libxml2-dev; \
    docker-php-source extract; \
    docker-php-ext-configure gd --with-freetype --with-jpeg; \
    docker-php-ext-configure zip; \
    docker-php-ext-install -j"$(nproc)" \
        pdo_mysql gd zip bcmath ctype fileinfo mbstring tokenizer xml; \
    docker-php-source delete; \
    apk del .build-deps

COPY --chown=www-data:www-data --from=vendor /app/ /var/www/html/
COPY --chown=www-data:www-data --from=frontend /app/public/build/ ./public/build/

RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8000
CMD php artisan serve --host=0.0.0.0 --port=8000
