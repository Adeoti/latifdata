<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectricityIntegration extends Model
{
    use HasFactory;
    protected $fillable = [
        
        'user_id',
        'primary_charges',
        'agent_charges',
        'special_charges',
        'api_charges',

        'vendor_name',
        'active_status',
    ];

    protected function casts(): array{
        return [
            'active_status'=>'boolean'
        ];
    }
}
