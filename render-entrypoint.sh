#!/bin/sh

echo "🚀 Starting SIMRS Admisi on Render.com..."

# Create necessary directories
echo "📁 Creating required directories..."
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache
mkdir -p /tmp/nginx
mkdir -p /var/log/nginx

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

# Start PHP-FPM in background
echo "🔧 Starting PHP-FPM..."
php-fpm -D

# Generate nginx config with PORT variable
echo "🌐 Configuring Nginx for port $PORT..."
cat > /tmp/nginx-render.conf <<EOF
user www-data;
worker_processes auto;
pid /tmp/nginx.pid;
error_log /dev/stderr;

events {
    worker_connections 1024;
}

http {
    include /etc/nginx/mime.types;
    default_type application/octet-stream;

    access_log /dev/stdout;

    sendfile on;
    tcp_nopush on;
    tcp_nodelay on;
    keepalive_timeout 65;
    types_hash_max_size 2048;
    client_max_body_size 20M;

    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types text/plain text/css text/xml text/javascript 
               application/json application/javascript application/xml+rss;

    server {
        listen ${PORT:-8000};
        server_name _;
        root /var/www/html/public;

        index index.php index.html;

        location / {
            try_files \$uri \$uri/ /index.php?\$query_string;
        }

        location = /favicon.ico { access_log off; log_not_found off; }
        location = /robots.txt  { access_log off; log_not_found off; }

        error_page 404 /index.php;

        location ~ \.php$ {
            fastcgi_pass 127.0.0.1:9000;
            fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
            include fastcgi_params;
            fastcgi_hide_header X-Powered-By;
        }

        location ~ /\.(?!well-known).* {
            deny all;
        }

        location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
            expires 1y;
            add_header Cache-Control "public, immutable";
            access_log off;
        }
    }
}
EOF

# Start Nginx
echo "🌐 Starting Nginx on port $PORT..."
exec nginx -c /tmp/nginx-render.conf -g 'daemon off;'
