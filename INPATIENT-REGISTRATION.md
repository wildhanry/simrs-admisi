# Inpatient Registration Module Documentation

## Overview

Complete inpatient registration system with **pessimistic locking** to prevent bed double booking, database transactions for data integrity, and PDF admission letter generation.

---

## Critical Feature: Preventing Double Booking

### The Problem
**Scenario:** Two staff members try to book the same bed simultaneously:
```
Time    Staff A                    Staff B
10:00   Checks bed 101 (available) 
10:00                              Checks bed 101 (available)
10:01   Books bed 101 ✓
10:01                              Books bed 101 ✗ (CONFLICT!)
```

### The Solution: Pessimistic Locking

**Implementation:**
```php
DB::transaction(function () use ($bedId) {
    // Step 1: Lock the row with SELECT FOR UPDATE
    $bed = Bed::where('id', $bedId)
        ->lockForUpdate()  // ← CRITICAL: Locks the row
        ->first();
    
    // Step 2: Validate status (still available?)
    if ($bed->status !== 'available') {
        throw new Exception('Bed no longer available');
    }
    
    // Step 3: Update to occupied
    $bed->update(['status' => 'occupied']);
    
    // Step 4: Create registration
    Registration::create([...]);
    
    // Transaction commits, lock releases
});
```

**How It Works:**
1. **lockForUpdate()** acquires a database row lock
2. Other transactions trying to lock the same row must **wait**
3. First transaction completes → lock releases
4. Second transaction sees bed is occupied → fails gracefully
5. **No double booking possible**

---

## Architecture

### Service Layer: BedAllocationService

**Purpose:** Centralize bed allocation logic with locking

**Location:** `app/Services/BedAllocationService.php`

**Key Methods:**

1. **allocateBed(int $bedId): Bed**
   - Acquires pessimistic lock on bed row
   - Validates bed is available
   - Updates status to 'occupied'
   - Sets occupied_at timestamp
   - Returns bed object
   - **Thread-safe:** Prevents concurrent allocation

2. **releaseBed(int $bedId): Bed**
   - Acquires lock on bed row
   - Updates status to 'available'
   - Clears occupied_at timestamp
   - Returns bed object

3. **getAvailableBeds()**
   - Lists all available beds with ward info
   - No locking (read-only)
   - Ordered by ward and bed number

4. **getAvailabilityByWard(): array**
   - Groups available bed count by ward
   - Returns ward info with counts
   - Useful for dashboard/statistics

5. **isBedAvailable(int $bedId): bool**
   - Quick availability check
   - No locking (read-only)
   - Used for pre-validation

6. **validateBedAvailability(int $bedId): void**
   - Pre-validation before transaction
   - Throws exception if unavailable
   - Provides user-friendly error messages

### Controller: InpatientRegistrationController

**Location:** `app/Http/Controllers/InpatientRegistrationController.php`

**Dependencies:** `BedAllocationService` (constructor injection)

**Methods:**

1. **index(Request $request)**
   - List inpatient registrations
   - Search by registration number or patient
   - Filter by status and date
   - Pagination (15 per page)
   - Eager loads: patient, doctor, ward, bed

2. **create()**
   - Show registration form
   - Load wards with available beds
   - Load active doctors
   - Display bed availability summary

3. **store(StoreInpatientRegistrationRequest $request)**
   - **Main registration flow with transaction**
   - Validates bed availability
   - Calls BedAllocationService to allocate bed
   - Generates registration number
   - Creates registration record
   - **All or nothing:** Transaction ensures atomicity

4. **show(Registration $registration)**
   - Display registration details
   - Shows bed assignment prominently
   - Print and discharge buttons

5. **discharge(Registration $registration)**
   - Release bed (calls releaseBed())
   - Update registration status to 'completed'
   - Set actual discharge date
   - **Transaction:** Ensures bed release + status update together

6. **print(Registration $registration)**
   - Generate PDF admission letter
   - Download with filename format: `admission-letter-{registration_number}.pdf`

7. **getBeds(Request $request)** [AJAX]
   - Get available beds for selected ward
   - Returns JSON array
   - Used for dynamic bed dropdown

8. **checkBedAvailability(Request $request)** [AJAX]
   - Real-time bed availability check
   - Returns JSON with availability status
   - Optional: Use before form submit

### Validation: StoreInpatientRegistrationRequest

