<?php

namespace App\Http\Controllers\Backend\Due;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\CompanyCashFlow;
use App\Models\Depo;
use App\Models\DepoCashFlow;
use App\Models\DepoDueAccount;
use App\Models\DepoDueCollection;
use App\Models\DepoLedger;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Yajra\DataTables\Facades\DataTables;

class DepoDueCollectionController extends Controller
{

    public function index(Request $request){
        if($request->ajax()){

            $dueCollection = DepoDueCollection::with(['depo', 'depoAccount', 'companyAccount']);

            // Date range filter
            if ($request->filled('start_date')) {
                $startDate = Carbon::parse($request->start_date)->startOfDay();
                $dueCollection->where('payment_date', '>=', $startDate);
            }

            if ($request->filled('end_date')) {
                $endDate = Carbon::parse($request->end_date)->endOfDay();
                $dueCollection->where('payment_date', '<=', $endDate);
            }

            // Payment Status filter
            if ($request->filled('payment_status')) {
                $dueCollection->where('payment_status', $request->payment_status);
            }

            // General Status filter
            if ($request->filled('status')) {
                $dueCollection->where('status', $request->status);
            }

            // Get the filtered data
            $dueCollection = $dueCollection->orderByDesc('id')->get();

            return DataTables::of($dueCollection)
                ->addIndexColumn()
                ->addColumn('depo_name', fn($row) => $row->depo_name ?? 'N/A')
                ->addColumn('payment_voucher', fn($row) => $row->payment_voucher ?? 'N/A')
                ->addColumn('payment_date', fn($row) => $row->payment_date ?? 'N/A')
                ->addColumn('depo_account', fn($row) => $row->depoAccount ? $row->depoAccount->account_name : 'N/A')
                ->addColumn('company_account', fn($row) => $row->companyAccount->account_name ??  'N/A')
                ->addColumn('due_balance', fn($row) => number_format($row->balance, 2))
                ->addColumn('receiving_amount', fn($row) => number_format($row->receiving_amount, 2))
                ->addColumn('current_receivable', fn($row) => number_format($row->balance - $row->receiving_amount, 2))
                ->addColumn('document', fn($row) => $row->document)
                ->addColumn('payment_status', function($row){
                    switch($row->payment_status){
                        case 1: return '<span class="inline-block px-4 py-1 text-xs font-semibold text-green-800 bg-green-200 rounded-full">Paid</span>';
                        case 2: return '<span class="inline-block px-4 py-1 text-xs font-semibold text-yellow-800 bg-yellow-200 rounded-full">Partial</span>';
                        default: return '<span class="inline-block px-4 py-1 text-xs font-semibold text-gray-800 bg-gray-200 rounded-full">partial</span>';
                    }
                })
                ->addColumn('status', function($row){
                    switch($row->status){
                        case 1: return '<span class="inline-block px-4 py-1 text-xs font-semibold text-gray-800 bg-yellow-200 rounded-full">Pending</span>';
                        case 2: return '<span class="inline-block px-4 py-1 text-xs font-semibold text-gray-800 bg-green-200 rounded-full">Approved</span>';
                        case 3: return '<span class="inline-block px-4 py-1 text-xs font-semibold text-gray-800 bg-red-200 rounded-full">Rejected</span>';
                        default: return '<span class="inline-block px-4 py-1 text-xs font-semibold text-gray-800 bg-gray-200 rounded-full">Pending</span>';
                    }
                })

                ->addColumn('action', function($row){
                    $showUrl = route('admin.depo-due-collection.show', $row->id);
                    $editUrl = route('admin.depo-due-collection.edit', $row->id);

                    $buttons = '<div class="flex gap-2">';

                    // Always show View button
                    $buttons .= '<a href="'.$showUrl.'"
                    class="inline-flex items-center px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold rounded"
                    title="View">
                    <i class="fa fa-eye"></i>
                </a>';

                    // Only show Edit button if status is 1 (pending)
                    if ($row->status == 1) {
                        $buttons .= '<a href="'.$editUrl.'"
                        class="inline-flex items-center px-2 py-1 bg-green-500 hover:bg-green-600 text-white text-xs font-semibold rounded"
                        title="Edit">
                        <i class="fa fa-edit"></i>
                    </a>';
                    }

                    $buttons .= '</div>';

                    return $buttons;
                })
                ->rawColumns(['status','payment_status','action'])
                ->make(true);
        }

        return view('admin.extends.payment.depo_collection.index');
    }


