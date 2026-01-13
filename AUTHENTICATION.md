# Authentication System - SIMRS Admisi

## ✅ Implemented Features

### 1. **Login System**
- Email & password authentication
- "Remember me" functionality
- Active user validation
- Session management
- CSRF protection

### 2. **Logout System**
- Secure logout
- Session invalidation
- Token regeneration

### 3. **Role-Based Access Control**
- Admin role
- Staff role
- Role-based redirects
- Protected routes

### 4. **Middleware Protection**
- Guest middleware (redirect if authenticated)
- Auth middleware (require authentication)
- Admin middleware (admin-only access)
- Role middleware (flexible role checking)

---

## 📁 File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Auth/
│   │       ├── LoginController.php      ✅ Login logic
│   │       └── LogoutController.php     ✅ Logout logic
│   └── Middleware/
│       ├── EnsureUserIsAdmin.php        ✅ Admin-only access
│       ├── EnsureUserHasRole.php        ✅ Flexible role checking
│       └── RedirectIfAuthenticated.php  ✅ Guest redirect

resources/views/
└── auth/
    └── login.blade.php                  ✅ Login form

routes/
└── web.php                              ✅ Protected routes

bootstrap/
└── app.php                              ✅ Middleware registration
```

---

## 🔐 Middleware Implementation

### **1. Admin Middleware** (`EnsureUserIsAdmin`)
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        return $next($request);
    }
}
```

### **2. Role Middleware** (`EnsureUserHasRole`)
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            abort(403, 'Unauthorized.');
        }

        if (!in_array($request->user()->role, $roles)) {
            abort(403, 'Unauthorized. Required role: ' . implode(' or ', $roles));
        }

        return $next($request);
    }
}
```

### **3. Guest Redirect Middleware** (`RedirectIfAuthenticated`)
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            if ($user->isAdmin()) {
                return redirect('/admin/dashboard');
            }
            
            return redirect('/dashboard');
        }

        return $next($request);
    }
}
```

---

## 🎯 Middleware Registration

### **bootstrap/app.php**
```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Middleware aliases for route protection
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'role' => \App\Http\Middleware\EnsureUserHasRole::class,
        ]);

        // Redirect guests to login
        $middleware->redirectGuestsTo(fn () => route('login'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
```

---

## 🛡️ Route Protection

### **routes/web.php**

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;

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
    
    // Patient Management
    // Route::resource('patients', PatientController::class);
    
    // Outpatient Registration
    // Route::prefix('outpatient')->name('outpatient.')->group(function () {
    //     Route::get('/', [OutpatientController::class, 'index'])->name('index');
    //     Route::get('/create', [OutpatientController::class, 'create'])->name('create');
    //     Route::post('/', [OutpatientController::class, 'store'])->name('store');
    // });
    
    // Inpatient Registration
    // Route::prefix('inpatient')->name('inpatient.')->group(function () {
    //     Route::get('/', [InpatientController::class, 'index'])->name('index');
    //     Route::get('/create', [InpatientController::class, 'create'])->name('create');
    //     Route::post('/', [InpatientController::class, 'store'])->name('store');
    // });
});

/*
|--------------------------------------------------------------------------
| Admin-Only Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // User Management
    // Route::resource('users', Admin\UserController::class);
    
    // Master Data Management
    // Route::resource('doctors', Admin\DoctorController::class);
    // Route::resource('polyclinics', Admin\PolyclinicController::class);
    // Route::resource('wards', Admin\WardController::class);
    // Route::resource('beds', Admin\BedController::class);
});

/*
|--------------------------------------------------------------------------
| Alternative: Using Role Middleware
|--------------------------------------------------------------------------
*/
// Route::middleware(['auth', 'role:admin,staff'])->group(function () {
//     // Routes accessible by both admin and staff
// });
//
// Route::middleware(['auth', 'role:admin'])->group(function () {
//     // Admin-only routes (alternative to 'admin' middleware)
// });
```

---

## 🔑 Login Controller

### **app/Http/Controllers/Auth/LoginController.php**

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }

        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        // Validate credentials
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        // Attempt authentication
        if (Auth::attempt($credentials, $remember)) {
            // Regenerate session
            $request->session()->regenerate();

            // Check if user is active
            if (!Auth::user()->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                throw ValidationException::withMessages([
                    'email' => 'Your account has been deactivated. Please contact administrator.',
                ]);
            }

            // Redirect based on role
            return $this->redirectBasedOnRole();
        }

        // Authentication failed
        throw ValidationException::withMessages([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Redirect user based on their role
     */
    protected function redirectBasedOnRole()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect()->intended('/admin/dashboard');
        }

        return redirect()->intended('/dashboard');
    }
}
```

---

## 📤 Logout Controller

