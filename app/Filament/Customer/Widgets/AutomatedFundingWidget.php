<?php

namespace App\Filament\Customer\Widgets;

use App\Models\PaymentIntegration;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AutomatedFundingWidget extends BaseWidget
{
    protected function getStats(): array
    {

        $payMessage = 'Deposit to this account number for instant wallet credit.';
        $automated_charges = PaymentIntegration::first()->automated_charges;
        $ngn = "₦";






            if(auth()->user()->has_accounts){



                $accountsJson = auth()->user()->accounts;

            // Decode the JSON data into an associative array
            $accountsArray = json_decode($accountsJson, true);

            // Check if decoding was successful
            if ($accountsArray !== null) {
                $stats = [];
            
                // Loop through each account in the array
                foreach ($accountsArray as $account) {
                    // Access individual account details
                    $bankName = $account['bankName'];
                    $accountNumber = $account['accountNumber'];
                    $accountName = $account['accountName'];
            
                    // Construct the Stat object and add it to the $stats array
                    $stats[] = Stat::make($bankName, $accountNumber)
                        ->description($payMessage . " | Charges = $ngn" . $automated_charges)
                        ->descriptionColor('primary');
                }
            
                // Return the array of Stat objects
                return $stats;
            }else {
                // Handle the case where JSON decoding failed
            }

              
            }else{
                return [
                    Stat::make('Generate automated accounts','Fill KYC')
                        ->description('Go to your profile section to fill your KYC form in order to generate your automated account numbers. Your account numbers will show here after the exercise.')
                ];
            }

       
    }
}
