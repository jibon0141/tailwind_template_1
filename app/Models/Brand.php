<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand_name',
        'brand_description',
        'status',
    ];

    public function medicine(){
        return $this->hasMany(Medicine::class,'brand_id','id');
    }
}
