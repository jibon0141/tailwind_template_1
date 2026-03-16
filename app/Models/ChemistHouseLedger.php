<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChemistHouseLedger extends Model
{
    use HasFactory;

    protected $fillable = [
        'chemist_house_id',
        'date',
        'invoice_id',
        'purpose',
        'debit',
        'credit',
        'voucher_route',
        'voucher_id',
    ];

    protected $casts = [
        'date' => 'date',
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    public function chemistHouse()
    {
        return $this->belongsTo(ChemistHouse::class);
    }

}
