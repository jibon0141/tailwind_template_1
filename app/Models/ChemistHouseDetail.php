<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChemistHouseDetail extends Model
{
    use HasFactory;

    protected $casts = [
        'drug_license_expire_date' => 'date:Y-m-d',
        'trade_license_expire_date' => 'date:Y-m-d',
    ];

    protected $fillable = [
        'chemist_house_id',
        'drug_license_number','drug_license_expire_date','drug_license_image',
        'trade_license','trade_license_expire_date','trade_license_image','tin_number'
    ];

    public function chemistHouse()
    {
        return $this->belongsTo(ChemistHouse::class,'chemist_house_id','id');
    }

}
