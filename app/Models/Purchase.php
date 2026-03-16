<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_voucher',
        'purchase_date',
        'user_id',
        'depo_id',
        'supplier_id',
        'account_id',
        'total',
        'discount',
        'vat',
        'advance',
        'previous_due',
        'final_total',
        'given_amount',
        'payable_amount',
        'payment_status',
        'purchased_by'
    ];


    protected static function booted()
    {
        static::creating(function ($purchase) {
            $purchase->purchase_voucher = 'TEMP';
        });

        static::created(function ($purchase) {
            $purchase->purchase_voucher = 'PUR-' . (1000 + $purchase->id);
            $purchase->save();
        });
    }



    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

}
