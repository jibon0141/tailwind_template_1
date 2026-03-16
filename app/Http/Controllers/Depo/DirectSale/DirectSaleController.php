<?php

namespace App\Http\Controllers\Depo\DirectSale;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\ChemistHouse;
use App\Models\ChemistHouseDueAccount;
use App\Models\ChemistHouseLedger;
use App\Models\CompanySetting;
use App\Models\Depo;
use App\Models\DepoCashFlow;
use App\Models\DistributeItem;
use App\Models\Employee;
use App\Models\Medicine;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class DirectSaleController extends Controller
{

    public function index(Request $request)
    {
        $userId = !empty(Session::get('userObj'))
            ? Session::get('userObj')->id
            : Auth::user()->id;

        $depoId = Depo::where('user_id', $userId)->first()->id;

        if ($request->ajax()) {

            // Start query
            $sales = Sale::with('chemistHouse.chemistHouseDueAccount', 'account')
                ->where('user_id', $userId)
                ->where('depo_id', $depoId)
                ->whereNull('mpo_id');

            // Date range filter
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $start = Carbon::parse($request->start_date)->format('Y-m-d');
                $end = Carbon::parse($request->end_date)->format('Y-m-d');
                $sales->whereBetween('sale_date', [$start, $end]);
            }

            // Order status filter
            if ($request->filled('order_status')) {
                $sales->where('order_status', $request->order_status);
            }


            $sales->orderByDesc('id');

            return datatables()->of($sales)
                ->addIndexColumn()
                ->addColumn('sale_voucher', fn($row) => $row->sale_voucher)
                ->addColumn('sale_date', fn($row) => Carbon::parse($row->sale_date)->format('d-m-Y'))
                ->addColumn('shop_name', fn($row) => $row->chemistHouse ? $row->chemistHouse->shop_name : 'N/A')
                ->addColumn('final_total', fn($row) => $row->final_total ?? 'N/A')
                ->addColumn('payment_status', function($row) {
                    return match($row->payment_status) {
                        1 => '<span class="px-2 py-1 bg-green-200 text-green-800 rounded-lg text-xs font-semibold">Paid</span>',
                        2 => '<span class="px-2 py-1 bg-red-200 text-red-800 rounded-lg text-xs font-semibold">Unpaid</span>',
                        3 => '<span class="px-2 py-1 bg-yellow-200 text-yellow-800 rounded-lg text-xs font-semibold">Partial</span>',
                        default => '<span class="px-2 py-1 bg-gray-200 text-gray-800 rounded-lg text-xs font-semibold">N/A</span>',
                    };
                })
                ->addColumn('order_status', function($row) {
                    return match($row->order_status) {
                        1 => '<span class="px-2 py-1 bg-yellow-200 text-yellow-800 rounded-lg text-xs font-semibold">Pending</span>',
                        2 => '<span class="px-2 py-1 bg-green-200 text-green-800 rounded-lg text-xs font-semibold">Approved</span>',
                        3 => '<span class="px-2 py-1 bg-red-200 text-red-800 rounded-lg text-xs font-semibold">Rejected</span>',
                        default => '<span class="px-2 py-1 bg-gray-200 text-gray-800 rounded-lg text-xs font-semibold">N/A</span>',
                    };
                })
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('depo.direct-sale.show', $row->id) . '" class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded">
                <i class="fa fa-eye"></i></a>';
                })
                ->rawColumns(['order_status', 'action', 'payment_status'])
                ->make(true);
        }

        return view('depo.extends.direct_sale.index');
    }


    public function create(Request $request)
    {
        $userId = !empty(Session::get('userObj'))
        ? Session::get('userObj')->id
        : Auth::user()->id;

        $depoId = Depo::where('user_id', $userId)->first()->id;


        if ($request->isMethod('POST')) {



            $rules = [
                'shop_id' => 'required|exists:chemist_houses,id',
                'sale_date' => 'required|date',
                'delivery_date' => 'nullable|date',
                'items' => 'required|array|min:1',
                'items.*.medicine_id' => 'required|exists:medicines,id',
                'items.*.unit_cost' => 'required|numeric|min:0',
                'items.*.mrp' => 'required|numeric|min:0',
                'items.*.medicine_discount' => 'required|numeric|min:0',
                'items.*.quantity' => 'required|numeric|min:1',
                'items.*.free_quantity' => 'nullable|numeric|min:0',
                'previous_due' => 'nullable|numeric|min:0',
                'given_amount' => 'nullable|numeric|min:0',
                'discount' => 'nullable|numeric|min:0',
                'vat' => 'nullable|numeric|min:0',
                'account_id' => 'required|exists:accounts,id',
            ];

            $validator = Validator::make($request->all(), $rules);

             // Custom validation for unit_cost vs actual cost & MRP
            $validator->after(function ($validator) use ($request) {
                foreach ($request->items as $index => $item) {
                    // Stock validation start
                    $qty  = floatval($item['quantity'] ?? 0);
                    $free = floatval($item['free_quantity'] ?? 0);
                    $stock = floatval($item['stock'] ?? 0);

                    if (($qty + $free) > $stock) {
                        $validator->errors()->add(
                            "items.$index.quantity",
                            "Your order (quantity + free quantity) exceeds available stock ({$stock})."
                        );
                    }


                    // Stock validation end


                    $medicine =Medicine::find($item['medicine_id']);
                    if (!$medicine) continue;

                    $actualUnitCost = $medicine->sale_price; // your actual cost field
                    $unitCost       = floatval($item['unit_cost']);
                    $mrp            = floatval($item['mrp']);

                    if ($unitCost < $actualUnitCost) {
                        $validator->errors()->add(
                            "items.$index.unit_cost",
                            "You Can't go bellow actual unit cost. Actual Unit Cost ({$actualUnitCost})."
                        );
                    }

                    if ($unitCost > $mrp) {
                        $validator->errors()->add(
                            "items.$index.unit_cost",
                            "You Can't go Above actual Mrp. Actual Mrp ({$mrp})."
                        );
                    }
                }
            });

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            try {
                DB::beginTransaction();

                $filterChemistHouse=ChemistHouse::find($request->shop_id);

                $total = 0;
                foreach ($request->items as $item) {
                    $total += $item['unit_cost'] * $item['quantity'];
                }

                $discount     = (float) ($request->discount ?? 0);
                $vatPercent   = (float) ($request->vat ?? 0);
                $previousDue  = (float) ($request->previous_due ?? 0);
                $givenAmount  = (float) ($request->given_amount ?? 0);

                $vatAmount  = ($total * $vatPercent) / 100;
                $finalTotal = $total + $vatAmount - $discount ;


                $receivableAmount = $finalTotal - $givenAmount;

                if (is_null($filterChemistHouse->mpo_id)){

                    $sale = Sale::create([
                        'sale_date'          => $request->sale_date,
                        'delivery_date'      =>$request->delivery_date,
                        'user_id'            => $userId,
                        'depo_id'            => $depoId,
                        'chemist_house_id'   => $request->shop_id,
                        'total'              => $total,
                        'discount'           => $discount,
                        'vat'                => $vatPercent,
                        'previous_due'       => 0,
                        'final_total'        => $finalTotal,
                        'given_amount'       => $finalTotal,
                        'receivable_amount'  => 0,
                        'payment_status'     => 1,
                        'order_status'       => 2,
                        'created_at'         => now(),
                    ]);


                    $saleItems = [];
                    foreach ($request->items as $item) {

                        $saleItems[] = [
                            'sale_id'       => $sale->id,
                            'medicine_id'   => $item['medicine_id'],
                            'unit_cost'     => $item['unit_cost'],
                            'mrp'          => $item['mrp'],
                            'medicine_discount' => $item['medicine_discount'],
                            'quantity'      => $item['quantity'],
                            'free_quantity' => $item['free_quantity'] ?? 0,
                            'sub_total'     => $item['unit_cost'] * $item['quantity'],
                            'created_at'    => now(),

                        ];
                    }

                    SaleItem::insert($saleItems);

                    // Update Depo Account Balance
                    if ($request->account_id) {
                        $depoAccount = Account::find($request->account_id);
                        if ($depoAccount) {
                            $depoAccount->balance += $finalTotal;
                            $depoAccount->save();
                        }
                    }
                    else{
                        return redirect()->back()->with('error', 'Please select a valid account');
                    }

                }
                else{

                     $paymentStatus=2;

                     $statusTotal=$finalTotal+$previousDue;

                     if($statusTotal == $givenAmount){
                         $paymentStatus=1;
                     }elseif($givenAmount>0 && $statusTotal>$givenAmount){
                         $paymentStatus=3;
                     }

                    $sale = Sale::create([
                        'sale_date'          => $request->sale_date,
                        'delivery_date'      => $request->delivery_date,
                        'user_id'            => $userId,
                        'depo_id'            => $depoId,
                        'chemist_house_id'   => $request->shop_id,
                        'total'              => $total,
                        'discount'           => $discount,
                        'vat'                => $vatPercent,
                        'previous_due'       => $previousDue,
                        'final_total'        => $finalTotal,
                        'given_amount'       => $givenAmount,
                        'receivable_amount'  => $receivableAmount,
                        'payment_status'     => $paymentStatus,
                        'order_status'       => 2,
                        'created_at'         => now(),
                    ]);


                    $saleItems = [];
                    foreach ($request->items as $item) {

                        $saleItems[] = [
                            'sale_id'       => $sale->id,
                            'medicine_id'   => $item['medicine_id'],
                            'unit_cost'     => $item['unit_cost'],
                            'mrp'          => $item['mrp'],
                            'medicine_discount' => $item['medicine_discount'],
                            'quantity'      => $item['quantity'],
                            'free_quantity' => $item['free_quantity'] ?? 0,
                            'sub_total'     => $item['unit_cost'] * $item['quantity'],
                            'created_at'    => now(),

                        ];
                    }

                    SaleItem::insert($saleItems);

                    // Chemist House due increase
                    $chemistHouse = ChemistHouseDueAccount::where('chemist_house_id',$request->shop_id)->first();
                    if ($chemistHouse) {
                    $chemistHouse->due_balance+= $receivableAmount;
                    $chemistHouse->save();
                    }

                    // Update Depo Account Balance
                    if ($request->account_id) {
                        $depoAccount = Account::find($request->account_id);
                        if ($depoAccount) {
                            $depoAccount->balance += $givenAmount;
                            $depoAccount->save();
                        }
                    }
                    else{
                        return redirect()->back()->with('error', 'Please select a valid account');
                    }

                }


                if (is_null($filterChemistHouse->mpo_id)) {
                    $paidAmount = $finalTotal;
                } else {
                    $paidAmount = $givenAmount;
                }

                // Ledger Section
                ChemistHouseLedger::create([
                    'chemist_house_id'  => $request->shop_id,
                    'date'              => $request->sale_date,
                    'invoice_id'        => $sale->sale_voucher,
                    'purpose'           => 'Purchase Medicine',
                    'debit'             => $paidAmount,
                    'credit'            => $finalTotal,
                    'voucher_route'     => 'depo.direct-sale.show',
                    'voucher_id'        => $sale->id,
                ]);

                // Store in cash flow
                DepoCashFlow::create([
                    'date'         => $request->sale_date,
                    'invoice_id'   => $sale->sale_voucher,
                    'description'  => 'Medicine Sale',
                    'dr_amount'    => 0,
                    'cr_amount'    => $paidAmount,
                    'balance'      => $depoAccount->balance,
                    'depo_id'      => $depoId,
                    'account_id'   => $request->account_id,
                    'voucher_route'=> 'depo.direct-sale.show',
                    'voucher_id'   => $sale->id,
                ]);

                DB::commit();
                Log::info('Sale created: ' . $sale->sale_voucher);
                return redirect('/depo/direct-sale/show/'.$sale->id)->with('success', 'Sale created successfully And Account Balance is Updated.');


            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->with('error', $e->getMessage());
            }
        }

        // Get default chemist house (mpo_id = 0) for this depo
        $defaultChemist = ChemistHouse::with('chemistHouseDueAccount')
            ->where('depo_id', $depoId)
            ->where('mpo_id', 0)
            ->select('id', 'shop_name', 'account_number', 'owner_name')
            ->first();

        // Get all chemist houses for this depo (for dropdown selection)
        $allChemists = ChemistHouse::with('chemistHouseDueAccount')
            ->where('depo_id', $depoId)
            ->select('id', 'shop_name', 'account_number', 'owner_name', 'mpo_id')
            ->get();

        return view('depo.extends.direct_sale.create', compact('defaultChemist', 'allChemists'));
    }


    public function show($id)
    {
        $userId = !empty(Session::get('userObj'))
            ? Session::get('userObj')->id
            : Auth::user()->id;

        $depoId = Depo::where('user_id', $userId)->first()->id;

        try {

            $depo=Depo::where('id', $depoId)->first();

            $sale = Sale::with('items.medicine', 'chemistHouse', 'account','depo')->find($id);

            if (empty($sale)) {
                Log::error('Sale not found: ID ' . $id);
                return redirect()->back()->with('error', 'Sale not found');
            }

            return view('depo.extends.direct_sale.show', compact('sale','depo'));

        } catch (\Exception $e) {
            Log::error('Sale show failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Sale Show Failed!');
        }
    }

    public function print($id){

        $userId = !empty(Session::get('userObj'))
            ? Session::get('userObj')->id
            : Auth::user()->id;

        $depoId = Depo::where('user_id', $userId)->first()->id;

        try {

            $depo=Depo::where('id', $depoId)->first();

            $sale = Sale::with('items.medicine', 'chemistHouse', 'account','depo')->find($id);

            if (empty($sale)) {
                Log::error('Sale not found: ID ' . $id);
                return redirect()->back()->with('error', 'Sale not found');
            }

            return view('depo.print.direct_sale_voucher', compact('sale','depo'));

        } catch (\Exception $e) {
            Log::error('Sale show failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Sale Show Failed!');
        }
    }

    public function posPrint($id){

        $userId = !empty(Session::get('userObj'))
            ? Session::get('userObj')->id
            : Auth::user()->id;

        $depoId = Depo::where('user_id', $userId)->first()->id;

        try {

            $depo=Depo::where('id', $depoId)->first();

            $sale = Sale::with('items.medicine', 'chemistHouse', 'account','depo')->find($id);

            if (empty($sale)) {
                Log::error('Sale not found: ID ' . $id);
                return redirect()->back()->with('error', 'Sale not found');
            }

            return view('depo.print.direct_sale_voucher_pos', compact('sale','depo'));

        } catch (\Exception $e) {
            Log::error('Sale show failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Sale Show Failed!');
        }
    }




    public function getSaleData(Request $request)
    {
        $search = $request->query('q');

        $userId = !empty(Session::get('userObj'))
            ? Session::get('userObj')->id
            : Auth::user()->id;

        $depoId = Depo::where('user_id', $userId)->first()->id;

        // Get chemists for this depo, optionally search by name
        $chemist = ChemistHouse::with('chemistHouseDueAccount')
            ->where('depo_id', $depoId)
            ->when($search, function($query, $search) {
                $query->where('shop_name', 'like', "%{$search}%");
            })
            ->select('id', 'shop_name', 'account_number', 'owner_name', 'mpo_id')
            ->get();

        // Transform to required structure with default selection
        $chemistShops = $chemist->map(function($c) {
            return [
                'id' => $c->id,
                'shop_name' => $c->shop_name,
                'receivable_amount' => $c->chemistHouseDueAccount->due_balance ?? 0,
                'is_default' => $c->mpo_id == 0, // Mark default chemist
            ];
        });

        // Get depo accounts
        $accounts = Account::where('depo_id', $depoId)
            ->select('id', 'account_name', 'balance')
            ->get();

        return response()->json([
            'chemistShops' => $chemistShops,
            'accounts' => $accounts,
        ]);
    }




    // Medicine search (AJAX)
    public function searchMedicine(Request $request)
    {
        $userId = Session::get('userObj')->id ?? Auth::user()->id;
        $depoId = Depo::where('user_id', $userId)->value('id');

        $medicines = Medicine::where('medicine_name', 'like', '%' . $request->q . '%')
            ->whereIn('id', function ($query) use ($depoId) {
                $query->select('distribute_items.medicine_id')
                    ->from('distribute_items')
                    ->join('distributes', 'distributes.id', '=', 'distribute_items.distribute_id')
                    ->where('distributes.depo_id', $depoId);
            })
            ->select('id', 'medicine_name', 'sale_price', 'mrp')
            ->limit(10)
            ->get();

        foreach ($medicines as $m) {

            // Total distributed to this depo
            $distributed = DistributeItem::where('medicine_id', $m->id)
                ->whereHas('distribute', function ($q) use ($depoId) {
                    $q->where('depo_id', $depoId);
                })
                ->sum(DB::raw('quantity + free_quantity'));

            // Total sold from this depo
            $sold = SaleItem::where('medicine_id', $m->id)
                ->whereHas('sale', function ($q) use ($depoId) {
                    $q->where('depo_id', $depoId);
                })
                ->sum(DB::raw('quantity + free_quantity'));


            // Current stock
            $m->current_stock = $distributed - $sold ;
        }

        return response()->json($medicines);
    }



    public function getDepoAccounts()
    {
        $userId = !empty(Session::get('userObj'))
        ? Session::get('userObj')->id
        : Auth::user()->id;

        $depoId=Depo::where('user_id', $userId)->first()->id;

        return response()->json([
            'accounts'  => Account::where('depo_id', $depoId)
                ->select('id','account_name','balance','is_default')->where('user_id',$userId)
                ->get(),
        ]);
    }




}
