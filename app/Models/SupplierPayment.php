<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'supplier_name',
        'payment_voucher',
        'supplier_code',
        'payment_date',
        'phone',
        'account_id',
        'balance',
        'paying_amount',
        'refund_amount',
        'payment_status'
    ];


    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function account(){
        return $this->belongsTo(Account::class);
    }

    protected static function booted()
    {
        static::creating(function ($supplierPayment) {
            // temporary placeholder to satisfy DB
            $supplierPayment->payment_voucher = 'TEMP';
        });

        static::created(function ($supplierPayment) {
            $supplierPayment->payment_voucher = 'Spay-' . (1000 + $supplierPayment->id);
            $supplierPayment->save();
        });
    }

}
