<?php

namespace App\Http\Controllers\Backend\Stock;
use App\Http\Controllers\Controller;
use App\Models\DistributeItem;
use App\Models\Medicine;
use App\Models\PurchaseItem;
use App\Models\SaleItem;
use App\Models\TempDistribute;
use App\Models\TempDistributeItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class StockController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = Medicine::with(['genericName', 'brand']);

            return DataTables::of($query)
                ->addIndexColumn()

                ->addColumn('medicine_name', fn ($m) => $m->medicine_name)
                ->addColumn('generic_name', fn ($m) => $m->genericName->generic_name ?? '-')
                ->addColumn('brand_name', fn ($m) => $m->brand->brand_name ?? '-')
                ->addColumn('buying_price', fn ($m) => number_format($m->purchase_price, 2))
                ->addColumn('selling_price', fn ($m) => number_format($m->sale_price, 2))

                ->addColumn('total_purchase', fn ($m) =>
                PurchaseItem::where('medicine_id', $m->id)->sum('quantity')
                )

                ->addColumn('purchase_free_quantity', fn ($m) =>
                PurchaseItem::where('medicine_id', $m->id)->sum('free_quantity')
                )

                ->addColumn('total_sale', function ($m) {
                    $distribute = DistributeItem::where('medicine_id', $m->id)->sum('quantity');

                    $temp = TempDistributeItem::where('medicine_id', $m->id)
                        ->whereHas('tempDistribute', fn ($q) => $q->where('order_status', 1))
                        ->sum('quantity');

                    return $distribute + $temp;
                })

                ->addColumn('sale_free_quantity', function ($m) {
                    $distribute = DistributeItem::where('medicine_id', $m->id)->sum('free_quantity');

                    $temp = TempDistributeItem::where('medicine_id', $m->id)
                        ->whereHas('tempDistribute', fn ($q) => $q->where('order_status', 1))
                        ->sum('free_quantity');

                    return $distribute + $temp;
                })

                ->addColumn('current_stock', function ($m) {
                    $purchased = PurchaseItem::where('medicine_id', $m->id)
                        ->sum(DB::raw('quantity + free_quantity'));

                    $sold = DistributeItem::where('medicine_id', $m->id)
                        ->sum(DB::raw('quantity + free_quantity'));

                    $temp = TempDistributeItem::where('medicine_id', $m->id)
                        ->whereHas('tempDistribute', fn ($q) => $q->where('order_status', 1))
                        ->sum(DB::raw('quantity + free_quantity'));

                    return $purchased - ($sold + $temp);
                })

                ->addColumn('stock_value', function ($m) {
                    $purchased = PurchaseItem::where('medicine_id', $m->id)
                        ->sum(DB::raw('quantity + free_quantity'));

                    $sold = DistributeItem::where('medicine_id', $m->id)
                        ->sum(DB::raw('quantity + free_quantity'));

                    $temp = TempDistributeItem::where('medicine_id', $m->id)
                        ->whereHas('tempDistribute', fn($q) => $q->where('order_status', 1))
                        ->sum(DB::raw('quantity + free_quantity'));

                    $currentStock = $purchased - ($sold + $temp);

                    return number_format($currentStock * $m->sale_price, 2);
                })


                ->filter(function ($query) use ($request) {

                    if ($request->search['value'] ?? false) {

                        $search = $request->search['value'];

                        $query->where(function ($q) use ($search) {

                            $q->where('medicine_name', 'like', "%{$search}%")

                                ->orWhereHas('genericName', function ($q) use ($search) {
                                    $q->where('generic_name', 'like', "%{$search}%");
                                })

                                ->orWhereHas('brand', function ($q) use ($search) {
                                    $q->where('brand_name', 'like', "%{$search}%");
                                })

                                ->orWhere('purchase_price', 'like', "%{$search}%")
                                ->orWhere('sale_price', 'like', "%{$search}%");
                        });
                    }
                })

                ->make(true);
        }

        return view('admin.extends.stock.index');
    }


}
