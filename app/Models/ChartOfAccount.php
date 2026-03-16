<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model
{
    use HasFactory;

    protected $table = 'chart_of_accounts';

    protected $fillable = ['gl_account_id', 'head_type', 'head_name', 'status', 'user_id'];

    protected $casts = ['status' => 'boolean'];

    public function glAccount()
    {
        return $this->belongsTo(GlAccount::class, 'gl_account_id','id');
    }

    public function debitVoucherItems(){
        return $this->hasMany(DebitVoucherItem::class);
    }

    public function creditVoucherItems(){
        return $this->hasMany(CreditVoucherItem::class);

    }
}
