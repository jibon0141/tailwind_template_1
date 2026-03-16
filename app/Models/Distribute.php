<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Distribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'distribute_voucher',
        'distribute_date',
        'depo_id',
        'total',
        'discount',
        'vat',
        'advance',
        'previous_due',
        'final_total',
        'receivable_amount',
        'payment_status',
        'order_status'
    ];



    public function items(){
        return $this->hasMany(DistributeItem::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function depo()
    {
        return $this->belongsTo(Depo::class, 'depo_id', 'id');
    }

    protected static function booted()
    {
        static::creating(function ($distribute) {

            // If voucher already exists, do nothing
            if (!empty($distribute->distribute_voucher)) {
                return;
            }

            $lastVoucher = self::whereNotNull('distribute_voucher')
                ->orderBy('id', 'desc')
                ->first();

            if ($lastVoucher && preg_match('/\d+$/', $lastVoucher->distribute_voucher, $matches)) {
                $nextNumber = ((int) $matches[0]) + 1;
            } else {
                $nextNumber = 1001;
            }

            $distribute->distribute_voucher = 'DIV-' . $nextNumber;


        });
    }

}
