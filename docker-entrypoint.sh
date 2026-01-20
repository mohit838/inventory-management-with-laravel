#!/bin/bash
set -e

# Color codes for better logging
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Timeout settings (in seconds)
MAX_WAIT=60
WAIT_INTERVAL=2

# Load environment variables from .env if present
if [ -f .env ]; then
    export $(grep -v '^#' .env | xargs)
fi

# Function to wait for a service with timeout
wait_for_service() {
  local service_name=$1
  local check_command=$2
  local elapsed=0

  echo -e "${YELLOW}==> Waiting for ${service_name} (timeout: ${MAX_WAIT}s)...${NC}"

  while [ $elapsed -lt $MAX_WAIT ]; do
    if eval "$check_command" >/dev/null 2>&1; then
      echo -e "${GREEN}==> ${service_name} is ready!${NC}"
      return 0
    fi
    echo "    ${service_name} not ready... waiting ${WAIT_INTERVAL}s (elapsed: ${elapsed}s)"
    sleep $WAIT_INTERVAL
    elapsed=$((elapsed + WAIT_INTERVAL))
  done

  echo -e "${RED}==> ERROR: ${service_name} failed to become ready within ${MAX_WAIT}s${NC}"
  return 1
}

# Wait for MySQL
if [ -n "${DB_HOST}" ] && [ -n "${DB_PORT}" ]; then
  # Use simple TCP connection check instead of authentication
  # This avoids SSL and authentication plugin compatibility issues
  wait_for_service "MySQL at ${DB_HOST}:${DB_PORT}" \
    "nc -z \"${DB_HOST}\" \"${DB_PORT}\" 2>/dev/null"
else
  echo -e "${YELLOW}==> Skipping MySQL check (DB_HOST or DB_PORT not set)${NC}"
fi

# Wait for Redis (support both authenticated and non-authenticated)
if [ -n "${REDIS_HOST}" ] && [ -n "${REDIS_PORT}" ]; then
  if [ -n "${REDIS_PASSWORD}" ] && [ "${REDIS_PASSWORD}" != "null" ]; then
    # Redis with authentication
    wait_for_service "Redis at ${REDIS_HOST}:${REDIS_PORT} (authenticated)" \
      "redis-cli -h \"${REDIS_HOST}\" -p \"${REDIS_PORT}\" -a \"${REDIS_PASSWORD}\" --no-auth-warning ping 2>/dev/null | grep -q PONG"
  else
    # Redis without authentication
    wait_for_service "Redis at ${REDIS_HOST}:${REDIS_PORT} (no auth)" \
      "redis-cli -h \"${REDIS_HOST}\" -p \"${REDIS_PORT}\" ping 2>/dev/null | grep -q PONG"
  fi
else
  echo -e "${YELLOW}==> Skipping Redis check (REDIS_HOST or REDIS_PORT not set)${NC}"
fi

# Clear all Laravel caches
echo -e "${YELLOW}==> Clearing cached config/routes/views/events...${NC}"
php artisan optimize:clear || true

# Ensure storage directory structure exists
echo -e "${YELLOW}==> Initializing storage directory structure...${NC}"
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p storage/logs
mkdir -p storage/app/public
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Create storage link if it doesn't exist
echo -e "${YELLOW}==> Creating storage symlink...${NC}"
php artisan storage:link || true

# Run database migrations
echo -e "${YELLOW}==> Running database migrations...${NC}"
php artisan migrate --force

# Optional: Seed database in development
if [ "${APP_ENV}" = "local" ] || [ "${APP_ENV}" = "development" ]; then
  echo -e "${YELLOW}==> Seeding database (development mode)...${NC}"
  php artisan db:seed --force || true
fi

# Rebuild Laravel caches for production performance
echo -e "${YELLOW}==> Rebuilding Laravel caches...${NC}"
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true
php artisan event:cache || true

# Generate API documentation if in development
if [ "${APP_ENV}" = "local" ] || [ "${APP_ENV}" = "development" ]; then
  echo -e "${YELLOW}==> Generating API documentation...${NC}"
  php artisan l5-swagger:generate || true
fi

echo -e "${GREEN}==> All initialization complete. Starting application...${NC}"
exec "$@"
