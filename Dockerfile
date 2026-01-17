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
    redis \
    supervisor \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev

# ---------- Build deps for PECL + phpize ----------
RUN apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    linux-headers

# ---------- PHP Extensions (core) ----------
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    intl \
    zip \
    bcmath \
    gd \
    opcache

# ---------- PHPRedis (PECL) ----------
RUN pecl install redis \
    && docker-php-ext-enable redis

# ---------- Production PHP Config ----------
RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=10000'; \
    echo 'opcache.validate_timestamps=0'; \
    } > /usr/local/etc/php/conf.d/opcache.ini

# ---------- Remove build deps ----------
RUN apk del .build-deps

# ---------- Composer ----------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ---------- Set Working Directory ----------
WORKDIR /var/www/html

# ---------- Copy composer files first (better cache) ----------
COPY composer.json composer.lock ./

# IMPORTANT FIX:
# Don't run Laravel scripts during build (package:discover runs here and fails)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# ---------- Copy Project Files ----------
COPY . .

# ---------- Permissions ----------
RUN chown -R www-data:www-data storage bootstrap/cache

# ---------- Entrypoint ----------
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# ---------- Default Port ----------
ENV PORT=4002
EXPOSE 4002

# ---------- Execution ----------
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]

# JSON array CMD (fixes warning + proper signals)
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=4002"]
