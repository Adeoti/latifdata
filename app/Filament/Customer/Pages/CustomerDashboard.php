<?php

namespace App\Filament\Customer\Pages;

use Filament\Pages\Page;
use App\Models\Announcement;
use Filament\Widgets\Widget;
use Filament\Facades\Filament;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\WidgetConfiguration;
use App\Filament\Customer\Widgets\CustomerStats;
use App\Filament\Customer\Widgets\ServicesLinksWidget;
use App\Filament\Customer\Widgets\CustomMobileHomeScreen;
use App\Filament\Customer\Widgets\RecentActivitiesWidget;

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


    public $announcement_status;
    public $announcement_content;
    public $announcement_style;
    public $user_balance;
    public $cashback_amount;

    public $balanceToggle;

    protected $listeners = ['toggleBalance'];

    public function mount()
    {
        $announcement = Announcement::where('is_active', true)->first();

        $this->user_balance = number_format(auth()->user()->balance,2);
        $this->cashback_amount = number_format(auth()->user()->cashback_balance,2);

        $this->balanceToggle = auth()->user()->balance_toggle;
            
        if ($announcement) {
            $this->announcement_status = $announcement->is_active;
            $this->announcement_style = $announcement->style;
            $this->announcement_content = $announcement->message;
        
        }
    }

    public function toggleBalance()
    {
        $user = auth()->user();
        $user->balance_toggle = !$user->balance_toggle;
        $user->save();

        $this->balanceToggle = $user->balance_toggle;
    }

    public static function getRoutePath(): string
    {
        return static::$routePath;
    }

    protected function getHeaderWidgets(): array
    {
        return [
           // CustomMobileHomeScreen::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            
            //AccountWidget::class,
            //FilamentInfoWidget::class,
            //CustomerStats::class,
            RecentActivitiesWidget::class,
           // ServicesLinksWidget::class,
        ];
    }

   

    
}
