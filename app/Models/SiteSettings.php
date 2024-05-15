<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSettings extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'agent_charges',
        'special_charges',
        'api_charges',
        'portal_dev_charges',
        'wallet_to_charges',
        'refferal_commision',
        'refferal_status',
        'whatsapp_number',
        'cashbak_cap_amount',
        'referral_cap_amount',
        'default_theme'
    ];


    protected function casts(): array
    {
        return [
            'refferal_status' => 'boolean'
        ];
    }
}
