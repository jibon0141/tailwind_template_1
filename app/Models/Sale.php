<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable=[

        'sale_voucher',
        'sale_date',
        'delivery_date',
        'user_id',
        'depo_id',
        'mpo_id',
        'chemist_house_id',
        'total',
        'discount',
        'vat',
        'previous_due',
        'final_total',
        'given_amount',
        'receivable_amount',
        'payment_status',
        'order_status',

    ];


    public function mpo()
    {
        return $this->belongsTo(Employee::class, 'mpo_id')
            ->where('employee_type', 'mpo');
    }

    public function chemistHouse(){
        return $this->belongsTo(ChemistHouse::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function depo(){
        return $this->belongsTo(Depo::class);
    }

//    protected static function booted()
//    {
//        static::created(function ($sale) {
//            $sale->sale_voucher = 'SAL-' .  (1000 + $sale->id);
//            $sale->save();
//        });
//    }


    protected static function booted()
    {
        // Temporary value before insert
        static::creating(function ($sale) {
            $sale->sale_voucher = 'TEMP';
        });

        // Set final voucher after insert
        static::created(function ($sale) {
            $sale->sale_voucher = 'SAL-' . (1000 + $sale->id);
            $sale->save();
        });
    }


}
