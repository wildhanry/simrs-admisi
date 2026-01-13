# Patient Management Documentation

## Overview

Complete patient management system with AJAX search, auto-generated medical record numbers, and full CRUD operations accessible by both admin and staff roles.

## Features Implemented

### 1. AJAX Patient Search
**Real-time search** by patient name or medical record number without page reload.

**Implementation:**
- Debounced input (300ms) for performance
- Minimum 2 characters to trigger search
- Returns top 10 matches
- Displays: MR Number, NIK, Name, Age, Gender, Phone
- Click result to view patient details
- Auto-hide on outside click

**Endpoint:** `GET /patients-search?q={query}`

**JavaScript:** Vanilla JS (no jQuery dependency)

**Response Format:**
```json
[
  {
    "id": 1,
    "medical_record_number": "RM-20260112-0001",
    "nik": "3174012505900001",
    "name": "John Doe",
    "birth_date": "1990-05-25",
    "gender": "male",
    "phone": "081234567890"
  }
]
```

---

### 2. Auto-Generated Medical Record Number

**Service:** `App\Services\MedicalRecordService`

**Format:** `RM-YYYYMMDD-XXXX`
- Prefix: `RM-`
- Date: Current date (YYYYMMDD)
- Sequence: 4-digit sequential number (0001-9999)

**Example:** `RM-20260112-0001`

**Features:**
- Database transaction for thread safety
- Row locking (`lockForUpdate()`) to prevent duplicates
- Auto-resets daily (starts from 0001 each day)
- Validation method for format checking
- Existence checker

**Methods:**
```php
generateMedicalRecordNumber(): string
isValidFormat(string $mrn): bool
exists(string $mrn): bool
```

---

### 3. Patient CRUD Operations

#### Create Patient
**Route:** `GET /patients/create`

**Features:**
- Pre-generated MR number displayed
- Hidden field submits MR number
- NIK validation (16 digits)
- Birth date must be before today
- Emergency contact fields (optional)
- All fields validated server-side

**Required Fields:**
- NIK (16 digits)
- Full Name
- Birth Date
- Gender
- Phone
- Address

**Optional Fields:**
- Birth Place
- Blood Type (A, B, AB, O, Unknown)
- Marital Status (Single, Married, Divorced, Widowed)
- Religion
- Occupation
- Emergency Contact Name & Phone

#### Read/View Patient
**Route:** `GET /patients/{id}`

**Displays:**
- Complete patient information
- Emergency contact (if available)
- Recent registrations (last 10)
- Edit and back buttons

#### Update Patient
**Route:** `PUT /patients/{id}`

**Features:**
- Medical Record Number is read-only
- NIK uniqueness validated (ignores current patient)
- Birth date must be before today
- Same validation as create

#### Delete Patient
**Route:** `DELETE /patients/{id}`

**Features:**
- Soft delete (deleted_at timestamp)
- Confirmation dialog required
- Redirects with success message

#### List Patients
**Route:** `GET /patients`

**Features:**
- Paginated (15 per page)
- AJAX search box at top
- Displays: MR Number, NIK, Name, Birth info, Gender, Age, Phone
- Color-coded gender badges (Blue: Male, Pink: Female)
- Actions: View, Edit, Delete
- Empty state message

---

## File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── PatientController.php
│   └── Requests/
│       ├── StorePatientRequest.php
│       └── UpdatePatientRequest.php
└── Services/
    └── MedicalRecordService.php

resources/views/
└── patients/
    ├── index.blade.php
    ├── create.blade.php
    ├── edit.blade.php
    └── show.blade.php

