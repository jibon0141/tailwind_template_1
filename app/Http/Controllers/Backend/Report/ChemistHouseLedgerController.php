<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Models\ChemistHouse;
use App\Models\ChemistHouseLedger;
use App\Models\Depo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class ChemistHouseLedgerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            // Check if any filter is applied
            $hasFilter = $request->filled('custom_search')
                || $request->filled('start_date')
                || $request->filled('end_date');

            // No filter → return empty table
            if (!$hasFilter) {
                return DataTables::of([])->make(true);
            }

            $query = ChemistHouseLedger::with('chemistHouse');

            // Filter by Chemist House (Select2)
            if ($request->filled('custom_search')) {
                $chemistHouseId = $request->custom_search;
                $query->where('chemist_house_id', $chemistHouseId);
            }

            // Date filters
            if ($request->filled('start_date')) {
                $startDate = Carbon::parse($request->start_date)->startOfDay();
                $query->where('date', '>=', $startDate);
            }

            if ($request->filled('end_date')) {
                $endDate = Carbon::parse($request->end_date)->endOfDay();
                $query->where('date', '<=', $endDate);
            }

            // Order for correct running balance
            $query->orderBy('date', 'asc')->orderBy('id', 'asc');

            $ledgers = $query->get();

            // Opening balance (can be extended later)
            $openingBalance = 0;

            // Running balance
            $balance = $openingBalance;

            $data = $ledgers->map(function ($row, $key) use (&$balance) {

                // Chemist House Rule:
                // balance = credit - debit
                $balance = $balance + $row->credit - $row->debit;

                return [
                    'DT_RowIndex'    => $key + 1,
                    'date'           => Carbon::parse($row->date)->format('Y-m-d'),
                    'invoice_id'     => $row->voucher_route
                        ? '<a href="' . route($row->voucher_route, $row->voucher_id) . '" class="text-teal-600">'
                        . $row->invoice_id . '</a>'
                        : $row->invoice_id,
                    'chemist_house'  => $row->chemistHouse->shop_name ?? '',
                    'purpose'        => $row->purpose,
                    'voucher_amount' => number_format(max($row->debit, $row->credit), 2),
                    'debit'          => number_format($row->debit, 2),
                    'credit'         => number_format($row->credit, 2),
                    'balance'        => number_format($balance, 2),
                ];
            });

            return DataTables::of($data)
                ->with('opening_balance', $openingBalance)
                ->rawColumns(['invoice_id'])
                ->make(true);
        }

        return view('admin.extends.report.chemist_house_ledger_report.index');
    }


    public function getChemistHouses(Request $request)
    {
        $search = $request->q;

        $query = ChemistHouse::query();

        if ($search) {
            $query->where('shop_name', 'like', "%{$search}%")
                ->orWhere('owner_name', 'like', "%{$search}%");
        }

        $results = $query
            ->orderBy('shop_name')
            ->limit(50)
            ->get()
            ->map(fn ($s) => [
                'id'         => $s->id,
                'text'       => $s->shop_name,
                'owner_name' => $s->owner_name,
            ]);

        return response()->json(['results' => $results]);
    }



}
