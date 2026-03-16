<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'chemist_shop_id',
        'drug_license_number',
        'drug_license_expire_date',
        'drug_license_image',
        'trade_license',
        'trade_license_expire_date',
        'trade_license_image',
        'tin_number',
    ];

}
