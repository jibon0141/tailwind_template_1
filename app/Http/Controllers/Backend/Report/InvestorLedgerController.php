<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Models\Investor;
use App\Models\InvestorLedger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class InvestorLedgerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            // Check if any filter is applied
            $hasFilter = $request->filled('custom_search')
                || $request->filled('start_date')
                || $request->filled('end_date');

            if (!$hasFilter) {
                return DataTables::of([])->make(true);
            }

            $query = InvestorLedger::with('investor');

            // Filter by investor
            if ($request->filled('custom_search')) {
                $query->where('investor_id', $request->custom_search);
            }

            // Date filters
            if ($request->filled('start_date')) {
                $query->whereDate('date', '>=', Carbon::parse($request->start_date));
            }

            if ($request->filled('end_date')) {
                $query->whereDate('date', '<=', Carbon::parse($request->end_date));
            }

            $query->orderBy('date')->orderBy('id');

            $ledgers = $query->get();

            // Opening balance
            $openingBalance = $ledgers->first()?->investor?->opening_balance ?? 0;
            $balance = $openingBalance;

            $data = $ledgers->map(function ($row, $key) use (&$balance) {

                $balance = $balance + $row->credit - $row->debit;

                return [
                    'DT_RowIndex' => $key + 1,
                    'date'       => Carbon::parse($row->date)->format('Y-m-d'),
                    'invoice_id' => $row->voucher_route
                        ? '<a href="' . route($row->voucher_route, $row->voucher_id) . '" class="text-teal-600">'
                        . $row->invoice_id . '</a>'
                        : $row->invoice_id,
                    'investor'   => $row->investor->name ?? '',
                    'purpose'    => $row->status == 1 ? 'Investment' : 'Withdraw',
                    'voucher_amount' => number_format(max($row->credit, $row->debit), 2),
                    'debit'      => number_format($row->debit, 2),
                    'credit'     => number_format($row->credit, 2),
                    'balance'    => number_format($balance, 2),
                ];
            });

            return DataTables::of($data)
                ->with('opening_balance', $openingBalance)
                ->rawColumns(['invoice_id'])
                ->make(true);
        }

        return view('admin.extends.report.investor_ledger_report.index');
    }

    public function getInvestors(Request $request)
    {
        $search = $request->q;

        $investors = Investor::when($search, function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%");
        })
            ->orderBy('name')
            ->limit(50)
            ->get();

        return response()->json([
            'results' => $investors->map(fn ($i) => [
                'id'   => $i->id,
                'text' => $i->name,
                'investor_code' => $i->investor_code,
            ])
        ]);
    }
}
