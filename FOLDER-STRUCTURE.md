# SIMRS Admisi - Complete Folder Structure

```
simrs-admisi/
│
├── app/
│   ├── Console/
│   │   └── Commands/                    # Custom artisan commands
│   │
│   ├── Exceptions/
│   │   └── Handler.php                  # Global exception handler
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Controller.php           # ✅ Base controller with helpers
│   │   │   ├── DashboardController.php  # ✅ Dashboard example
│   │   │   │
│   │   │   ├── Admin/                   # Admin-only controllers
│   │   │   │   ├── UserController.php
│   │   │   │   ├── DoctorController.php
│   │   │   │   ├── PolyclinicController.php
│   │   │   │   ├── WardController.php
│   │   │   │   └── BedController.php
│   │   │   │
│   │   │   ├── Auth/                    # Authentication controllers
│   │   │   │   ├── LoginController.php
│   │   │   │   └── RegisterController.php
│   │   │   │
│   │   │   ├── PatientController.php    # Patient management
│   │   │   ├── OutpatientController.php # Rawat Jalan
│   │   │   ├── InpatientController.php  # Rawat Inap
│   │   │   ├── ReportController.php     # Reports
│   │   │   └── PrintController.php      # PDF printing
│   │   │
│   │   ├── Middleware/
│   │   │   ├── Authenticate.php
│   │   │   ├── EnsureUserIsAdmin.php    # ✅ Admin middleware
│   │   │   ├── EnsureUserHasRole.php    # ✅ Role middleware
│   │   │   └── ...
│   │   │
│   │   └── Requests/                    # Form validation requests
│   │       ├── StorePatientRequest.php
│   │       ├── UpdatePatientRequest.php
│   │       ├── StoreOutpatientRequest.php
│   │       ├── StoreInpatientRequest.php
│   │       └── ...
│   │
│   ├── Models/
│   │   ├── User.php                     # ✅ With role methods
│   │   ├── Patient.php                  # ✅ With relationships
│   │   ├── Doctor.php                   # ✅ With scopes
│   │   ├── Polyclinic.php              # ✅ Complete
│   │   ├── Ward.php                     # ✅ With bed management
│   │   ├── Bed.php                      # ✅ With status methods
│   │   └── Registration.php             # ✅ With generation methods
│   │
│   ├── Policies/                        # Authorization policies
│   │   ├── PatientPolicy.php
│   │   ├── RegistrationPolicy.php
│   │   └── ...
│   │
│   ├── Providers/
│   │   ├── AppServiceProvider.php       # ✅ Service providers
│   │   └── ...
│   │
│   └── Services/                        # ⚠️ CREATE THIS - Business logic layer
│       ├── PatientService.php           # Patient operations
│       ├── RegistrationService.php      # Registration with transactions
│       ├── BedManagementService.php     # Bed allocation
│       ├── MedicalRecordService.php     # MR number generation
│       └── ReportService.php            # Statistics & reports
│
├── bootstrap/
│   ├── app.php                          # ✅ Middleware aliases configured
│   ├── providers.php
│   └── cache/
│
├── config/
│   ├── app.php                          # App configuration
│   ├── auth.php                         # Auth configuration
│   ├── database.php                     # Database configuration
│   └── ...
│
├── database/
│   ├── factories/
│   │   ├── UserFactory.php
│   │   ├── PatientFactory.php           # ⚠️ CREATE for testing
│   │   └── ...
│   │
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php        # ✅ With roles
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   ├── 2024_01_01_000001_create_patients_table.php     # ✅ Complete
│   │   ├── 2024_01_01_000002_create_doctors_table.php      # ✅ Complete
│   │   ├── 2024_01_01_000003_create_polyclinics_table.php  # ✅ Complete
│   │   ├── 2024_01_01_000004_create_wards_table.php        # ✅ Complete
│   │   ├── 2024_01_01_000005_create_beds_table.php         # ✅ Complete
│   │   └── 2024_01_01_000006_create_registrations_table.php # ✅ Complete
│   │
│   └── seeders/
│       ├── DatabaseSeeder.php           # ✅ Calls all seeders
│       ├── UserSeeder.php               # ✅ Admin & Staff users
│       ├── DoctorSeeder.php             # ✅ 5 sample doctors
│       ├── PolyclinicSeeder.php         # ✅ 5 polyclinics
│       └── WardSeeder.php               # ✅ 4 wards with 50 beds
│
├── public/
│   ├── index.php                        # Entry point
│   ├── robots.txt
│   └── build/                           # ⚠️ Created after npm run build
│       ├── assets/
│       └── manifest.json
│
├── resources/
│   ├── css/
│   │   └── app.css                      # ✅ Tailwind with custom utilities
│   │
│   ├── js/
│   │   ├── app.js                       # Main JS file
│   │   └── bootstrap.js
│   │
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php            # ✅ Main layout
│       │   ├── guest.blade.php          # ⚠️ CREATE - Guest layout
│       │   └── admin.blade.php          # ⚠️ CREATE - Admin layout
│       │
│       ├── components/                  # ⚠️ CREATE - Reusable components
│       │   ├── header.blade.php
│       │   ├── sidebar.blade.php
│       │   ├── alert.blade.php
│       │   ├── modal.blade.php
│       │   └── ...
│       │
│       ├── auth/                        # Authentication views
│       │   ├── login.blade.php
│       │   └── register.blade.php
│       │
│       ├── dashboard.blade.php          # ✅ Dashboard view
│       │
│       ├── patients/                    # Patient views
│       │   ├── index.blade.php
│       │   ├── create.blade.php
│       │   ├── edit.blade.php
│       │   └── show.blade.php
│       │
│       ├── outpatient/                  # Outpatient registration views
│       │   ├── index.blade.php
│       │   ├── create.blade.php
│       │   └── show.blade.php
│       │
│       ├── inpatient/                   # Inpatient registration views
│       │   ├── index.blade.php
│       │   ├── create.blade.php
│       │   └── show.blade.php
│       │
│       ├── admin/                       # Admin views
│       │   ├── users/
│       │   ├── doctors/
│       │   ├── polyclinics/
│       │   ├── wards/
│       │   └── beds/
│       │
│       ├── reports/                     # Report views
│       │   ├── daily.blade.php
│       │   ├── monthly.blade.php
│       │   └── statistics.blade.php
│       │
│       └── pdf/                         # PDF templates
│           ├── registration.blade.php
│           ├── patient-card.blade.php
│           └── admission-letter.blade.php
│
├── routes/
│   ├── web.php                          # ⚠️ DEFINE - Web routes
│   ├── console.php                      # Artisan commands
│   └── api.php                          # ⚠️ Optional - API routes
│
├── storage/
│   ├── app/
│   │   ├── private/                     # Private files
│   │   └── public/                      # Public files (symlinked)
│   │       ├── uploads/
│   │       └── pdf/
│   ├── framework/
│   │   ├── cache/
│   │   ├── sessions/
│   │   └── views/
│   └── logs/
│       └── laravel.log
│
├── tests/
│   ├── Feature/
│   │   ├── ExampleTest.php
│   │   ├── PatientTest.php              # ⚠️ CREATE
│   │   ├── RegistrationTest.php         # ⚠️ CREATE
│   │   └── ...
│   │
│   ├── Unit/
│   │   ├── ExampleTest.php
│   │   ├── PatientModelTest.php         # ⚠️ CREATE
│   │   └── ...
│   │
│   └── TestCase.php
│
├── vendor/                              # Composer dependencies (git ignored)
│
├── node_modules/                        # NPM dependencies (git ignored)
│
├── .env                                 # ✅ Environment configuration
├── .env.example                         # Environment template
├── .gitignore                           # Git ignore file
├── artisan                              # Artisan CLI
├── composer.json                        # PHP dependencies
├── composer.lock
├── package.json                         # ✅ NPM dependencies (Tailwind configured)
├── package-lock.json
├── phpunit.xml                          # PHPUnit configuration
├── vite.config.js                       # Vite configuration
├── README.md                            # Project readme
├── SETUP.md                             # ✅ Setup documentation
└── COMMANDS.md                          # ✅ Command reference

```

