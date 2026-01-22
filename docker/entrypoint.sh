#!/bin/sh

echo "🚀 Starting SIMRS Admisi Docker Container..."

# Wait for database to be ready (skip for external databases like Railway)
if [ "$DB_HOST" = "db" ] || [ "$DB_HOST" = "localhost" ] || [ "$DB_HOST" = "127.0.0.1" ]; then
    echo "⏳ Waiting for local database connection..."
    until php artisan db:show > /dev/null 2>&1; do
        echo "Database is unavailable - sleeping"
        sleep 2
    done
    echo "✅ Database is ready!"
else
    echo "⚡ Using external database: $DB_HOST"
    echo "⏩ Skipping database wait check..."
fi

# Run migrations
echo "📊 Running database migrations..."
php artisan migrate --force

# Seed database if RUN_SEEDER is set to true
if [ "$RUN_SEEDER" = "true" ]; then
    echo "🌱 Seeding database..."
    php artisan db:seed --force
fi

# Clear and cache config
echo "⚡ Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage link if not exists
if [ ! -L /var/www/html/public/storage ]; then
    echo "🔗 Creating storage symlink..."
    php artisan storage:link
fi

# Set proper permissions
echo "🔐 Setting permissions..."
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

echo "✅ Application ready!"

# Start supervisor to manage nginx and php-fpm
echo "🌐 Starting services..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
