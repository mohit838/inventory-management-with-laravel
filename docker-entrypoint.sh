#!/bin/bash
set -e

echo "Waiting for MySQL..."

# keep trying until MySQL answers
until mysql -h"${DB_HOST}" -P"${DB_PORT:-3306}" -u"${DB_USERNAME}" -p"${DB_PASSWORD}" -e "SELECT 1" >/dev/null 2>&1
do
  echo "MySQL not ready... sleeping"
  sleep 2
done

echo "MySQL is ready!"

# migrate + seed (optional)
php artisan migrate --force || exit 1

# caches
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Starting application..."
exec "$@"
