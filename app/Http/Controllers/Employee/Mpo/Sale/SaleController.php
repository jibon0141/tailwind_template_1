<?php

namespace App\Http\Controllers\Employee\Mpo\Sale;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\ChemistHouse;
use App\Models\ChemistHouseDueAccount;
use App\Models\ChemistHouseLedger;
use App\Models\CompanySetting;
use App\Models\Employee;
use App\Models\Medicine;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use carbon\carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $userId=!empty(Session::get('userObj')) ? Session::get('userObj')->id : Auth::user()->id;
        if ($request->ajax()) {

            $sales = Sale::with('chemistHouse.chemistHouseDueAccount', 'account')
                ->where('user_id', $userId)
                ->orderBy('id', 'desc');

            // Date range filter using Carbon Y-m-d format
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $start = Carbon::parse($request->start_date)->format('Y-m-d');
                $end = Carbon::parse($request->end_date)->format('Y-m-d');
                $sales->whereBetween('sale_date', [$start, $end]);
            }

            // Order status filter
            if ($request->filled('order_status')) {
                $sales->where('order_status', $request->order_status);
            }

            $sales = $sales->get();

            return datatables()->of($sales)
                ->addIndexColumn()
                ->addColumn('sale_voucher', fn($row) => $row->sale_voucher)
                ->addColumn('sale_date', fn($row) => Carbon::parse($row->sale_date)->format('d-m-Y'))
                ->addColumn('shop_name', fn($row) => $row->chemistHouse ? $row->chemistHouse->shop_name : 'N/A')
                ->addColumn('final_total', fn($row) => $row->final_total ?? 'N/A')
                ->addColumn('order_status', function($row) {
                    return match($row->order_status) {
                        1 => '<span class="px-2 py-1 bg-yellow-200 text-yellow-800 rounded-full text-xs font-semibold">Pending</span>',
                        2 => '<span class="px-2 py-1 bg-green-200 text-green-800 rounded-full text-xs font-semibold">Approved</span>',
                        3 => '<span class="px-2 py-1 bg-red-200 text-red-800 rounded-full text-xs font-semibold">Rejected</span>',
                        default => '<span class="px-2 py-1 bg-gray-200 text-gray-800 rounded-full text-xs font-semibold">N/A</span>',
                    };
                })
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('mpo.sale.show', $row->id) . '" class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded">
                    <i class="fa fa-eye"></i></a>';
                })
                ->rawColumns(['order_status', 'action'])
                ->make(true);
        }

        return view('employee.mpo.extends.sale.index');
    }





    public function create(Request $request)
    {
        $userId=!empty(Session::get('userObj')) ? Session::get('userObj')->id : Auth::user()->id;
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
                'discount' => 'nullable|numeric|min:0',
                'vat' => 'nullable|numeric|min:0',

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


                $receivableAmount = $finalTotal;


                $employee = Employee::where('user_id', Auth::id())
                    ->where('employee_type', 'mpo')
                    ->firstOrFail();


                if (!empty($request->account_id) && $givenAmount > 0) {
                    $account = Account::findOrFail($request->account_id);
                    $account->balance += $givenAmount; // company receives money
                    $account->save();
                }


//                //  Payment Status
//                if($finalTotal == $givenAmount){
//                    $payment_status = '1';    // paid=1
//                }
//                elseif ($givenAmount == 0) {
//                    $payment_status = '2'; // unpaid
//                }
//                elseif ($givenAmount>0 && $finalTotal > $givenAmount) {
//                    $payment_status = '3';  // partial paid
//                }


                $sale = Sale::create([
                    'sale_date'          => $request->sale_date,
                    'delivery_date'      => $request->delivery_date,
                    'user_id'            => $userId,
                    'depo_id'            => $employee->depo_id,
                    'mpo_id'             => $employee->id,
                    'chemist_house_id'   => $request->shop_id,
                    'total'              => $total,
                    'discount'           => $discount,
                    'vat'                => $vatPercent,
                    'previous_due'       => $previousDue,
                    'final_total'        => $finalTotal,
                    'receivable_amount'  => $receivableAmount,
                    'payment_status'     => 2,
                    'order_status'     => 1,
                    'created_at'         => now(),

                ]);


                $saleItems = [];
                foreach ($request->items as $item) {

                    $saleItems[] = [
                        'sale_id'             => $sale->id,
                        'medicine_id'         => $item['medicine_id'],
                        'unit_cost'           => $item['unit_cost'],
                        'mrp'                 => $item['mrp'],
                        'medicine_discount'   => $item['medicine_discount'],
                        'quantity'            => $item['quantity'],
                        'free_quantity'       => $item['free_quantity'] ?? 0,
                        'sub_total'           => $item['unit_cost'] * $item['quantity'],
                        'created_at'          => now(),

                    ];
                }

                // Chemist House due increase
                $chemistHouse = ChemistHouseDueAccount::where('chemist_house_id',$request->shop_id)->first();
                if ($chemistHouse) {
                    $chemistHouse->due_balance+= $receivableAmount;
                    $chemistHouse->save();
                }



                SaleItem::insert($saleItems);

                // Ledger Section
                ChemistHouseLedger::create([
                    'chemist_house_id'  => $request->shop_id,
                    'date'              => $request->sale_date,
                    'invoice_id'        => $sale->sale_voucher,
                    'purpose'           => 'Purchase Medicine',
                    'debit'             => 0,
                    'credit'            => $finalTotal,
                    'voucher_route'     => 'depo.sale.show',
                    'voucher_id'        => $sale->id,
                ]);

                DB::commit();
                Log::info('Sale created: ' . $sale->sale_voucher);
                return redirect()->back()->with('success', 'Sale created successfully');

            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->with('error', $e->getMessage());
            }
        }

        return view('employee.mpo.extends.sale.create');
    }



    public function show($id)
    {
        try {
            $mainCompany=CompanySetting::first();

            $sale = Sale::with('items.medicine', 'chemistHouse', 'account','depo','mpo')->find($id);

            if (empty($sale)) {
                Log::error('Sale not found: ID ' . $id);
                return redirect()->back()->with('error', 'Sale not found');
            }

            return view('employee.mpo.extends.sale.show', compact('sale','mainCompany'));

        } catch (\Exception $e) {
            Log::error('Sale show failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Sale Show Failed!');
        }
    }

    public function print($id){
        try {

            $mainCompany=CompanySetting::first();

            $sale = Sale::with('items.medicine', 'chemistHouse', 'account','depo','mpo')->find($id);

            if (empty($sale)) {
                Log::error('Sale not found: ID ' . $id);
                return redirect()->back()->with('error', 'Sale not found');
            }

            return view('employee.mpo.print.sale_voucher', compact('sale','mainCompany'));

        } catch (\Exception $e) {
            Log::error('Sale show failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Sale Show Failed!');
        }
    }

    public function posPrint($id){
        try {

            $mainCompany=CompanySetting::first();

            $sale = Sale::with('items.medicine', 'chemistHouse', 'account','depo','mpo')->find($id);

            if (empty($sale)) {
                Log::error('Sale not found: ID ' . $id);
                return redirect()->back()->with('error', 'Sale not found');
            }

            return view('employee.mpo.print.sale_voucher_pos', compact('sale','mainCompany'));

        } catch (\Exception $e) {
            Log::error('Sale show failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Sale Show Failed!');
        }
    }




    public function getSaleData(Request $request)
    {
        $search = $request->query('q'); // Select2 sends search term as 'q'
        $userId=!empty(Session::get('userObj')) ? Session::get('userObj')->id : Auth::user()->id;
        $employee = Employee::where('user_id', $userId)->first();
        $depoId = $employee->depo_id;

        $query = ChemistHouse::with('chemistHouseDueAccount')
            ->where('depo_id', $depoId)
            ->whereNotNull('mpo_id')
            ->select('id', 'shop_name', 'account_number','owner_name');

        if ($search) {
            $query->where('shop_name', 'like', "%{$search}%");
        }

        $chemistShops = $query->get()->map(function ($shop) {
            $shop->receivable_amount = $shop->chemistHouseDueAccount->due_balance ?? 0;
            return $shop;
        });

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
        $userId=!empty(Session::get('userObj')) ? Session::get('userObj')->id : Auth::user()->id;

        $employee = Employee::where('user_id', $userId)->first();
        $depoId   = $employee->depo_id;

        return Medicine::where('medicine_name', 'like', '%' . $request->q . '%')
            ->whereIn('id', function ($query) use ($depoId) {
                $query->select('distribute_items.medicine_id')
                    ->from('distribute_items')
                    ->join('distributes', 'distributes.id', '=', 'distribute_items.distribute_id')
                    ->where('distributes.depo_id', $depoId);
            })
            ->select([
                'medicines.id',
                'medicines.medicine_name',
                'medicines.sale_price',
                'medicines.mrp',
            ])
            ->selectSub(function ($q) use ($depoId) {
                // TOTAL DISTRIBUTED TO MPO (FROM THIS DEPO)
                $q->from('distribute_items')
                    ->join('distributes', 'distributes.id', '=', 'distribute_items.distribute_id')
                    ->whereColumn('distribute_items.medicine_id', 'medicines.id')
                    ->where('distributes.depo_id', $depoId)
                    ->selectRaw('COALESCE(SUM(distribute_items.quantity + distribute_items.free_quantity),0)');
            }, 'distributed_qty')
            ->selectSub(function ($q) use ($depoId) {
                // TOTAL SOLD BY MPO (FROM THIS DEPO)
                $q->from('sale_items')
                    ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
                    ->whereColumn('sale_items.medicine_id', 'medicines.id')
                    ->where('sales.depo_id', $depoId)
                    ->selectRaw('COALESCE(SUM(sale_items.quantity + sale_items.free_quantity),0)');
            }, 'sold_qty')
            ->limit(10)
            ->get()
            ->map(function ($m) {
                // FINAL CURRENT STOCK
                $m->current_stock = max(
                    ($m->distributed_qty ?? 0) - ($m->sold_qty ?? 0),
                    0
                );

                unset($m->distributed_qty, $m->sold_qty);
                return $m;
            });
    }




}
