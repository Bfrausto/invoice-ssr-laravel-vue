FROM composer:2 as vendor
WORKDIR /app
COPY . .
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

FROM node:20-alpine as frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm install
COPY . .
RUN NODE_OPTIONS=--openssl-legacy-provider npm run build

FROM php:8.2-fpm

RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        nginx \
        supervisor \
        $PHPIZE_DEPS \
        libzip-dev zlib1g-dev \
        libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
        libxml2-dev \
        libonig-dev \
    ; \
    docker-php-source extract; \
    docker-php-ext-configure gd --with-freetype --with-jpeg; \
    docker-php-ext-configure zip; \
    docker-php-ext-install -j"$(nproc)" gd pdo_mysql zip bcmath mbstring xml; \
    docker-php-source delete; \
    apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false $PHPIZE_DEPS; \
    rm -rf /var/lib/apt/lists/*

COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY nginx.conf /etc/nginx/sites-available/default
RUN ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

WORKDIR /var/www/html
COPY --from=vendor /app/ /var/www/html/
COPY --from=frontend /app/public/build/ ./public/build/

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache


CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
