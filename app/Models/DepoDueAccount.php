<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepoDueAccount extends Model
{
    use HasFactory;

    public $fillable = [
        'depo_id',
        'due_balance'

    ];

    public function depo(){
        return $this->belongsTo(Depo::class);
    }
}
