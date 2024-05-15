<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customers extends Model
{
    use HasFactory;


    protected $fillable = [

    ];

    protected $hidden = [
        'password'
    ];
    protected function casts(): array
    {
        return [
            'active_status' => 'boolean',
            'package' => 'array',
            'profile_image' => 'array',
            'password' => 'hashed'
        ];
    }
}
