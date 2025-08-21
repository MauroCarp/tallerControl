<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;

class FilamentNotificationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Inyectar meta tag CSRF en el head solo si el usuario está autenticado
        FilamentView::registerRenderHook(
            'panels::head.end',
            fn (): string => auth()->check() 
                ? '<meta name="csrf-token" content="' . csrf_token() . '">' 
                : ''
        );
        
        // Inyectar el toast de notificaciones solo si el usuario está autenticado
        FilamentView::registerRenderHook(
            'panels::body.end',
            fn (): string => auth()->check() 
                ? Blade::render('@include("components.simple-notification-toast")') 
                : ''
        );
    }
}
