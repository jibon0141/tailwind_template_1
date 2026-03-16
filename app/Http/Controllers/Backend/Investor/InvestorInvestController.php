<?php

namespace App\Http\Controllers\Backend\Investor;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\CompanySetting;
use App\Models\Investor;
use App\Models\InvestorInvest;
use App\Models\InvestorLedger;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class InvestorInvestController extends Controller
{
        public function index(Request $request)
        {

            if ($request->ajax()) {

                $query = InvestorInvest::with(['account', 'investor']);

//                // Filter by payment status
//                if ($request->filled('payment_status')) {
//                    $query->where('payment_status', $request->payment_status);
//                }
//
                // Filter by date range
                if ($request->filled('start_date') && $request->filled('end_date')) {
                    $start = Carbon::parse($request->start_date)->startOfDay();
                    $end = Carbon::parse($request->end_date)->endOfDay();
                    $query->whereBetween('payment_date', [$start, $end]);
                }

                $investorInvests = $query->orderByDesc('id')->get();


                return DataTables::of($investorInvests)
                    ->addIndexColumn()
                    ->addColumn('investor_name', function ($row) {
                        return $row->investor_name ?? 'N/A';
                    })
                    ->addColumn('investor_code', function ($row) {
                        return $row->investor_code ?? 'N/A';
                    })
                    ->addColumn('invest_voucher ', function ($row) {
                        return $row->invest_voucher ?? 'N/A';
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
                    ->addColumn('invest_amount', function ($row) {
                        return $row->invest_amount ?? 'N/A';
                    })
                    ->addColumn('investing_amount', function ($row) {
                        return $row->investing_amount ?? 'N/A';
                    })

                    ->addColumn('current_total_invest', function ($row) {

                        $currentTotalInvest =
                            ($row->invest_amount ?? 0) + ($row->investing_amount ?? 0);


                        // Format as 2 decimal points
                        return number_format($currentTotalInvest, 2);
                    })

                    ->addColumn('payment_status', function ($row) {

                        $baseClass = 'inline-flex justify-center items-center px-3 py-1 text-xs font-semibold rounded-full min-w-[80px]';

                        switch ((int) $row->payment_status) {

                            case 1: // Invest
                                return "<span class='{$baseClass} text-green-800 bg-green-200'>Invest</span>";

                            case 2: // Withdraw
                                return "<span class='{$baseClass} text-red-800 bg-red-200'>Withdraw</span>";

                            default:
                                return "<span class='{$baseClass} text-gray-800 bg-gray-200'>Unknown</span>";
                        }
                    })


                    ->addColumn('action', function ($row) {
                        $editUrl = route('admin.investor.invest.show', $row->id);


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
        return view('admin.extends.investor_invest.index');
    }

    public function create(Request $request){

        if($request->isMethod('post')){

            $request->validate([
                'investor_id'        => 'required|exists:investors,id',
                'investor_name'      => 'required|string|max:255',
                'investor_code'      => 'required|string|max:100',
                'phone'              => 'nullable|string|max:20',
                'payment_date'       => 'required|date',
                'account_id'         => 'required|exists:accounts,id',
                'invest_amount'      => 'required|numeric|min:0',
                'investing_amount'   => 'required|numeric|min:0.01',
            ]);

            try{

                DB::beginTransaction();

                $InvestorInvest = InvestorInvest::create([
                    'investor_id'      => $request->investor_id,
                    'investor_name'    => $request->investor_name,
                    'investor_code'    => $request->investor_code,
                    'phone'            => $request->phone,
                    'payment_date'     => $request->payment_date,
                    'account_id'       => $request->account_id,
                    'invest_amount'    => $request->invest_amount,
                    'investing_amount' => $request-> investing_amount,
                    'payment_status'   => 1,
                    'created_at'       => now(),
                ]);

            // Updating Company Account
                $companyAccount=Account::where('id',$request->account_id)->first();
                if(empty($companyAccount)){
                    Log::info('Account not found.');
                    return redirect()->back()->with('error','Investor not found.');
                }
                $currentBalance=$companyAccount->balance+$request->investing_amount;
                $companyAccount->update([
                    'balance' => $currentBalance,
                ]);


            // Updating Investor Invest Balance
                $investor=Investor::where('id',$request->investor_id)->first();

                if(empty($investor)){
                    Log::info('Investor not found.');
                    return redirect()->back()->with('error','Investor not found.');
                }
                $investorBalance=$investor->invest_amount+$request->investing_amount;
                $investor->update([
                    'invest_amount'=>$investorBalance,
                ]);

            // Investor Ledger Part
                InvestorLedger::create([
                    'investor_id'     => $investor->id,
                    'date'            => $request->payment_date,
                    'invoice_id'      => $InvestorInvest->invest_voucher,
                    'purpose'         => 'Invest',
                    'debit'           => 0,
                    'credit'          => $request->investing_amount ?? 0,
                    'current_amount'  => $investorBalance,
                    'voucher_route'   => 'admin.investor.invest.show',
                    'voucher_id'      => $InvestorInvest->id,
                    'status'          => 1,
                    'created_at'       => now(),
                ]);

                DB::commit();
                Log::info('Investor Invest successfully created.');
                return redirect('admin/investor/invest/show/' . $InvestorInvest->id)
                    ->with('success', 'Investor added successfully.');

            }
            catch(\Exception $e){
                DB::rollBack();
                Log::error($e->getMessage());
                return redirect()->back()->with('error','Investor not found.');
            }

        }
        $companyAccounts = Account::where('depo_id', 0)->get();
        return view('admin.extends.investor_invest.create',compact('companyAccounts'));
    }



    public function show($id){

            try{
                $mainCompany=CompanySetting::first();
                $invest=InvestorInvest::with(['account','investor'])
                ->where('id',$id)->first();

                if(empty($invest)){
                    Log::info('Investor not found.');
                    return redirect()->back()->with('error','Investor not found.');
                }

                Log::info('Investor Invest successfully Showed.');
                return view('admin.extends.investor_invest.show',compact('invest','mainCompany'));
            }
            catch(\Exception $e){
                Log::error($e->getMessage());
                return redirect()->back()->with('error','Investor not Showed.');
            }
    }

    public function print($id){

        try{
            $mainCompany=CompanySetting::first();
            $invest=InvestorInvest::with(['account','investor'])
                ->where('id',$id)->first();

            if(empty($invest)){
                Log::info('Investor not found.');
                return redirect()->back()->with('error','Investor not found.');
            }

            Log::info('Investor Invest successfully Printed.');
            return view('admin.print.investor_invest_print',compact('invest','mainCompany'));
        }
        catch(\Exception $e){
            Log::error($e->getMessage());
            return redirect()->back()->with('error','Investor Invest not Printed.');
        }
    }

    public function getInvestorData(Request $request){

        try {
            $search = $request->query('query');
            $investor = Investor::where('name', 'like', "%{$search}%")->get();
            Log::info('Investor Data Found.');
            return response()->json($investor);
        } catch (\Exception $e) {
            Log::info('Investor Data Not Found.');
        }

    }

}
