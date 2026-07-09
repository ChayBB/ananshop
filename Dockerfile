FROM php:8.3-fpm-alpine AS base

RUN apk add --no-cache \
        nginx \
        supervisor \
        bash \
        curl \
        git \
        unzip \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        libzip-dev \
        libwebp-dev \
        libcurl \
        curl-dev \
        icu-dev \
        oniguruma-dev \
        libxml2-dev \
        nodejs \
        npm \
        gettext \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        intl \
        gd \
        zip \
        calendar \
        bcmath \
        exif \
        curl \
        opcache \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress \
    && npm install && npm run build \
    && (cd packages/Webkul/Admin && npm install && npm run build) \
    && (cd packages/Webkul/Shop && npm install && npm run build) \
    && (cd packages/Webkul/Installer && npm install && npm run build) \
    && rm -rf node_modules \
        packages/Webkul/Admin/node_modules \
        packages/Webkul/Shop/node_modules \
        packages/Webkul/Installer/node_modules

RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache

COPY docker/nginx.conf.template /etc/nginx/templates/nginx.conf.template
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 10000

ENTRYPOINT ["/entrypoint.sh"]
