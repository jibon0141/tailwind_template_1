<?php

namespace App\Http\Controllers\Depo\Purchase;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\CompanySetting;
use App\Models\Depo;
use App\Models\DepoDueAccount;
use App\Models\DepoLedger;
use App\Models\Distribute;
use App\Models\DistributeItem;
use App\Models\Medicine;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\TempDistribute;
use App\Models\TempDistributeItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $userId=!empty(Session::get('userObj')) ? Session::get('userObj')->id : Auth::user()->id;

        if ($request->ajax()) {
            $depo=Depo::where('user_id',$userId)->first();

            $distributes = Distribute::with('depo.depoDueAccount', 'account')
                ->where('depo_id',$depo->id)
                ->orderBy('id', 'desc');



            // Filter by date range
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $start = Carbon::parse($request->start_date)->startOfDay();
                $end = Carbon::parse($request->end_date)->endOfDay();
                $distributes->whereBetween('distribute_date', [$start, $end]);
            }
            $distributes = $distributes->get();


            return datatables()->of($distributes)
                ->addIndexColumn()
                ->addColumn('distribute_voucher', fn($row) => $row->distribute_voucher ?? 'N/A')
//                ->addColumn('company_account', function($row) {
//                    $account_name=Account::where('id', $row->company_account_id)->first();
//                    return  $account_name ? $account_name->account_name : 'N/A';
//                })
//                ->addColumn('account_name', fn($row) => $row->account ? $row->account->account_name : 'N/A')
                ->addColumn('distribute_date', function ($row) {
                    return \Carbon\Carbon::parse($row->distribute_date)->format('d-m-Y');
                })
                ->addColumn('previous_due', fn($row) => $row->previous_due)
                ->addColumn('final_total', fn($row) => $row->final_total)
                ->addColumn('receivable_amount', fn($row) => $row->receivable_amount)
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('depo.purchase.show', $row->id) . '" class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded">
            <i class="fa fa-eye"></i></a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('depo.extends.purchase.index');
    }

    public function pendingPurchase(Request $request){

        $userId=!empty(Session::get('userObj')) ? Session::get('userObj')->id : Auth::user()->id;

        if ($request->ajax()) {
            $depo=Depo::where('user_id',$userId)->first();

            $distributes = TempDistribute::with('depo', 'account')
                ->where('depo_id',$depo->id)
                ->where('order_status',1)
                ->orderBy('id', 'desc');



            // Filter by date range
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $start = Carbon::parse($request->start_date)->startOfDay();
                $end = Carbon::parse($request->end_date)->endOfDay();
                $distributes->whereBetween('distribute_date', [$start, $end]);
            }
            $distributes = $distributes->get();


            return datatables()->of($distributes)
                ->addIndexColumn()
                ->addColumn('distribute_voucher', fn($row) => $row->distribute_voucher ?? 'N/A')

                ->addColumn('distribute_date', function ($row) {
                    return \Carbon\Carbon::parse($row->distribute_date)->format('d-m-Y');
                })
                ->addColumn('previous_due', fn($row) => $row->previous_due)
                ->addColumn('voucher_total', function($row) {
                    $total    = $row->total ?? 0;
                    $discount = $row->discount ?? 0;
                    $vatPerc  = $row->vat ?? 0;

                    // Calculate VAT on total after discount
                    $vatAmount = ($total - $discount) * ($vatPerc / 100);

                    return ($total - $discount) + $vatAmount;
                })
                ->addColumn('receivable_amount', fn($row) => $row->receivable_amount)
                ->addColumn('order_status', function ($row) {
                    return match ($row->order_status) {
                        1 => '<span class="px-2 py-1 text-xs rounded bg-yellow-300 text-black">Pending</span>',
                        2 => '<span class="px-2 py-1 text-xs rounded bg-blue-300 text-black">Approved</span>',
                        3 => '<span class="px-2 py-1 text-xs rounded bg-green-300 text-black">Delivered</span>',
                        default => '<span class="px-2 py-1 text-xs rounded bg-gray-300 text-black">Unknown</span>',
                    };
                })

                ->addColumn('action', function ($row) {
                    return '<a href="' . route('depo.pending-purchase.show', $row->id) . '" class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded">
            <i class="fa fa-eye"></i></a>
            <a href="' . route('depo.pending-purchase.edit', $row->id) . '" class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded">
            <i class="fa fa-edit"></i></a>
            ';
                })

                ->rawColumns(['order_status','action'])
                ->make(true);
        }

        return view('depo.extends.purchase.pending_purchase');
    }



    public function create(Request $request)
    {
        $userId=!empty(Session::get('userObj')) ? Session::get('userObj')->id : Auth::user()->id;


        if ($request->isMethod('POST')) {

            $validated = $request->validate([
                'supplier_id'         => 'required|exists:suppliers,id',
                'purchase_date'       => 'required|date',
                'account_id'          => 'nullable|exists:accounts,id',
                'items'               => 'required|array|min:1',
                'items.*.medicine_id' => 'required|exists:medicines,id',
                'items.*.unit_cost'   => 'required|numeric|min:0',
                'items.*.quantity'    => 'required|numeric|min:1',
                'items.*.free_quantity' => 'nullable|numeric|min:0',
                'discount'            => 'nullable|numeric|min:0',
                'vat'                 => 'nullable|numeric|min:0',
                'advance'             => 'nullable|numeric|min:0',
                'previous_due'        => 'nullable|numeric|min:0',
                'given_amount'        => 'nullable|numeric|min:0',
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

                $depo=Depo::where('user_id',$userId)->first();

                // If account is selected, check balance
                if (!empty($request->account_id) && $givenAmount > 0) {
                    $account = Account::where('id',$request->account_id)->first();
                    if ($account->balance < $givenAmount) {
                        return redirect()->back()->with('error', 'Account balance is insufficient for the given amount.');
                    }
                    // Deduct from account
                    $account->balance -= $givenAmount;
                    $account->save();
                }

                // Create Purchase
                $purchase = Purchase::create([
                    'purchase_date'    => $request->purchase_date,
                    'user_id'          => $userId,
                    'depo_id'           => $depo->id,
                    'supplier_id'      => $request->supplier_id,
                    'account_id'       => $request->account_id ?? null,
                    'total'            => $total,
                    'discount'         => $discount,
                    'vat'              => $vatPercent,
                    'advance'          => $advance,
                    'previous_due'     => $previousDue,
                    'final_total'      => $finalTotal,
                    'given_amount'     => $givenAmount,
                ]);


                // Create Purchase Items
                $requestItems=[];
                foreach ($request->items as $key=>$item) {
                   $requestItem[$key]=[
                       'purchase_id'   => $purchase->id,
                       'medicine_id'   => $item['medicine_id'],
                       'unit_cost'     => $item['unit_cost'],
                       'quantity'      => $item['quantity'],
                       'free_quantity' => $item['free_quantity'] ?? 0,
                       'sub_total'     => $item['unit_cost'] * $item['quantity'],
                       'created_at'  =>now(),
                   ];
                }

                PurchaseItem::insert($requestItem);

                // Update Supplier Balance (due cannot be negative)
                $supplier = Supplier::where('id',$request->supplier_id)->first();
                $supplier->balance = max($finalTotal - $givenAmount, 0);
                $supplier->save();

                DB::commit();

                return redirect()->back()->with('success', 'Purchase created successfully');

            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->with('error', $e->getMessage());
            }
        }

        return view('depo.extends.purchase.create');
    }


    public function pendingPurchaseShow($id){

        try{
            $mainCompany= CompanySetting::first();
            $distribute = TempDistribute::with('items.medicine', 'depo')->where('id',$id)->first();

            if(empty($distribute)){
                Log::error('Distribute not found');
                return redirect()->back()->with('error', 'Distribute not found');
            }

            return view('depo.extends.purchase.pending_purchase_show', compact('distribute','mainCompany'));
        }
        catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Distribute Show Failed!');
        }

    }

    public function pendingPurchasePrint($id){

        try{
            $distribute = TempDistribute::with('items.medicine', 'depo', 'account')->where('id',$id)->first();
            $mainCompany= CompanySetting::first();

            if(empty($distribute)){
                Log::error('Distribute not found');
                return redirect()->back()->with('error', 'Distribute not found');
            }

            return view('depo.print.pending_purchase_voucher', compact('distribute','mainCompany'));
        }
        catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Distribute Show Failed!');
        }

    }


    public function pendingPurchasePosPrint($id){

        try{
            $distribute = TempDistribute::with('items.medicine', 'depo', 'account')->where('id',$id)->first();
            $mainCompany= CompanySetting::first();

            if(empty($distribute)){
                Log::error('Distribute not found');
                return redirect()->back()->with('error', 'Distribute not found');
            }

            return view('depo.print.pending_purchase_pos', compact('distribute','mainCompany'));
        }
        catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Distribute Show Failed!');
        }

    }


    public function pendingPurchaseEdit($id){

        try{
//            $distributeStatus=TempDistribute::all();
            $distribute = TempDistribute::with('items.medicine', 'depo')->where('id',$id)->first();


            if(empty($distribute)){
                Log::error('Distribute not found');
                return redirect()->back()->with('error', 'Distribute not found');
            }

            return view('depo.extends.purchase.pending_purchase_edit', compact('distribute'));
        }
        catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Distribute Show Failed!');
        }

    }


    public function purchaseVerification(Request $request, $id){


        $userId=!empty(Session::get('userObj')) ? Session::get('userObj')->id : Auth::user()->id;

        $data=TempDistribute::with('items.medicine')->where('id',$id)->first();

        $validated=$request->validate([
            'order_status'=>'required',
        ]);


        if ($data->order_status != 1) {
            return back()->with('error', 'This distribution is already verified.');
        }


        try{

            DB::beginTransaction();

            if($request->order_status !=2){
                $data->update([
                    'order_status'=>$request->order_status,
                ]);

                DB::commit();

                Log::info('Distribution Handled Successfully.');
                return redirect()->route('depo.purchase.pending')
                    ->with('success', 'Distribution Handled Successfully.');
            }


            // Total Calculation
            $total = 0;
            foreach ($data->items as $item) {
                $total += $item['unit_cost'] * $item['quantity'];
            }

            $discount = $data->discount ?? 0;
            $vatPercent = $data->vat ?? 0;
            $previousDue = $data->previous_due ?? 0;

            $vatAmount = ($total * $vatPercent) / 100;
            $finalTotal = $total - $discount + $vatAmount;



            // Create Distribute
            $distribute = Distribute::Create([
                'depo_id' => $data->depo_id,
                'distribute_date' => $data->distribute_date,
                'total' => $total,
                'discount' => $discount,
                'vat' => $vatPercent,
                'previous_due' => $previousDue,
                'final_total' => $finalTotal,
                'receivable_amount' => $data->receivable_amount,
                'payment_status' =>  2,
                'order_status'  => 3,
            ]);


            // Distribute Items
            $items = [];
            foreach ($data->items as $item) {
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

            $depo = DepoDueAccount::where('depo_id',$data->depo_id)->first();
            $depo->due_balance +=$finalTotal ;
            $depo->save();


            $data->update([
                'order_status'=>$request->order_status,
            ]);

           // Depo Ledger Part
            DepoLedger::create([
                'depo_id'         => $data->depo_id,
                'date'            => $data->distribute_date,
                'invoice_id'      => $distribute->distribute_voucher,
                'purpose'         => 'Medicine Order',
                'debit'           => 0,
                'credit'          => $finalTotal,
                'voucher_route'   => 'admin.distribute.show',
                'voucher_id'      =>  $distribute->id,

            ]);


            DB::commit();
            Log::info('Distribute Accepted successfully');
            return redirect()->route('depo.purchase.pending')
                ->with('success', 'Distribution Accepted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return back()->with('error', 'Distribution Failed.');
        }

    }


    public function show($id){

        try{
            $distribute = Distribute::with('items.medicine', 'depo', 'account')->where('id',$id)->first();
            $mainCompany= CompanySetting::first();
            if(empty($distribute)){
                Log::error('Distribute not found');
                return redirect()->back()->with('error', 'Distribute not found');
            }

            return view('depo.extends.purchase.show', compact('distribute','mainCompany'));
        }
        catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Distribute Show Failed!');
        }

    }

   public function print($id){

       try{
           $distribute = Distribute::with('items.medicine', 'depo', 'account')->where('id',$id)->first();
           $mainCompany= CompanySetting::first();

           if(empty($distribute)){
               Log::error('Distribute not found');
               return redirect()->back()->with('error', 'Distribute not found');
           }

           return view('depo.print.purchase_voucher', compact('distribute','mainCompany'));
       }
       catch (\Exception $e) {
           Log::error($e->getMessage());
           return redirect()->back()->with('error', 'Distribute Show Failed!');
       }

   }

    public function posPrint($id){

        try{
            $distribute = Distribute::with('items.medicine', 'depo', 'account')->where('id',$id)->first();
            $mainCompany= CompanySetting::first();

            if(empty($distribute)){
                Log::error('Distribute not found');
                return redirect()->back()->with('error', 'Distribute not found');
            }

            return view('depo.print.purchase_voucher_pos', compact('distribute','mainCompany'));
        }
        catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Distribute Show Failed!');
        }

    }



    public function getPurchaseData()
    {
        $userId=!empty(Session::get('userObj')) ? Session::get('userObj')->id : Auth::user()->id;
        return response()->json([
            'suppliers' => Supplier::select('id','supplier_name','balance')->get(),
            'accounts'  => Account::select('id','account_name','balance')->where('user_id',$userId)->get(),
        ]);
    }

    // Medicine search (AJAX)
    public function searchMedicine(Request $request)
    {
        return Medicine::where('medicine_name','like','%'.$request->q.'%')
            ->select('id','medicine_name','purchase_price')
            ->limit(10)
            ->get();
    }


}
