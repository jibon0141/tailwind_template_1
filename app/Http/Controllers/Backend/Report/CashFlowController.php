<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\CompanyCashFlow;
use App\Models\Depo;
use App\Models\DepoCashFlow;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class CashFlowController extends Controller
{

    public function companyCashFlow(Request $request)
    {
        if ($request->ajax()) {

            $query = CompanyCashFlow::with('account');

            if ($request->filled('account_id')) {
                // Filter by selected account
                $query->where('account_id', $request->account_id);

                $openingBalance = optional(
                    Account::find($request->account_id)
                )->opening_balance ?? 0;
            } else {
                // No account selected → sum of all accounts with depo_id = 0
                $openingBalance = Account::where('depo_id', 0)->sum('opening_balance');
                // Show transactions only for these accounts
                $query->whereHas('account', function($q){
                    $q->where('depo_id', 0);
                });
            }

            // Date filters
            if ($request->filled('start_date')) {
                $query->whereDate('date', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('date', '<=', $request->end_date);
            }

            $query->orderBy('date', 'asc')->orderBy('id', 'asc');

            $rows = $query->get();

            // Calculate running balance
            $balance = $openingBalance;
            $data = $rows->map(function ($row, $key) use (&$balance) {
                $balance = $balance - $row->dr_amount + $row->cr_amount;

                return [
                    'DT_RowIndex' => $key + 1,
                    'date'        => Carbon::parse($row->date)->format('Y-m-d'),
                    'invoice_id'  => ($row->voucher_route && $row->voucher_id)
                        ? '<a href="' . route($row->voucher_route, $row->voucher_id) . '" class="text-teal-600">'
                        . ($row->invoice_id ?? 'View') .
                        '</a>'
                        : ($row->invoice_id ?? '—'),
                    'account_name' => $row->account->account_name,
                    'description' => $row->description,
                    'debit'       => number_format($row->dr_amount, 2),
                    'credit'      => number_format($row->cr_amount, 2),
                    'balance'     => number_format($balance, 2),
                ];
            });

            return DataTables::of($data)
                ->with('opening_balance', $openingBalance)
                ->rawColumns(['invoice_id'])
                ->make(true);
        }

        return view('admin.extends.report.cash_flow.company_cash_flow');
    }



    public function getCompanyAccounts(Request $request)
    {
        $search = $request->q;

        $query = Account::where('depo_id', 0);

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


    public function depoCashFlow(Request $request)
    {
        if ($request->ajax()) {

            $query = DepoCashFlow::with(['account','depo']);

            // Filter by depo
            if ($request->filled('depo_id')) {
                $query->where('depo_id', $request->depo_id);

                // Only account selected → opening balance of that account
                if ($request->filled('account_id')) {
                    $query->where('account_id', $request->account_id);
                    $openingBalance = optional(Account::find($request->account_id))->opening_balance ?? 0;
                } else {
                    // Only depo → sum of all accounts in that depo
                    $openingBalance = Account::where('depo_id', $request->depo_id)->sum('opening_balance');
                }
            } else {
                // No depo selected → sum opening balances of all depos
                $openingBalance = Account::sum('opening_balance');
            }

            // Optional date filters
            if ($request->filled('start_date')) {
                $query->whereDate('date', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('date', '<=', $request->end_date);
            }

            $query->orderBy('date', 'asc')->orderBy('id', 'asc');
            $rows = $query->get();

            // Calculate running balance
            $balance = $openingBalance;
            $data = $rows->map(function ($row, $key) use (&$balance) {
                $balance = $balance - $row->dr_amount + $row->cr_amount;

                return [
                    'DT_RowIndex'  => $key + 1,
                    'date'         => Carbon::parse($row->date)->format('Y-m-d'),
                    'invoice_id'   => ($row->voucher_route && $row->voucher_id)
                        ? '<a href="' . route($row->voucher_route, $row->voucher_id) . '" class="text-teal-600">'
                        . ($row->invoice_id ?? 'View') .
                        '</a>'
                        : ($row->invoice_id ?? '—'),
                    'depo_name'    => $row->depo->depo_name,
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

        return view('admin.extends.report.cash_flow.depo_cash_flow');
    }



    public function getDepos(Request $request)
    {
        $depos = Depo::select('id', 'depo_name')->get();

        $results = $depos->map(function($depo){
            return [
                'id' => $depo->id,
                'text' => $depo->depo_name
            ];
        });

        return response()->json(['results' => $results]);
    }




    public function getDepoAccounts(Request $request)
    {
        $search = $request->q;

        $query = Account::query();

        if ($request->filled('depo_id')) {
            $query->where('depo_id', $request->depo_id);
        }

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
