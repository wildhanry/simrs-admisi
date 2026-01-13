# SIMRS Admisi - Quick Command Reference

## Initial Setup Commands

```powershell
# 1. Create MySQL database
# Via MySQL CLI:
mysql -u root -p
CREATE DATABASE simrs_admisi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# Or using Laragon - database auto-creates on first connection

# 2. Install all dependencies
composer install
npm install

# 3. Generate app key (if not set)
php artisan key:generate

# 4. Run migrations
php artisan migrate

# 5. Seed database with initial data
php artisan db:seed

# 6. Build frontend assets
npm run build
```

## Development Commands

```powershell
# Start Laravel development server
php artisan serve

# Start Vite dev server (in separate terminal)
npm run dev

# Or run both simultaneously (uses concurrently)
npm run dev & php artisan serve

# Watch for file changes and auto-reload
npm run dev
```

## Database Commands

```powershell
# Run all migrations
php artisan migrate

# Rollback last migration batch
php artisan migrate:rollback

# Rollback all migrations
php artisan migrate:reset

# Rollback and re-run all migrations
php artisan migrate:refresh

# Refresh database and seed
php artisan migrate:refresh --seed

# Run specific seeder
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=DoctorSeeder

# Create new migration
php artisan make:migration create_table_name

# Create model with migration
php artisan make:model ModelName -m
```

## Code Generation Commands

```powershell
# Create controller
php artisan make:controller PatientController
php artisan make:controller Admin/DoctorController --resource

# Create model
php artisan make:model Patient
php artisan make:model Patient -m  # with migration
php artisan make:model Patient -mfs # with migration, factory, seeder

# Create middleware
php artisan make:middleware EnsureUserIsAdmin

# Create form request
php artisan make:request StorePatientRequest

# Create seeder
php artisan make:seeder PatientSeeder

# Create factory
php artisan make:factory PatientFactory

# Create service (manual - create in app/Services/)
# No artisan command, create manually

# Create policy
php artisan make:policy PatientPolicy --model=Patient

# Create command
php artisan make:command GenerateMedicalRecordNumber
```

## Cache Commands

```powershell
# Clear all caches
php artisan optimize:clear

# Clear specific caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Create cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Testing Commands

```powershell
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/PatientTest.php

# Run with coverage
php artisan test --coverage

# Create test
php artisan make:test PatientTest
php artisan make:test PatientTest --unit
```

## Queue Commands

```powershell
# Run queue worker
php artisan queue:work

# Run queue with specific connection
php artisan queue:work database

# Process specific queue
php artisan queue:work --queue=high,default

# Run queue listener
php artisan queue:listen

# Retry failed jobs
php artisan queue:retry all
```

## Maintenance Commands

```powershell
# Put application in maintenance mode
php artisan down

# Bring application back online
php artisan up

# Create symbolic link for storage
php artisan storage:link
```

## Useful Artisan Commands

```powershell
# List all routes
php artisan route:list

# List specific routes (filter)
php artisan route:list --name=patient
php artisan route:list --path=admin

# Show application information
php artisan about

# List all registered commands
php artisan list

# Interactive tinker shell
php artisan tinker

# Check code style (Pint)
./vendor/bin/pint

# Fix code style
./vendor/bin/pint --repair
```

## NPM Commands

```powershell
# Install dependencies
npm install

# Development build with watch
npm run dev

# Production build
npm run build

# Update dependencies
npm update
```

## Git Commands (Recommended)

```powershell
# Initialize repository
git init

# Add all files
git add .

# Commit
git commit -m "Initial SIMRS setup"

# Create .gitignore (already exists in Laravel)
# Ensure vendor/, node_modules/, .env are ignored
```

## Composer Commands

```powershell
# Install dependencies
composer install

# Install for production
composer install --optimize-autoloader --no-dev

# Update dependencies
composer update

# Update specific package
composer update laravel/framework

# Dump autoload
composer dump-autoload

# Install new package
composer require barryvdh/laravel-dompdf
```

## Database Inspection (Tinker)

```powershell
php artisan tinker

# Check user count
>>> App\Models\User::count()

# Get first admin
>>> App\Models\User::where('role', 'admin')->first()

# Check beds availability
>>> App\Models\Bed::where('status', 'available')->count()

# Generate test registration number
>>> App\Models\Registration::generateRegistrationNumber('outpatient')

# Exit tinker
>>> exit
```

## Permission Commands (Linux/Mac)

```bash
# Set proper permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Set ownership (if needed)
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache
```

## Windows Laragon Specific

```powershell
# Laragon usually handles permissions automatically

# If you need to clear opcache in Laragon:
# Just restart Apache/Nginx from Laragon panel

# Database location in Laragon:
# C:\laragon\data\mysql\simrs-admisi
```

## Production Deployment

```powershell
# 1. Update .env for production
# APP_ENV=production
# APP_DEBUG=false

# 2. Install dependencies
composer install --optimize-autoloader --no-dev
npm install
npm run build

# 3. Generate key
php artisan key:generate

# 4. Run migrations
php artisan migrate --force

# 5. Seed database (if needed)
php artisan db:seed --force

# 6. Cache everything
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Create storage link
php artisan storage:link
```

## Troubleshooting

```powershell
# Permission denied errors
php artisan cache:clear
php artisan config:clear
composer dump-autoload

# Migration issues
php artisan migrate:fresh --seed

# Class not found
composer dump-autoload

# View not found
php artisan view:clear

# NPM build issues
rm -rf node_modules package-lock.json
npm install
npm run build
```

## Quick Development Workflow

```powershell
# Terminal 1: Start Laravel server
php artisan serve

# Terminal 2: Start Vite dev server
npm run dev

# Terminal 3: Watch logs (optional)
tail -f storage/logs/laravel.log

# Or use the built-in dev script:
npm run dev
```

## Health Checks

```powershell
# Check PHP version
php -v

# Check Composer version
composer --version

# Check Node version
node -v

# Check NPM version
npm -v

# Check Laravel version
php artisan --version

# Check database connection
php artisan tinker
>>> DB::connection()->getPdo()
```
