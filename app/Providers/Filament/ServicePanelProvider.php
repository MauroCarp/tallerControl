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
use App\Filament\Widgets\ServiceList;
use App\Filament\Widgets\CronogramaService;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\App;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;


class ServicePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('service')
            ->path('service')
            ->brandLogo(asset('images/barlovento-logo.png'))
            ->favicon(asset('images/favicon.png'))
            ->brandName('Control de Taller')
            ->sidebarCollapsibleOnDesktop()            
            ->maxContentWidth('full')
            ->sidebarWidth('14rem')
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->discoverResources(in: app_path('Filament/Service/Resources'), for: 'App\\Filament\\Service\\Resources')
            ->discoverPages(in: app_path('Filament/Service/Pages'), for: 'App\\Filament\\Service\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Service/Widgets'), for: 'App\\Filament\\Service\\Widgets')
            ->widgets([
                CronogramaService::class,
                ServiceList::class,
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
