<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyCashFlow extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'description',
        'invoice_id',
        'dr_amount',
        'cr_amount',
        'balance',
        'account_id',
        'voucher_route',
        'voucher_id',
    ];

    protected $casts = [
        'date' => 'date',
        'dr_amount' => 'decimal:2',
        'cr_amount' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
