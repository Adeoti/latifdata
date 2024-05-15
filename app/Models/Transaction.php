<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;



    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }

    protected $fillable = [
            'type',
            'user_id',
            'api_response',
            'status',
            'note',
            'phone_number',
            'provider',
            'amount',
            'old_balance',
            'new_balance',
            'amount_paid',
            'cashback',
            'reference_number',
            'iuc_number',
            'plan_name',
            'token_pin',
            'customer_name',
            'customer_address',
            'network',
            'meter_type',
            'meter_number',
            'meter_name',
            'cable_plan',
            'operator_id',
            'charges',
            'token',
            'disco_name',
            'quantity'
    ];




}
