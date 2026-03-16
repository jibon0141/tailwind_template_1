<?php

namespace App\Http\Controllers\Depo\ChemistHouseDuePayment;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\ChemistHouse;
use App\Models\ChemistHouseDueAccount;
use App\Models\ChemistHouseDuePayment;
use App\Models\ChemistHouseLedger;
use App\Models\Depo;
use App\Models\DepoCashFlow;
use App\Traits\ManageImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class ChemistHouseDuePaymentController extends Controller
{

    use ManageImage;
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $userId = !empty(Session::get('userObj')) ? Session::get('userObj')->id : Auth::user()->id;
            $depoId = Depo::where('user_id', $userId)->first()->id;

            $query = ChemistHouseDuePayment::with(['account', 'chemistHouse'])
                ->whereHas('account', function($query) use ($depoId) {
                    $query->where('depo_id', $depoId);
                })
                ->when($request->start_date, function ($query) use ($request) {
                    $start = Carbon::parse($request->start_date)->startOfDay();
                    $query->where('payment_date', '>=', $start);
                })
                ->when($request->end_date, function ($query) use ($request) {
                    $end = Carbon::parse($request->end_date)->endOfDay();
                    $query->where('payment_date', '<=', $end);
                })
                ->when($request->payment_status, function ($query) use ($request) {
                    $query->where('payment_status', $request->payment_status);
                })
                ->orderByDesc('id'); // Keep the ordering here

            // Pass query builder to DataTables instead of get()
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('chemist_house_name', function ($row) {
                    return $row->chemistHouse->shop_name ?? 'N/A';
                })
                ->addColumn('payment_voucher', function ($row) {
                    return $row->payment_voucher ?? 'N/A';
                })
                ->addColumn('payment_date', function ($row) {
                    return $row->payment_date ?? 'N/A';
                })
                ->addColumn('contact', function ($row) {
                    return $row->contact ?? 'N/A';
                })
                ->addColumn('depo_account', function ($row) {
                    return $row->account->account_name ?? 'N/A';
                })
                ->addColumn('due_balance', function ($row) {
                    return $row->balance ?? 'N/A';
                })
                ->addColumn('receiving_amount', function ($row) {
                    return $row->receiving_amount ?? 'N/A';
                })
                ->addColumn('current_due', function ($row) {
                    return round(($row->balance - $row->receiving_amount) ?? 0);
                })
                ->addColumn('note', function ($row) {
                    return $row->note ?? 'N/A';
                })
                ->addColumn('document', function ($row) {
                    return $row->document;
                })
                ->addColumn('payment_status', function ($row) {
                    switch ($row->payment_status) {
                        case 1:
                            return '<span class="inline-block px-4 py-1 text-xs font-semibold text-green-800 bg-green-200 rounded-full">Paid</span>';
                        case 2:
                            return '<span class="inline-block px-4 py-1 text-xs font-semibold text-yellow-800 bg-yellow-200 rounded-full">Partial</span>';
                        case 3:
                            return '<span class="inline-block px-4 py-1 text-xs font-semibold text-green-800 bg-green-200 rounded-full">Advance</span>';
                        default:
                            return '<span class="inline-block px-4 py-1 text-xs font-semibold text-gray-800 bg-gray-200 rounded-full">Unpaid</span>';
                    }
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('depo.chemist-house-due-payment.show', $row->id);
                    return '<div class="flex gap-2">
                            <a href="' . $editUrl . '" class="inline-flex items-center px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold rounded" title="View">
                                <i class="fa fa-eye"></i>
                            </a>
                        </div>';
                })
                ->rawColumns(['payment_status', 'action'])
                ->make(true);
        }

        return view('depo.extends.chemist_house_due_payment.index');
    }

    public function create(Request $request)
    {


        $userId=!empty(Session::get('userObj')) ? Session::get('userObj')->id : Auth::user()->id;
        $depoId = Depo::where('user_id', $userId)->first()->id;

        if ($request->isMethod('POST')) {

            $request->validate([
                'chemist_house_id'     => 'required|exists:chemist_houses,id',
                'chemist_house_name'   => 'required|string|max:255',
                'payment_date'         => 'nullable|date',
                'contact'              => 'nullable|string|max:20',
                'balance'              => 'required|numeric|min:0',
                'receiving_amount'     => 'required|numeric|min:0',
                'document'             => 'nullable',
                'note'                 => 'nullable',
            ]);

            try {
                DB::beginTransaction();

                $paymentDate = $request->filled('payment_date')
                    ? $request->payment_date
                    : now()->toDateString();

                if ($request->receiving_amount == $request->balance) {
                    $paymentStatus = 1; // paid
                } elseif ($request->receiving_amount > 0 && $request->receiving_amount < $request->balance) {
                    $paymentStatus = 2; // partial
                }elseif($request->receiving_amount > $request->balance ){
                    $paymentStatus = 3; //advance
                }

                $document = null;

                if ($request->hasFile('document')) {
                    $document= $this->storeImage($request->document,'image/DepoDueCollection');
                }

                $chemistHousePayment = ChemistHouseDuePayment::create([
                    'chemist_house_id' => $request->chemist_house_id,
                    'account_id' => $request->account_id,
                    'chemist_house_name' => $request->chemist_house_name,
                    'payment_date' => $paymentDate,
                    'contact' => $request->contact,
                    'balance' => $request->balance,
                    'receiving_amount' => $request->receiving_amount,
                    'payment_status' => $paymentStatus,
                    'document'          => $document,
                    'note'              => $request->note,
                ]);

                $depoAccount = Account::where('id', $request->account_id)->first();
                if (empty($depoAccount)) {
                    Log::info('Chemist House Payment Created Failed');
                    return redirect()->back()->with('error', 'Depo Account Not Found!');
                }
//                if ($depoAccount->balance < $request->paying_amount) {
//                    Log::info('Chemist House Payment Created Failed');
//                    return redirect()->back()->with('error', 'Insufficient Balance!');
//                }
                $newDepoBalance = $depoAccount->balance + $request->receiving_amount;
                $depoAccount->update([
                    'balance' => $newDepoBalance,
                ]);

                $chemistHouse = ChemistHouseDueAccount::where('chemist_house_id', $request->chemist_house_id)->first();
                if (empty($chemistHouse)) {
                    Log::info('Chemist House Payment Created Failed');
                    return redirect()->back()->with('error', 'Chemist House Not Found!');
                }
                $newBalance = $chemistHouse->due_balance - $request->receiving_amount;
                $chemistHouse->update([
                    'due_balance' => $newBalance
                ]);


                // Ledger Section
                ChemistHouseLedger::create([
                    'chemist_house_id'  => $request->chemist_house_id,
                    'date'              => $paymentDate,
                    'invoice_id'        => $chemistHousePayment->payment_voucher,
                    'purpose'           => 'Due Payment',
                    'debit'             => $request->receiving_amount,
                    'credit'            => 0,
                    'voucher_route'     => 'depo.chemist-house-due-payment.show',
                    'voucher_id'        => $chemistHousePayment ->id,
                ]);


                // Store in cash flow
                DepoCashFlow::create([
                    'date'         => $paymentDate,
                    'invoice_id'   => $chemistHousePayment->payment_voucher,
                    'description'  => 'Due Collection',
                    'dr_amount'    => 0,
                    'cr_amount'    => $request->receiving_amount,
                    'balance'      => $newDepoBalance,
                    'depo_id'      => $depoId,
                    'account_id'   => $request->account_id,
                    'voucher_route'=> 'depo.chemist-house-due-payment.show',
                    'voucher_id'   =>  $chemistHousePayment->id,
                ]);

                DB::commit();
                Log::info('Chemist House Payment Created Successfully');
                return redirect()->back()->with('success', 'Chemist House payment created successfully!');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e->getMessage());
                return redirect()->back()->with('Chemist House Payment Created Failed');
            }
        }

        $depoId =Depo::where('user_id', $userId)->first()->id;
        $depoAccounts = Account::where('depo_id', $depoId)->get();
        return view('depo.extends.chemist_house_due_payment.create', compact('depoAccounts'));
    }

    public function show($id)
    {
        try {
            $payment = ChemistHouseDuePayment::with(['account', 'chemistHouse'])->findOrFail($id);

            Log::info('Chemist House Payment Showed Successfully.');
            return view('depo.extends.chemist_house_due_payment.show', compact('payment'));
        } catch (\Exception $e) {
            Log::info('Chemist House Payment Show Failed.');
            return redirect()->back()->with('error', $e->getMessage());
        }
    }



    public function getChemistHouseData(Request $request)
    {
        try {
            $userId=!empty(Session::get('userObj')) ? Session::get('userObj')->id : Auth::user()->id;
            $depoId=Depo::where('user_id', $userId)->first()->id;

            $search = $request->query('query');

            $chemistHouses = ChemistHouse::with('chemistHouseDueAccount')
                ->where('depo_id', $depoId)
                ->whereNotNull('mpo_id')
                ->where('shop_name', 'like', "%{$search}%")
                ->get();


            Log::info('Chemist House Payment Data Found');
            return response()->json($chemistHouses);
        } catch (\Exception $e) {
            Log::info('Chemist House Payment Data Not Found');
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
