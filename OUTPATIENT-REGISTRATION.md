# Outpatient Registration Module Documentation

## Overview

Complete outpatient registration system with patient search, polyclinic/doctor selection, queue number generation, database transactions, and PDF receipt printing.

---

## Features Implemented

### 1. Patient Search & Selection
**AJAX-based patient search** integrated into registration form.

**How it works:**
1. Type patient name or medical record number
2. Live search results appear (300ms debounce)
3. Click to select patient
4. Patient details displayed with option to change

**Benefits:**
- No need to remember exact MR number
- Quick patient lookup
- Prevents duplicate registrations
- Shows patient basic info for verification

---

### 2. Dynamic Doctor Selection
**Doctors filtered by polyclinic availability.**

**Flow:**
1. Select polyclinic
2. System loads only available doctors for that polyclinic
3. Shows doctor name and specialization
4. Only doctors with `availability = 'available'` and `is_active = true`

**AJAX Endpoint:** `GET /outpatient/doctors?polyclinic_id={id}`

---

### 3. Queue Number Generation

**Service:** `App\Services\QueueService`

**Format:** `OP-YYYYMMDD-POLYCODE-XXX`
- Prefix: `OP-` (Outpatient)
- Date: YYYYMMDD (e.g., 20260112)
- Polyclinic Code: From polyclinics table
- Sequence: 3-digit number (001-999)

**Examples:**
- `OP-20260112-UMUM-001` (First outpatient at General polyclinic today)
- `OP-20260112-GIGI-015` (15th outpatient at Dental polyclinic today)

**Key Features:**
- Thread-safe generation using DB transaction + lockForUpdate
- Resets daily per polyclinic (each polyclinic has independent queue)
- Prevents duplicate queue numbers
- Handles concurrent registrations

**Methods:**
```php
generateQueueNumber(string $polyclinicCode): string
isValidFormat(string $queueNumber): bool
exists(string $queueNumber): bool
getCurrentPosition(string $polyclinicCode): int
```

---

### 4. Registration Number Generation

**Format:** `REG-YYYYMMDD-XXXX`
- Prefix: `REG-`
- Date: YYYYMMDD
- Sequence: 4-digit number (0001-9999)

**Example:** `REG-20260112-0001`

**Features:**
- Unique for all registrations (both outpatient and inpatient)
- Thread-safe with lockForUpdate
- Resets daily
- Generated in controller transaction

---

### 5. Database Transaction

**All registration operations wrapped in DB transaction:**
```php
DB::transaction(function () {
    1. Get polyclinic data
    2. Generate queue number (with lock)
    3. Generate registration number (with lock)
    4. Create registration record
    5. Return registration object
});
```

**Benefits:**
- Atomic operations (all or nothing)
- Prevents race conditions
- Maintains data integrity
- Automatic rollback on error

---

### 6. PDF Receipt Generation

**Library:** `barryvdh/laravel-dompdf`

**Receipt Contents:**
- Hospital header with logo area
- Registration number and date
- **Large queue number display** (for easy viewing)
- Patient information (MR, name, NIK, age, gender, phone)
- Registration details (polyclinic, doctor, payment method)
- Chief complaint
- Important notes for patient
- Registration officer signature
- Print timestamp

**Access:** Click "Print Receipt" button or use direct route

**Route:** `GET /outpatient/{registration}/print`

**Download:** PDF automatically downloads with filename: `receipt-{registration_number}.pdf`

---

## File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── OutpatientRegistrationController.php
│   └── Requests/
│       └── StoreOutpatientRegistrationRequest.php
└── Services/
    ├── QueueService.php
    └── MedicalRecordService.php (existing)

resources/views/
├── outpatient/
│   ├── index.blade.php      (list registrations)
│   ├── create.blade.php     (registration form)
│   ├── show.blade.php       (registration details)
│   └── receipt.blade.php    (PDF template)
└── layouts/
    └── app.blade.php        (updated navigation)

