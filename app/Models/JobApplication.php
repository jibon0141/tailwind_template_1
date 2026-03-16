<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'father_or_husband_name',
        'mother_name',
        'mobile',
        'email',
        'marital_status',
        'date_of_birth',
        'age',
        'nationality',
        'religion',
        'experience',
        'nid_no',
        'blood_group',
        'current_address',
        'permanent_address',
        'designation',
        'branch',
        'application_date',

    ];


}