    public function create(Request $request){

        if($request->isMethod('POST')){

            $request->validate([
                'depo_id'           => 'required|integer|exists:depos,id',
                'depo_name'         => 'required|string|max:255',
                'contact'           => 'nullable|string|max:255',
                'payment_date'      => 'required|date',
                'depo_account_id'   => 'required|integer|exists:accounts,id',
                'account_id'        => 'required|integer|exists:accounts,id',
                'balance'           => 'required|numeric|min:0',
                'receiving_amount'  => 'required|numeric|min:0',
                'payment_status'    => 'nullable|in:1,2', // 1=paid, 2=partial,3=advance

            ]);

        try{
            DB::beginTransaction();

            $paymentDate=$request->filled('payment_date') ?
                $request->payment_date :
                 now()->toDateString();

            if($request->balance > $request->receiving_amount){
                $paymentStatus=2;
            }elseif($request->balance <= $request->receiving_amount){
                $paymentStatus=1;
            }



            $depoPayment=DepoDueCollection::create([
                'depo_id'           => $request->depo_id,
                'depo_name'         => $request->depo_name,
                'contact'           => $request->contact,
                'payment_date'      => $paymentDate,
                'depo_account_id'   => $request->depo_account_id,
                'account_id'        => $request->account_id,
                'balance'           => $request->balance,
                'receiving_amount'  => $request->receiving_amount,
                'payment_status'    => $paymentStatus,


            ]);


            //  Depo Due Update(decrease)
            $depo=Depo::where('id', $request->depo_id)->first();
            if(!$depo){
                return redirect()->back()->with('error', 'Depo not found');
            }
            $depo->balance=$depo->balance-$request->receiving_amount;
            $depo->save();

            //  Depo Account Balance Update(Decrease)
            $depoAccount=Account::where('id', $request->depo_account_id)->first();
            if(!$depoAccount){
                return redirect()->back()->with('error', 'Depo Account not found');
            }
            if($depoAccount->balance < $request->receiving_amount){
                return redirect()->back()->with('error', 'Insufficient Balance');
            }
            $depoAccount->balance=$depoAccount->balance-$request->receiving_amount;
            $depoAccount->save();

            //  Company Balance Update(Increase)
            $account=Account::where('id', $request->account_id)->first();
            if(!$account){
                return redirect()->back()->with('error', 'Account not found');
            }
            $account->balance=$account->balance+$request->receiving_amount;
            $account->save();

            // Depo Ledger Part Here
            DepoLedger::create([
                'depo_id'         => $request->depo_id,
                'date'            => $paymentDate,
                'invoice_id'      => $depoPayment->payment_voucher,
                'purpose'         => 'Due Payment',
                'debit'           => 0,
                'credit'          => $request->receiving_amount,
                'voucher_route'   => 'admin/depo-due-collection/show/',
                'voucher_id'      => $depoPayment->id,

            ]);



            DB::commit();
            Log::info('Depo Due Collection created successfully.');
            return redirect()->back()->with('success', 'Depo Due Collection created successfully.');

        }
        catch(\Exception $e){
            DB::rollBack();
            Log::error('Depo Due Collection created failed. '.$e->getMessage());
            return redirect()->back()->with('error', 'Depo Due Collection created failed.');
        }

        }
        $companyAccounts=Account::where('depo_id',0)->get();
        return view('admin.extends.payment.depo_collection.create',compact('companyAccounts'));
    }

    public function show($id)
    {
        try {
            $depoDueCollection = DepoDueCollection::with([
                'depo',
                'depoAccount',
                'companyAccount'
            ])->find($id);
            if(!$depoDueCollection){
                Log::error('Depo Due Collection not found.');
                return redirect()->back()->with('error', 'Depo Due Collection not found.');
            }

            Log::info('Depo Due Collection showed successfully. ID: ' . $id);

            return view(
                'admin.extends.payment.depo_collection.show',
                compact('depoDueCollection')
            );
        } catch (\Exception $e) {
            Log::error('Depo Due Collection show failed. ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Depo Due Collection not found!');
        }
    }

