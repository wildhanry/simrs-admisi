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

        // Redirect authenticated users
        $middleware->redirectGuestsTo(fn () => route('login'));
        
        // Uncomment to use custom redirect for authenticated users
        // $middleware->redirectUsersTo(fn () => auth()->user()->isAdmin() 
        //     ? '/admin/dashboard' 
        //     : '/dashboard'
        // );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
