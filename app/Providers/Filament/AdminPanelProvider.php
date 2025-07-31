<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Enums\ThemeMode;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;

class AdminPanelProvider extends PanelProvider
{

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->brandName('Control de Taller')
            ->brandLogo(asset('images/barlovento-logo.png'))
            ->path('admin')
            ->login()
            ->darkMode(false)
            ->favicon(asset('images/favicon.png'))
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->sidebarWidth('14rem')
            ->sidebarCollapsibleOnDesktop()            
            // ->sidebarFullyCollapsibleOnDesktop()           
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->resources([
                \App\Filament\Mantenimiento\Resources\MantenimientosResource::class,
                \App\Filament\Mantenimiento\Resources\RodadosHerramientasResource::class,
                \App\Filament\MantenimientoGeneral\Resources\MantenimientoGeneralResource::class,
                \App\Filament\RoturasReparacion\Resources\RoturasReparacionResource::class,
                \App\Filament\Service\Resources\ServicesResource::class,
                \App\Filament\CombustiblesLubricantes\Resources\CombustiblesLubricantesResource::class,
            ])
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->widgets([
                \App\Filament\Resources\BarloventoResource\Widgets\BarloventoButton::class,
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
