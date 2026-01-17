#!/bin/sh
set -e

echo "==> Waiting for MySQL at ${DB_HOST}:${DB_PORT} ..."

# Avoid leaking password via ps output
export MYSQL_PWD="${DB_PASSWORD}"

until mysql \
  -h "${DB_HOST}" \
  -P "${DB_PORT}" \
  -u "${DB_USERNAME}" \
  -e "SELECT 1" >/dev/null 2>&1
do
  echo "MySQL not ready... sleeping 2s"
  sleep 2
done

unset MYSQL_PWD
echo "==> MySQL is ready!"

# Optional: wait for Redis too
if [ -n "${REDIS_HOST}" ] && [ -n "${REDIS_PORT}" ] && [ -n "${REDIS_PASSWORD}" ]; then
  echo "==> Waiting for Redis at ${REDIS_HOST}:${REDIS_PORT} ..."
  until redis-cli -h "${REDIS_HOST}" -p "${REDIS_PORT}" -a "${REDIS_PASSWORD}" ping 2>/dev/null | grep -q PONG
  do
    echo "Redis not ready... sleeping 2s"
    sleep 2
  done
  echo "==> Redis is ready!"
fi

echo "==> Clearing cached config/routes/views..."
php artisan optimize:clear || true

echo "==> Running migrations..."
php artisan migrate --force

echo "==> Rebuilding caches..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "==> Starting application..."
exec "$@"
