<?php

namespace App\Filament\Customer\Pages;

use App\Filament\Customer\Widgets\CustomerStats;
use App\Filament\Customer\Widgets\RecentActivitiesWidget;
use App\Filament\Customer\Widgets\ServicesLinksWidget;
use Filament\Pages\Page;
use Filament\Widgets\Widget;
use Filament\Facades\Filament;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\WidgetConfiguration;

class CustomerDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.customer.pages.customer-dashboard';

    protected static ?string $navigationLabel = "Dashboard";
    protected  ?string $heading = "";
    protected static ?string $title = "Dashboard";
    protected static ?string $slug = "";


    protected static string $routePath = '/';

    protected static ?int $navigationSort = -2;

    public static function getRoutePath(): string
    {
        return static::$routePath;
    }

    protected function getFooterWidgets(): array
    {
        return [
            AccountWidget::class,
            FilamentInfoWidget::class,
            CustomerStats::class,
            RecentActivitiesWidget::class,
            ServicesLinksWidget::class,
        ];
    }

   

    
}
