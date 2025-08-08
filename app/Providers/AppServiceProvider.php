<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse;
use App\Filament\Responses\CustomLogoutResponse;
use App\Models\MantenimientoGeneral;
use App\Observers\MantenimientoGeneralObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(LogoutResponse::class, CustomLogoutResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        MantenimientoGeneral::observe(MantenimientoGeneralObserver::class);
    }
}
