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

# 1. Wait for Database
if [ "$DB_CONNECTION" = "mysql" ]; then
    wait_for_mysql
fi

# 2. Database migration and seeding
# We do this BEFORE optimizations because 'optimize:clear' (cache:clear) 
# fails if the database tables (like 'cache') don't exist yet.
if [ "$APP_ENV" != "production" ] || [ "$DB_AUTO_MIGRATE" = "true" ]; then
    echo "Running database migrations..."
    
    # Pre-check for SQLite if driver is sqlite
    if [ "$DB_CONNECTION" = "sqlite" ]; then
        DB_PATH="$DB_DATABASE"
        if [[ ! "$DB_PATH" = /* ]] && [ "$DB_PATH" != ":memory:" ]; then
            DB_PATH="/var/www/html/database/$DB_DATABASE"
        fi
        
        if [ ! -f "$DB_PATH" ] && [ "$DB_PATH" != ":memory:" ]; then
            echo "CRITICAL: SQLite database file not found at $DB_PATH"
            exit 1
        fi
    fi

    php artisan migrate --force || { 
        echo "CRITICAL: Database migration failed.";
        exit 1; 
    }
    
    if [ "$DB_AUTO_SEED" = "true" ]; then
        if [ "$APP_ENV" = "production" ]; then
            echo "WARNING: Running seeders in PRODUCTION."
        fi
        php artisan db:seed --force || { echo "WARNING: Seeding failed, but continuing..."; }
    fi
fi

# 3. Run optimizations
echo "Running Laravel optimizations..."
php artisan optimize:clear || { echo "CRITICAL: optimize:clear failed"; exit 1; }
php artisan config:cache || { echo "CRITICAL: config:cache failed"; exit 1; }
php artisan route:cache || { echo "CRITICAL: route:cache failed"; exit 1; }
php artisan view:cache || { echo "CRITICAL: view:cache failed"; exit 1; }
php artisan l5-swagger:generate || { echo "CRITICAL: l5-swagger:generate failed"; exit 1; }

# Execute the CMD
echo "Starting application..."
exec "$@"