routes/
└── web.php (updated)
```

---

## Controller

**File:** `app/Http/Controllers/PatientController.php`

**Dependency Injection:** `MedicalRecordService` via constructor

**Methods:**
1. `index(Request $request)` - List with pagination
2. `search(Request $request)` - AJAX search endpoint
3. `create()` - Show create form with generated MRN
4. `store(StorePatientRequest $request)` - Create patient
5. `show(Patient $patient)` - View patient details
6. `edit(Patient $patient)` - Show edit form
7. `update(UpdatePatientRequest $request, Patient $patient)` - Update patient
8. `destroy(Patient $patient)` - Soft delete patient

---

## Validation Rules

### StorePatientRequest

```php
'medical_record_number' => 'nullable|string|max:50|unique:patients',
'nik' => 'required|string|size:16|unique:patients',
'name' => 'required|string|max:100',
'birth_date' => 'required|date|before:today',
'birth_place' => 'nullable|string|max:100',
'gender' => 'required|in:male,female',
'address' => 'required|string|max:255',
'phone' => 'required|string|max:20',
'blood_type' => 'nullable|in:A,B,AB,O,unknown',
'marital_status' => 'nullable|in:single,married,divorced,widowed',
'religion' => 'nullable|string|max:50',
'occupation' => 'nullable|string|max:100',
'emergency_contact_name' => 'nullable|string|max:100',
'emergency_contact_phone' => 'nullable|string|max:20',
```

### UpdatePatientRequest

Same as Store but:
- No `medical_record_number` validation (not editable)
- NIK unique validation ignores current patient

**Custom Messages:**
- `nik.size` → "NIK must be exactly 16 digits."
- `birth_date.before` → "Birth date must be before today."

---

## Routes

```php
// Authenticated routes (admin + staff)
Route::middleware(['auth'])->group(function () {
    Route::resource('patients', PatientController::class);
    Route::get('/patients-search', [PatientController::class, 'search'])
        ->name('patients.search');
});
```

**Generated Resource Routes:**
- `GET /patients` - patients.index
- `GET /patients/create` - patients.create
- `POST /patients` - patients.store
- `GET /patients/{patient}` - patients.show
- `GET /patients/{patient}/edit` - patients.edit
- `PUT /patients/{patient}` - patients.update
- `DELETE /patients/{patient}` - patients.destroy

**Custom Route:**
- `GET /patients-search` - patients.search (AJAX)

---

## Service Layer

### MedicalRecordService

**Purpose:** Generate unique medical record numbers with thread safety

**Key Features:**
1. **Transaction Wrapper:** Ensures atomicity
2. **Row Locking:** Prevents concurrent duplicates
3. **Daily Reset:** Sequence resets each day
4. **Format Validation:** Regex pattern matching
5. **Existence Checking:** Query database for duplicates

**Usage Example:**
```php
use App\Services\MedicalRecordService;

$service = app(MedicalRecordService::class);
$mrn = $service->generateMedicalRecordNumber();
// Returns: RM-20260112-0001

if ($service->isValidFormat($mrn)) {
    // Valid format
}

if (!$service->exists($mrn)) {
    // MRN doesn't exist yet
}
```

---

## AJAX Search Implementation

### Frontend (JavaScript)

**Location:** `resources/views/patients/index.blade.php`

**Key Features:**
- Debounce timer (300ms)
- Minimum 2 characters
- Fetch API for requests
- Dynamic HTML generation
- Outside click detection
- Age calculation in JS

**Flow:**
1. User types in search box
2. Wait 300ms after last keystroke
3. If ≥2 characters, send AJAX request
4. Receive JSON response
5. Build HTML dynamically
6. Display results dropdown
7. Click result → navigate to patient detail

### Backend (Controller)

**Method:** `search(Request $request)`

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

**Uses Model Scope:** `Patient::search($term)`

---

## UI/UX Features

### Color Coding
- **Gender Badges:**
  - Male: Blue (bg-blue-100, text-blue-800)
  - Female: Pink (bg-pink-100, text-pink-800)
- **MR Number:** Blue monospace badge
- **Registration Status:**
  - Active: Green
  - Completed: Blue
  - Cancelled: Gray

### Form Validation
- Server-side via FormRequest
- Client-side HTML5 validation
- Error messages below fields
- Red border on error fields
- Old input preservation

### Empty States
- Icon with message
- Friendly call-to-action
- Consistent across all lists

### Responsive Design
- Mobile-first approach
- Grid system for forms
- Stacked layout on small screens
- Full-width inputs

---

## Testing Checklist

### Medical Record Number Generation
- [ ] Generate first MRN of the day
- [ ] Generate second MRN (should increment)
- [ ] Test on different days (should reset)
- [ ] Test concurrent requests (should not duplicate)
- [ ] Validate format regex
- [ ] Check existence method

### Patient CRUD
- [ ] Access create form
- [ ] Verify MRN is pre-generated
- [ ] Submit with all required fields
- [ ] Verify NIK validation (must be 16 digits)
- [ ] Verify birth date validation (must be past)
- [ ] Test duplicate NIK (should fail)
- [ ] Edit patient and change NIK to another existing (should fail)
- [ ] Edit patient and keep same NIK (should pass)
- [ ] Delete patient (soft delete)
- [ ] View patient details page
- [ ] Check pagination on list page

### AJAX Search
- [ ] Type 1 character (should not search)
- [ ] Type 2+ characters (should search)
- [ ] Wait for debounce (300ms)
- [ ] Verify search by name works
- [ ] Verify search by MRN works
- [ ] Click result (should navigate)
- [ ] Click outside (should hide results)
- [ ] Test with no results
- [ ] Test with 10+ results (should limit to 10)

### Navigation
- [ ] Patients menu link works
- [ ] Active state highlights correct menu
- [ ] Accessible by both admin and staff
- [ ] Back buttons work correctly

---

## Database Considerations

### Indexes
The Patient model uses these indexes (from migration):
- `medical_record_number` - Unique index
- `nik` - Unique index
- `name` - Regular index for search
- `birth_date` - Regular index

### Soft Deletes
Patients use `SoftDeletes` trait:
- `deleted_at` column nullable timestamp
- Deleted records excluded from queries automatically
- Can be restored if needed

### Search Performance
The `search()` scope uses LIKE queries:
```sql
WHERE medical_record_number LIKE '%term%' 
   OR nik LIKE '%term%' 
   OR name LIKE '%term%'
