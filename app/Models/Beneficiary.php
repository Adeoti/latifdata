<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'number',
        'network',
        'pinned',
    ];

    protected function casts(): array{
        return [
            'pinned'=>'boolean'
        ];
    }
}
