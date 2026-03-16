<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlAccount extends Model
{
    use HasFactory;

    protected $fillable = ['account_name'];

    public function chartOfAccounts()
    {
        return $this->hasMany(ChartOfAccount::class, 'gl_account_id','id');
    }
}
