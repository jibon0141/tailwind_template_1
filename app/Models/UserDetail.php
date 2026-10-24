<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDetail extends Model
{
    protected $fillable = [
        'user_id',
        'father_name',
        'mother_name',
        'age',
        'gender',
        'address',
        'nid',
        'image',
        'nid_front_image',
        'nid_back_image',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
