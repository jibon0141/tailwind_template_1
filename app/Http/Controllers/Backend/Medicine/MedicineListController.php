<?php

namespace App\Http\Controllers\Backend\Medicine;

use App\Http\Controllers\Controller;
use App\Models\PurchaseItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MedicineListController extends Controller
{

    public function index(Request $request){

        if($request->ajax()){

            $oneYearFromNow = Carbon::now()->addYear();

            $medicine= PurchaseItem::with([
                'medicine.company',
                'medicine.brand',
                'medicine.medicineCategory',
                'medicine.genericName',
                'medicine.dosageForm',
                'purchase'
            ])
                ->whereNotNull('expire_date')
                ->whereDate('expire_date', '<=', $oneYearFromNow) // below 1 year
                ->whereDate('expire_date', '>', Carbon::now())   // not expired
                ->latest();


            return Datatables::of($medicine->get())
                ->addIndexColumn()

                ->addColumn('medicine_name', function($row){
                    return $row->medicine->medicine_name ?? 'N/A';
                })
                ->addColumn('voucher_no',function($row){
                    return $row->purchase->purchase_voucher ?? 'N/A';
                })
                ->addColumn('generic_name',function($row){
                    return $row->medicine->genericName->generic_name ?? 'N/A';
                })
                ->addColumn('company_name', function($row){
                    return $row->medicine->company->company_name ?? 'N/A';
                })
                ->addColumn('brand_name', function($row){
                    return $row->medicine->brand->brand_name ?? 'N/A';
                })
                ->addColumn('category_name', function($row){
                    return $row->medicine->medicineCategory->category_name ?? 'N/A';
                })
                ->addColumn('dosage_form', function($row){
                    return $row->medicine->dosageForm->dosage_name ?? 'N/A';
                })
                ->addColumn('expire_date', function($row){
                    return $row->expire_date ?? 'N/A';
                })

                ->addColumn('status', function ($row) {

                    if (!$row->expire_date) {
                        return '<span class="px-2 py-1 text-xs rounded bg-gray-200 text-gray-700">
                    No Expiry
                </span>';
                    }

                    $expireDate = Carbon::parse($row->expire_date);
                    $oneYearFromNow = Carbon::now()->addYear();

                    // Expired
                    if ($expireDate->isPast()) {
                        return '<span class="px-2 py-1 text-xs rounded bg-red-600 text-white">
                    Expired
                </span>';
                    }

                    // Expiring within 1 year (Sell Early)
                    if ($expireDate->lessThanOrEqualTo($oneYearFromNow)) {
                        return '<span class="px-2 py-1 text-xs rounded bg-yellow-500 text-white">
                    Sell Early
                </span>';
                    }

                    // Safe
                    return '<span class="px-2 py-1 text-xs rounded bg-green-600 text-white">
                Safe
            </span>';
                })

                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('admin.extends.medicine_list.index');
    }

}
