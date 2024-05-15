<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'message',
        'is_active',
        'style'
        
    ];
    
    public function user(): BelongsTo{
        return $this -> belongsTo(User::class);
    }


   

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'style' => 'array'
        ];
    }
}
