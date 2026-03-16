<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChemistHouseDueAccount extends Model
{
    use HasFactory;

    protected $fillable=[
        'chemist_house_id',
        'due_balance'
    ];

    public function chemistHouse(){
        return $this->belongsTo(ChemistHouse::class);
    }
}
