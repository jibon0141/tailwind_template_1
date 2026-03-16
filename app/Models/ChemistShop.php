<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChemistShop extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_name',
        'owner_name',
        'shop_type',
        'depo_id',
        'mpo_id',
        'status',
        'bank_name',
        'account_number',
        'contact',
        'address',
    ];
}
