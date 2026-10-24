<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNominee extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'email',
        'relation',
        'address',
        'image',
        'nid_front_image',
        'nid_back_image',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
