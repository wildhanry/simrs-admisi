#!/bin/sh

echo "🚀 Starting SIMRS Admisi on Render.com..."

# Render uses native PHP, not Docker!
# Just use built-in PHP server for simplicity

# Create necessary directories
echo "📁 Creating required directories..."
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/testing
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache
mkdir -p storage/app/public

# Install dependencies if not present
if [ ! -d "vendor" ]; then
    echo "📦 Installing Composer dependencies..."
    composer install --no-dev --optimize-autoloader --no-interaction
fi

if [ ! -d "node_modules" ]; then
    echo "📦 Installing NPM dependencies..."
    npm ci
fi

# Build frontend assets if not present
if [ ! -d "public/build" ]; then
    echo "🎨 Building frontend assets..."
    npm run build
fi

# Create storage directories (ensure they exist)
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/testing
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p storage/app/public

# Create storage link
echo "🔐 Setting permissions..."
chmod -R 775 storage bootstrap/cache

# Create storage link
echo "🔗 Creating storage symlink..."
php artisan storage:link --force || true

# Run migrations
echo "📊 Running database migrations..."
php artisan migrate --force

# Seed database if RUN_SEEDER is true
if [ "$RUN_SEEDER" = "true" ]; then
    echo "🌱 Seeding database..."
    php artisan db:seed --force
fi

# Cache optimization
echo "⚡ Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Application ready!"
echo "🌐 Starting PHP built-in server on port ${PORT}..."

# Use PHP built-in server (simple and works on Render)
exec php -S 0.0.0.0:${PORT} -t public public/index.php
