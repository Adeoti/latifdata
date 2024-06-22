<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use App\Models\Saving;
use App\Models\User;
use App\Models\PaymentIntegration;

use Illuminate\Support\Facades\Http;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    //protected int | string | array $columnSpan = 1;

    const CURRENCY_SIGN = '₦';
    const ADMIN_BALANCE = '40,440,000.20';
    const USERS_BALANCE = '12,333,090.90';
     
    const COMPANY_EXPENSES = '2,030,000';
    const COMPANY_SAVINGS = '50,000,000';
    const COMPANY_STAFF = '300';




    protected function getColumns(): int
    {
        

        return 3;
    }


    public function getMySweetBillBalance(){
        $balance_mi = 0;

        $response = Http::withHeaders([
            'email' => PaymentIntegration::first()->sweetbill_email,
            'password' => PaymentIntegration::first()->sweetbill_password,
            'api_key' => PaymentIntegration::first()->sweetbill_api_key,
            
            'Content-Type' => 'application/json',

        ])->get('https://sweetbill.ng/api/v1/balance');
        
           // dd($response);

        // Check if the request was successful
        if ($response->successful()) {
            // Request was successful, handle the response
            $responseData = $response->json();
           
            $balance_mi = $responseData['balance'];

            $balance_mi = number_format($balance_mi,2);

        } else {

            $this->dispatch('alert',
                title: 'ERROR BLNC',
                text: 'Something went wrong. Try again or chat our reps!',
                button: 'Got it',
                type: 'error'

            );

            return;
            

        }


        return $balance_mi;
    }




    protected function getStats(): array
    {

        $total_company_expenses = Expense::where('is_customer', false)->sum('amount');
        $total_company_savings = Saving::where('is_customer', false)->sum('amount');
        
        $total_users_balance = User::sum('balance');
        $total_users_cashback_balance = User::sum('cashback_balance');

        


        return [
         
         
            Stat::make('SweetBill Wallet Balance', SELF::CURRENCY_SIGN.$this->getMySweetBillBalance())
                ->chart([7, 2, 10, 3, 15, 4, 17]) 
                -> color('success')
                
            ,
            Stat::make('User\'s Wallet Balance',SELF::CURRENCY_SIGN.number_format($total_users_balance,2))
                -> description('The sum of all users\' wallet balance')
                -> descriptionColor('primary')
                -> descriptionIcon('heroicon-m-user-plus')
            ,
            Stat::make('User\'s Cashback Balance',SELF::CURRENCY_SIGN.number_format($total_users_cashback_balance,2))
                -> description('The sum of all users\' cashback balance')
                -> descriptionColor('info')
                -> descriptionIcon('heroicon-m-user-plus')
            ,
            Stat::make('LatifData Users', User::count()) 
                -> chart([7, 1, 4, 30, 15, 4, 2])
                -> color('primary'),

             Stat::make('Total Staff', User::where('is_staff',true)->count())
                -> descriptionIcon('heroicon-m-arrow-up')
                -> chart([7, 1, 4, 30, 15, 4, 2])
                -> color('primary')
            ,

            // Expenses, Savings, Staff



            Stat::make('Expenses', SELF::CURRENCY_SIGN.number_format($total_company_expenses,2))
                -> description('This year\'s expenses')
                -> descriptionColor('info')
                -> descriptionIcon('heroicon-m-arrow-down')
            ,
            Stat::make('Savings', SELF::CURRENCY_SIGN.number_format($total_company_savings,2))
                -> description('This years\' savings')
                -> descriptionColor('success')
                -> descriptionIcon('heroicon-m-arrow-up')
                ,

           

          
        ];
    }
}