## Legend:
- ✅ **Already Created/Configured**
- ⚠️ **Needs to be Created** (based on requirements)
- 📝 **Standard Laravel** (no changes needed)

## Next Steps to Complete:

### 1. Create Service Layer
```powershell
# Manually create these files in app/Services/
New-Item -Path "app/Services" -ItemType Directory
# Then create service classes
```

### 2. Create Form Requests
```powershell
php artisan make:request StorePatientRequest
php artisan make:request UpdatePatientRequest
php artisan make:request StoreOutpatientRequest
php artisan make:request StoreInpatientRequest
```

### 3. Create Controllers
```powershell
php artisan make:controller PatientController --resource
php artisan make:controller OutpatientController
php artisan make:controller InpatientController
php artisan make:controller Admin/DoctorController --resource
php artisan make:controller Admin/PolyclinicController --resource
php artisan make:controller Admin/WardController --resource
php artisan make:controller Admin/BedController --resource
php artisan make:controller ReportController
php artisan make:controller PrintController
```

### 4. Create Views
Manually create Blade files in `resources/views/` following the structure above.

### 5. Define Routes
Edit `routes/web.php` to define all routes with proper middleware.

### 6. Create Policies (Optional but Recommended)
```powershell
php artisan make:policy PatientPolicy --model=Patient
php artisan make:policy RegistrationPolicy --model=Registration
```

