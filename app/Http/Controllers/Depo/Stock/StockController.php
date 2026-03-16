<?php

namespace App\Http\Controllers\Depo\Stock;

use App\Http\Controllers\Controller;
use App\Models\Depo;
use App\Models\DistributeItem;
use App\Models\Medicine;
use App\Models\PurchaseItem;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class StockController extends Controller
{
    public function index(Request $request)
    {

        $userId=!empty(Session::get('userObj')) ? Session::get('userObj')->id : Auth::user()->id;


        $depo = Depo::where('user_id', $userId)->firstOrFail();
        $depoId = $depo->id;

        if ($request->ajax()) {

            // Only medicines that exist in this depo purchases
            $medicines = Medicine::with(['genericName', 'brand'])
                ->whereHas('distributeItems.distribute', function ($q) use ($depoId) {
                    $q->where('depo_id', $depoId);
                })
                ->get();

            return DataTables::of($medicines)
                ->addIndexColumn()

                ->addColumn('medicine_name', fn ($m) => $m->medicine_name)
                ->addColumn('generic_name', fn ($m) => $m->genericName->generic_name ?? '-')
                ->addColumn('brand_name', fn ($m) => $m->brand->brand_name ?? '-')
                ->addColumn('buying_price', fn ($m) => number_format($m->purchase_price, 2))
                ->addColumn('selling_price', fn ($m) => number_format($m->sale_price, 2))

                //  Total purchased (this depo)
                ->addColumn('total_purchase', function ($m) use ($depoId) {
                    return DistributeItem::where('medicine_id', $m->id)
                        ->whereHas('distribute', fn ($q) => $q->where('depo_id', $depoId))
                        ->sum('quantity');
                })

                //  Total free quantity (this depo)
                ->addColumn('purchase_free_quantity', function ($m) use ($depoId) {
                    return DistributeItem::where('medicine_id', $m->id)
                        ->whereHas('distribute', fn ($q) => $q->where('depo_id', $depoId))
                        ->sum('free_quantity');
                })

                //  Total sold (this depo)
                ->addColumn('total_sale', function ($m) use ($depoId) {
                    return SaleItem::where('medicine_id', $m->id)
                        ->whereHas('sale', fn ($q) => $q->where('depo_id', $depoId))
                        ->sum('quantity');
                })

                //  Total sold (this depo)
                ->addColumn('sale_free_quantity', function ($m) use ($depoId) {
                    return SaleItem::where('medicine_id', $m->id)
                        ->whereHas('sale', fn ($q) => $q->where('depo_id', $depoId))
                        ->sum('free_quantity');
                })

                //  Current stock = (purchase qty + free) - (sale qty + free)
                ->addColumn('current_stock', function ($m) use ($depoId) {

                    $purchased = DistributeItem::where('medicine_id', $m->id)
                        ->whereHas('distribute', fn ($q) => $q->where('depo_id', $depoId))
                        ->sum(DB::raw('quantity + free_quantity'));

                    $sold = SaleItem::where('medicine_id', $m->id)
                        ->whereHas('sale', fn ($q) => $q->where('depo_id', $depoId))
                        ->sum(DB::raw('quantity + free_quantity'));

                    return $purchased - $sold;
                })


                //  Stock value (based on purchase price)
                ->addColumn('stock_value', function ($m) use ($depoId) {
                    $purchased = DistributeItem::where('medicine_id', $m->id)
                        ->whereHas('distribute', fn ($q) => $q->where('depo_id', $depoId))
                        ->sum(DB::raw('quantity + free_quantity'));

                    $sold = SaleItem::where('medicine_id', $m->id)
                        ->whereHas('sale', fn ($q) => $q->where('depo_id', $depoId))
                        ->sum(DB::raw('quantity + free_quantity'));


                    $currentStock = $purchased - $sold;

                    return number_format($currentStock * $m->purchase_price, 2);
                })

                ->make(true);
        }

        return view('depo.extends.stock.index');
    }





}
