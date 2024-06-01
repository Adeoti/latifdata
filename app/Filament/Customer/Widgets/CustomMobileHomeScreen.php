<?php

namespace App\Filament\Customer\Widgets;

use App\Models\Announcement;
use Filament\Widgets\Widget;

class CustomMobileHomeScreen extends Widget
{
    protected static string $view = 'filament.customer.widgets.custom-mobile-home-screen';
    protected int | string | array $columnSpan = 'full';
    protected static ?string $pollingInterval = '10s';

    public $announcement_status;
    public $announcement_content;
    public $announcement_style;
    public $user_balance;
    public $cashback_amount;

    public function mount()
    {
        $announcement = Announcement::where('is_active', true)->first();

        $this->user_balance = number_format(auth()->user()->balance,2);
        $this->cashback_amount = number_format(auth()->user()->cashback_balance,2);

            
        if ($announcement) {
            $this->announcement_status = $announcement->is_active;
            $this->announcement_style = $announcement->style;
            $this->announcement_content = $announcement->message;
        
        }
    }
}
