<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\LegacyComponents\Widget;
use Filament\Widgets;
use App\Filament\Widgets\MantenimientosList;
use App\Filament\Widgets\CronogramaMantenimientos;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\App;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;


class MantenimientoPanelProvider extends PanelProvider
{
    

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('mantenimiento')
            ->path('mantenimiento')
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->brandName('Control de Taller')
            ->brandLogo(asset('images/barlovento-logo.png'))
            ->favicon(asset('images/favicon.png'))
            ->sidebarCollapsibleOnDesktop()    
            ->sidebarWidth('14rem')
            ->maxContentWidth('full')   
            ->discoverResources(in: app_path('Filament/Mantenimiento/Resources'), for: 'App\\Filament\\Mantenimiento\\Resources')
            ->discoverPages(in: app_path('Filament/Mantenimiento/Pages'), for: 'App\\Filament\\Mantenimiento\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Mantenimiento/Widgets'), for: 'App\\Filament\\Mantenimiento\\Widgets')
            ->widgets([
                CronogramaMantenimientos::class,
                MantenimientosList::class,
            ])
            ->resources([
                \App\Filament\Mantenimiento\Resources\RodadosHerramientasResource::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
