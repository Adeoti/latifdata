<?php

namespace App\Providers\Filament;

use App\Filament\Customer\Pages\CustomerDashboard;
use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Enums\ThemeMode;
use Filament\Navigation\MenuItem;
use Filament\Support\Colors\Color;
use Filament\Http\Middleware\Authenticate;
use App\Filament\Customer\Pages\EditProfile;
use App\Filament\Pages\Auth\Register;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class CustomerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('customer')
            ->path('app')
            ->login()
            ->registration() 
            ->passwordReset()
            ->profile(false)
            ->darkMode(false)
            ->sidebarCollapsibleOnDesktop()
            ->userMenuItems([ 
                'profile' => MenuItem::make()->url(fn (): string => EditProfile::getUrl())
            ]) 
           // ->topNavigation()
            ->topbar(true)
            ->spa()
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
            ->discoverResources(in: app_path('Filament/Customer/Resources'), for: 'App\\Filament\\Customer\\Resources')
            ->discoverPages(in: app_path('Filament/Customer/Pages'), for: 'App\\Filament\\Customer\\Pages')
            ->pages([
                //Pages\Dashboard::class,
                //Pages\Dashboard2::class,
                CustomerDashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Customer/Widgets'), for: 'App\\Filament\\Customer\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->spa()
            ->registration()
            ->passwordReset()
            ->profile()
            ->sidebarCollapsibleOnDesktop()
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
