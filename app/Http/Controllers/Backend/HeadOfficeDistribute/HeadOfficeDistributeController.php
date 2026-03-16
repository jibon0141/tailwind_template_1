<?php

namespace App\Http\Controllers\Backend\HeadOfficeDistribute;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\CompanySetting;
use App\Models\Depo;
use App\Models\DepoDueAccount;
use App\Models\Distribute;
use App\Models\DistributeItem;
use App\Models\Medicine;
use App\Models\PurchaseItem;
use App\Models\TempDistributeItem;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class HeadOfficeDistributeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $distributes = Distribute::with('depo')->orderBy('id', 'desc');

            // Filter by date range
            if ($request->filled('start_date')) {
                $distributes->where('distribute_date', '>=', Carbon::parse($request->start_date)->startOfDay());
            }
            if ($request->filled('end_date')) {
                $distributes->where('distribute_date', '<=', Carbon::parse($request->end_date)->endOfDay());
            }

            // Filter by order status
            if ($request->filled('order_status')) {
                $distributes->where('order_status', $request->order_status);
            }

            $distributes = $distributes->get();

            return datatables()->of($distributes)
                ->addIndexColumn()
                ->addColumn('distribute_voucher', fn($row) => $row->distribute_voucher ?? 'N/A')
                ->addColumn('depo_name', fn($row) => $row->depo->depo_name ?? 'N/A')
                ->addColumn('distribute_date', function ($row) {
                    return \Carbon\Carbon::parse($row->distribute_date)->format('d-m-Y');
                })
                ->addColumn('final_total', fn($row) => $row->final_total ?? 'N/A')
                ->addColumn('order_status', function($row) {
                    return match($row->order_status) {
                        1 => '<span class="px-2 py-1 bg-yellow-200 text-yellow-800 rounded-full text-xs font-semibold">Pending</span>',
                        2 => '<span class="px-2 py-1 bg-green-200 text-green-800 rounded-full text-xs font-semibold">Approved</span>',
                        3 => '<span class="px-2 py-1 bg-blue-200 text-blue-800 rounded-full text-xs font-semibold">Delivered</span>',
                        default => '<span class="px-2 py-1 bg-gray-200 text-gray-800 rounded-full text-xs font-semibold">N/A</span>',
                    };
                })
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('admin.distribute.show', $row->id) . '" class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded">
            <i class="fa fa-eye"></i></a>';
                })
                ->rawColumns(['order_status','action'])
                ->make(true);
        }

        return view('admin.extends.distribute.index');
    }


    public function create(Request $request)
    {
        if ($request->isMethod('POST')) {


            $validated = $request->validate([
                'depo_id' => 'required|exists:depos,id',
                'distribute_date' => 'required|date',
                'items' => 'required|array|min:1',
                'items.*.medicine_id' => 'required|exists:medicines,id',
                'items.*.unit_cost' => 'required|numeric|min:0',
                'items.*.quantity' => 'required|numeric|min:1',
                'items.*.free_quantity' => 'nullable|numeric|min:0',
                'discount' => 'nullable|numeric|min:0',
                'vat' => 'nullable|numeric|min:0',
                'previous_due' => 'nullable|numeric',
                'receivable_amount' => 'nullable|numeric',
            ]);


            try {
                DB::beginTransaction();


                // Total Calculation
                $total = 0;
                foreach ($request->items as $item) {
                    $total += $item['unit_cost'] * $item['quantity'];
                }

                $discount = $request->discount ?? 0;
                $vatPercent = $request->vat ?? 0;
                $previousDue = $request->previous_due ?? 0;

                $vatAmount = ($total * $vatPercent) / 100;
                $finalTotal = $total - $discount + $vatAmount;



                // Create Distribute
                $distribute = Distribute::Create([
                    'depo_id' => $request->depo_id,
                    'distribute_date' => $request->distribute_date,
                    'total' => $total,
                    'discount' => $discount,
                    'vat' => $vatPercent,
                    'previous_due' => $previousDue,
                    'final_total' => $finalTotal,
                    'receivable_amount' => $request->receivable_amount,
                    'payment_status' =>  2,
                    'order_status'  => 1,
                ]);


                // Distribute Items
                $items = [];
                foreach ($request->items as $item) {
                    $items[] = [
                        'distribute_id' => $distribute->id,
                        'medicine_id' => $item['medicine_id'],
                        'unit_cost' => $item['unit_cost'],
                        'quantity' => $item['quantity'],
                        'free_quantity' => $item['free_quantity'] ?? 0,
                        'sub_total' => $item['unit_cost'] * $item['quantity'],
                        'created_at' => now(),
                    ];
                }

                DistributeItem::insert($items);


                $depo = DepoDueAccount::where('depo_id',$request->depo_id)->first();
                $depo->due_balance +=$finalTotal ;
                $depo->save();


                DB::commit();
                Log::info('Distribute created successfully');
                return back()->with('success', 'Distribution completed successfully');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e->getMessage());
                return back()->with('error', 'Distribution Failed.');
            }
        }

        return view('admin.extends.distribute.create');
    }


    public function show($id){

        try{
            $distribute = Distribute::with('items.medicine', 'depo', 'account')->where('id',$id)->first();
            $mainCompany= CompanySetting::first();

            if(empty($distribute)){
                Log::error('Distribute not found');
                return redirect()->back()->with('error', 'Distribute not found');
            }

            return view('admin.extends.distribute.show', compact('distribute','mainCompany'));
        }
        catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Distribute Show Failed!');
        }

    }

    public function print($id)
    {
        try{
            $distribute = Distribute::with('items.medicine', 'depo', 'account')->where('id',$id)->first();
            $mainCompany= CompanySetting::first();

            if(empty($distribute)){
                Log::error('Distribute not found');
                return redirect()->back()->with('error', 'Distribute not found');
            }

            return view('admin.print.distribute_voucher_print', compact('distribute','mainCompany'));
        }
        catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Distribute Show Failed!');
        }

    } public function posPrint($id)
    {
        try{
            $distribute = Distribute::with('items.medicine', 'depo', 'account')->where('id',$id)->first();
            $mainCompany= CompanySetting::first();

            if(empty($distribute)){
                Log::error('Distribute not found');
                return redirect()->back()->with('error', 'Distribute not found');
            }

            return view('admin.print.distribute_voucher_pos', compact('distribute','mainCompany'));
        }
        catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Distribute Show Failed!');
        }

    }


    public function getDistributeData(Request $request)
    {
        $search = $request->query('q');


        $query = Depo::with('depoDueAccount')
            ->select('id', 'Depo_name', 'contact');

        if ($search) {
            $query->where('Depo_name', 'like', "%{$search}%")
                ->orWhere('contact', 'like', "%{$search}%");
        }

        $depos = $query->get()->map(function($depo) {
            // Add the due_balance as receivable_amount (same style as your Chemist example)
            $depo->receivable_amount = $depo->depoDueAccount->due_balance ?? 0;
            return $depo;
        });



        return response()->json([
            'depos' => $depos
        ]);
    }



    public function searchMedicine(Request $request)
    {
        $medicines = Medicine::where('medicine_name', 'like', '%' . $request->q . '%')
            ->whereHas('purchaseItems.purchase', function ($query) {
                $query->where('user_id', 1); // keep as you used
            })
            ->select('id', 'medicine_name', 'purchase_price')
            ->limit(10)
            ->get();

        foreach ($medicines as $m) {

            $purchased = PurchaseItem::where('medicine_id', $m->id)
                ->sum(DB::raw('quantity + free_quantity'));

            $sold = DistributeItem::where('medicine_id', $m->id)
                ->sum(DB::raw('quantity + free_quantity'));

            $temp = TempDistributeItem::where('medicine_id', $m->id)
                ->whereHas('tempDistribute', function ($q) {
                    $q->where('order_status', 1);
                })
                ->sum(DB::raw('quantity + free_quantity'));

            $m->current_stock = $purchased - ($sold + $temp);
        }

        return response()->json($medicines);
    }



}
