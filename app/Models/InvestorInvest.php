<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestorInvest extends Model
{
    use HasFactory;

    protected $fillable = [
        'investor_id',
        'investor_name',
        'invest_voucher',
        'investor_code',
        'payment_date',
        'phone',
        'account_id',
        'invest_amount',
        'investing_amount',
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
        static::creating(function ($InvestorInvest) {
            // temporary placeholder to satisfy DB
            $InvestorInvest->invest_voucher = 'TEMP';
        });

        static::created(function ($InvestorInvest) {
            $InvestorInvest->invest_voucher = 'Iinv-' . (1000 + $InvestorInvest->id);
            $InvestorInvest->save();
        });
    }

}
