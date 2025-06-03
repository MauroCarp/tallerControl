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
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\App;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class RoturasReparacionPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('roturasReparacion')
            ->path('roturasReparacion')
            ->brandLogo(asset('images/barlovento-logo.png'))
            ->favicon(asset('images/favicon.png'))
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->discoverResources(in: app_path('Filament/RoturasReparacion/Resources'), for: 'App\\Filament\\RoturasReparacion\\Resources')
            ->discoverPages(in: app_path('Filament/RoturasReparacion/Pages'), for: 'App\\Filament\\RoturasReparacion\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/RoturasReparacion/Widgets'), for: 'App\\Filament\\RoturasReparacion\\Widgets')
            ->widgets([
                // ReparacionesPropiasList::class,
                // ReparacionesTercierizadasList::class,
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
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
