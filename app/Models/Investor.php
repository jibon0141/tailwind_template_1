<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'investor_code',
        'email',
        'contact',
        'nid',
        'nid_front',
        'nid_back',
        'bank_details',
        'address',
        'invest_amount',
        'opening_balance',
        'nominee_name',
        'nominee_relation',
        'nominee_address',
        'nominee_contact',
        'nominee_bank_details',
        'nominee_nid',
        'nominee_nid_front',
        'nominee_nid_back',
        'status',
    ];

    protected $casts = [
        'payment_date' => 'date',
    ];

    protected static function booted()
    {
        // Step 1: Before creating, assign a temporary placeholder
        static::creating(function ($investor) {
            $investor->investor_code = 'TEMP';
        });

        // Step 2: After created, assign the final voucher using ID
        static::created(function ($investor) {
            $investor->investor_code = 'IC-' . (1000 + $investor->id);
            $investor->save(); // avoids firing events again
        });
    }
}
