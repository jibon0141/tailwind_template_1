<?php

namespace App\Http\Controllers\Depo\Sale;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\CompanySetting;
use App\Models\Depo;
use App\Models\Employee;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class SaleController extends Controller
{

    public function index(Request $request)
    {
        $userId = !empty(Session::get('userObj')) ? Session::get('userObj')->id : Auth::user()->id;

        if ($request->ajax()) {
            $depo = Depo::where('user_id', $userId)->first();

            $sales = Sale::with('chemistHouse', 'account', 'mpo')
                ->where('depo_id', $depo->id)
                ->whereNotNull('mpo_id')
                ->orderBy('id', 'desc');

            // Filter by date range
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $start = \Carbon\Carbon::parse($request->start_date)->startOfDay();
                $end = \Carbon\Carbon::parse($request->end_date)->endOfDay();
                $sales->whereBetween('sale_date', [$start, $end]);
            }

            // Filter by order status
            if ($request->filled('order_status')) {
                $sales->where('order_status', $request->order_status);
            }

            return datatables()->of($sales->get())
                ->addIndexColumn()
                ->addColumn('sale_voucher', fn($row) => $row->sale_voucher ?? 'N/A')
                ->addColumn('mpo_name', fn($row) => $row->mpo?->full_name ?? 'N/A')
                ->addColumn('sale_date', fn($row) => \Carbon\Carbon::parse($row->sale_date)->format('d-m-Y'))
                ->addColumn('final_total', fn($row) => $row->final_total ?? 'N/A')
                ->addColumn('order_status', function($row) {
                    return match($row->order_status) {
                        1 => '<span class="px-2 py-1 bg-yellow-200 text-yellow-800 rounded-full text-xs font-semibold">Pending</span>',
                        2 => '<span class="px-2 py-1 bg-green-200 text-green-800 rounded-full text-xs font-semibold">Approved</span>',
                        3 => '<span class="px-2 py-1 bg-blue-200 text-blue-800 rounded-full text-xs font-semibold">Delivered</span>',
                        default => '<span class="px-2 py-1 bg-gray-200 text-gray-800 rounded-full text-xs font-semibold">N/A</span>',
                    };
                })
                ->addColumn('action', fn($row) =>
                    '<a href="'.route('depo.sale.show', $row->id).'" class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded mr-1">
        <i class="fa fa-eye"></i>
    </a>
    <a href="'.route('depo.sale.manage.edit', $row->id).'" class="px-2 py-1 bg-green-500 hover:bg-green-600 text-white text-xs rounded">
        <i class="fa fa-edit"></i>
    </a>'
                )
                ->rawColumns(['order_status','action'])
                ->make(true);
        }

        return view('depo.extends.sale.index');
    }

    public function manageEdit($id){
        $userId = !empty(Session::get('userObj'))
            ? Session::get('userObj')->id
            : Auth::user()->id;
        $depoId = Depo::where('user_id', $userId)->first()->id;

        $accounts=Account::where('depo_id', $depoId)->get();

        $sale = Sale::with('items.medicine', 'chemistHouse', 'account')->find($id);

        return view('depo.extends.sale.manageEdit', compact('sale','accounts'));

    }

    public function manageUpdate(Request $request, $id)
    {

        $request->validate([
            'order_status' => 'required|in:1,2,3',
        ]);


        $sale = Sale::findOrFail($id);

        $sale->order_status = $request->order_status;

        $sale->save();


        return redirect()->back()->with('success', 'Sale Status Updated');
    }


    public function show($id)
    {
        $userId = !empty(Session::get('userObj'))
            ? Session::get('userObj')->id
            : Auth::user()->id;

        $depoId = Depo::where('user_id', $userId)->first()->id;

        try {
            $depo=Depo::where('id', $depoId)->first();
            $sale = Sale::with('items.medicine', 'chemistHouse', 'account')->find($id);

            if (empty($sale)) {
                Log::error('Sale not found: ID ' . $id);
                return redirect()->back()->with('error', 'Sale not found');
            }

            return view('depo.extends.sale.show', compact('sale','depo'));

        } catch (\Exception $e) {
            Log::error('Sale show failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Sale Show Failed!');
        }
    }

   public function print($id){

       $userId = !empty(Session::get('userObj'))
           ? Session::get('userObj')->id
           : Auth::user()->id;

       $depoId = Depo::where('user_id', $userId)->first()->id;

       try {
           $depo=Depo::where('id', $depoId)->first();
           $sale = Sale::with('items.medicine', 'chemistHouse', 'account')->find($id);

           if (empty($sale)) {
               Log::error('Sale not found: ID ' . $id);
               return redirect()->back()->with('error', 'Sale not found');
           }

           return view('depo.print.sale_voucher', compact('sale','depo'));

       } catch (\Exception $e) {
           Log::error('Sale show failed: ' . $e->getMessage());
           return redirect()->back()->with('error', 'Sale Show Failed!');
       }

   }


    public function posPrint($id){

        $userId = !empty(Session::get('userObj'))
            ? Session::get('userObj')->id
            : Auth::user()->id;

        $depoId = Depo::where('user_id', $userId)->first()->id;

        try {
            $depo=Depo::where('id', $depoId)->first();
            $sale = Sale::with('items.medicine', 'chemistHouse', 'account')->find($id);

            if (empty($sale)) {
                Log::error('Sale not found: ID ' . $id);
                return redirect()->back()->with('error', 'Sale not found');
            }

            return view('depo.print.sale_voucher_pos', compact('sale','depo'));

        } catch (\Exception $e) {
            Log::error('Sale show failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Sale Show Failed!');
        }

    }

}
