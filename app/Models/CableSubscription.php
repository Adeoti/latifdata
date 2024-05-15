<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CableSubscription extends Model
{
    use HasFactory;




    protected $fillable = [
        'user_id',
        'name',
        'plan_type',
        'price',

        'primary_charges',
        'agent_charges',
        'special_charges',
        'api_charges',

        'country_code',
        'api_code',
        'service_id',
        'endpoint',
        'vendor_name',
        'active_status',
    ];

    protected function casts(): array{
        return [
            'active_status'=>'boolean'
        ];
    }
}
