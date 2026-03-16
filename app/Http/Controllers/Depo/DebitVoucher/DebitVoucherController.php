<?php

namespace App\Http\Controllers\Depo\DebitVoucher;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\ChartOfAccount;
use App\Models\DebitVoucher;
use App\Models\Depo;
use App\Models\DepoCashFlow;
use App\Models\Party;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class DebitVoucherController extends Controller
{
    public function index(Request $request)
    {
        $userId=!empty(Session::get('userObj')) ? Session::get('userObj')->id : Auth::user()->id;
        if ($request->ajax()) {

            $query = DebitVoucher::with(['party', 'account'])->where('user_id',$userId);

            // Global search input - Apply before pagination
            if ($request->has('search') && !empty($request->search['value'])) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->where('debit_voucher', 'like', "%{$search}%")
                        ->orWhereHas('party', function($q2) use ($search) {
                            $q2->where('party_name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('account', function($q3) use ($search) {
                            $q3->where('account_name', 'like', "%{$search}%");
                        });
                });
            }

            // Filter by custom search inputs (from date range and global search box)
            if ($request->has('start_date') && $request->has('end_date') &&
                !empty($request->start_date) && !empty($request->end_date)) {
                $query->whereBetween('payment_date', [$request->start_date, $request->end_date]);
            }

            // Additional filter for custom search box
            if ($request->has('custom_search') && !empty($request->custom_search)) {
                $customSearch = $request->custom_search;
                $query->where(function($q) use ($customSearch) {
                    $q->where('debit_voucher', 'like', "%{$customSearch}%")
                        ->orWhereHas('party', function($q2) use ($customSearch) {
                            $q2->where('party_name', 'like', "%{$customSearch}%");
                        })
                        ->orWhereHas('account', function($q3) use ($customSearch) {
                            $q3->where('account_name', 'like', "%{$customSearch}%");
                        });
                });
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('party', function($row) {
                    return $row->party->party_name ?? 'N/A';
                })
                ->editColumn('account', function($row) {
                    return $row->account->account_name ?? 'N/A';
                })
                ->editColumn('payment_date', function($row) {
                    return $row->payment_date ? Carbon::parse($row->payment_date)->format('d-m-Y') : '';
                })
                ->editColumn('total_amount', function($row) {
                    return number_format($row->total_amount, 2) . ' TK';
                })
                ->addColumn('actions', function ($row) {
                    $editUrl = route('depo.debit-voucher.show', $row->id);
                    return '
                <div class="flex gap-2">
                    <a href="' . $editUrl . '" class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded">
                        <i class="fa fa-eye"></i>
                    </a>

                </div>
                ';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('depo.extends.debit_voucher.index');
    }


    public function create(Request $request)
    {
        $userId = !empty(Session::get('userObj'))
            ? Session::get('userObj')->id
            : Auth::user()->id;
        $depoId = Depo::where('user_id', $userId)->first()->id;

        if(empty($depoId)){
            Log::info("Depo id not found");
            return redirect()->back()->with('error', 'Depo id not found.');
        }

        if ($request->isMethod('post')) {

            $request->validate([
                'party_id' => 'required|exists:parties,id',
                'account_id' => 'required|exists:accounts,id',
                'payment_date' => 'required|date',
                'chart_of_account_id' => 'required|array',
                'chart_of_account_id.*' => 'required|exists:chart_of_accounts,id',
                'paid_amount' => 'required|array',
                'paid_amount.*' => 'required|numeric|min:0.01',
                'description' => 'nullable|array',
            ]);



            try {
                DB::beginTransaction();
                $account = Account::lockForUpdate()->findOrFail($request->account_id);

                // Calculate total
                $totalAmount = array_sum($request->paid_amount);

                if ($account->balance < $totalAmount) {
                    return back()->with('error', 'Insufficient account balance.');
                }

                // Create ONE voucher
                $voucher = DebitVoucher::create([
                    'user_id'      => $userId,
                    'party_id'     => $request->party_id,
                    'account_id'   => $request->account_id,
                    'payment_date' => $request->payment_date,
                    'total_amount' => $totalAmount,
                ]);

                // Create MANY items and store in cash flow
                foreach ($request->chart_of_account_id as $index => $coaId) {

                    $paidAmount = $request->paid_amount[$index];
                    $description = $request->description[$index] ?? 'Party Payment';

                    // Create voucher item
                    $voucher->items()->create([
                        'chart_of_account_id' => $coaId,
                        'description'         => $description,
                        'paid_amount'         => $paidAmount,
                    ]);

                    // Update account balance for each item
                    $account->balance -= $paidAmount;

                    // Store in cash flow
                    DepoCashFlow::create([
                        'date'         => $request->payment_date,
                        'invoice_id'   => $voucher->debit_voucher,
                        'description'  => $description,
                        'dr_amount'    => $paidAmount,
                        'cr_amount'    => 0,
                        'balance'      => $account->balance,
                        'depo_id'      => $depoId,
                        'account_id'   => $account->id,
                        'voucher_route'=> 'depo.debit-voucher.show',
                        'voucher_id'   => $voucher->id,
                    ]);
                }

                $account->save();

                DB::commit();
                return back()->with('success', 'Debit voucher created successfully');

            } catch (\Exception $e) {
                DB::rollBack();
                return back()->withErrors(['error' => $e->getMessage()]);
            }
        }
        return view('depo.extends.debit_voucher.create');
    }

    public function show($id)
    {

        // Fetch the debit voucher with related party, account, and chart of account
        $debitVoucher = DebitVoucher::with(['party', 'account','items.coa'])->find($id);

        // Format the data if needed


        return view('depo.extends.debit_voucher.show', compact('debitVoucher'));
    }


  public function print($id)
    {
        $debitVoucher = DebitVoucher::with(['party', 'account', 'items.coa'])->find($id);

        return view('depo.print.debit_voucher', compact('debitVoucher'));
    }


    public function getParties(Request $request)
    {
        $userId = !empty(Session::get('userObj'))
            ? Session::get('userObj')->id
            : Auth::user()->id;

        $search = $request->get('q', '');

        $parties = Party::where('user_id', $userId)
            ->where(function ($query) use ($search) {
                $query->where('party_name', 'LIKE', "%{$search}%")
                    ->orWhere('party_code', 'LIKE', "%{$search}%");
            })
            ->limit(10)
            ->get(['id', 'party_name', 'party_code']);

        $results = $parties->map(function ($party) {
            return [
                'id'   => $party->id,
                'text' => $party->party_name . ' (' . $party->party_code . ')',
            ];
        });

        return response()->json(['results' => $results]);
    }



    public function getBankAccounts(Request $request)
    {
        $search = $request->get('q');
        $userId = session('userObj')->id ?? Auth::id();

        $accounts = Account::where(function ($q) use ($search) {
            $q->where('account_name', 'LIKE', "%{$search}%")
                ->orWhere('id', 'LIKE', "%{$search}%");
        })
            ->where('user_id', $userId)
            ->where('status', 1)
            ->limit(10)
            ->get();

        $results = [];
        foreach ($accounts as $account) {
            $results[] = [
                'id' => $account->id,
                'text' => $account->account_name,
                'balance' =>  $account->balance,
            ];
        }

        return response()->json(['results' => $results]);
    }


    public function getExpenseCoa(Request $request)
    {
        $userId=!empty(Session::get('userObj')) ? Session::get('userObj')->id : Auth::user()->id;

        $search = $request->get('q', '');

        $coaHeads = ChartOfAccount::where('user_id', $userId)
            ->where('head_type', 'Expense')
            ->where('status', 1)
            ->when($search, function ($query) use ($search) {
                $query->where('head_name', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get(['id', 'head_name', 'head_type']);

        $results = $coaHeads->map(function ($head) {
            return [
                'id'   => $head->id,
                'text' => $head->head_name . ' (' . $head->head_type . ')',
            ];
        });

        return response()->json([
            'results' => $results
        ]);
    }


}
