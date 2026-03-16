<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepoLedger extends Model
{
    use HasFactory;

    protected $fillable = [
        'depo_id',
        'date',
        'invoice_id',
        'purpose',
        'debit',
        'credit',
        'balance',
        'voucher_route',
        'voucher_id',
    ];

    protected $casts = [
        'date' => 'date',
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    public function depo()
    {
        return $this->belongsTo(Depo::class);
    }
}
