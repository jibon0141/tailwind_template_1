<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChemistHouseDuePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'chemist_house_id',
        'chemist_house_name',
        'payment_voucher',
        'payment_date',
        'contact',
        'account_id',
        'balance',
        'receiving_amount',
        'payment_status',
        'document',
        'note'
    ];

    public function chemistHouse()
    {
        return $this->belongsTo(ChemistHouse::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    protected static function booted()
    {
        static::creating(function ($chemistHousePayment) {
            $chemistHousePayment->payment_voucher = 'TEMP';
        });

        static::created(function ($chemistHousePayment) {
            $chemistHousePayment->payment_voucher = 'CHpay-' . (1000 + $chemistHousePayment->id);
            $chemistHousePayment->save();
        });
    }
}
