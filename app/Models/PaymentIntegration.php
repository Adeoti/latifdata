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

        'sweetbill_api_key',
        'sweetbill_email',
        'sweetbill_password',

        'monnify_base_url',
        'vtpass_api_key',
        'vtpass_public_key',
        'vtpass_secret_key',

        'paystack_secret_key',
        'paystack_live_key',

        'manual_bank_name',
        'manual_account_name',
        'manual_account_number',

    ];
}
