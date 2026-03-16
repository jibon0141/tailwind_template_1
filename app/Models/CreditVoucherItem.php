<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditVoucherItem extends Model
{
    use HasFactory;

    protected $fillable=[
        'credit_voucher_id',
        'chart_of_account_id',
        'description',
        'paid_amount',
    ];

    public function creditVoucher(){
        return $this->belongsTo(CreditVoucher::class);
    }

    public function coa(){
        return $this->belongsTo(ChartOfAccount::class,'chart_of_account_id');
    }
}
