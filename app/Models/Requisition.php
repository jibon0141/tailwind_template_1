<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requisition extends Model
{
    use HasFactory;

    protected $fillable = [
        'requisition_voucher',
        'company_name',
        'requisition_date',
        'final_total',
    ];

    protected $casts = [
        'requisition_date' => 'date',
    ];

    public function requisitionItems(){
        return $this->hasMany(RequisitionItem::class);
    }

    protected static function booted()
    {
        // Before creating, set a temporary code
        static::creating(function ($requisition) {
            $requisition->requisition_voucher = 'TEMP';
        });

        // After created, generate final code
        static::created(function ($requisition) {
            $requisition->requisition_voucher = 'REQ-' . (1000 + $requisition->id);
            $requisition->save();
        });
    }
}