### 7. Install PDF Package
```powershell
composer require barryvdh/laravel-dompdf
```

### 8. Create Tests
```powershell
php artisan make:test PatientTest
php artisan make:test RegistrationTest
php artisan make:test PatientModelTest --unit
```

## Key Directories Explained:

### app/Services/
Business logic layer. Controllers call services, services use models.
Example: `RegistrationService` handles complex registration logic with transactions.

### app/Http/Requests/
Form validation classes. Each form should have its own request class.

### app/Policies/
Authorization logic. Define who can view/create/update/delete resources.

### resources/views/
All Blade templates. Organized by feature.
- `layouts/` - Base layouts
- `components/` - Reusable UI components
- Feature folders - Views for each module

### storage/app/public/
Files accessible via web (after `php artisan storage:link`)

### tests/
- `Feature/` - Test entire features (HTTP tests)
- `Unit/` - Test individual classes/methods

## Database Structure Summary:

- **users**: Authentication + roles (admin/staff)
- **patients**: Patient master data
- **doctors**: Doctor master data
- **polyclinics**: Outpatient clinics
- **wards**: Inpatient wards
- **beds**: Individual beds in wards
- **registrations**: Universal registration (outpatient + inpatient)

## Important Files Already Configured:

1. **.env** - Database: simrs-admisi (MySQL)
2. **app/Models/** - All 7 models with relationships
3. **database/migrations/** - All 7 tables
4. **database/seeders/** - Initial data (users, doctors, polyclinics, wards, beds)
5. **app/Http/Middleware/** - Role-based access control
6. **bootstrap/app.php** - Middleware aliases registered
7. **resources/css/app.css** - Tailwind with hospital theme
8. **package.json** - Tailwind 4 configured

## Current Status:

✅ **Complete:**
- Database schema & migrations
- All models with relationships
- Seeders with test data
- Middleware for role-based access
- Tailwind CSS configuration
- Basic dashboard example
- Documentation (SETUP.md, COMMANDS.md)

⚠️ **To Do:**
- Authentication views (can use Laravel Breeze)
- Feature controllers & services
- All CRUD views for each module
- Route definitions
- PDF generation setup
- Testing suite
