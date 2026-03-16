<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Party extends Model
{
    use HasFactory;

    protected $fillable = [
        'party_code',
        'user_id',
        'party_name',
        'email',
        'phone',
        'address',
    ];

    public function debitVoucher(){
        return $this->hasMany(DebitVoucher::class);
    }

    protected static function booted()
    {
        // Before insert (ID not available yet)
        static::creating(function ($party) {
            $party->party_code = 'TEMP';
        });

        // After insert (ID is available)
        static::created(function ($party) {
            $party->party_code = 'party-' . (1000 + $party->id);
            $party->save();
        });
    }


}
