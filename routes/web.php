<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\PolyclinicController;
use App\Http\Controllers\Admin\WardController;
use App\Http\Controllers\Admin\BedController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\OutpatientRegistrationController;
use App\Http\Controllers\InpatientRegistrationController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes - SIMRS Admisi
|--------------------------------------------------------------------------
*/

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Guest Routes (Not Authenticated)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (All Roles)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // Logout
    Route::post('/logout', LogoutController::class)->name('logout');
    
    // Staff Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Patient Management (accessible by both admin and staff)
    Route::resource('patients', PatientController::class);
    Route::get('/patients/{patient}/print-card', [PatientController::class, 'printCard'])->name('patients.printCard');
    Route::get('/patients-search', [PatientController::class, 'search'])->name('patients.search');
    
    // Outpatient Registration
    Route::get('/outpatient/doctors', [OutpatientRegistrationController::class, 'getDoctors'])->name('outpatient.doctors');
    Route::resource('outpatient', OutpatientRegistrationController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('/outpatient/{registration}/print', [OutpatientRegistrationController::class, 'print'])->name('outpatient.print');
    
    // Inpatient Registration
    Route::get('/inpatient/beds', [InpatientRegistrationController::class, 'getBeds'])->name('inpatient.beds');
    Route::get('/inpatient/check-bed', [InpatientRegistrationController::class, 'checkBedAvailability'])->name('inpatient.check-bed');
    Route::resource('inpatient', InpatientRegistrationController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('/inpatient/{registration}/discharge', [InpatientRegistrationController::class, 'discharge'])->name('inpatient.discharge');
    Route::get('/inpatient/{registration}/print', [InpatientRegistrationController::class, 'print'])->name('inpatient.print');
    
    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.exportPdf');
});

/*
|--------------------------------------------------------------------------
| Admin-Only Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard
    Route::get('/dashboard', function () {
        $stats = [
            'total_patients' => \App\Models\Patient::count(),
            'today_registrations' => \App\Models\Registration::today()->count(),
            'available_beds' => \App\Models\Bed::where('status', 'available')->count(),
            'active_doctors' => \App\Models\Doctor::active()->count(),
        ];
        return view('admin.dashboard', compact('stats'));
    })->name('dashboard');
    
    // Master Data Management
    Route::resource('users', UserController::class);
    Route::resource('doctors', DoctorController::class);
    Route::resource('polyclinics', PolyclinicController::class);
    Route::resource('wards', WardController::class);
    Route::resource('beds', BedController::class);
});

// Alternative: Using role middleware for specific roles
// Route::middleware(['auth', 'role:admin,staff'])->group(function () {
//     // Routes accessible by admin OR staff
// });
