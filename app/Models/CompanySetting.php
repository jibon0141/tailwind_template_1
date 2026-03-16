<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'email',
        'phone',
        'address',
        'logo',
        'favicon',
        'website_url',
    ];


    public function mainAccounts(){
        return $this->hasMany(MainAccount::class,'company_setting_id');
    }


}