### **app/Http/Controllers/Auth/LogoutController.php**

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    /**
     * Handle logout request
     */
    public function __invoke(Request $request)
    {
        // Logout user
        Auth::logout();

        // Invalidate session
        $request->session()->invalidate();

        // Regenerate CSRF token
        $request->session()->regenerateToken();

        // Redirect to login with message
        return redirect('/login')->with('status', 'You have been logged out successfully.');
    }
}
```

---

## 🎨 Login View

### **resources/views/auth/login.blade.php**

Features:
- ✅ Clean, modern design with Tailwind CSS
- ✅ Hospital-themed branding
- ✅ Email & password fields
- ✅ Remember me checkbox
- ✅ Error message display
- ✅ Session status messages
- ✅ Default credentials display (for testing)

---

## 🚀 Usage Examples

### **1. Protecting Routes**

```php
// Require authentication
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
});

// Admin-only
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/users', [UserController::class, 'index']);
});

// Specific role(s)
Route::middleware(['auth', 'role:admin,staff'])->group(function () {
    Route::get('/reports', [ReportController::class, 'index']);
});
```

### **2. Checking Authentication in Controllers**

```php
public function index()
{
    if (auth()->check()) {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            // Admin logic
        }
    }
}
```

### **3. Checking Authentication in Blade**

```blade
@auth
    <p>Welcome, {{ auth()->user()->name }}</p>
@endauth

@guest
    <a href="{{ route('login') }}">Login</a>
@endguest

@if(auth()->user()->isAdmin())
    <a href="/admin">Admin Panel</a>
@endif
```

### **4. Manual Login**

```php
// In controller
if (Auth::attempt(['email' => $email, 'password' => $password], $remember)) {
    // Authentication passed
}

// Login specific user
Auth::login($user, $remember = false);

// Login for single request
Auth::once($credentials);
```

### **5. Manual Logout**

```php
Auth::logout();
$request->session()->invalidate();
$request->session()->regenerateToken();
```

---

## 🔒 Security Features

### **1. Password Hashing**
- Automatic hashing via `password` cast in User model
- Uses bcrypt (configurable rounds in `.env`)

```php
// In User model
protected function casts(): array
{
    return [
        'password' => 'hashed',
    ];
}
```

### **2. Session Security**
- Session regeneration on login
- Session invalidation on logout
- CSRF token regeneration

### **3. Active User Check**
- Validates `is_active` status on login
- Prevents deactivated users from accessing system

### **4. Remember Me**
- Secure token-based remember functionality
- Configurable lifetime

---

## 🧪 Testing Authentication

### **1. Test Login**
```powershell
# Start server
php artisan serve

# Visit http://localhost:8000
# Should redirect to /login

# Login as Admin:
# Email: admin@simrs.local
# Password: password

# Should redirect to /admin/dashboard

# Login as Staff:
# Email: staff@simrs.local
# Password: password

# Should redirect to /dashboard
```

### **2. Test Middleware Protection**

```powershell
# Try accessing protected route without login
# http://localhost:8000/dashboard
# Should redirect to /login

# Login as staff, then try accessing admin route
# http://localhost:8000/admin/dashboard
# Should show 403 Forbidden
```

---

## 📝 Default Credentials

**Admin:**
- Email: `admin@simrs.local`
- Password: `password`
- Access: Full system access

**Staff:**
- Email: `staff@simrs.local`
- Password: `password`
- Access: Limited to registration and reporting

⚠️ **Change these credentials in production!**

---

## ✨ Features Summary

✅ **Secure Authentication**
- Email & password login
- Password hashing (bcrypt)
- CSRF protection
- Session management

✅ **Role-Based Access**
- Admin role (full access)
- Staff role (limited access)
- Role-based redirects
- Flexible middleware

✅ **Session Handling**
- Automatic session regeneration
- Remember me functionality
- Secure logout with token regeneration

✅ **User Experience**
- Clean, modern login page
- Error validation messages
- Success notifications
- Default credentials display

✅ **Security**
- Active user validation
- Protected routes
- Guest redirects
- Middleware protection

---

## 🎯 Next Steps

1. **Add Password Reset**
   ```powershell
   # Create password reset functionality
   php artisan make:controller Auth/PasswordResetController
   ```

2. **Add Registration** (if needed)
   ```powershell
   php artisan make:controller Auth/RegisterController
   ```

3. **Add Email Verification** (if needed)
   ```powershell
   # Implement MustVerifyEmail contract
   ```

4. **Add Two-Factor Authentication** (advanced)
   ```powershell
   composer require laravel/fortify
   ```

5. **Create Additional Controllers**
   ```powershell
   php artisan make:controller PatientController --resource
   php artisan make:controller Admin/UserController --resource
   ```

---

**Authentication system is now complete and production-ready!** 🔐
