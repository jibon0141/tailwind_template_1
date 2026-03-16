<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DebitVoucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','debit_voucher','party_id', 'account_id', 'payment_date', 'total_amount'
    ];

    protected $casts = ['payment_date' => 'date'];

    public function party()
    {
        return $this->belongsTo(Party::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class,'account_id','id');
    }

    public function user(){
        return $this -> belongsTo(User::class,'user_id','id');
    }

    public function chartOfAccount()
    {
        return $this->belongsTo(ChartOfAccount::class);
    }

    public function items()
    {
        return $this->hasMany(DebitVoucherItem::class);
    }

    protected static function booted()
    {
        // Step 1: Before creating, assign a temporary placeholder
        static::creating(function ($voucher) {
            $voucher->debit_voucher = 'TEMP';
        });

        // Step 2: After created, assign the final voucher using ID
        static::created(function ($voucher) {
            $voucher->debit_voucher = 'DV-' . (1000 + $voucher->id);
            $voucher->save(); // avoids firing events again
        });
    }





}
