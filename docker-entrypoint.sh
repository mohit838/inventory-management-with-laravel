#!/bin/bash
set -e

# Function to wait for MySQL
wait_for_mysql() {
    echo "Waiting for MySQL to be ready..."
    until mysql -h "$DB_HOST" -u "$DB_USERNAME" -p"$DB_PASSWORD" -e "SELECT 1" > /dev/null 2>&1; do
        sleep 2
        echo "MySQL is still unavailable - sleeping"
    done
    echo "MySQL is ready!"
}

# Run optimizations
echo "Running Laravel optimizations..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan l5-swagger:generate

# Database migration and seeding
if [ "$APP_ENV" != "production" ] || [ "$DB_AUTO_MIGRATE" = "true" ]; then
    echo "Running database migrations..."
    php artisan migrate --force
    
    if [ "$DB_AUTO_SEED" = "true" ]; then
        echo "Running database seeders..."
        php artisan db:seed --force
    fi
fi

# Execute the CMD
echo "Starting application..."
exec "$@"
