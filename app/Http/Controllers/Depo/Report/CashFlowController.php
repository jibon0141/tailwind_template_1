<?php

namespace App\Http\Controllers\Depo\Report;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Depo;
use App\Models\DepoCashFlow;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class CashFlowController extends Controller
{
    public function cashFlow(Request $request)
    {
        if ($request->ajax()) {

            $userId = !empty(Session::get('userObj'))
                ? Session::get('userObj')->id
                : Auth::user()->id;

            // Get logged-in depo
            $depo = Depo::where('user_id', $userId)->first();

            if (!$depo) {
                return DataTables::of([])->make(true);
            }

            $query = DepoCashFlow::with(['account','depo'])
                ->where('depo_id', $depo->id); // ✅ Always filter by login depo

            // Opening Balance Calculation
            if ($request->filled('account_id')) {

                $query->where('account_id', $request->account_id);

                $openingBalance = optional(
                    Account::where('id', $request->account_id)
                        ->where('depo_id', $depo->id)
                        ->first()
                )->opening_balance ?? 0;

            } else {
                // Sum all account opening balances of this depo
                $openingBalance = Account::where('depo_id', $depo->id)
                    ->sum('opening_balance');
            }

            // Date Filters
            if ($request->filled('start_date')) {
                $query->whereDate('date', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('date', '<=', $request->end_date);
            }

            $query->orderBy('date', 'asc')->orderBy('id', 'asc');

            $rows = $query->get();

            // Running Balance
            $balance = $openingBalance;

            $data = $rows->map(function ($row, $key) use (&$balance) {

                $balance = $balance - $row->dr_amount + $row->cr_amount;

                return [
                    'DT_RowIndex'  => $key + 1,
                    'date'         => \Carbon\Carbon::parse($row->date)->format('Y-m-d'),
                    'invoice_id'   => ($row->voucher_route && $row->voucher_id)
                        ? '<a href="' . route($row->voucher_route, $row->voucher_id) . '" class="text-teal-600">'
                        . ($row->invoice_id ?? 'View') .
                        '</a>'
                        : ($row->invoice_id ?? '—'),
                    'depo_name'    => $row->depo->depo_name ?? '—',
                    'account_name' => $row->account->account_name ?? '—',
                    'description'  => $row->description,
                    'debit'        => number_format($row->dr_amount, 2),
                    'credit'       => number_format($row->cr_amount, 2),
                    'balance'      => number_format($balance, 2),
                ];
            });

            return DataTables::of($data)
                ->with('opening_balance', $openingBalance)
                ->rawColumns(['invoice_id'])
                ->make(true);
        }

        return view('depo.extends.report.cash_flow.index');
    }


    public function getDepoAccounts(Request $request)
    {
        $userId = !empty(Session::get('userObj'))
            ? Session::get('userObj')->id
            : Auth::user()->id;

        $depo = Depo::where('user_id', $userId)->first();

        if (!$depo) {
            return response()->json([
                'results' => []
            ]);
        }

        $search = $request->q;

        $query = Account::where('depo_id', $depo->id);

        if (!empty($search)) {
            $query->where('account_name', 'like', "%{$search}%");
        }

        $accounts = $query
            ->orderBy('account_name')
            ->limit(50)
            ->get();

        $results = $accounts->map(function ($account) {
            return [
                'id'   => $account->id,
                'text' => $account->account_name,
                'code' => $account->account_no ?? '',
            ];
        });

        return response()->json([
            'results' => $results
        ]);
    }



}
