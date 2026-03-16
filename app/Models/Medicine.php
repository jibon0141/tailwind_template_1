<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'medicine_name',
        'generic_name_id',
        'brand_id',
        'company_id',
        'medicine_category_id',
        'medicine_dosage_form_id',
        'strength_name',
        'mrp',
        'purchase_percentage',
        'purchase_price',
        'sale_percentage',
        'sale_price',
        'status',
    ];

    // One medicine can appear in many purchase items
    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function distributeItems(){
        return $this->hasMany(DistributeItem::class);
    }

    public function saleItems(){
        return $this->hasMany(SaleItem::class);
    }

//    public function supplier(){
//        return $this->belongsTo(Supplier::class);
//    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function medicineCategory()
    {
        return $this->belongsTo(MedicineCategory::class, 'medicine_category_id');
    }

    public function genericName()
    {
        return $this->belongsTo(GenericName::class, 'generic_name_id');
    }

    public function dosageForm()
    {
        return $this->belongsTo(MedicineDosageForm::class, 'medicine_dosage_form_id');
    }

    public function strength()
    {
        return $this->belongsTo(Strength::class, 'strength_id');
    }

    public function company(){
        return $this->belongsTo(Company::class, 'company_id');
    }
}