**Location:** `app/Http/Requests/StoreInpatientRegistrationRequest.php`

**Rules:**
```php
'patient_id' => 'required|exists:patients,id'
'doctor_id' => 'required|exists:doctors,id'
'bed_id' => 'required|exists:beds,id'
'diagnosis' => 'required|string|max:500'
'planned_admission_date' => 'nullable|date|after_or_equal:today'
'estimated_discharge_date' => 'nullable|date|after:planned_admission_date'
'payment_method' => 'required|in:cash,bpjs,insurance,company'
'notes' => 'nullable|string|max:1000'
```

**Custom Validations:**
- Admission date cannot be in the past
- Discharge date must be after admission date
- Diagnosis required (max 500 chars)
- Notes optional (max 1000 chars)

---

## Transaction Flow

### Complete Registration Flow

```php
// Step-by-step breakdown
DB::transaction(function () use ($request) {
    // STEP 1: Pre-validation (throws exception if fails)
    $this->bedAllocationService->validateBedAvailability($request->bed_id);
    
    // STEP 2: Allocate bed with pessimistic lock
    // This is where SELECT FOR UPDATE happens
    $bed = $this->bedAllocationService->allocateBed($request->bed_id);
    // At this point: bed is locked, status = occupied
    
    // STEP 3: Generate registration number
    $registrationNumber = $this->generateRegistrationNumber();
    // Uses lockForUpdate() to prevent duplicate numbers
    
    // STEP 4: Create registration record
    $registration = Registration::create([
        'registration_number' => $registrationNumber,
        'registration_type' => 'inpatient',
        'patient_id' => $request->patient_id,
        'doctor_id' => $request->doctor_id,
        'ward_id' => $bed->ward_id,
        'bed_id' => $bed->id,
        'diagnosis' => $request->diagnosis,
        'payment_method' => $request->payment_method,
        'status' => 'active',
        // ... other fields
    ]);
    
    return $registration;
    
    // Transaction commits here
    // If any step fails, entire transaction rolls back
    // Bed remains available, no partial data
});
```

### Discharge Flow

```php
DB::transaction(function () use ($registration) {
    // STEP 1: Release bed with lock
    if ($registration->bed_id) {
        $this->bedAllocationService->releaseBed($registration->bed_id);
    }
    // Bed now: status = available, occupied_at = null
    
    // STEP 2: Update registration
    $registration->update([
        'status' => 'completed',
        'actual_discharge_date' => now(),
    ]);
    
    // Transaction commits
    // Both bed release and status update happen together
});
```

---

## Database Locking Details

### What is lockForUpdate()?

**SQL Generated:**
```sql
SELECT * FROM beds 
WHERE id = 101 
FOR UPDATE;
```

**Behavior:**
- Acquires **exclusive lock** on selected row(s)
- Other transactions **cannot** modify locked rows
- Other transactions **can** read (unless using `FOR UPDATE`)
- Lock held until transaction commits/rolls back
- If row already locked, transaction **waits** (or times out)

### Lock Types in Laravel

1. **lockForUpdate()** - Pessimistic write lock
   - Prevents reads and writes
   - Use for: Critical updates (bed allocation)

2. **sharedLock()** - Pessimistic read lock
   - Prevents writes, allows reads
   - Use for: Reading data that shouldn't change

3. **No lock** - Optimistic approach
   - Fast but risky for concurrent updates
   - Use for: Non-critical reads

### Why Pessimistic Locking Here?

**Scenario:** Bed allocation is **critical**
- Can't have two patients in one bed
- Race condition consequences are severe
- Better to wait a few milliseconds than double-book

**Alternative (Optimistic Locking):**
```php
// Check version before update
$bed = Bed::find($bedId);
$originalVersion = $bed->version;

$bed->update([
    'status' => 'occupied',
    'version' => $originalVersion + 1
]);

// WHERE version = $originalVersion
// If version changed, update fails
```

**Why we didn't use optimistic:**
- Requires version column
- Failure means re-trying (complex UX)
- Pessimistic is simpler and more reliable here

---

## Error Handling

### Exception Types

1. **Bed Not Found**
   ```php
   throw new Exception('Bed not found.');
   ```

2. **Bed Not Available**
   ```php
   throw new Exception("Bed {$bedNumber} is no longer available. Current status: {$status}");
   ```

3. **Transaction Failure**
   - Automatic rollback
   - Database state unchanged
   - User sees error message

