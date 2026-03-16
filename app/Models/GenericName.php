<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GenericName extends Model
{
    use HasFactory;

    protected $fillable = [
        'generic_name',
        'description',
        'status'
    ];

    public function medicines()
    {
        return $this->hasMany(Medicine::class, 'generic_name_id', 'id');
    }
}
