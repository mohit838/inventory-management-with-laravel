# ---------- Base Image ----------
FROM php:8.3-fpm-alpine

# ---------- System Dependencies ----------
RUN apk add --no-cache \
    bash \
    curl \
    git \
    unzip \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    mysql-client \
    supervisor

# ---------- Build deps for PECL + phpize ----------
RUN apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    linux-headers

# ---------- PHP Extensions (core) ----------
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    intl \
    zip \
    bcmath

# ---------- PHPRedis (PECL) ----------
RUN pecl install redis \
    && docker-php-ext-enable redis

# ---------- Remove build deps ----------
RUN apk del .build-deps

# ---------- Composer ----------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ---------- Set Working Directory ----------
WORKDIR /var/www/html

# ---------- Copy Project Files ----------
COPY . .

# ---------- Install PHP Dependencies ----------
# (For local dev you may remove --no-dev if you want dev packages)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# ---------- Permissions ----------
RUN chown -R www-data:www-data storage bootstrap/cache

# ---------- Default Port ----------
ENV PORT=4002
EXPOSE 4002

# ---------- Start Laravel ----------
CMD ["sh", "-lc", "php artisan serve --host=0.0.0.0 --port=${PORT}"]