```

For better performance with large datasets:
- Consider full-text search indexes
- Implement Laravel Scout with Meilisearch/Algolia
- Add composite indexes on frequently searched columns

---

## Security Considerations

1. **CSRF Protection:** All forms include @csrf token
2. **Authorization:** Routes protected by auth middleware
3. **SQL Injection:** Eloquent ORM parameterized queries
4. **XSS Prevention:** Blade automatic escaping
5. **Mass Assignment:** Fillable attributes defined in model
6. **Validation:** Server-side via FormRequests
7. **Transaction Safety:** MRN generation in DB transaction

---

## Common Issues & Solutions

### Issue: Duplicate Medical Record Numbers
**Cause:** Concurrent requests without transaction
**Solution:** MedicalRecordService uses DB transaction with row locking

### Issue: AJAX search not working
**Check:**
1. Route registered: `patients.search`
2. CSRF token in meta tag
3. JavaScript console for errors
4. Network tab for request/response

### Issue: NIK validation failing
**Cause:** NIK must be exactly 16 digits
**Solution:** Use `size:16` validation, not `max:16`

### Issue: Birth date validation failing
**Cause:** Date is today or future
**Solution:** Use `before:today` validation rule

---

## Future Enhancements

1. **Advanced Search:**
   - Filter by age range
   - Filter by gender
   - Filter by registration date
   - Export to Excel/PDF

2. **QR Code:**
   - Generate QR code for MR number
   - Print patient card with QR
   - Scan QR for quick lookup

3. **Medical History:**
   - Allergies tracking
   - Chronic diseases
   - Previous surgeries
   - Family medical history

4. **Photo Upload:**
   - Patient photo for identification
   - Image storage (local/S3)
   - Thumbnail generation

5. **Insurance Integration:**
   - BPJS number tracking
   - Insurance provider selection
   - Coverage verification

6. **Duplicate Detection:**
   - Smart matching on create
   - Warn if similar patient exists
   - Merge duplicate records

7. **Audit Trail:**
   - Track who created/updated
   - Change history
   - Activity log

---

## API Usage (if implementing)

While currently web-only, here's how an API might look:

### Search Patients
```http
GET /api/patients/search?q=john
Authorization: Bearer {token}
Accept: application/json
```

### Create Patient
```http
POST /api/patients
Authorization: Bearer {token}
Content-Type: application/json

{
  "nik": "3174012505900001",
  "name": "John Doe",
  "birth_date": "1990-05-25",
  "gender": "male",
  "phone": "081234567890",
  "address": "Jakarta"
}
```

---

Last Updated: 2026-01-12
