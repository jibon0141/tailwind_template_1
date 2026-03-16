<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineDosageForm extends Model
{
    use HasFactory;

    protected $table = 'medicine_dosage_forms';

    protected $fillable = [
        'dosage_name',
        'dosage_description',
        'status',
    ];

    public function strength(){
        return $this->hasMany(Strength::class,'medicine_dosage_form_id','id');
    }
    public function medicine(){
        return $this->hasMany(Medicine::class,'medicine_dosage_form_id','id');
    }
}
