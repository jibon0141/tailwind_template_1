<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestorWithdraw extends Model
{
    use HasFactory;

    protected $fillable = [
        'investor_id',
        'investor_name',
        'withdraw_voucher',
        'investor_code',
        'payment_date',
        'phone',
        'account_id',
        'invest_amount',
        'withdraw_amount',
        'payment_status'
    ];

    public function investor(){
        return $this->belongsTo(Investor::class);
    }

    public function account(){
        return $this->belongsTo(Account::class);
    }

    protected static function booted()
    {
        static::creating(function ($investorWithdraw) {
            // temporary placeholder to satisfy DB
            $investorWithdraw->withdraw_voucher = 'TEMP';
        });

        static::created(function ($investorWithdraw) {
            $investorWithdraw->withdraw_voucher = 'Idraw-' . (1000 + $investorWithdraw->id);
            $investorWithdraw->save();
        });
    }
}
