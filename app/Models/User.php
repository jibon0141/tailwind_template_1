<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'user_id',
        'phone',
        'email',
        'user_type',
        'ref_id',
        'designation_id',
        'status',
        'password',
        'role'
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function designation(){
        return $this->belongsTo(Designation::class,'designation_id');
    }

    public function sells(){
        return $this->hasMany(Sell::class);
    }

    public function commissionDistributes(){
        return $this->hasMany(CommissionDistribute::class);
    }

    public function detail(){
        return $this->hasOne(UserDetail::class);
    }

    public function nominees(){
        return $this->hasMany(UserNominee::class);
    }

    public function paymentMethods(){
        return $this->hasMany(UserPaymentMethod::class);
    }

    public function permissions(){
        return $this->hasMany(AdminUserPermission::class);
    }


    protected static function booted()
    {
        static::creating(function ($user) {
            $lastUser = self::whereNotNull('user_id')
                ->orderByDesc('user_id')
                ->first();

            if ($lastUser) {
                $lastNumber = (int) substr($lastUser->user_id, 3);
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }

            $user->user_id = 'LDZ' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
        });
    }


}

