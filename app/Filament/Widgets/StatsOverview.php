<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{

    const CURRENCY_SIGN = '₦';
    const ADMIN_BALANCE = '40,440,000.20';
    const USERS_BALANCE = '12,333,090.90';
     
    const COMPANY_EXPENSES = '2,030,000';
    const COMPANY_SAVINGS = '50,000,000';
    const COMPANY_STAFF = '300';



    public function apiBalance($vendor = 'datalight'){

        switch($vendor){
            case 'twins10':
                $auth_route = "https://twins10.com/api/user";
                $pass_n_username = 'Adeoti360:7DP75syvXML$$Ade#';
            break;
            case 'datalight':
                $auth_route = "https://datalight.ng/api/user";
                $pass_n_username = 'SweetBill:7DP75syvXML$$Ade#';
            break;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($pass_n_username),
        ])->post($auth_route);
        $json = $response->body();
        
        $responseData = json_decode($json, true);
        $username = $responseData['username'];
        $balance = (float) str_replace(',', '', $responseData['balance']);
            $balance = SELF::CURRENCY_SIGN.number_format($balance,2);
        return $balance;
        
    }
    protected function getStats(): array
    {


        $total_users_balance = User::sum('balance');
        $total_users_cashback_balance = User::sum('cashback_balance');


        return [
         
            Stat::make(' Twins10 Wallet Balance', $this->apiBalance('twins10'))
                ->chart([7, 2, 10, 3, 15, 4, 17]) 
                -> color('success')
            ,
         
            Stat::make(' DataLight Wallet Balance', $this->apiBalance('datalight'))
                ->chart([7, 2, 10, 3, 15, 4, 17]) 
                -> color('danger')
            ,
         
            Stat::make(' VTPass Wallet Balance', SELF::CURRENCY_SIGN.SELF::ADMIN_BALANCE)
                ->chart([7, 2, 10, 3, 15, 4, 17]) 
                -> color('success')
            ,
            Stat::make('User\'s Wallet Balance',SELF::CURRENCY_SIGN.number_format($total_users_balance,2))
                -> description('The sum of all users\' wallet balance')
                -> descriptionColor('danger')
                -> descriptionIcon('heroicon-m-user-plus')
            ,
            Stat::make('User\'s Cashback Balance',SELF::CURRENCY_SIGN.number_format($total_users_cashback_balance,2))
                -> description('The sum of all users\' cashback balance')
                -> descriptionColor('info')
                -> descriptionIcon('heroicon-m-user-plus')
            ,
            Stat::make('SweetBill Users', User::count()) 
                -> chart([7, 1, 4, 30, 15, 4, 2])
                -> color('warning'),

             Stat::make('Total Staff', User::where('is_staff',true)->count())
                -> descriptionIcon('heroicon-m-arrow-up')
                -> chart([7, 1, 4, 30, 15, 4, 2])
                -> color('warning')
            ,

            // Expenses, Savings, Staff



            Stat::make('Expenses', SELF::CURRENCY_SIGN.SELF::COMPANY_EXPENSES)
                -> description('This year\'s expenses')
                -> descriptionColor('info')
                -> descriptionIcon('heroicon-m-arrow-down')
            ,
            Stat::make('Savings', SELF::CURRENCY_SIGN.SELF::COMPANY_SAVINGS)
                -> description('This years\' savings')
                -> descriptionColor('success')
                -> descriptionIcon('heroicon-m-arrow-up')
                ,

           

          
        ];
    }
}
