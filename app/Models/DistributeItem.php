<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributeItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'distribute_id',
        'medicine_id',
        'unit_cost',
        'quantity',
        'free_quantity',
        'sub_total',
    ];

    public function distribute()
    {
        return $this->belongsTo(Distribute::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

}
