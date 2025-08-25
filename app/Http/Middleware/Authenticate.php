<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // For API/JSON requests, return null to trigger a 401 response
        if ($request->expectsJson()) {
            return null;
        }
        
        // For push notification routes, redirect to Filament admin login
        if ($request->is('push/*') || $request->is('push-*')) {
            return url('/admin/login');
        }
        
        // For all other web routes, redirect to Filament admin login
        return url('/admin/login');
    }
}
