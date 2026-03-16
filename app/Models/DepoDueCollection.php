<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepoDueCollection extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'payment_voucher',
        'depo_id',
        'depo_name',
        'contact',
        'payment_date',
        'depo_account_id',
        'account_id',
        'balance',
        'receiving_amount',
        'payment_status',
        'document',
        'note',
        'status'
    ];

    public function depo()
    {
        return $this->belongsTo(Depo::class, 'depo_id');
    }

    public function companyAccount(){
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function depoAccount(){
        return $this->belongsTo(Account::class, 'depo_account_id');
    }

    protected static function booted()
    {
        static::creating(function ($depoPayment) {
            $depoPayment->payment_voucher = 'TEMP';
        });

        static::created(function ($depoPayment) {
            $depoPayment->payment_voucher = 'Dpay-' . (1000 + $depoPayment->id);
            $depoPayment->save();
        });
    }
}
