<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempDistributeItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'temp_distribute_id',
        'medicine_id',
        'unit_cost',
        'quantity',
        'free_quantity',
        'sub_total',
    ];

    public function distribute()
    {
        return $this->belongsTo(TempDistribute::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    public function tempDistribute(){
        return $this->belongsTo(TempDistribute::class);
    }

}
