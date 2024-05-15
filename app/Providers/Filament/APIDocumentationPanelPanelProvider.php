<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Enums\ThemeMode;
use Filament\Support\Colors\Color;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use App\Filament\APIDocumentationPanel\Pages\APIIntroduction;

class APIDocumentationPanelPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('aPIDocumentationPanel')
            ->path('api-docs')
            ->topbar(true)
            ->spa()
            ->login()
            ->sidebarCollapsibleOnDesktop()
            ->defaultThemeMode(ThemeMode::Light)

            //->favicon(asset('images/favicon.png'))
            //->brandLogo(asset('images/logo.svg'))
            //->darkModeBrandLogo()
            //->brandLogoHeight('2rem')
            
            ->colors([
                'primary' => "#fe5006",
                'danger' => Color::Rose,
                'gray' => '#000000', //Color::Gray,
                'info' => Color::Blue,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
                'blue' => Color::Blue,
                'indigo' => Color::Indigo,
                'purple' => Color::Purple,
                'yellow' => Color::Yellow,
                'fuchsia' => Color::Fuchsia,
                
            ])
            ->databaseNotifications(true)
            ->databaseNotificationsPolling(100)
            ->discoverResources(in: app_path('Filament/APIDocumentationPanel/Resources'), for: 'App\\Filament\\APIDocumentationPanel\\Resources')
            ->discoverPages(in: app_path('Filament/APIDocumentationPanel/Pages'), for: 'App\\Filament\\APIDocumentationPanel\\Pages')
            ->pages([
               // Pages\Dashboard::class,
               APIIntroduction::class
            ])
            ->discoverWidgets(in: app_path('Filament/APIDocumentationPanel/Widgets'), for: 'App\\Filament\\APIDocumentationPanel\\Widgets')
            ->widgets([
                //Widgets\AccountWidget::class,
                //Widgets\FilamentInfoWidget::class,
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
