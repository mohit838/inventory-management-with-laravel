#!/bin/bash
set -e

echo "Waiting for MySQL..."

until mysql -h"${DB_HOST}" -P"${DB_PORT:-3306}" -u"${DB_USERNAME}" -p"${DB_PASSWORD}" -e "SELECT 1" >/dev/null 2>&1
do
  echo "MySQL not ready... sleeping"
  sleep 2
done

echo "MySQL is ready!"

# Clear cached config so wrong old values don't remain
php artisan optimize:clear || true

# migrate
php artisan migrate --force

# cache again
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Starting application..."
exec "$@"
