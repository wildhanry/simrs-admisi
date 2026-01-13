# Admin CRUD Modules Documentation

## Overview

Complete CRUD (Create, Read, Update, Delete) modules for hospital master data management accessible only to admin users.

## Modules Implemented

### 1. Doctors Management
**Route Prefix:** `/admin/doctors`

**Features:**
- List all doctors with pagination (10 per page)
- Search by name, specialization, or SIP number
- Filter by active/inactive status
- Create new doctor with specialization
- Edit doctor details
- Delete doctor (soft delete)
- Track doctor availability schedule

**Fields:**
- Name (required, max 100 chars)
- Specialization (required, max 50 chars)
- SIP Number (required, unique, max 50 chars)
- Phone (optional, max 20 chars)
- Email (optional, valid email)
- Availability (optional, text area)
- Active Status (boolean checkbox)

**Validation:**
- FormRequest: `StoreDoctorRequest`, `UpdateDoctorRequest`
- Unique SIP number validation
- Email format validation

---

### 2. Polyclinics Management
**Route Prefix:** `/admin/polyclinics`

**Features:**
- List all polyclinics with pagination
- Search by code or name
- Filter by active/inactive status
- Create new polyclinic
- Edit polyclinic
- Delete polyclinic

**Fields:**
- Code (required, unique, max 10 chars)
- Name (required, max 100 chars)
- Active Status (boolean checkbox)

**Validation:**
- FormRequest: `StorePolyclinicRequest`, `UpdatePolyclinicRequest`
- Unique code validation

---

### 3. Wards Management
**Route Prefix:** `/admin/wards`

**Features:**
- List all wards with bed count statistics
- Search by code or name
- Filter by ward class (VIP, Class 1, Class 2, Class 3)
- Create new ward
- Edit ward
- Delete ward
- Display available beds count per ward

**Fields:**
- Code (required, unique, max 10 chars)
- Name (required, max 100 chars)
- Class (required, enum: VIP|Class 1|Class 2|Class 3)
- Building (optional, max 50 chars)
- Floor (optional, integer 1-20)

**Validation:**
- FormRequest: `StoreWardRequest`, `UpdateWardRequest`
- Unique code validation
- Ward class enum validation

---

### 4. Beds Management
**Route Prefix:** `/admin/beds`

**Features:**
- List all beds with ward relationship
- Search by bed number or ward name
- Filter by ward
- Filter by status (available, occupied, maintenance, reserved)
- Create new bed
- Edit bed
- Delete bed
- Color-coded status badges

**Fields:**
- Ward (required, foreign key to wards)
- Bed Number (required, max 20 chars)
- Status (required, enum: available|occupied|maintenance|reserved)

**Validation:**
- FormRequest: `StoreBedRequest`, `UpdateBedRequest`
- Ward existence validation
- Status enum validation

**Status Badge Colors:**
- Available: Green
- Occupied: Red
- Maintenance: Yellow
- Reserved: Blue

---

### 5. Users Management
**Route Prefix:** `/admin/users`

**Features:**
- List all users with pagination
- Search by name or email
- Filter by role (admin, staff)
- Create new user
- Edit user
- Delete user (cannot delete self)
- Password management (optional on update)
- Role-based badge colors

**Fields:**
- Name (required, max 100 chars)
- Email (required, unique, valid email)
- Password (required on create, optional on update, min 8 chars)
- Role (required, enum: admin|staff)
- Active Status (boolean checkbox)

**Validation:**
- FormRequest: `StoreUserRequest`, `UpdateUserRequest`
- Unique email validation (ignores self on update)
- Password minimum 8 characters
- Role enum validation
- Password hashing (bcrypt)

**Security:**
- Cannot delete own account
- Password optional on update (preserves existing if blank)

---

## Admin Layout

**File:** `resources/views/layouts/admin.blade.php`

**Features:**
- Sidebar navigation with active state highlighting
- Top header with page title
- Flash message display (success/error)
- User profile in sidebar
- Logout button
- Responsive design
- Tailwind CSS styling

**Navigation Menu:**
- Dashboard
- Master Data:
  - Doctors
  - Polyclinics
  - Wards
  - Beds
  - Users

---

## Admin Dashboard

**Route:** `/admin/dashboard`

**Features:**
- Statistics cards showing:
  - Total Patients
  - Today's Registrations
  - Available Beds
  - Active Doctors
- Quick action links to all modules
- System information panel
- Welcome message

---

## Routes

All routes are protected with `auth` and `admin` middleware:

```php
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', ...)->name('dashboard');
    Route::resource('users', UserController::class);
    Route::resource('doctors', DoctorController::class);
    Route::resource('polyclinics', PolyclinicController::class);
    Route::resource('wards', WardController::class);
    Route::resource('beds', BedController::class);
});
```

**Resource Routes Generated:**
- `index` - GET /admin/{module}
- `create` - GET /admin/{module}/create
- `store` - POST /admin/{module}
- `show` - GET /admin/{module}/{id}
- `edit` - GET /admin/{module}/{id}/edit
- `update` - PUT/PATCH /admin/{module}/{id}
- `destroy` - DELETE /admin/{module}/{id}

---

## Controllers

All controllers extend `App\Http\Controllers\Controller` and are in `App\Http\Controllers\Admin` namespace:

- `DoctorController`
- `PolyclinicController`
- `WardController`
- `BedController`
- `UserController`

