# SIMRS Admisi - Hospital Admission System

Laravel 12 based Hospital Management System for Outpatient and Inpatient Registration

## Tech Stack
- **Framework**: Laravel 12.46.0
- **Database**: MySQL (simrs-admisi)
- **Frontend**: Tailwind CSS 4 via Vite
- **Authentication**: Laravel built-in auth
- **Roles**: Admin, Staff

## Installation & Setup

### Prerequisites
- PHP 8.2+
- Composer
- MySQL
- Node.js & NPM

### Step 1: Create Database
```sql
CREATE DATABASE simrs_admisi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Or if using Laragon, the database `simrs-admisi` will auto-create when accessed.

### Step 2: Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### Step 3: Environment Configuration
The `.env` file is already configured with:
- App name: "SIMRS Admisi"
- Database: simrs-admisi (MySQL)
- Default credentials ready

### Step 4: Generate Application Key (if needed)
```bash
php artisan key:generate
```

### Step 5: Run Migrations
```bash
php artisan migrate
```

This will create the following tables:
- `users` (with role: admin/staff)
- `patients`
- `doctors`
- `polyclinics`
- `wards`
- `beds`
- `registrations`

### Step 6: Seed Database
```bash
php artisan db:seed
```

This creates:
- **Admin user**: admin@simrs.local / password
- **Staff user**: staff@simrs.local / password
- 5 sample doctors
- 5 polyclinics
- 4 wards with beds (VIP: 5, Class I: 10, Class II: 15, Class III: 20)

### Step 7: Build Frontend Assets
```bash
npm run build
```

For development with hot reload:
```bash
npm run dev
```

### Step 8: Start Server
```bash
php artisan serve
```

Visit: http://localhost:8000

## Project Structure

### Database Schema

#### Users
- Basic auth + `role` (admin/staff) + `is_active`

#### Patients
- Medical record number (auto-generated)
- NIK, demographics, contact info
- Emergency contact

#### Doctors
- SIP number (license)
- Specialization
- Contact info

#### Polyclinics
- Code, name, location
- For outpatient registration

#### Wards & Beds
- Ward: code, name, class (VIP/I/II/III)
- Bed: number, status (available/occupied/maintenance)
- Relationship: 1 ward has many beds

#### Registrations
- Universal registration for both outpatient & inpatient
- Type: `outpatient` or `inpatient`
- Outpatient: uses `polyclinic_id`
- Inpatient: uses `bed_id`, `admission_date`, `discharge_date`
- Status: waiting, in_progress, completed, cancelled
- Auto-generated registration number (RJ-YYYYMMDD-0001 / RI-YYYYMMDD-0001)

### Models with Relationships

All models in `app/Models/`:
- `User` - isAdmin(), isStaff()
- `Patient` - age attribute, search scope
- `Doctor` - active scope, search scope
- `Polyclinic` - active scope
- `Ward` - beds relationship, available beds count
- `Bed` - ward relationship, status management, race condition prevention
- `Registration` - all relationships, type scopes, number generation

### Middleware

- `admin` - Ensures user is admin
- `role:admin,staff` - Ensures user has specific role(s)

Usage in routes:
```php
Route::middleware(['auth', 'admin'])->group(function () {
    // Admin-only routes
});

Route::middleware(['auth', 'role:admin,staff'])->group(function () {
    // Routes accessible by admin or staff
});
```

### Services Layer (Recommended Structure)

Create services in `app/Services/`:
- `PatientService` - Patient CRUD & medical record generation
- `RegistrationService` - Registration logic with transaction
- `BedManagementService` - Bed allocation with race condition prevention
- `ReportService` - Generate reports & statistics

### Form Requests (Recommended)

Create form requests in `app/Http/Requests/`:
- `StorePatientRequest`
- `UpdatePatientRequest`
- `StoreRegistrationRequest`
- etc.

## Key Features to Implement

### 1. Authentication Module
- Login/Logout
- Password reset
- Role-based dashboard redirect

### 2. Master Data Management (Admin Only)
- Doctors CRUD
- Polyclinics CRUD
- Wards CRUD
- Beds CRUD
- Users CRUD

### 3. Patient Management
- Patient registration (auto MR number)
- Patient search
- Patient history
- Update patient data

### 4. Outpatient Registration
- Select patient (or register new)
- Select polyclinic
- Select doctor
- Queue number generation
- Print registration slip

### 5. Inpatient Registration
- Select patient
- Select ward & bed (with availability check)
- Select doctor
- Admission date/time
- Prevent race condition for bed booking using transactions:

```php
DB::transaction(function () use ($bedId) {
    $bed = Bed::where('id', $bedId)
        ->where('status', 'available')
        ->lockForUpdate()
        ->firstOrFail();
    
    $bed->markAsOccupied();
    
    // Create registration
});
```

### 6. Printing (PDF)
- Registration slip
- Patient card
- Admission letter

Use packages like:
- `barryvdh/laravel-dompdf`
- `tecnickcom/tcpdf`

### 7. Reports
- Daily registrations
- Patient statistics
- Bed occupancy
- Doctor workload
- Revenue (if applicable)

## Coding Standards

### Controllers
- Thin controllers, delegate to services
- Use FormRequest for validation
- Return views or JSON responses

### Services
- Business logic here
- Use transactions for critical operations
- Handle exceptions properly

### Models
- Relationships
- Scopes
- Accessors/Mutators
- Minimal business logic

### Blade Views
- Use components for reusability
- Layouts: guest, authenticated, admin
- Partials: header, sidebar, footer

## Security Considerations

1. **Input Validation**: Use FormRequest
2. **SQL Injection**: Use Eloquent/Query Builder (parameterized)
3. **XSS**: Blade auto-escapes `{{ }}`
4. **CSRF**: `@csrf` in forms
5. **Authorization**: Middleware + Policies
6. **Race Conditions**: DB transactions with `lockForUpdate()`

## Testing

```bash
# Run tests
php artisan test

# Run with coverage
php artisan test --coverage
```

Create tests in `tests/Feature/` and `tests/Unit/`

## Deployment Checklist

1. Set `APP_ENV=production`
2. Set `APP_DEBUG=false`
3. Configure production database
4. Run `php artisan config:cache`
5. Run `php artisan route:cache`
6. Run `php artisan view:cache`
7. Run `composer install --optimize-autoloader --no-dev`
8. Run `npm run build`
9. Set proper file permissions
10. Configure web server (Nginx/Apache)

## Default Credentials

**Admin:**
- Email: admin@simrs.local
- Password: password

**Staff:**
- Email: staff@simrs.local
- Password: password

**⚠️ Change these credentials in production!**

## Support

For issues and questions, refer to:
- Laravel Documentation: https://laravel.com/docs
- Tailwind CSS: https://tailwindcss.com/docs

## License

Proprietary - Hospital Internal Use
