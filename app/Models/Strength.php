<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Strength extends Model
{
    use HasFactory;

    protected $fillable = [
        'strength_name',
        'medicine_dosage_form_id',
        'strength_description',
        'status',
    ];

    public function dosage()
    {
        return $this->belongsTo(MedicineDosageForm::class ,'medicine_dosage_form_id','id');
    }

    public function medicines()
    {
        return $this->hasMany(Medicine::class, 'strength_id', 'id');
    }


}


