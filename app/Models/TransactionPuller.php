<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionPuller extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'transaction_key',
        'status',
        'title',
        'message',
    ];
}
