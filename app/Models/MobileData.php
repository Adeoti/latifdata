<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MobileData extends Model
{
    use HasFactory;


    protected $fillable = [
        'network',
        'plan_size',
        'plan_type',
        'validity',
        'user_id',

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
    ];


    protected function casts(): array{
        return [
            'active_status' => 'boolean'
        ];
    }

}
