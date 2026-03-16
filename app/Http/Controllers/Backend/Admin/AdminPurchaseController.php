<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\CompanyCashFlow;
use App\Models\CompanySetting;
use App\Models\Medicine;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\SupplierLedger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class AdminPurchaseController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $purchases = Purchase::with('supplier', 'account')
                ->where('user_id', Auth::id())
                ->where('depo_id', 0)
                ->orderBy('id', 'desc');

            // Supplier filter
            if ($request->filled('supplier_id')) {
                $purchases->where('supplier_id', $request->supplier_id);
            }

            // Date range filter
            if ($request->filled('start_date')) {
                $startDate = Carbon::parse($request->start_date)->startOfDay();
                $purchases->where('purchase_date', '>=', $startDate);
            }

            if ($request->filled('end_date')) {
                $endDate = Carbon::parse($request->end_date)->endOfDay();
                $purchases->where('purchase_date', '<=', $endDate);
            }

            $purchases = $purchases->get();

            return DataTables::of($purchases)
                ->addIndexColumn()
                ->addColumn('purchase_voucher', fn($row) => $row->purchase_voucher ?? 'N/A')
                ->addColumn('supplier_name', fn($row) => $row->supplier ? $row->supplier->supplier_name : 'N/A'
                )
                ->addColumn('account_name', fn($row) => $row->account ? $row->account->account_name : 'N/A'
                )
                ->addColumn('purchase_date', fn($row) => Carbon::parse($row->purchase_date)->format('d-m-Y')
                )
                ->addColumn('voucher_total', fn($row) => number_format(($row->final_total - $row->previous_due), 2)
                )
                ->addColumn('final_total', fn($row) => number_format($row->final_total, 2)
                )
                ->addColumn('paid', fn($row) => number_format($row->given_amount, 2)
                )
                ->addColumn('payable_amount', fn($row) => number_format($row->payable_amount, 2)
                )

                ->addColumn('payment_status', function ($row) {
                    return match ($row->payment_status) {
                        1 => '<span class="px-4 py-1 bg-green-300 text-black rounded-lg text-xs font-semibold">Paid</span>',
                        2 => '<span class="px-2 py-1 bg-red-300 text-black rounded-lg text-xs font-semibold">Unpaid</span>',
                        3 => '<span class="px-3 py-1 bg-yellow-300 text-black rounded-lg text-xs font-semibold">Partial</span>',
                        4 => '<span class="px-2 py-1 bg-blue-300 text-black rounded-lg text-xs font-semibold">Advance</span>',
                        default => '<span class="px-2 py-1 bg-gray-300 text-black rounded-lg text-xs font-semibold">N/A</span>',
                    };
                })
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('admin.medicine.purchase.show', $row->id) . '"
            class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded">
            <i class="fa fa-eye"></i>
        </a>';
                })
                ->rawColumns(['payment_status', 'action'])
                ->make(true);

        }


        return view('admin.extends.purchase_admin_purchase.index');
    }

    public function create(Request $request)
    {

        if ($request->isMethod('POST')) {

            $validated = $request->validate([
                'supplier_id'                  => 'required|exists:suppliers,id',
                'purchase_date'                => 'required|date',
                'account_id'                   => 'required|exists:accounts,id',
                'items'                        => 'required|array|min:1',
                'items.*.medicine_id'          => 'required|exists:medicines,id',
                'items.*.mrp'                  => 'required',
                'items.*.medicine_discount'    => 'required',
                'items.*.unit_cost'            => 'required|numeric|min:0',
                'items.*.quantity'             => 'required|numeric|min:1',
                'items.*.expire_date'          => 'required',
                'items.*.free_quantity'        => 'nullable|numeric|min:0',
                'discount'                     => 'nullable|numeric|min:0',
                'vat'                          => 'nullable|numeric|min:0',
                'advance'                      => 'nullable|numeric',
                'previous_due'                 => 'nullable|numeric',
                'given_amount'                 => 'nullable|numeric|min:0',
                'payable_amount'               => 'nullable|numeric',
                'payment_status'               => 'nullable|numeric|min:0',
            ]);



            try {
                DB::beginTransaction();


                // Total calculation
                $total = 0;
                foreach ($request->items as $item) {
                    $total += $item['unit_cost'] * $item['quantity'];
                }

                $discount    = $request->discount ?? 0;
                $vatPercent  = $request->vat ?? 0;
                $advance     = $request->advance ?? 0;
                $previousDue = $request->previous_due ?? 0;
                $givenAmount = $request->given_amount ?? 0;

                $vatAmount = ($total * $vatPercent) / 100;

                $finalTotal = $total - $discount + $vatAmount + $previousDue - $advance;


                // If account is selected, check balance
                $accountBalance = 0;
                if (!empty($request->account_id) && $givenAmount > 0) {
                    $account = Account::where('id',$request->account_id)->first();
                    if ($account->balance < $givenAmount) {
                        return redirect()->back()->with('error', 'Account balance is insufficient for the given amount.');
                    }
                    // Deduct from account
                    $account->balance -= $givenAmount;
                    $account->save();
                    $accountBalance = $account->balance;
                }

                //  Payment Status
                if($finalTotal == $givenAmount){
                    $payment_status = 1;    // paid
                }
                elseif ($givenAmount == 0) {
                    $payment_status = 2; // unpaid
                }
                elseif ($givenAmount>0 && $finalTotal > $givenAmount) {
                    $payment_status = 3;  // partially paid
                }else{
                    $payment_status=4;   // advance
                }



                // Create Purchase
                $purchase = Purchase::create([
                    'purchase_date'    => $request->purchase_date,
                    'user_id'          => Auth::id(),
                    'depo_id'          => 0,
                    'supplier_id'      => $request->supplier_id,
                    'account_id'       => $request->account_id ?? null,
                    'total'            => $total,
                    'discount'         => $discount,
                    'vat'              => $vatPercent,
                    'advance'          => $advance,
                    'previous_due'     => $previousDue,
                    'final_total'      => $finalTotal,
                    'given_amount'     => $givenAmount,
                    'payable_amount'   => $finalTotal - $givenAmount,
                    'payment_status'   => $payment_status,
                    'purchased_by'      => Auth::id(),
                ]);



                // Create Purchase Items
                $requestItems=[];
                foreach ($request->items as $key=>$item) {
                    $requestItem[$key]=[
                        'purchase_id'       => $purchase->id,
                        'medicine_id'       => $item['medicine_id'],
                        'mrp'               => $item['mrp'],
                        'medicine_discount' => $item['medicine_discount'],
                        'unit_cost'         => $item['unit_cost'],
                        'quantity'          => $item['quantity'],
                        'free_quantity'     => $item['free_quantity'] ?? 0,
                        'expire_date'       => $item['expire_date'],
                        'sub_total'         => $item['unit_cost'] * $item['quantity'],
                        'created_at'        =>now(),
                    ];



                    // Update Medicine Price
                    Medicine::where('id', $item['medicine_id'])
                        ->update([
                            'purchase_price'       => $item['unit_cost'],
                            'purchase_percentage'  => $item['medicine_discount'],
                            'updated_at'           => now(),
                        ]);
                }

                PurchaseItem::insert($requestItem);


                $supplier = Supplier::find($request->supplier_id);
                $supplier->balance = $finalTotal - $givenAmount;

                $supplier->save();


                // Supplier Ledger
                $ledgerTotal = $total - $discount + $vatAmount - $advance;
                SupplierLedger::create([
                    'supplier_id'   => $request->supplier_id,
                    'date'          => $request->purchase_date,
                    'invoice_id'    => $purchase->purchase_voucher,
                    'purpose'       => 'Purchase Invoice',
                    'debit'         => $ledgerTotal,
                    'credit'        => $givenAmount,
                    'balance'       => 0,
                    'voucher_route' => 'admin.medicine.purchase.show',
                    'voucher_id'    => $purchase->id,
                ]);

                // CashFlow Section
                CompanyCashFlow::create([
                    'date'          => $request->purchase_date,
                    'invoice_id'    => $purchase->purchase_voucher,
                    'description'   => 'Purchase Invoice',
                    'dr_amount'     => $givenAmount,
                    'cr_amount'     => 0,
                    'balance'       => $accountBalance,
                    'account_id'    => $request->account_id,
                    'voucher_route' => 'admin.medicine.purchase.show',
                    'voucher_id'    => $purchase->id,
                ]);


                DB::commit();
                Log::info('Purchase Created Successfully.');
                return redirect('/admin/medicine-purchase/show/'.$purchase->id)->with('success', 'Purchase created successfully.');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e->getMessage());
                return redirect()->back()->with('error', 'Purchase Created Failed.');
            }
        }

        return view('admin.extends.purchase_admin_purchase.create');
    }


    public function show($id)
    {
        try{
            $mainCompany=CompanySetting::first();
            $purchase = Purchase::with('items.medicine', 'supplier', 'account')->where('id',$id)->first();

            if(empty($purchase)){
                Log::error('Purchase not found');
                return redirect()->back()->with('error', 'Purchase not found');
            }

            return view('admin.extends.purchase_admin_purchase.show', compact('purchase','mainCompany'));
        }
        catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Purchase Show Failed!');
        }

    }

    public function print($id){

        try{
            $mainCompany=CompanySetting::first();
            $purchase = Purchase::with('items.medicine', 'supplier', 'account')->where('id',$id)->first();

            if(empty($purchase)){
                Log::error('Purchase not found');
                return redirect()->back()->with('error', 'Purchase not found');
            }

            return view('admin.print.sub_admin_purchase_print', compact('purchase','mainCompany'));
        }
        catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Purchase Show Failed!');
        }

    }


    public function getPurchaseData()
    {
        return response()->json([
//            'suppliers' => Supplier::select('id','supplier_name','balance')->get(),
            'accounts'  => Account::select('id','account_name','balance','is_default')
                ->where('depo_id',0)
                ->get(),
        ]);
    }

    // Medicine search (AJAX)
    public function searchMedicine(Request $request)
    {
        return Medicine::where('medicine_name','like','%'.$request->q.'%')
            ->select('id','medicine_name','purchase_price','mrp','purchase_percentage','purchase_price')
            ->distinct()
            ->get();
    }


    public function getSuppliers(Request $request)
    {
        $q = $request->q;

        $suppliers = Supplier::where('supplier_name', 'like', "%{$q}%")
            ->orWhere('phone', 'like', "%{$q}%")
            ->limit(20)
            ->get();

        return response()->json([
            'results' => $suppliers->map(fn ($s) => [
                'id'   => $s->id,
                'balance'=> $s->balance,
                'text' => $s->supplier_name . ' (' . ($s->phone ?? 'N/A') . ')',
            ])
        ]);
    }




}
