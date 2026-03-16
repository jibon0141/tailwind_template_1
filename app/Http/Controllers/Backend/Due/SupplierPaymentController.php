<?php

namespace App\Http\Controllers\Backend\Due;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\CompanyCashFlow;
use App\Models\Supplier;
use App\Models\SupplierLedger;
use App\Models\SupplierPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class SupplierPaymentController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {

            $query = SupplierPayment::with(['account', 'supplier']);

            // Filter by payment status
            if ($request->filled('payment_status')) {
                $query->where('payment_status', $request->payment_status);
            }

            // Filter by date range
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $start = Carbon::parse($request->start_date)->startOfDay();
                $end = Carbon::parse($request->end_date)->endOfDay();
                $query->whereBetween('payment_date', [$start, $end]);
            }

            $supplierPayments = $query->orderByDesc('id')->get();


            return DataTables::of($supplierPayments)
                ->addIndexColumn()
                ->addColumn('supplier_name', function ($row) {
                    return $row->supplier_name ?? 'N/A';
                })
                ->addColumn('supplier_code', function ($row) {
                    return $row->supplier_code ?? 'N/A';
                })
                ->addColumn('payment_voucher ', function ($row) {
                    return $row->payment_voucher ?? 'N/A';
                })
                ->addColumn('payment_date', function ($row) {
                    return $row->payment_date ?? 'N/A';
                })
                ->addColumn('phone', function ($row) {
                    return $row->phone ?? 'N/A';
                })
                ->addColumn('company_account', function ($row) {
                    return $row->account->account_name ?? 'N/A';
                })
                ->addColumn('due_balance', function ($row) {
                    return $row->balance ?? 'N/A';
                })
                ->addColumn('paying_amount', function ($row) {
                    return $row->paying_amount ?? 'N/A';
                })
                ->addColumn('refund_amount', function ($row) {
                    return $row->refund_amount ?? 'N/A';
                })
                ->addColumn('current_due', function ($row) {
                    // Ensure numeric values
                    $balance = $row->balance ?? 0;
                    $payingAmount = $row->paying_amount ?? 0;
                    $refundAmount = $row->refund_amount ?? 0;

                    // Current due = balance - paying_amount + refund_amount
                    $currentDue = $balance - $payingAmount + $refundAmount;

                    // Format as 2 decimal points
                    return number_format($currentDue, 2);
                })
                ->addColumn('payment_status', function ($row) {

                    $baseClass = 'inline-flex justify-center items-center px-3 py-1 text-xs font-semibold rounded-full min-w-[70px]';

                    switch ((int)$row->payment_status) {

                        case 1: // Paid
                            return "<span class='{$baseClass} text-green-800 bg-green-200'>Paid</span>";

                        case 2: // Partial
                            return "<span class='{$baseClass} text-yellow-800 bg-yellow-200'>Partial</span>";

                        case 3: // Advance (Negative Balance)
                            return "<span class='{$baseClass} text-red-800 bg-red-200'>Advance</span>";

                        case 4: // Refund
                            return "<span class='{$baseClass} text-red-900 bg-red-300'>Refund</span>";

                        default:
                            return "<span class='{$baseClass} text-gray-800 bg-gray-200'>Unknown</span>";
                    }
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('admin.supplier-due-payment.show', $row->id);


                    $buttons = '<div class="flex gap-2">';

                    // Edit button
                    $buttons .= '<a href="' . $editUrl . '"
                     class="inline-flex items-center px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold rounded"
                     title="Edit">
                     <i class="fa fa-eye"></i>
                 </a>';


                    $buttons .= '</div>';

                    return $buttons;
                })
                ->rawColumns(['payment_status', 'action'])
                ->make(true);
        }

        return view('admin.extends.payment.supplier_payment.index');
    }

    public function create(Request $request)
    {

        if (!$request->isMethod('POST')) {
            session(['supplier_payment_form_token' => (string) Str::uuid()]);

            $companyAccounts = Account::where('depo_id', 0)->get();
            return view('admin.extends.payment.supplier_payment.create', compact('companyAccounts'));
        }

            //   DUPLICATE CHECK FIRST (before validation)
            if ($request->form_token !== session('supplier_payment_form_token')) {
                return redirect()->back()
                    ->with('error', 'Duplicate submission detected. Please reload the page.');
            }

            //  Invalidate token immediately
            session()->forget('supplier_payment_form_token');

            $request->validate([
                'supplier_id'   => 'required|exists:suppliers,id',
                'supplier_name' => 'required|string|max:255',
                'supplier_code' => 'nullable|string|max:255',
                'payment_date'  => 'nullable|date',
                'phone'         => 'nullable|string|max:20',
                'balance'       => 'required|numeric',
                'paying_amount' => 'nullable|numeric|min:0',
                'refund_amount' => 'nullable|numeric|min:0',
                'account_id'    => 'required|exists:accounts,id',
            ]);


            try {
                DB::beginTransaction();

                // Default payment date to today if not provided
                $paymentDate = $request->filled('payment_date') ? $request->payment_date : now()->toDateString();

                // Fetch amounts safely
                $payingAmount = $request->paying_amount ?? 0;
                $refundAmount = $request->refund_amount ?? 0;

                // Determine payment status
                $netPayment = $payingAmount - $refundAmount;
                if ($refundAmount > 0 || $netPayment < 0) {
                    $paymentStatus = 4; // Refund
                } elseif ($request->balance < 0) {
                    $paymentStatus = 3; // Advance (negative balance)
                } elseif ($netPayment == $request->balance && $request->balance > 0) {
                    $paymentStatus = 1; // Paid
                } elseif ($netPayment > 0 && $netPayment < $request->balance) {
                    $paymentStatus = 2; // Partial
                } else {
                    $paymentStatus = 0; // Unpaid / No payment
                }

                // Create Supplier Payment record
                $supplierPayment = SupplierPayment::create([
                    'supplier_id'    => $request->supplier_id,
                    'account_id'     => $request->account_id,
                    'supplier_name'  => $request->supplier_name,
                    'supplier_code'  => $request->supplier_code,
                    'payment_date'   => $paymentDate,
                    'phone'          => $request->phone,
                    'balance'        => $request->balance,
                    'paying_amount'  => $payingAmount,
                    'refund_amount'  => $refundAmount,
                    'payment_status' => $paymentStatus,
                ]);

                // Fetch company and supplier
                $companyAccount = Account::find($request->account_id);
                $supplier = Supplier::find($request->supplier_id);

                if (!$companyAccount || !$supplier) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Account or Supplier Not Found!');
                }

                //  Calculate net amount for company
                $netAmount = bcsub($payingAmount, $refundAmount, 2); // paying - refund

                // Check company balance if company needs to pay
                if ($netAmount > 0 && $companyAccount->balance < $netAmount) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Insufficient Company Balance!');
                }

                //  Update company account balance
                $companyAccount->balance = bcsub($companyAccount->balance, $netAmount, 2);
                $companyAccount->save();

                //  Update supplier balance correctly
                // Formula: supplier balance - paying amount + refund amount
                $supplier->balance = bcsub($supplier->balance, $payingAmount, 2); // subtract payment
                $supplier->balance = bcadd($supplier->balance, $refundAmount, 2); // add refund
                $supplier->save();

                //  Create Supplier Ledger
                SupplierLedger::create([
                    'supplier_id'   => $supplier->id,
                    'date'          => $paymentDate,
                    'invoice_id'    => $supplierPayment->payment_voucher,
                    'purpose'       => 'Payment Invoice',
                    'debit'         => $refundAmount > 0 ? $refundAmount : 0,   // refund recorded as debit
                    'credit'        => $payingAmount > 0 ? $payingAmount : 0,   // payment recorded as credit
                    'balance'       => $supplier->balance,                       // updated running balance
                    'voucher_route' => 'admin.supplier-due-payment.show',
                    'voucher_id'    => $supplierPayment->id,
                ]);

                // Cash Flow Section
                CompanyCashFlow::create([
                    'date'          => $paymentDate,
                    'invoice_id'    => $supplierPayment->payment_voucher,
                    'description'   => 'Payment Invoice',
                    'dr_amount'     => $payingAmount,
                    'cr_amount'     => $refundAmount,
                    'balance'       => $companyAccount->balance,
                    'account_id'    => $request->account_id,
                    'voucher_route' => 'admin.supplier-due-payment.show',
                    'voucher_id'    => $supplierPayment->id,
                ]);

                DB::commit();
                return redirect()
                    ->route('admin.supplier-due-payment.show', $supplierPayment->id)
                    ->with('success', 'Supplier payment created successfully!');

            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Payment creation failed: ' . $e->getMessage());
            }


    }



    public function show($id)
    {
        try {
            $payment = SupplierPayment::with(['account', 'supplier'])->where('id', $id)->first();

            if (empty($payment)) {
                Log::info('Supplier Payment Not Found.');
                return redirect()->back()->with('error', 'Supplier payment not found!');
            }
            Log::info('Supplier Payment Showed Successfully.');
            return view('admin.extends.payment.supplier_payment.show', compact('payment'));
        } catch (\Exception $e) {
            Log::info('Supplier Payment Show Failed.');
            return redirect()->back()->with('error', $e->getMessage());
        }

    }


    public function print($id)
    {

        try {
            $payment = SupplierPayment::with(['account', 'supplier'])->where('id', $id)->first();

            if (empty($payment)) {
                Log::info('Supplier Payment Not Found.');
                return redirect()->back()->with('error', 'Supplier payment not found!');
            }
            Log::info('Supplier Payment Showed Successfully.');
            return view('admin.print.supplier_payment_voucher', compact('payment'));
        } catch (\Exception $e) {
            Log::info('Supplier Payment Show Failed.');
            return redirect()->back()->with('error', $e->getMessage());
        }

    }


    public function getSupplierData(Request $request)
    {

        try {
            $search = $request->query('query');
            $supplier = Supplier::where('supplier_name', 'like', "%{$search}%")->get();
            Log::info('Supplier Payment Data Found');
            return response()->json($supplier);
        } catch (\Exception $e) {
            Log::info('Supplier Payment Data Not Found');
        }

    }

}
