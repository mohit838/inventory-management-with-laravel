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
php artisan optimize:clear || { echo "CRITICAL: optimize:clear failed"; exit 1; }
php artisan config:cache || { echo "CRITICAL: config:cache failed. Check your .env/database configuration."; exit 1; }
php artisan route:cache || { echo "CRITICAL: route:cache failed"; exit 1; }
php artisan view:cache || { echo "CRITICAL: view:cache failed"; exit 1; }
php artisan l5-swagger:generate || { echo "CRITICAL: l5-swagger:generate failed"; exit 1; }

# Database migration and seeding
if [ "$APP_ENV" != "production" ] || [ "$DB_AUTO_MIGRATE" = "true" ]; then
    echo "Running database migrations..."
    
    # Pre-check for SQLite if driver is sqlite
    if [ "$DB_CONNECTION" = "sqlite" ]; then
        # Resolve database path (basic logic to match our database.php fix)
        DB_PATH="$DB_DATABASE"
        if [[ ! "$DB_PATH" = /* ]]; then
            DB_PATH="/var/www/html/database/$DB_DATABASE"
        fi
        
        if [ ! -f "$DB_PATH" ] && [ "$DB_PATH" != ":memory:" ]; then
            echo "CRITICAL: SQLite database file not found at $DB_PATH"
            echo "Container failed to start - check database volume/path in production."
            exit 1
        fi
    fi

    php artisan migrate --force || { 
        echo "CRITICAL: Database migration failed.";
        echo "Container failed to start - database migration error.";
        exit 1; 
    }
    
    if [ "$DB_AUTO_SEED" = "true" ]; then
        echo "Running database seeders..."
        php artisan db:seed --force || { echo "WARNING: Seeding failed, but continuing..."; }
    fi
fi

# Execute the CMD
echo "Starting application..."
exec "$@"
