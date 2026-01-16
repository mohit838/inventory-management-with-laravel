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

# ---------- PHP Extensions ----------
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

# ---------- Working Directory ----------
WORKDIR /var/www/html

# ---------- Copy Project Files ----------
COPY . .

# ---------- Install Dependencies ----------
# For dev, you can remove --no-dev
RUN composer install --no-dev --optimize-autoloader --no-interaction

# ---------- Permissions ----------
RUN mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# ---------- Default Port ----------
ENV PORT=4002
EXPOSE 4002

# ---------- Start Laravel ----------
CMD ["sh", "-lc", "php artisan serve --host=0.0.0.0 --port=${PORT}"]
