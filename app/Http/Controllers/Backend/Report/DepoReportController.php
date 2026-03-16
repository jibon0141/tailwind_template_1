<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;
use App\Models\Depo;
use App\Models\DepoLedger;
use App\Models\DistributeItem;
use App\Models\Sale;
use Carbon\Carbon;
use Dflydev\DotAccessData\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class DepoReportController extends Controller
{
    public function depoLedger(Request $request){
        if ($request->ajax()) {

            // Check if any filter is applied
            $hasFilter = $request->filled('custom_search')
                || $request->filled('start_date')
                || $request->filled('end_date');

            // No filter → empty table
            if (!$hasFilter) {
                return DataTables::of([])->make(true);
            }

            $query = DepoLedger::with('depo');

            // Filter by depo (Select2)
            if ($request->filled('custom_search')) {
                $depoId = $request->custom_search;
                $query->where('depo_id', $depoId);
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

            $query->orderBy('date', 'asc')->orderBy('id', 'asc');

            $ledgers = $query->get();

            // Opening balance safely
            $openingBalance = 0;

            // Start running balance
            $balance = $openingBalance;

            $data = $ledgers->map(function ($row, $key) use (&$balance) {

                // Running balance calculation
                $balance = $balance + $row->credit - $row->debit;

                return [
                    'DT_RowIndex'    => $key + 1,
                    'date'           => Carbon::parse($row->date)->format('Y-m-d'),
                    'invoice_id'     => $row->voucher_route
                        ? '<a href="' . route($row->voucher_route, $row->voucher_id) . '" class="text-teal-600">'
                        . $row->invoice_id . '</a>'
                        : $row->invoice_id,
                    'depo'           => $row->depo->depo_name ?? '',
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
        return view('admin.extends.report.depo_report.ledger_report');
    }
    public function profit(Request $request)
    {

        if ($request->ajax()) {
            $sales = Sale::with('chemistHouse', 'account', 'mpo', 'depo', 'items')
                ->orderBy('id', 'desc');

            // Filter by date range
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $start = Carbon::parse($request->start_date)->startOfDay();
                $end = Carbon::parse($request->end_date)->endOfDay();
                $sales->whereBetween('sale_date', [$start, $end]);
            }

            // Filter by order status
            if ($request->filled('order_status')) {
                $sales->where('order_status', $request->order_status);
            }

            return datatables()->of($sales->get())
                ->addIndexColumn()
                ->addColumn('sale_voucher', fn($row) => $row->sale_voucher ?? 'N/A')
                ->addColumn('depo_name', fn($row) => $row->depo?->depo_name ?? 'N/A')
                ->addColumn('chemist_house', fn($row) => $row->chemistHouse?->shop_name ?? 'N/A')
                ->addColumn('mpo_name', fn($row) => $row->mpo?->full_name ?? 'Depo Sale')
                ->addColumn('sale_date', fn($row) => Carbon::parse($row->sale_date)->format('d-m-Y'))
                ->addColumn('final_total', fn($row) => $row->final_total ?? 'N/A')
                ->addColumn('total_profit', function ($row) {
                    return number_format(
                        $row->items->sum(function ($item) {
                            $purchase = $item->medicine->purchase_price ?? 0;
                            $sale = $item->unit_cost ?? 0;
                            $qty = $item->quantity ?? 0;

                            return (($sale * $qty))
                                - ($purchase * $qty);
                        }),
                        2
                    );
                })
                ->addColumn('order_status', function ($row) {
                    return match ($row->order_status) {
                        1 => '<span class="px-2 py-1 bg-yellow-200 text-yellow-800 rounded-full text-xs font-semibold">Pending</span>',
                        2 => '<span class="px-2 py-1 bg-green-200 text-green-800 rounded-full text-xs font-semibold">Approved</span>',
                        3 => '<span class="px-2 py-1 bg-blue-200 text-blue-800 rounded-full text-xs font-semibold">Delivered</span>',
                        default => '<span class="px-2 py-1 bg-gray-200 text-gray-800 rounded-full text-xs font-semibold">N/A</span>',
                    };
                })
                ->addColumn('action', fn($row) => '<a href="' . route('report.depo.show.profit', $row->id) . '" class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded"><i class="fa fa-eye"></i></a>')
                ->rawColumns(['order_status', 'action'])
                ->make(true);

        }
        return view('admin.extends.report.depo_report.profit');
    }

    public function showProfit($id)
    {
        try {
            $sale = Sale::with('items.medicine', 'chemistHouse', 'account')->find($id);

            if (empty($sale)) {
                Log::error('Sale not found: ID ' . $id);
                return redirect()->back()->with('error', 'Sale not found');
            }

            $depo = Depo::where('id', $sale->depo_id)->first();

            return view('admin.extends.report.depo_report.profit_show', compact('sale', 'depo'));

        } catch (\Exception $e) {
            Log::error('Sale show failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Sale Show Failed!');
        }
    }

    public function printProfit($id)
    {
        try {
            $sale = Sale::with('items.medicine', 'chemistHouse', 'account')->find($id);

            if (empty($sale)) {
                Log::error('Sale not found: ID ' . $id);
                return redirect()->back()->with('error', 'Sale not found');
            }

            $depo = Depo::where('id', $sale->depo_id)->first();

            return view('admin.print.depo_profit_report_print', compact('sale', 'depo'));

        } catch (\Exception $e) {
            Log::error('Sale show failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Sale Show Failed!');
        }
    }

    public function monthlyProfit(Request $request)
    {
        if ($request->ajax()) {

            $startDate = $request->start_date
                ? Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay()
                : null;

            $endDate = $request->end_date
                ? Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay()
                : null;


            $sales = Sale::with(['depo', 'items'])
                ->whereBetween('sale_date', [$startDate, $endDate])
                ->get();

            $depoProfits = [];

            foreach ($sales as $sale) {
                $month = Carbon::parse($sale->sale_date)->format('F Y'); // e.g., February 2026

                foreach ($sale->items as $item) {

                    $distributeItem = DistributeItem::whereHas('distribute', function ($q) use ($sale) {
                        $q->where('depo_id', $sale->depo_id);
                    })
                        ->where('medicine_id', $item->medicine_id)
                        ->latest()
                        ->first();

                    if ($distributeItem) {
                        $profit = ($item->unit_cost - $distributeItem->unit_cost) * $item->quantity;

                        $key = $sale->depo->depo_name . '|' . $month; // unique per depo + month

                        if (!isset($depoProfits[$key])) {
                            $depoProfits[$key] = [
                                'depo_name' => $sale->depo->depo_name,
                                'month' => $month,
                                'profit' => 0,
                            ];
                        }

                        $depoProfits[$key]['profit'] += $profit;
                    }
                }
            }

            $depoProfitsCollection = collect($depoProfits)->map(function ($row) {
                return [
                    'depo_name' => $row['depo_name'],
                    'month' => $row['month'],
                    'profit' => number_format($row['profit'], 2),
                ];
            });

            return DataTables::of($depoProfitsCollection)
                ->addIndexColumn()
                ->addColumn('depo_name', fn($row) => $row['depo_name'])
                ->addColumn('month', fn($row) => $row['month'])
                ->addColumn('profit', fn($row) => $row['profit'])
                ->make(true);
        }

        return view('admin.extends.report.depo_report.monthly_profit');
    }




    public function getDepos(Request $request)
    {
        $search = $request->q;

        $query = Depo::query();

        if ($search) {
            $query->where('depo_name', 'like', "%{$search}%");
        }

        $depos = $query->orderBy('depo_name')->limit(50)->get();

        $results = $depos->map(fn ($d) => [
            'id'   => $d->id,
            'text' => $d->depo_name,
        ]);

        return response()->json(['results' => $results]);
    }

}
