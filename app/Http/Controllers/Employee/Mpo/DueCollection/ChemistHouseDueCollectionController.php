<?php

namespace App\Http\Controllers\Employee\Mpo\DueCollection;

use App\Http\Controllers\Controller;
use App\Models\ChemistHouseDuePayment;
use App\Models\Depo;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class ChemistHouseDueCollectionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $userId=!empty(Session::get('userObj')) ? Session::get('userObj')->id : Auth::user()->id;

            $depoId = Employee::where('user_id', $userId)
                ->value('depo_id'); // get logged-in MPO's depo

            $query = ChemistHouseDuePayment::with(['account', 'chemistHouse'])
                ->whereHas('chemistHouse', function ($q) use ($depoId, $userId) {
                    $q->where('depo_id', $depoId)
                        ->where('mpo_id', $userId); // MPO filter
                })
                ->whereHas('account', function ($q) use ($depoId) {
                    $q->where('depo_id', $depoId); // Depo filter
                });

            // Filter by start_date
            if ($request->start_date) {
                $startDate = Carbon::parse($request->start_date)->format('Y-m-d');
                $query->whereDate('payment_date', '>=', $startDate);
            }

            // Filter by end_date
            if ($request->end_date) {
                $endDate = Carbon::parse($request->end_date)->format('Y-m-d');
                $query->whereDate('payment_date', '<=', $endDate);
            }

            // Filter by payment status
            if ($request->payment_status) {
                $query->where('payment_status', $request->payment_status);
            }

            $chemistHousePayments = $query->orderByDesc('id')->get();

        return DataTables::of($chemistHousePayments)
                ->addIndexColumn()
                ->addColumn('chemist_house_name', function ($row) {
                    return $row->chemist_house_name ?? 'N/A';
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
                return isset($row->receiving_amount)
                    ? number_format($row->receiving_amount, 2)
                    : 'N/A';
            })
            ->addColumn('current_due', function ($row) {
                if (isset($row->balance) && isset($row->receiving_amount)) {
                    $current_due = $row->balance - $row->receiving_amount;
                    return number_format($current_due, 2);
                }
                return 'N/A';
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
                    $editUrl = route('mpo.chemist-house-due-payment.show', $row->id);

                    $buttons = '<div class="flex gap-2">';
                    $buttons .= '<a href="' . $editUrl . '"
                     class="inline-flex items-center px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold rounded"
                     title="View">
                     <i class="fa fa-eye"></i>
                 </a>';
                    $buttons .= '</div>';

                    return $buttons;
                })
                ->rawColumns(['payment_status', 'action'])
                ->make(true);
        }

        return view('employee.mpo.extends.chemist_house_due_payment.index');
    }


    public function show($id)
    {
        try {
            $payment = ChemistHouseDuePayment::with(['account', 'chemistHouse'])->findOrFail($id);

            Log::info('Chemist House Payment Showed Successfully.');
            return view('employee.mpo.extends.chemist_house_due_payment.show', compact('payment'));
        } catch (\Exception $e) {
            Log::info('Chemist House Payment Show Failed.');
            return redirect()->back()->with('error', $e->getMessage());
        }
    }



    public function print($id){
        try {
            $payment = ChemistHouseDuePayment::with(['account', 'chemistHouse'])->findOrFail($id);

            Log::info('Chemist House Payment Showed Successfully.');
            return view('employee.mpo.print.due_collection', compact('payment'));
        } catch (\Exception $e) {
            Log::info('Chemist House Payment Show Failed.');
            return redirect()->back()->with('error', $e->getMessage());
        }

    }

}