### Error Flow

```php
try {
    $registration = DB::transaction(function () {
        // Allocation logic
    });
    
    return redirect()->route('inpatient.show', $registration)
        ->with('success', 'Registration created successfully.');
        
} catch (Exception $e) {
    return back()
        ->withInput()  // Preserve form data
        ->with('error', 'Failed: ' . $e->getMessage());
}
```

**User Experience:**
- Form data preserved
- Clear error message displayed
- Can retry immediately
- No partial data in database

---

## Views

### 1. index.blade.php - Registration List

**Features:**
- Search by registration number or patient
- Filter by status (active, completed, cancelled)
- Filter by date
- Pagination
- Shows: Registration #, Date, Patient, Ward/Bed, Doctor, Status
- Actions: View, Discharge (if active)

**Bed Display:**
```blade
Ward: {{ $registration->ward->name }}
Bed: {{ $registration->bed->bed_number }}
```

### 2. create.blade.php - Registration Form

**Features:**
- **Bed Availability Summary** at top
  - Shows each ward with available bed count
  - Helps staff choose ward quickly

- **Patient Search (AJAX)**
  - Same as outpatient module
  - Reusable JavaScript

- **Ward Selection**
  - Dropdown with bed count display
  - Example: "VIP Ward (VIP) - 3 beds available"

- **Bed Selection (Dynamic)**
  - Loads via AJAX when ward selected
  - Shows bed number and type
  - Disabled until ward chosen

- **Real-time Availability Check**
  - Optional: Check bed before submit
  - Warns if bed becomes unavailable

**JavaScript:**
```javascript
// Ward changes → Load beds
wardSelect.addEventListener('change', function() {
    fetch(`/inpatient/beds?ward_id=${wardId}`)
        .then(response => response.json())
        .then(beds => {
            // Populate bed dropdown
        });
});

// Bed changes → Check availability
bedSelect.addEventListener('change', function() {
    fetch(`/inpatient/check-bed?bed_id=${bedId}`)
        .then(response => response.json())
        .then(data => {
            if (!data.available) {
                alert(data.message);
            }
        });
});
```

### 3. show.blade.php - Registration Details

**Features:**
- **Registration Number** prominently displayed
- **Bed Assignment** highlighted in purple box
  - Ward name and class
  - Bed number (large font)
  - Bed type

- **Patient Information** section
- **Registration Information** section
- **Diagnosis** section
- **Admission Details** (planned, estimated, actual discharge)
- **Notes** (if any)

**Actions:**
- Print Admission Letter (PDF)
- Discharge Patient (if active)
- Back to List

### 4. admission-letter.blade.php - PDF Template

**Features:**
- Hospital header
- **BED ASSIGNMENT** highlighted prominently
- Patient information (complete)
- Admission information
- Diagnosis
- Important instructions for patient
- Signatures (Officer + Doctor)
- Print timestamp

**Styling:**
- Professional PDF layout
- Bed info in blue box (easy to see)
- Important notes in yellow box
- Clear sections with borders

---

## Routes

```php
// Resource routes
Route::resource('inpatient', InpatientRegistrationController::class)
    ->only(['index', 'create', 'store', 'show']);

// Custom routes
Route::post('/inpatient/{registration}/discharge', [...])
    ->name('inpatient.discharge');
    
Route::get('/inpatient/{registration}/print', [...])
    ->name('inpatient.print');
    
Route::get('/inpatient/beds', [...])
    ->name('inpatient.beds');  // AJAX
    
Route::get('/inpatient/check-bed', [...])
    ->name('inpatient.check-bed');  // AJAX
```

**Generated Routes:**
- `GET /inpatient` - inpatient.index
- `GET /inpatient/create` - inpatient.create
- `POST /inpatient` - inpatient.store
- `GET /inpatient/{id}` - inpatient.show
- `POST /inpatient/{id}/discharge` - inpatient.discharge
- `GET /inpatient/{id}/print` - inpatient.print
- `GET /inpatient/beds?ward_id={id}` - inpatient.beds
- `GET /inpatient/check-bed?bed_id={id}` - inpatient.check-bed

---

## Testing Concurrent Bookings

### Manual Test

**Setup:** Open two browser windows (different sessions)

**Steps:**
1. Window A: Navigate to create registration
2. Window B: Navigate to create registration
3. Window A: Select patient, ward, bed 101
4. Window B: Select patient, ward, bed 101
5. Window A: Submit form ✓ (succeeds)
6. Window B: Submit form ✗ (fails with error)

