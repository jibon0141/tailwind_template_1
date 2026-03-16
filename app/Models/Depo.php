<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Depo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'depo_name',
        'person_name',
        'area_code',
        'email',
        'contact',
        'division_id',
        'district_id',
        'account_no',
        'address',
        'status',
    ];

    public function depoDueAccount(){
        return $this->hasOne(DepoDueAccount::class);
    }

    public function DepoDueCollections(){
        return $this->hasMany(DepoDueCollection::class, 'depo_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function account(){
        return $this->hasMany(Account::class,'depo_id','id');
    }

    public function division(){
        return $this->belongsTo(Division::class);
    }

    public function district(){
        return $this->belongsTo(District::class);
    }

    public function companySetting()
    {
        return $this->belongsTo(CompanySetting::class);
    }

    public function vatSetting(){
        return $this->belongsTo(VatSetting::class,'depo_id','id');
    }

    // Relationship with mpo in employee table
    public function mpo(){
        return $this->hasMany(Employee::class,'depo_id','id')
            ->where('employee_type','mpo');

    }

    public function distributes()
    {
        return $this->hasMany(Distribute::class, 'depo_id', 'id');
    }

}
