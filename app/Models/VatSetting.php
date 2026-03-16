<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VatSetting extends Model
{
    use HasFactory;

    protected $fillable=[
        'depo_id',
        'vat_percentage',
        'status'
    ];

    public function depo(){
        return $this->belongsTo(Depo::class,'depo_id','id');
    }
}
