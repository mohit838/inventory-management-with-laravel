FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl bash \
    libpng-dev libonig-dev libxml2-dev \
    zip unzip libzip-dev \
    libwebp-dev libjpeg62-turbo-dev libfreetype6-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd opcache zip

# Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy only composer files first (better caching)
COPY composer.json composer.lock ./

# Install dependencies (DEV: keep dev deps)
RUN composer install --prefer-dist --no-interaction

# Copy the rest
COPY . .

# Ensure permissions (won't matter if volume mounted, but ok for non-mounted)
RUN mkdir -p /var/www/storage /var/www/bootstrap/cache \
    && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
