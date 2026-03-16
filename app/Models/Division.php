<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
    ];


    public function depo(){
        return $this->hasMany(Depo::class);
    }

    public function district()
    {
        return $this->hasMany(District::class, 'division_id');
    }

    public function employee(){
        return $this->hasMany(Employee::class, 'division_id','id');
    }
}
