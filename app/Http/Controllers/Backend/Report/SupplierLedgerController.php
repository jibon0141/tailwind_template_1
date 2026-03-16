<?php

namespace App\Http\Controllers\Backend\Report;
use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\SupplierLedger;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class SupplierLedgerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            // Check if any filter is applied
            $hasFilter = $request->filled('custom_search') || $request->filled('start_date') || $request->filled('end_date');

            // If no filter, return empty DataTable
            if (!$hasFilter) {
                return DataTables::of([])->make(true);
            }

            $query = SupplierLedger::with('supplier');

            // Filter by selected supplier from Select2
            if ($request->filled('custom_search')) {
                $supplierId = $request->custom_search;
                $query->whereHas('supplier', function ($q) use ($supplierId) {
                    $q->where('id', $supplierId);
                });
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

            // get opening balance safely
            $openingBalance = optional($ledgers->first()?->supplier)->opening_balance ?? 0;

           // start balance from opening balance
            $balance = $openingBalance;

            $data = $ledgers->map(function ($row, $key) use (&$balance) {
                // Calculate balance correctly
                $balance = $balance + $row->debit - $row->credit;

                return [
                    'DT_RowIndex'    => $key + 1,
                    'date'           => Carbon::parse($row->date)->format('Y-m-d'),
                    'invoice_id'     => $row->voucher_route
                        ? '<a href="' . route($row->voucher_route, $row->voucher_id) . '" class="text-teal-600">' . $row->invoice_id . '</a>'
                        : $row->invoice_id,
                    'supplier'       => $row->supplier->supplier_name ?? '',
                    'purpose'        => $row->purpose,
                    'voucher_amount' => number_format(max($row->debit, $row->credit), 2),
                    'debit'          => number_format($row->debit, 2),
                    'credit'         => number_format($row->credit, 2),
                    'balance'        => number_format($balance, 2),
                ];
            });


            return DataTables::of($data)
                ->with('opening_balance', $ledgers->first()->supplier->opening_balance ?? 0)
                ->rawColumns(['invoice_id'])->make(true);
        }

        return view('admin.extends.report.supplier_ledger_report.index');
    }




    public function getSuppliers(Request $request)
    {
        $search = $request->q;
        $query = Supplier::query();
        if ($search) {
            $query->where('supplier_name', 'like', "%$search%");
        }

        $suppliers = $query->orderBy('supplier_name')->limit(50)->get();

        // Map correctly
        $results = $suppliers->map(fn($s) => [
            'id'   => $s->id,
            'text' => $s->supplier_name,
            'code' => $s->supplier_code,
        ]);

        return response()->json(['results' => $results]);
    }




}