routes/
└── web.php (updated with outpatient routes)
```

---

## Controller

**File:** `app/Http/Controllers/OutpatientRegistrationController.php`

**Dependency Injection:** `QueueService` via constructor

**Methods:**

1. **index(Request $request)**
   - List all outpatient registrations
   - Search by registration number, queue number, patient name/MR
   - Filter by status (active, completed, cancelled)
   - Filter by date
   - Pagination (15 per page)
   - Eager loads: patient, doctor, polyclinic

2. **create()**
   - Show registration form
   - Load active polyclinics
   - Returns view with polyclinics

3. **store(StoreOutpatientRegistrationRequest $request)**
   - Validate request data
   - Start DB transaction
   - Generate queue number
   - Generate registration number
   - Create registration record
   - Redirect to show page with success message
   - Automatic rollback on error

4. **show(Registration $registration)**
   - Display registration details
   - Eager loads relationships
   - Shows large queue number
   - Print receipt button

5. **print(Registration $registration)**
   - Generate PDF receipt
   - Load receipt view
   - Download PDF with custom filename

6. **getDoctors(Request $request)** [AJAX]
   - Get available doctors for polyclinic
   - Filter by: polyclinic_id, is_active, availability
   - Returns JSON array

---

## Validation Rules

**File:** `app/Http/Requests/StoreOutpatientRegistrationRequest.php`

```php
'patient_id' => 'required|exists:patients,id'
'polyclinic_id' => 'required|exists:polyclinics,id'
'doctor_id' => 'required|exists:doctors,id'
'complaint' => 'required|string|max:500'
'payment_method' => 'required|in:cash,bpjs,insurance,company'
```

**Custom Messages:**
- Patient required: "Please select a patient."
- Doctor required: "Please select a doctor."
- Complaint max: "The chief complaint must not exceed 500 characters."
- Payment method invalid: "The selected payment method is invalid."

---

## Routes

```php
// Resource routes (only: index, create, store, show)
Route::resource('outpatient', OutpatientRegistrationController::class)
    ->only(['index', 'create', 'store', 'show']);

// Custom routes
Route::get('/outpatient/{registration}/print', [OutpatientRegistrationController::class, 'print'])
    ->name('outpatient.print');
    
Route::get('/outpatient/doctors', [OutpatientRegistrationController::class, 'getDoctors'])
    ->name('outpatient.doctors');
