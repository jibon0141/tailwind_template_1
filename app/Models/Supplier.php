<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_name',
        'supplier_code',
        'company_id',
        'email',
        'phone',
        'balance',
        'opening_balance',
        'bank',
        'nid',
        'type',
        'voucher_address',
        'address',
    ];


//    public function medicine(){
//        return $this->hasMany(Medicine::class);
//    }



    public function company(){
        return $this->belongsTo(Company::class);
    }

    public function ledgers()
    {
        return $this->hasMany(SupplierLedger::class);
    }


    public function purchase(){
        return $this->hasMany(Purchase::class);
    }

    public function payment(){
        return $this->hasMany(SupplierPayment::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($supplier) {

            if (empty($supplier->supplier_code)) {

                $prefix = 'Sup-';


                $lastSupplier = static::where('supplier_code', 'like', $prefix.'%')
                    ->orderBy('id', 'desc')
                    ->first();

                if ($lastSupplier) {
                    $lastNumber = (int) str_replace($prefix, '', $lastSupplier->supplier_code);
                    $nextNumber = $lastNumber + 1;
                } else {
                    $nextNumber = 1001;
                }

                $supplier->supplier_code = $prefix . $nextNumber;
            }
        });
    }


}
