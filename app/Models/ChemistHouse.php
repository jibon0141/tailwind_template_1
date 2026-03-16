<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChemistHouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_name',
        'owner_name',
        'email',
        'user_id',
        'depo_id',
        'mpo_id',
        'status',
        'bank_name',
        'account_number',
        'contact',
        'whatsapp',
        'address',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function depo(){
        return $this->belongsTo(Depo::class);
    }

    public function mpo()
    {
        return $this->belongsTo(Employee::class, 'mpo_id', 'user_id');
    }


    public function sales(){
        return $this->hasMany(Sale::class);
    }


    public function chemistHouseDetail()
    {
        return $this->hasOne(ChemistHouseDetail::class,'chemist_house_id','id');
    }

    public function chemistHouseDueAccount(){
        return $this->hasOne(ChemistHouseDueAccount::class,'chemist_house_id','id');
    }

//    public function mpo(){
//        return $this->belongsTo(Mpo::class);
//    }
}