    public function edit($id){

        try{
            $depoDueCollection = DepoDueCollection::where('id', $id)->first();
            $companyAccount = Account::where('depo_id',0)->get();

            if(!$depoDueCollection){
                Log::error('Depo Due Collection not found.');
                return redirect()->back()->with('error', 'Depo Due Collection not found.');
            }
            Log::info('Depo Due Collection edit successfully. ID: ' . $id);
            return view('admin.extends.payment.depo_collection.edit',compact('depoDueCollection','companyAccount'));
        }
        catch(\Exception $e){
            Log::error('Depo Due Collection edit failed. '.$e->getMessage());
            return redirect()->back()->with('error', 'Depo Due Collection edit failed.');
        }
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'account_id'       => 'nullable|integer|exists:accounts,id',
            'receiving_amount' => 'required|numeric|min:0',
            'status'           => 'required|in:1,2,3', // 1=Pending, 2=Approved, 3=Rejected
        ]);

        try {
            DB::beginTransaction();

            if($request->status==1){
                return redirect()->back()->with('success','Depo Due Collection status Still Pending.');
            }

            $deuCollection = DepoDueCollection::where('id', $id)->first();
            $companyAccount = Account::where('id', $request->account_id)->first();
            $depoAccount = Account::where('id', $deuCollection->depo_account_id)->first();
            $depoDueAccount = DepoDueAccount::where('depo_id', $deuCollection->depo_id)->first();

            if (!$deuCollection) {
                Log::error('Depo Due Collection not found. ID: '.$id);
                return redirect()->back()->with('error', 'Depo Due Collection not found.');
            }

            if ($request->status==2 && !$companyAccount) {
                Log::error('Company Account not found. ID: '.$request->account_id);
                return redirect()->back()->with('error', 'Company Account not found.');
            }

            $receivingAmount = $request->receiving_amount;
            $status = $request->status;

            // Update balances based on status
            if ($status == 2) { // Approved
                $companyAccount->balance += $receivingAmount;
                $companyAccount->save();

                // Depo Ledger Part
                DepoLedger::create([
                    'depo_id'         => $deuCollection->depo_id,
                    'date'            => $deuCollection->payment_date,
                    'invoice_id'      => $deuCollection->payment_voucher,
                    'purpose'         => 'Due Payment',
                    'debit'           => $deuCollection->receiving_amount,
                    'credit'          => 0,
                    'voucher_route'   => 'admin.depo-due-collection.show',
                    'voucher_id'      => $deuCollection->id,

                ]);

                // Cash Flow Section
                CompanyCashFlow::create([
                    'date'        => $deuCollection->payment_date,
                    'invoice_id'  => $deuCollection->payment_voucher,
                    'description' => 'Collection Invoice',
                    'dr_amount'   => 0,
                    'cr_amount'   => $request->receiving_amount,
                    'balance'     => $companyAccount->balance,
                    'account_id'  => $request->account_id,
                    'voucher_route'   => 'admin.depo-due-collection.show',
                    'voucher_id'      => $deuCollection->id,
                ]);

                // Depo Cash Flow
                DepoCashFlow::create([
                    'date'         => $deuCollection->payment_date,
                    'invoice_id'   => $deuCollection->payment_voucher,
                    'description'  => 'Due Payment',
                    'dr_amount'    => $request->receiving_amount,
                    'cr_amount'    => 0,
                    'balance'      => $depoAccount->balance,
                    'depo_id'      => $deuCollection->depo_id,
                    'account_id'   => $depoAccount->id,
                    'voucher_route'=> 'admin.depo-due-collection.show',
                    'voucher_id'   =>  $deuCollection->id,
                ]);



            } elseif ($status == 3) { // Rejected
                if ($depoAccount) {
                    $depoAccount->balance += $receivingAmount;
                    $depoAccount->save();
                }

                if ($depoDueAccount) {
                    $depoDueAccount->due_balance += $receivingAmount;
                    $depoDueAccount->save();
                }
            }

            // Update DepoDueCollection
            $deuCollection->status = $status;
            $deuCollection->account_id = $request->account_id;
            $deuCollection->save();


            DB::commit();

            Log::info('Depo Due Collection updated successfully. ID: '.$id);
            return redirect()->route('admin.depo-due-collection.index')
                ->with('success', 'Depo Due Collection updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Depo Due Collection update failed. '.$e->getMessage());
            return redirect()->back()->with('error', 'Depo Due Collection update failed.');
        }
    }



    public function print($id){

        try {
            $depoDueCollection = DepoDueCollection::with([
                'depo',
                'depoAccount',
                'companyAccount'
            ])->find($id);
            if(!$depoDueCollection){
                Log::error('Depo Due Collection not found.');
                return redirect()->back()->with('error', 'Depo Due Collection not found.');
            }

            Log::info('Depo Due Collection showed successfully. ID: ' . $id);

            return view(
                'admin.print.depo_due_collection_print',
                compact('depoDueCollection')
            );
        } catch (\Exception $e) {
            Log::error('Depo Due Collection show failed. ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Depo Due Collection not found!');
        }

    }

    public function getDepoDueData(Request $request)
    {
        try {
            $search = $request->query('query');

            $depo = Depo::with('account')
                ->where('depo_name', 'like', '%' . $search . '%')
                ->select('id', 'depo_name','contact','person_name')
                ->get();

            return response()->json([
                'message'=>'Depo Due data Found',
                'data' => $depo
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


}


