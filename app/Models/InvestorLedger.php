<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestorLedger extends Model
{
    use HasFactory;

    protected $fillable = [
        'investor_id',
        'date',
        'invoice_id',
        'purpose',
        'debit',
        'credit',
        'current_amount',
        'voucher_route',
        'voucher_id',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
        'invest_amount' => 'decimal:2',
    ];

    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }
}
