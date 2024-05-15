<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MobileAirtime extends Model
{
    use HasFactory;



    protected $fillable = [
        'user_id',
        'network',
        'plan_type',
        'primary_price',
        'agent_price',
        'special_price',
        'api_price',
        'primary_cashback',
        'agent_cashback',
        'special_cashback',
        'api_cashback',
        'country_code',
        'api_code',
        'service_id',
        'endpoint',
        'vendor_name',
        'active_status',
        'minimum_amount',
        'maximum_amount',
    ];


    protected function casts(): array{
        return [
            'active_status'=>'boolean'
        ];
    }
}
