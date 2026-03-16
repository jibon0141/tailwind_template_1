<?php

namespace App\Http\Controllers\ChemistHouse\ChemistOrder;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\ChemistHouse;
use App\Models\ChemistHouseDueAccount;
use App\Models\Company;
use App\Models\Depo;
use App\Models\Employee;
use App\Models\Medicine;
use App\Models\MedicineCategory;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ChemistOrderController extends Controller
{
    public function index(Request $request){

        $userId = Auth::user()->id;

        if ($request->ajax()) {

            $chemistId=ChemistHouse::where('user_id',$userId)->first()->id;


            $sales = Sale::with('chemistHouse', 'account', 'mpo')
                ->where('chemist_house_id', $chemistId)
                ->orderBy('id', 'desc');

            // Filter by date range
//            if ($request->filled('start_date') && $request->filled('end_date')) {
//                $start = \Carbon\Carbon::parse($request->start_date)->startOfDay();
//                $end = \Carbon\Carbon::parse($request->end_date)->endOfDay();
//                $sales->whereBetween('sale_date', [$start, $end]);
//            }
//
//            // Filter by order status
//            if ($request->filled('order_status')) {
//                $sales->where('order_status', $request->order_status);
//            }

            return datatables()->of($sales)
                ->addIndexColumn()
                ->addColumn('sale_voucher', fn($row) => $row->sale_voucher ?? 'N/A')
                ->addColumn('mpo_name', fn($row) => $row->mpo?->full_name ?? 'N/A')
                ->addColumn('sale_date', fn($row) => \Carbon\Carbon::parse($row->sale_date)->format('d-m-Y'))
                ->addColumn('final_total', fn($row) => $row->final_total ?? 'N/A')
                ->addColumn('order_status', function($row) {
                    return match($row->order_status) {
                        1 => '<span class="px-2 py-1 bg-yellow-200 text-yellow-800 rounded-full text-xs font-semibold">Pending</span>',
                        2 => '<span class="px-2 py-1 bg-green-200 text-green-800 rounded-full text-xs font-semibold">Approved</span>',
                        3 => '<span class="px-2 py-1 bg-blue-200 text-blue-800 rounded-full text-xs font-semibold">Delivered</span>',
                        default => '<span class="px-2 py-1 bg-gray-200 text-gray-800 rounded-full text-xs font-semibold">N/A</span>',
                    };
                })
                ->addColumn('action', fn($row) => '<a href="'.route('chemist.order.show', $row->id).'" class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded"><i class="fa fa-eye"></i></a>')
                ->rawColumns(['order_status','action'])
                ->make(true);
        }
        return view('chemist_house.extends.order.index');
    }


    public function create(Request $request)
    {
        if ($request->isMethod('POST')) {

            $validated = $request->validate([
                'shop_id'               => 'required|exists:chemist_houses,id',
                'sale_date'             => 'required|date',
                'items'                 => 'required|array|min:1',
                'items.*.medicine_id'   => 'required|exists:medicines,id',
                'items.*.unit_cost'     => 'required|numeric|min:0',
                'items.*.quantity'      => 'required|numeric|min:1',
                'items.*.free_quantity' => 'nullable|numeric|min:0',
                'discount'              => 'nullable|numeric|min:0',
                'vat'                   => 'nullable|numeric|min:0',
                'previous_due'          => 'nullable|numeric',
            ]);

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


                $chemistHouse = ChemistHouse::find($request->shop_id);
                $employee = Employee::where('user_id', $chemistHouse->mpo_id )->first();

                if(empty($chemistHouse)){
                    Log::error('Chemist House not found');
                    return redirect()->back()->with('error', 'Chemist House not found');
                };


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
                    'user_id'            => Auth::id(),
                    'depo_id'            => $chemistHouse->depo_id,
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
                        'sale_id'       => $sale->id,
                        'medicine_id'   => $item['medicine_id'],
                        'unit_cost'     => $item['unit_cost'],
                        'quantity'      => $item['quantity'],
                        'free_quantity' => $item['free_quantity'] ?? 0,
                        'sub_total'     => $item['unit_cost'] * $item['quantity'],
                        'created_at'    => now(),

                    ];
                }

                // Chemist House due increase
                $chemistHouse = ChemistHouseDueAccount::where('chemist_house_id',$request->shop_id)->first();
                if ($chemistHouse) {
                    $chemistHouse->due_balance+= $receivableAmount;
                    $chemistHouse->save();
                }



                SaleItem::insert($saleItems);

                DB::commit();
                session()->forget('cart_medicines');
                Log::info('Sale created: ' . $sale->sale_voucher);
                return redirect()->back()->with('success', 'Sale created successfully');

            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->with('error', $e->getMessage());
            }
        }

        // Get cart from session
        $cart = session('cart_medicines', []);

        $medicines = [];

        foreach ($cart as $item) {

            $medicine = Medicine::find($item['id']);

            if ($medicine) {

                $medicines[] = [
                    'id'           => $medicine->id,
                    'medicine_name'=> $medicine->medicine_name,
                    'sale_price'   => $medicine->sale_price,
                    'purchase_price'=> $medicine->purchase_price,
                    'qty'          => $item['qty'] ?? 1,
                ];
            }
        }

        return view('chemist_house.extends.order.create',compact('medicines'));
    }



    public function show($id)
    {
        try {

            $sale = Sale::with('items.medicine', 'chemistHouse', 'account')->find($id);

            if (empty($sale)) {
                Log::error('Sale not found: ID ' . $id);
                return redirect()->back()->with('error', 'Sale not found');
            }

            return view('chemist_house.extends.order.show', compact('sale'));

        } catch (\Exception $e) {
            Log::error('Sale show failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Sale Show Failed!');
        }
    }

    public function print($id){
        try {

            $sale = Sale::with('items.medicine', 'chemistHouse', 'account')->find($id);

            if (empty($sale)) {
                Log::error('Sale not found: ID ' . $id);
                return redirect()->back()->with('error', 'Sale not found');
            }

            return view('chemist_house.print.order_voucher', compact('sale'));

        } catch (\Exception $e) {
            Log::error('Sale show failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Sale Show Failed!');
        }
    }


    public function getSaleData()
    {
        $chemistHouse = ChemistHouse::with('chemistHouseDueAccount')
            ->where('user_id', Auth::id())
            ->first();

        return response()->json([
            'chemist' => [
                'id'   => $chemistHouse->id,
                'name' => $chemistHouse->shop_name,
                'due'  => $chemistHouse->chemistHouseDueAccount->due_balance ?? 0,
            ]
        ]);
    }


    // Medicine search (AJAX)
    public function searchMedicine(Request $request)
    {
        $user = Auth::user();

        $chemistHouse = ChemistHouse::where('user_id', $user->id)->first();
        $depoId = $chemistHouse->depo_id;

        return Medicine::where('medicine_name', 'like', '%' . $request->q . '%')
            ->whereIn('id', function ($query) use ($depoId) {
                $query->select('distribute_items.medicine_id')
                    ->from('distribute_items')
                    ->join('distributes', 'distributes.id', '=', 'distribute_items.distribute_id')
                    ->where('distributes.depo_id', $depoId);
            })
            ->select('id', 'medicine_name', 'sale_price')
            ->limit(10)
            ->get();
    }



}
