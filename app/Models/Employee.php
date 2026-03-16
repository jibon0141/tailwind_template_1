<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_code',
        'full_name',
        'email',
        'phone',
        'division_id',
        'district_id',
        'address',
        'employee_type',
        'depo_id',
        'parent_id',
    ];

    public function chemistHouse(){
        return $this->hasOne(ChemistHouse::class,'mpo_id', 'id');
    }

    // Parent employee
    public function parent()
    {
        return $this->belongsTo(Employee::class, 'parent_id');
    }

    // Children employees
    public function children()
    {
        return $this->hasMany(Employee::class, 'parent_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }


    public function depo()
    {
        return $this->belongsTo(Depo::class);
    }

    // Helper method
    public function isMpo()
    {
        return $this->employee_type === 'mpo';
    }


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($employee) {
            if (!$employee->employee_code) {
                // Use first 3 letters of employee_type as prefix
                $prefix = strtoupper(substr($employee->employee_type, 0, 3));
                $last = self::where('employee_type', $employee->employee_type)
                    ->latest('id')
                    ->first();

                $number = $last ? intval(substr($last->employee_code, 4)) + 1 : 1;
                $employee->employee_code = $prefix . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
            }
        });
    }
}

