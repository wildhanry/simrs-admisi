# 🐳 Docker Deployment Guide - SIMRS Admisi

Panduan lengkap untuk menjalankan SIMRS Admisi menggunakan Docker.

## 📋 Prerequisites

- Docker Engine 20.10+
- Docker Compose 2.0+
- Git

## 🚀 Quick Start

### 1. Clone Repository
```bash
git clone https://github.com/wildhanry/simrs-admisi.git
cd simrs-admisi
```

### 2. Setup Environment
```bash
cp .env.docker .env
```

### 3. Build & Run dengan Docker Compose
```bash
# Build images
docker-compose build

# Start all services
docker-compose up -d

# Check logs
docker-compose logs -f app
```

### 4. Akses Aplikasi

- **SIMRS Admisi**: http://localhost:8000
- **phpMyAdmin**: http://localhost:8080

### 5. Login Default

**Admin:**
- Email: `admin@simrs.local`
- Password: `password`

**Staff:**
- Email: `staff@simrs.local`
- Password: `password`

## 📦 Services

### App (Laravel)
- **Port**: 8000
- **Technology**: PHP 8.3-FPM + Nginx
- **Framework**: Laravel 12.46.0

### Database (MySQL)
- **Port**: 3307 (host) → 3306 (container)
- **Version**: MySQL 8.0
- **Database**: simrs_admisi
- **User**: simrs_user
- **Password**: simrs_password

### phpMyAdmin
- **Port**: 8080
- **User**: root
- **Password**: root_password

## 🛠️ Docker Commands

### Start Services
```bash
# Start all services
docker-compose up -d

# Start specific service
docker-compose up -d app
```

### Stop Services
```bash
# Stop all services
docker-compose down

# Stop and remove volumes (⚠️ deletes database)
docker-compose down -v
```

### View Logs
```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f app
docker-compose logs -f db
```

### Rebuild
```bash
# Rebuild all images
docker-compose build --no-cache

# Rebuild and restart
docker-compose up -d --build
```

### Execute Commands in Container
```bash
# Enter app container
docker-compose exec app sh

# Run artisan commands
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
docker-compose exec app php artisan optimize:clear

# Run composer
docker-compose exec app composer install
```

## 🗄️ Database Management

### Initial Setup (First Run)
```bash
# Set RUN_SEEDER=true in docker-compose.yml
# Then restart
docker-compose down
docker-compose up -d

# Or manually:
docker-compose exec app php artisan migrate:fresh --seed
```

### Backup Database
```bash
docker-compose exec db mysqldump \
  -u simrs_user \
  -psimrs_password \
  simrs_admisi > backup-$(date +%Y%m%d).sql
```

### Restore Database
```bash
docker-compose exec -T db mysql \
  -u simrs_user \
  -psimrs_password \
  simrs_admisi < backup.sql
```

## 🔧 Troubleshooting

### Port Already in Use
```bash
# Change ports in docker-compose.yml
ports:
  - "8001:80"  # Change 8000 to 8001
```

### Permission Issues
```bash
# Fix storage permissions
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### Database Connection Failed
```bash
# Check if database is ready
docker-compose exec db mysql -u root -proot_password -e "SHOW DATABASES;"

# Restart database
docker-compose restart db

# Check logs
docker-compose logs db
```

### Clear Laravel Cache
```bash
docker-compose exec app php artisan optimize:clear
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

### Rebuild Everything
```bash
# Stop and remove everything
docker-compose down -v

# Remove images
docker-compose rm -f
docker rmi simrs-admisi-app

# Rebuild from scratch
docker-compose build --no-cache
docker-compose up -d
```

## 🚀 Production Deployment

### Build for Production
```bash
# Edit docker-compose.yml
environment:
  - APP_ENV=production
  - APP_DEBUG=false
  - RUN_SEEDER=false

# Build and deploy
docker-compose -f docker-compose.yml up -d --build
```

### Using Standalone Docker (without Compose)

#### 1. Build Image
```bash
docker build -t simrs-admisi:latest .
```

#### 2. Run with External Database
```bash
docker run -d \
  --name simrs-admisi \
  -p 80:80 \
  -e APP_NAME="SIMRS Admisi" \
  -e APP_ENV=production \
  -e APP_DEBUG=false \
  -e APP_KEY=base64:YOUR_APP_KEY \
  -e DB_HOST=your-db-host \
  -e DB_PORT=3306 \
  -e DB_DATABASE=simrs_admisi \
  -e DB_USERNAME=your_user \
  -e DB_PASSWORD=your_password \
  simrs-admisi:latest
```

## 📊 Health Checks

### Check Application Status
```bash
curl http://localhost:8000/
```

### Check Database Connection
```bash
docker-compose exec app php artisan db:show
```

### Check All Services
```bash
docker-compose ps
```

## 🔐 Security Notes

### Production Checklist
- [ ] Change `APP_KEY` to unique value
- [ ] Set `APP_DEBUG=false`
- [ ] Use strong database passwords
- [ ] Change default user passwords
- [ ] Enable HTTPS (use reverse proxy like Nginx/Traefik)
- [ ] Set proper file permissions
- [ ] Regular backups
- [ ] Monitor logs

### Generate New APP_KEY
```bash
docker-compose exec app php artisan key:generate --show
# Copy output and update in docker-compose.yml
```

## 📈 Performance Optimization

### Increase PHP Memory
Edit `Dockerfile`:
```dockerfile
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory.ini
```

### Enable OPcache
```dockerfile
RUN docker-php-ext-install opcache
RUN echo "opcache.enable=1" > /usr/local/etc/php/conf.d/opcache.ini
```

### Increase Upload Limit
Edit `docker/nginx.conf`:
```nginx
client_max_body_size 100M;
```

## 🐛 Common Issues

### Issue: "Address already in use"
**Solution:** Port 8000/3307/8080 already used
```bash
# Change ports in docker-compose.yml or stop conflicting service
sudo netstat -tulpn | grep :8000
```

### Issue: "No such file or directory"
**Solution:** Build assets missing
```bash
npm install
npm run build
docker-compose up -d --build
```

### Issue: "SQLSTATE[HY000] [2002] Connection refused"
**Solution:** Database not ready yet
```bash
# Wait 10-15 seconds after docker-compose up
# Or check db logs
docker-compose logs db
```

## 📝 Docker Files Structure

```
simrs-admisi/
├── Dockerfile              # Multi-stage build (frontend + backend)
├── .dockerignore           # Files to exclude from build
├── docker-compose.yml      # Orchestration config
├── .env.docker            # Docker environment template
└── docker/
    ├── entrypoint.sh       # Startup script
    ├── nginx.conf          # Nginx configuration
    └── supervisord.conf    # Process manager config
```

## 🆘 Support

Jika mengalami masalah:

1. Check logs: `docker-compose logs -f`
2. Verify services: `docker-compose ps`
3. Restart: `docker-compose restart`
4. Rebuild: `docker-compose up -d --build`

---

**Happy Dockerizing! 🐳**