**Expected Result:**
- Window A: Success message, bed allocated
- Window B: Error: "Bed 101 is no longer available"
- Database: Only one registration for bed 101

### Automated Test (PHPUnit)

```php
public function test_prevents_bed_double_booking()
{
    $bed = Bed::factory()->create(['status' => 'available']);
    
    // Simulate concurrent requests
    $responses = collect(range(1, 2))->map(function() use ($bed) {
        return $this->post('/inpatient', [
            'patient_id' => Patient::factory()->create()->id,
            'doctor_id' => Doctor::factory()->create()->id,
            'bed_id' => $bed->id,
            'diagnosis' => 'Test diagnosis',
            'payment_method' => 'cash',
        ]);
    });
    
    // One should succeed, one should fail
    $this->assertEquals(1, $responses->filter->successful()->count());
    $this->assertEquals(1, $responses->filter->failed()->count());
    
    // Bed should be occupied
    $this->assertEquals('occupied', $bed->fresh()->status);
}
```

---

## Performance Considerations

### Database Locks

**Impact:**
- Lock held for ~10-50ms (typical)
- Other transactions wait
- Timeout after ~30 seconds (default)

**Optimization:**
- Keep transactions **short**
- Don't do external API calls inside transaction
- Pre-validate before locking

**Good:**
```php
// Validate first (no lock)
$this->bedAllocationService->validateBedAvailability($bedId);

// Then lock (fast)
DB::transaction(function () use ($bedId) {
    $bed = $this->bedAllocationService->allocateBed($bedId);
    // Quick operations only
});
```

**Bad:**
```php
DB::transaction(function () {
    $bed = Bed::lockForUpdate()->find($bedId);
    
    // ❌ Don't do this inside transaction:
    sleep(5);  // Slow operation
    Http::get('external-api.com');  // External call
    Mail::send(...);  // Email sending
});
```

### Indexes

**Required for performance:**
```sql
-- beds table
INDEX idx_bed_status (status)
INDEX idx_bed_ward (ward_id, status)

-- registrations table
INDEX idx_reg_bed (bed_id)
INDEX idx_reg_type_status (registration_type, status)
```

### Caching

**Don't cache:**
- Bed availability (changes frequently)
- Active registrations

**Can cache:**
- Ward list (rarely changes)
- Doctor list (stable)
- Configuration

---

## Common Issues & Solutions

### Issue: Deadlock

**Symptom:** Transaction timeout or deadlock error

**Cause:** Two transactions lock rows in different order
```
Transaction A: Lock bed 1, then bed 2
Transaction B: Lock bed 2, then bed 1
→ Deadlock!
```

**Solution:** Always lock in same order (by ID ascending)

### Issue: Long lock wait

**Symptom:** Form submit takes 30+ seconds

**Cause:** Previous transaction hasn't committed

**Solutions:**
1. Check for long-running transactions
2. Reduce transaction duration
3. Increase lock wait timeout (if needed)

### Issue: Bed shows available but allocation fails

**Cause:** Timing issue (bed booked between check and submit)

**Solution:** This is expected behavior! The lock prevents double booking. User just needs to select different bed.

---

## Security Considerations

1. **Authorization:** Auth middleware on all routes
2. **Validation:** Server-side via FormRequest
3. **Transaction Safety:** Prevents partial updates
4. **SQL Injection:** Eloquent ORM prevents this
5. **CSRF:** Token on all forms
6. **Mass Assignment:** Fillable attributes defined

---

## Future Enhancements

1. **Bed Transfer:**
   - Move patient to different bed
   - Release old bed, allocate new bed
   - Maintain history

2. **Bed Reservation:**
   - Reserve bed for future admission
   - Status: 'reserved' vs 'occupied'
   - Auto-release if admission doesn't happen

3. **Bed History:**
   - Track all patients who used a bed
   - Cleaning schedule
   - Maintenance log

4. **Real-time Bed Map:**
   - Visual floor plan
   - Color-coded availability
   - Click to allocate

5. **Discharge Summary:**
   - Medical summary document
   - Treatment history
   - Billing summary
   - PDF generation

6. **Notification System:**
   - SMS to patient when bed ready
   - Email admission letter
   - Discharge reminders

---

Last Updated: 2026-01-13
