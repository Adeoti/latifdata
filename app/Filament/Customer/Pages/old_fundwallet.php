<?php

namespace App\Filament\Customer\Widgets;

use App\Models\PaymentIntegration;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AutomatedFundingWidgeut extends BaseWidget
{
    protected function getStats(): array
    {

        $payMessage = 'Deposit to this account number for instant wallet credit.';
        $monniepoint = "";
        $wema = "";
        $sterling = "";
        $automated_charges = PaymentIntegration::first()->automated_charges;
        $ngn = "₦";
        

        if(!empty(auth()->user()->monniepoint_acct)){
            $monniepoint = auth()->user()->monniepoint_acct;
        }else{
            $monniepoint = "Not available now ...";
        }

        if(!empty(auth()->user()->wema_acct)){
            $wema = auth()->user()->wema_acct;
        }else{
            $wema = "Not available now ...";
        }

        if(!empty(auth()->user()->sterling_acct)){
            $sterling = auth()->user()->wema_acct;
        }else{
            $sterling = "Not available now ...";
        }






                        // Assuming $user contains the user model instance

            // Retrieve the accounts JSON from the database column
            $accountsJson = auth()->user()->accounts;

            // Decode the JSON data into an associative array
            $accountsArray = json_decode($accountsJson, true);

            // Check if decoding was successful
            if ($accountsArray !== null) {
                // Loop through each account in the array
                foreach ($accountsArray as $account) {
                    // Access individual account details
                    $bankName = $account['bankName'];
                    $accountNumber = $account['accountNumber'];
                    $accountName = $account['accountName'];

                    // Handle the account details as needed...
                    Stat::make($bankName,$accountNumber)
                        ->description($payMessage." | Charges = $ngn".$automated_charges)
                        ->descriptionColor('primary');
                }
            } else {
                // Handle the case where JSON decoding failed
            }










            if(auth()->user()->has_accounts){
                return [
                    //
                    Stat::make('Monniepoint',$monniepoint)
                        ->description($payMessage." | Charges = $ngn".$automated_charges)
                        ->descriptionColor('primary')
                        
                    ,
                    Stat::make('Wema',$wema)
                    ->description($payMessage." | Charges = $ngn".$automated_charges)
                    ->descriptionColor('primary')

                    ,
                    Stat::make('Sterling',$sterling)
                        ->description($payMessage." | Charges = $ngn".$automated_charges)
                        ->descriptionColor('primary')
                    
                    ,
        
        
                ];
            }else{
                return [
                    Stat::make('Generate automated accounts','Fill KYC')
                        ->description('Go to your profile section to fill your KYC form in order to generate your automated account numbers. Your account numbers will show here after the exercise.')
                ];
            }

       
    }
}
