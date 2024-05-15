<?php

namespace App\Filament\Customer\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CustomerStats extends BaseWidget
{
    public $ngn = "₦";

        

    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        // $customer_balance = User::find(auth()->id())->first()->balance;
        // $cashback_balance = User::find(auth()->id())->first()->cashback_balance;
        // $referral_balance = User::find(auth()->id())->first()->referral_balance;
        $customer_balance = auth()->user()->balance;
        $cashback_balance = auth()->user()->cashback_balance;
        $referral_balance = auth()->user()->referral_balance;


        $customer_balance = number_format($customer_balance,2);
        $cashback_balance = number_format($cashback_balance,2);
        $referral_balance = number_format($referral_balance,2);


        return [
            //
            
            Stat::make('Wallet Balance', "$this->ngn".$customer_balance)
                ->color('success')
                ->chart([2,23,4,6,40]),

            Stat::make('Cashback Balance', "$this->ngn".$cashback_balance)
                ->color('warning')
                ->chart([2,34,6,2,0]),

            Stat::make('Referral Bonus', "$this->ngn".$referral_balance)
                ->color('warning')
                ->chart([2,34,6,2,0]),

        ];
    }
}