**Standard Methods:**
- `index()` - List with search/filter/pagination
- `create()` - Show create form
- `store()` - Save new record
- `edit()` - Show edit form
- `update()` - Update record
- `destroy()` - Delete record (soft delete where applicable)

---

## Form Requests

**Location:** `app/Http/Requests/`

**Naming Convention:**
- `Store{Model}Request` - For creating
- `Update{Model}Request` - For updating

**Features:**
- Authorization always returns true (handled by middleware)
- Custom validation rules per model
- Custom attribute names for error messages
- Unique validation ignores current record on update

**List of FormRequests:**
1. `StoreDoctorRequest` / `UpdateDoctorRequest`
2. `StorePolyclinicRequest` / `UpdatePolyclinicRequest`
3. `StoreWardRequest` / `UpdateWardRequest`
4. `StoreBedRequest` / `UpdateBedRequest`
5. `StoreUserRequest` / `UpdateUserRequest`

---

## Views Structure

```
resources/views/
├── layouts/
│   └── admin.blade.php
└── admin/
    ├── dashboard.blade.php
    ├── doctors/
    │   ├── index.blade.php
    │   ├── create.blade.php
    │   └── edit.blade.php
    ├── polyclinics/
    │   ├── index.blade.php
    │   ├── create.blade.php
    │   └── edit.blade.php
    ├── wards/
    │   ├── index.blade.php
    │   ├── create.blade.php
    │   └── edit.blade.php
    ├── beds/
    │   ├── index.blade.php
    │   ├── create.blade.php
    │   └── edit.blade.php
    └── users/
        ├── index.blade.php
        ├── create.blade.php
        └── edit.blade.php
```

---

## Common Features Across All Modules

### Search & Filter
- Implemented in all index pages
- Query string preservation on pagination
- Clear filters button
- Real-time form submission

### Pagination
- Default 10-15 items per page
- Query string preservation
- Laravel's default pagination styling
- Only shows when needed (`hasPages()`)

### Flash Messages
- Success messages (green)
- Error messages (red)
- Displayed at top of content area
- Auto-dismissible via session flash

### Delete Confirmation
- JavaScript confirm dialog
- Inline form submission
- CSRF protection
- Method spoofing (DELETE)

### Form Validation
- Server-side validation via FormRequests
- Error display below each field
- Red border on error fields
- Old input preservation

### UI/UX
- Tailwind CSS styling
- Responsive design
- Icon usage (SVG)
- Color-coded badges for status
- Hover effects
- Focus states
- Loading states

---

## Testing the Modules

### Prerequisites
1. Run migrations: `php artisan migrate`
2. Run seeders: `php artisan db:seed`
3. Login as admin (admin@simrs.local / password)

### Test Checklist

**For Each Module:**
- [ ] Access index page
- [ ] Verify search works
- [ ] Verify filters work
- [ ] Verify pagination works
- [ ] Click "Add {Model}" button
- [ ] Submit form with validation errors
- [ ] Submit form with valid data
- [ ] Verify success message
- [ ] Click "Edit" on a record
- [ ] Update the record
- [ ] Verify update success
- [ ] Try to delete a record
- [ ] Verify deletion success
- [ ] Test empty state message

---

## Security Considerations

1. **Middleware Protection:**
   - All routes require authentication
   - All routes require admin role
   - Guest middleware on login routes

2. **CSRF Protection:**
   - All forms include @csrf token
   - POST/PUT/DELETE methods protected

3. **Input Validation:**
   - Server-side validation via FormRequests
   - SQL injection prevention (Eloquent ORM)
   - XSS prevention (Blade escaping)

4. **Password Security:**
   - Bcrypt hashing
   - Minimum 8 characters
   - Password optional on update

5. **Authorization:**
   - Cannot delete own user account
   - Admin-only access to all modules

---

## Future Enhancements

1. **Bulk Actions:**
   - Bulk delete
   - Bulk status update
   - Export to Excel/PDF

2. **Advanced Filtering:**
   - Date range filters
   - Multiple status selection
   - Advanced search with OR/AND conditions

3. **Audit Trail:**
   - Track who created/updated records
   - Activity log
   - Change history

4. **Import/Export:**
   - CSV import for bulk data
   - Excel export
   - PDF reports

5. **Relationships Display:**
   - Show related records count
   - Quick links to related data
   - Cascade delete warnings

---

## Troubleshooting

### Common Issues

**1. 403 Forbidden Error:**
- Verify user has admin role
- Check middleware configuration
- Verify user is logged in

**2. Validation Errors Not Showing:**
- Verify @error directives in view
- Check FormRequest authorize() returns true
- Verify old() helper used for input values

**3. Pagination Not Working:**
- Verify ->withQueryString() on paginator
- Check query parameters in URL
- Verify pagination links in view

**4. Foreign Key Constraint Errors:**
- Create parent records first (wards before beds)
- Check cascade delete configuration
- Verify relationship existence validation

**5. Search Not Working:**
- Verify model has search scope
- Check search parameter name in controller
- Verify LIKE query syntax

---

## API Documentation

Not implemented yet. All CRUD operations are currently web-based only.

---

## Performance Considerations

1. **Database Queries:**
   - Eager loading relationships (`with()`)
   - Pagination to limit result sets
   - Indexed columns for search fields

2. **Caching:**
   - Not implemented (future enhancement)
   - Consider caching dropdown data
   - Cache statistics on dashboard

3. **Asset Loading:**
   - Vite for asset bundling
   - Tailwind CSS purging in production
   - SVG icons (no external requests)

---

Last Updated: {{ now()->format('Y-m-d') }}
