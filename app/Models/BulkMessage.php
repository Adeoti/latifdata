<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BulkMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'graphics',
        'target',
        'user_id',
        'style'
    ];


    public function user(): BelongsTo{
        return $this -> belongsTo(User::class);
    }
}
