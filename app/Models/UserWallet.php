<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWallet extends Model
{
    use HasFactory;

    protected $fillable = ['id','user_id','commission_balance','return_balance','total_balance'];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
