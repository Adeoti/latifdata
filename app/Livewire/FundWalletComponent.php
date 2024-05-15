<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PaymentIntegration;

class FundWalletComponent extends Component
{
 



    public function render()
    {

        $payment = PaymentIntegration::all()->first();


        return view('livewire.fund-wallet-component',[
            'manual_bank_name' => $payment->manual_bank_name,
            'manual_account_name' => $payment->manual_account_name,
            'manual_account_number' => $payment->manual_account_number,
        ]);
    }
}
