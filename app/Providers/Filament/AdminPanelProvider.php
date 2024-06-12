<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Widgets\Widget;
use Filament\Enums\ThemeMode;
use Filament\Support\Colors\Color;
use App\Filament\Widgets\StatAction;
use App\Filament\Widgets\CashflowChart;
use App\Filament\Widgets\StatsOverview;
use App\Models\SiteSettings;
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

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('cpanel')
            ->login()
            //->registration()
            ->passwordReset()
           // ->profile(isSimple: false)
            ->brandName(SiteSettings::first()->name)
            ->sidebarCollapsibleOnDesktop()
            ->topbar(true)
            ->spa()
            ->defaultThemeMode(ThemeMode::Dark)

            //->favicon(asset('images/favicon.png'))
            //->brandLogo(asset('images/logo.svg'))
            //->darkModeBrandLogo()
            //->brandLogoHeight('2rem')

            ->colors([
                //'primary' => Color::Amber,
                'primary' => "#7534e5",
                'danger' => Color::Rose,
                'gray' => '#000000',//Color::Gray,
                'info' => Color::Blue,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->databaseNotifications(true)
            ->databaseNotificationsPolling(100)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
                StatsOverview::class,
                //CashflowChart::class

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
