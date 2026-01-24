# ---------- Stage 1: Build ----------
FROM php:8.3-fpm-alpine as build

# Install build dependencies
RUN apk add --no-cache \
    $PHPIZE_DEPS \
    linux-headers \
    git \
    unzip \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    intl \
    zip \
    bcmath \
    gd \
    opcache

# Install PHP Redis extension
RUN pecl install redis \
    && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files
COPY composer.json composer.lock ./

# Install dependencies (no dev, optimized)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copy project files
COPY . .

# ---------- Stage 2: Production ----------
FROM php:8.3-fpm-alpine

# Install runtime dependencies
RUN apk add --no-cache \
    bash \
    curl \
    libzip \
    icu-libs \
    libpng \
    libjpeg-turbo \
    freetype \
    oniguruma

# Copy PHP extension artifacts from build stage
COPY --from=build /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=build /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d

# Production PHP Configuration
RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=10000'; \
    echo 'opcache.validate_timestamps=0'; \
    } > /usr/local/etc/php/conf.d/opcache.init

# Set working directory
WORKDIR /var/www/html

# Copy application from build stage
COPY --from=build /var/www/html /var/www/html

# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache

# Entrypoint setup
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["php-fpm"]
