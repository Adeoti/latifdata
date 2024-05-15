<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;


    protected $fillable = [
        'title',
        'note',
        'amount',
        'dated',
        'user_id',
        'is_customer'
    ];

    public function user(): BelongsTo
    {
        return $this -> belongsTo(User::class);
    }


    protected function casts(): array
    {
        return [
            'is_customer' => 'boolean',
        ];
    }


}
