<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DebitVoucherItem extends Model
{
    protected $fillable = [
        'debit_voucher_id',
        'chart_of_account_id',
        'description',
        'paid_amount',
    ];

    public function voucher()
    {
        return $this->belongsTo(DebitVoucher::class, 'debit_voucher_id');
    }

    public function coa()
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id');
    }
}

