<?php

namespace App\Http\Controllers\Depo\DepoDuePayment;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Depo;
use App\Models\DepoDueAccount;
use App\Models\DepoDueCollection;
use Illuminate\Http\Request;
use App\Traits\ManageImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class DepoDuePaymentController extends Controller
{
    use ManageImage;
    public function index(Request $request)
    {
        $userId = !empty(Session::get('userObj')) ? Session::get('userObj')->id : Auth::user()->id;
        $depoId = Depo::where('user_id', $userId)->first()->id;

        if($request->ajax()) {

            $dueCollection = DepoDueCollection::with(['depo','depoAccount','companyAccount'])
                ->where('depo_id', $depoId)
                // Filter by start date
                ->when($request->start_date, function ($query) use ($request) {
                    $start = Carbon::parse($request->start_date)->startOfDay();
                    $query->where('payment_date', '>=', $start);
                })
                // Filter by end date
                ->when($request->end_date, function ($query) use ($request) {
                    $end = Carbon::parse($request->end_date)->endOfDay();
                    $query->where('payment_date', '<=', $end);
                })
                // Filter by payment status
                ->when($request->payment_status, function ($query) use ($request) {
                    $query->where('payment_status', $request->payment_status);
                })
                // Filter by general status
                ->when($request->status, function ($query) use ($request) {
                    $query->where('status', $request->status);
                })
                ->get();
            return DataTables::of($dueCollection)
                ->addIndexColumn()
                ->addColumn('payment_voucher', fn($row) => $row->payment_voucher ?? 'N/A')
                ->addColumn('payment_date', fn($row) => $row->payment_date ?? 'N/A')
                ->addColumn('depo_account', fn($row) => $row->depoAccount ? $row->depoAccount->account_name : 'N/A')
                ->addColumn('due_balance', fn($row) => number_format($row->balance, 2))
                ->addColumn('receiving_amount', fn($row) => number_format($row->receiving_amount, 2))
                ->addColumn('current_receivable', fn($row) => number_format($row->balance - $row->receiving_amount, 2))
                ->addColumn('note', fn($row) => $row->note ?? 'N/A')
                ->addColumn('document', fn($row) => $row->document)
                ->addColumn('payment_status', function($row){
                    switch($row->payment_status){
                        case 1: return '<span class="inline-block px-4 py-1 text-xs font-semibold text-green-800 bg-green-200 rounded-full">Paid</span>';
                        case 2: return '<span class="inline-block px-4 py-1 text-xs font-semibold text-yellow-800 bg-yellow-200 rounded-full">Partial</span>';
                        default: return '<span class="inline-block px-4 py-1 text-xs font-semibold text-gray-800 bg-gray-200 rounded-full">Partial</span>';
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
                    $editUrl = route('depo.depo-due-payment.show', $row->id);
                    return '<div class="flex gap-2">
                    <a href="'.$editUrl.'" class="inline-flex items-center px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold rounded" title="View"><i class="fa fa-eye"></i></a>
                </div>';
                })
                ->rawColumns(['status','payment_status','action'])
                ->make(true);
        }

        return view('depo.extends.depo_due_payment.index');
    }

    public function create(Request $request){

        $userId=!empty(Session::get('userObj')) ? Session::get('userObj')->id : Auth::user()->id;

        if($request->isMethod('POST')){

            $request->validate([
                'depo_id'           => 'required|integer|exists:depos,id',
                'depo_name'         => 'required|string|max:255',
                'contact'           => 'nullable|string|max:255',
                'payment_date'      => 'required|date',
                'depo_account_id'   => 'required|integer|exists:accounts,id',
                'balance'           => 'required|numeric',
                'receiving_amount'  => 'required|numeric',
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
                }elseif($request->balance < $request->receiving_amount){
                    $paymentStatus=3;
                }

                $document = null;

                if ($request->hasFile('document')) {
                    $document= $this->storeImage($request->document,'image/DepoDuePayment');
                }

                $depoPayment=DepoDueCollection::create([
                    'depo_id'           => $request->depo_id,
                    'depo_name'         => $request->depo_name,
                    'contact'           => $request->contact,
                    'payment_date'      => $paymentDate,
                    'depo_account_id'   => $request->depo_account_id,
                    'balance'           => $request->balance,
                    'receiving_amount'  => $request->receiving_amount,
                    'payment_status'    => $paymentStatus,
                    'note'              => $request->note,
                    'document'          => $document,
                    'status'            =>  1,
                ]);

                //  Depo Due Update(decrease)
                $depo=DepoDueAccount::where('depo_id', $request->depo_id)->first();
                if(!$depo){
                    return redirect()->back()->with('error', 'Depo not found');
                }
                $depo->due_balance=$depo->due_balance-$request->receiving_amount;
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

                DB::commit();
                Log::info('Depo Due Payment successfully.');
                return redirect()->back()->with('success', 'Depo Due Payment successfully.');

            }
            catch(\Exception $e){

                DB::rollBack();
                Log::error('Depo Due Collection created failed. '.$e->getMessage());
                return redirect()->back()->with('error', 'Depo Due Collection created failed.');
            }
        }
        $depo=Depo::with(['account','depoDueAccount'])->where('user_id',$userId)->first();
        return view('depo.extends.depo_due_payment.create',compact('depo'));
    }


    public function show($id)
    {
        $userId=!empty(Session::get('userObj')) ? Session::get('userObj')->id : Auth::user()->id;
        $depoId =Depo::where('user_id', $userId)->first()->id;
        try {
            $depoDueCollection = DepoDueCollection::with([
                'depo',
                'depoAccount',
                'companyAccount'
            ])
                ->where('depo_id',$depoId)
                ->find($id);

            if(!$depoDueCollection){
                Log::error('Depo Due Collection not found.');
                return redirect()->back()->with('error', 'Depo Due Collection not found.');
            }

            Log::info('Depo Due Collection showed successfully. ID: ' . $id);

            return view(
                'depo.extends.depo_due_payment.show',
                compact('depoDueCollection')
            );
        } catch (\Exception $e) {
            Log::error('Depo Due Collection show failed. ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Depo Due Collection not found!');
        }
    }


//    public function getDepoDueData(Request $request)
//    {
//        try {
//            $search = $request->query('query');
//
//            $depo = Depo::with(['account'])
//                ->where('depo_name', 'like', '%' . $search . '%')
//                ->select('id', 'depo_name','contact','person_name')
//                ->get();
//
//            return response()->json([
//                'message'=>'Depo Due data Found',
//                'data' => $depo
//            ],200);
//        } catch (\Exception $e) {
//            return response()->json([
//                'message' => 'Something went wrong',
//                'error'   => $e->getMessage()
//            ], 500);
//        }
//    }


}
