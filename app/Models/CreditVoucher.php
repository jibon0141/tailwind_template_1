<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditVoucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'credit_voucher',
        'party_id',
        'account_id',
        'payment_date',
        'total_amount'
    ];

    protected $casts = ['payment_date' => 'date'];

    public function items(){
        return $this->hasMany(CreditVoucherItem::class);
    }

    public function party()
    {
        return $this->belongsTo(Party::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    protected static function booted()
    {
        // Step 1: Before creating, assign a temp placeholder
        static::creating(function ($creditVoucher) {
            $creditVoucher->credit_voucher = 'TEMP';
        });

        // Step 2: After created, assign final voucher based on ID
        static::created(function ($creditVoucher) {
            $creditVoucher->credit_voucher = 'CV-' . (1000 + $creditVoucher->id);
            $creditVoucher->save();
        });
    }






}