```

**Generated Routes:**
- `GET /outpatient` - outpatient.index
- `GET /outpatient/create` - outpatient.create
- `POST /outpatient` - outpatient.store
- `GET /outpatient/{registration}` - outpatient.show
- `GET /outpatient/{registration}/print` - outpatient.print
- `GET /outpatient/doctors` - outpatient.doctors (AJAX)

**Access:** All routes protected by `auth` middleware (admin + staff)

---

## Registration Flow

### Step-by-Step Process

**1. Navigate to Create Page**
- Click "Outpatient" in navigation
- Click "New Registration" button
- Form loads with active polyclinics

**2. Search & Select Patient**
- Type in patient search box (minimum 2 characters)
- Wait for results (300ms debounce)
- Click desired patient from dropdown
- Patient info displayed in blue box
- Hidden input stores patient_id

**3. Select Polyclinic**
- Choose from dropdown of active polyclinics
- Doctor dropdown activates
- AJAX request loads available doctors

**4. Select Doctor**
- Choose from filtered doctors (only available)
- Shows doctor name and specialization
- Disabled until polyclinic selected

**5. Enter Chief Complaint**
- Describe patient's main complaint
- Maximum 500 characters
- Required field

**6. Select Payment Method**
- Cash, BPJS, Insurance, or Company
- Required selection

**7. Submit Registration**
- Click "Create Registration" button
- Server validates all fields
- DB transaction begins:
  - Generate queue number
  - Generate registration number
  - Save registration
  - Commit transaction
- Redirect to registration detail page
- Success message shows queue number

**8. View Registration Details**
- Large queue number displayed prominently
- All patient and registration information
- Print receipt button

**9. Print Receipt (Optional)**
- Click "Print Receipt" button
- PDF generates and downloads
- Contains all registration details
- Formatted for printing

---

## UI/UX Features

### Color Coding
- **Queue Numbers:** Blue monospace badge
- **Registration Numbers:** Gray monospace badge
- **Status Badges:**
  - Active: Green (bg-green-100, text-green-800)
  - Completed: Blue (bg-blue-100, text-blue-800)
  - Cancelled: Gray (bg-gray-100, text-gray-800)

### Form Validation
- Server-side via FormRequest
- Client-side HTML5 (required, maxlength)
- Error messages below fields
- Red border on error fields
- Old input preservation on error

### Patient Search UX
- Debounced input (reduces server load)
- Loading indicator during search
- "No patients found" message
- Click outside to hide results
- Clear/change patient option

### Doctor Selection UX
- Disabled until polyclinic selected
- Loading state: "Loading doctors..."
- Empty state: "No available doctors"
- Shows doctor info (name + specialization)

### Empty States
- Icon with friendly message
- Consistent design across all lists
- Call-to-action buttons

### Responsive Design
- Mobile-first approach
- Grid layouts for forms
- Overflow scrolling for tables
- Full-width on small screens

---

## Database Schema

### registrations table (used)
```sql
id                    - bigint primary key
registration_number   - varchar(50) unique
registration_type     - enum('outpatient', 'inpatient')
registration_date     - datetime
patient_id            - foreign key to patients
doctor_id             - foreign key to doctors
polyclinic_id         - foreign key to polyclinics (outpatient)
ward_id               - foreign key to wards (inpatient, nullable)
bed_id                - foreign key to beds (inpatient, nullable)
queue_number          - varchar(50) nullable
complaint             - text
payment_method        - enum('cash', 'bpjs', 'insurance', 'company')
status                - enum('active', 'completed', 'cancelled')
notes                 - text nullable
created_at, updated_at
```

### Relationships
```php
// Registration model
public function patient()
public function doctor()
public function polyclinic()
```

---

## Service Layer

### QueueService

**Purpose:** Generate unique queue numbers per polyclinic per day

**Implementation:**
```php
public function generateQueueNumber(string $polyclinicCode): string
{
    return DB::transaction(function () use ($polyclinicCode) {
        $today = now()->format('Ymd');
        $prefix = "OP-{$today}-{$polyclinicCode}";
        
        // Get last queue with lock
        $lastRegistration = Registration::where('registration_type', 'outpatient')
            ->where('queue_number', 'like', "{$prefix}-%")
            ->whereDate('registration_date', now())
            ->lockForUpdate()
            ->orderBy('queue_number', 'desc')
            ->first();
        
        // Increment or start at 1
        $newSequence = $lastRegistration 
            ? ((int) explode('-', $lastRegistration->queue_number)[3]) + 1 
            : 1;
        
        // Pad to 3 digits
        $sequence = str_pad($newSequence, 3, '0', STR_PAD_LEFT);
        
        return "{$prefix}-{$sequence}";
    });
}
```

**Validation:**
```php
public function isValidFormat(string $queueNumber): bool
{
    // Pattern: OP-YYYYMMDD-POLYCODE-XXX
    return preg_match('/^OP-\d{8}-[A-Z0-9]+-\d{3}$/', $queueNumber) === 1;
}
```

**Usage Example:**
```php
$queueService = app(QueueService::class);
$queueNumber = $queueService->generateQueueNumber('UMUM');
// Returns: OP-20260112-UMUM-001
```

---

## AJAX Implementation

### Patient Search

**Frontend (JavaScript in create.blade.php):**
```javascript
let searchTimeout;
patientSearch.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const query = this.value.trim();
    
    if (query.length < 2) {
        patientResults.classList.add('hidden');
        return;
    }
    
    searchTimeout = setTimeout(() => {
        fetch(`/patients-search?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => displayPatientResults(data));
    }, 300);
});
```

**Backend (PatientController):**
```php
public function search(Request $request)
{
    $search = $request->input('q');
    
    $patients = Patient::search($search)
        ->limit(10)
        ->get(['id', 'medical_record_number', 'nik', 'name', 'birth_date', 'gender', 'phone']);
    
    return response()->json($patients);
}
```

### Doctor Selection

**Frontend:**
```javascript
polyclinicSelect.addEventListener('change', function() {
    const polyclinicId = this.value;
    
    fetch(`/outpatient/doctors?polyclinic_id=${polyclinicId}`)
        .then(response => response.json())
        .then(doctors => {
            let html = '<option value="">Select Doctor</option>';
            doctors.forEach(doctor => {
                html += `<option value="${doctor.id}">${doctor.name} - ${doctor.specialization}</option>`;
            });
            doctorSelect.innerHTML = html;
            doctorSelect.disabled = false;
        });
});
```

**Backend:**
```php
public function getDoctors(Request $request)
{
    $doctors = Doctor::where('polyclinic_id', $request->polyclinic_id)
        ->where('is_active', true)
        ->where('availability', 'available')
        ->orderBy('name')
        ->get(['id', 'name', 'specialization']);
    
    return response()->json($doctors);
}
```

---

## Testing Checklist

### Queue Number Generation
- [ ] Generate first queue for polyclinic today
- [ ] Generate second queue (should increment)
- [ ] Generate queue for different polyclinic (independent counter)
- [ ] Test on different day (should reset to 001)
- [ ] Test concurrent registrations (no duplicates)
- [ ] Validate format with isValidFormat()

### Patient Search
- [ ] Type 1 character (no search)
- [ ] Type 2+ characters (search triggers)
- [ ] Search by patient name
- [ ] Search by MR number
- [ ] Select patient from results
- [ ] Clear selected patient
- [ ] Click outside to hide results

### Doctor Selection
- [ ] Select polyclinic
- [ ] Verify doctors load via AJAX
- [ ] Verify only available doctors shown
- [ ] Change polyclinic (doctors update)
- [ ] Submit without selecting doctor (validation error)

### Registration Creation
- [ ] Submit without patient (validation error)
- [ ] Submit without polyclinic (validation error)
- [ ] Submit without doctor (validation error)
- [ ] Submit without complaint (validation error)
- [ ] Submit with 501 character complaint (validation error)
- [ ] Submit without payment method (validation error)
- [ ] Submit valid registration (success)
- [ ] Verify queue number generated
- [ ] Verify registration number generated
- [ ] Verify redirect to show page

### Registration List
- [ ] View all registrations
- [ ] Search by registration number
- [ ] Search by queue number
- [ ] Search by patient name
- [ ] Filter by status (active, completed, cancelled)
- [ ] Filter by date
- [ ] Test pagination
- [ ] Clear filters

### PDF Receipt
- [ ] Click print button
- [ ] Verify PDF downloads
- [ ] Check filename format
- [ ] Verify all data in PDF
- [ ] Check queue number prominence
- [ ] Verify layout and formatting

---

## Security Considerations

1. **Authentication:** All routes protected by `auth` middleware
2. **Authorization:** Both admin and staff can create registrations
3. **CSRF Protection:** All forms include @csrf token
4. **SQL Injection:** Eloquent ORM with parameterized queries
5. **XSS Prevention:** Blade automatic escaping
6. **Mass Assignment:** Fillable attributes in models
7. **Validation:** Server-side via FormRequest
8. **Transaction Safety:** DB::transaction prevents partial saves
9. **Input Sanitization:** Laravel validation handles this
10. **File Security:** PDF generated in memory, not stored

---

## Common Issues & Solutions

### Issue: Duplicate queue numbers
**Cause:** Concurrent requests without proper locking
**Solution:** QueueService uses DB transaction with lockForUpdate()

### Issue: Doctor dropdown not loading
**Check:**
1. Polyclinic selected?
2. AJAX route registered: `outpatient.doctors`
3. JavaScript console for errors
4. Network tab shows request/response
5. Doctors have `is_active = true` and `availability = 'available'`

### Issue: PDF not generating
**Cause:** dompdf package not installed
**Solution:** Run `composer require barryvdh/laravel-dompdf`

### Issue: Patient search not working
**Check:**
1. Route registered: `patients.search`
2. Patient model has `search()` scope
3. JavaScript debounce working
4. Network requests in browser tools

### Issue: Queue number format invalid
**Cause:** Polyclinic code contains invalid characters
**Solution:** Ensure polyclinic codes are uppercase alphanumeric only

---

## Performance Optimization

### Database Indexes
Recommended indexes for optimal performance:
```sql
-- registrations table
INDEX idx_registration_type (registration_type)
INDEX idx_registration_date (registration_date)
INDEX idx_queue_number (queue_number)
INDEX idx_status (status)

-- Composite index for queue generation
INDEX idx_queue_search (registration_type, registration_date, queue_number)
```

### Query Optimization
- Use `select()` to limit columns
- Eager loading with `with()` to avoid N+1
- `limit()` on search results
- Date indexes for daily queries

### AJAX Optimization
- Debounce search (300ms)
- Minimum 2 characters before search
- Limit results to 10
- Cache polyclinic data

---

## Future Enhancements

1. **Real-time Queue Display:**
   - Large screen showing current queue
   - Auto-refresh every 30 seconds
   - Voice announcement integration

2. **SMS Notifications:**
   - Send queue number to patient phone
   - Reminder when queue is near
   - Status updates (completed, cancelled)

3. **Queue Management:**
   - Call next patient button
   - Skip/hold patient
   - Queue history tracking
   - Average wait time calculation

4. **Appointment System:**
   - Schedule future visits
   - Recurring appointments
   - Appointment reminders
   - Calendar view

5. **Analytics & Reports:**
   - Daily registration count by polyclinic
   - Peak hours analysis
   - Average wait time per polyclinic
   - Doctor workload report
   - Patient visit frequency

6. **Barcode/QR Code:**
   - Print QR code on receipt
   - Scan for check-in
   - Track patient movement
   - Automatic status updates

7. **Multi-language Support:**
   - Indonesian and English
   - Language switcher
   - Translated receipts

8. **Online Registration:**
   - Patient portal
   - Web-based registration
   - Mobile app integration

---

## API Documentation (Future)

While currently web-only, potential API endpoints:

### Create Registration
```http
POST /api/outpatient
Authorization: Bearer {token}
Content-Type: application/json

{
  "patient_id": 1,
  "polyclinic_id": 2,
  "doctor_id": 3,
  "complaint": "Headache for 3 days",
  "payment_method": "bpjs"
}

Response:
{
  "success": true,
  "data": {
    "id": 123,
    "registration_number": "REG-20260112-0001",
    "queue_number": "OP-20260112-UMUM-001",
    "status": "active"
  }
}
```

### Get Available Doctors
```http
GET /api/polyclinics/{id}/doctors
Authorization: Bearer {token}

Response:
{
  "data": [
    {
      "id": 1,
      "name": "Dr. John Doe",
      "specialization": "General Practitioner",
      "availability": "available"
    }
  ]
}
```

---

Last Updated: 2026-01-12
