<?php

namespace App\Http\Controllers\Employee\Mpo\Stock;

use App\Http\Controllers\Controller;
use App\Models\DistributeItem;
use App\Models\Employee;
use App\Models\Medicine;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class StockController extends Controller
{
    public function index(Request $request){

        $userId=!empty(Session::get('userObj')) ? Session::get('userObj')->id : Auth::user()->id;
        $depoId=Employee::where('user_id',$userId)->first()->depo_id;
        if($request->ajax()){

            $medicines= Medicine::with(['genericName','brand'])
                ->whereHas('distributeItems.distribute',function ($q) use ($depoId){
                    $q->where('depo_id',$depoId);
                })->get();
            return datatables::of($medicines)
                ->addIndexColumn()
                ->addColumn('medicine_name', function ($row) {
                    return $row->medicine_name ?? 'N/A';
                })
                ->addColumn('generic_name', function ($row) {
                    return $row->genericName->generic_name ?? 'N/A';
                })
                ->addColumn('brand_name', function ($row) {
                    return $row->brand->brand_name ?? 'N/A';
                })
                ->addColumn('current_stock', function ($m) use ($depoId) {

                    $purchased = DistributeItem::where('medicine_id', $m->id)
                        ->whereHas('distribute', fn ($q) => $q->where('depo_id', $depoId))
                        ->sum(DB::raw('quantity + free_quantity'));

                    $sold = SaleItem::where('medicine_id', $m->id)
                        ->whereHas('sale', fn ($q) => $q->where('depo_id', $depoId))
                        ->sum(DB::raw('quantity + free_quantity'));

                    return $purchased - $sold;
                })
                ->make(true);
        }
        return view('employee.mpo.extends.stock.index');
    }

}
