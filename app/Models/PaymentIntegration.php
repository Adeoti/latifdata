<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentIntegration extends Model
{
    use HasFactory;

    protected $fillable = [
        'monnify_api_key',
        'monnify_secret_key',
        'monnify_contract_code',
        'monnify_bvn',
        'automated_charges',

        'paystack_secret_key',
        'paystack_live_key',

        'manual_bank_name',
        'manual_account_name',
        'manual_account_number',

    ];
}
