<?php

namespace App\Http\Controllers\Backend\HeadOfficeDistribute;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\CompanySetting;
use App\Models\DepoDueAccount;
use App\Models\Distribute;
use App\Models\DistributeItem;
use App\Models\TempDistribute;
use App\Models\TempDistributeItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TempDistributeController extends Controller
{


    public function index(Request $request)
    {
        if ($request->ajax()) {

            $distributes = TempDistribute::with('depo')->orderBy('id', 'desc');

            // Filter by date range
            if ($request->filled('start_date')) {
                $distributes->where('distribute_date', '>=', Carbon::parse($request->start_date)->startOfDay());
            }
            if ($request->filled('end_date')) {
                $distributes->where('distribute_date', '<=', Carbon::parse($request->end_date)->endOfDay());
            }

            // Filter by order status
            if ($request->filled('order_status')) {
                $distributes->where('order_status', $request->order_status);
            }

            $distributes = $distributes->get();

            return datatables()->of($distributes)
                ->addIndexColumn()
                ->addColumn('distribute_voucher', fn($row) => $row->distribute_voucher ?? 'N/A')
                ->addColumn('depo_name', fn($row) => $row->depo->depo_name ?? 'N/A')
                ->addColumn('distribute_date', function ($row) {
                    return \Carbon\Carbon::parse($row->distribute_date)->format('d-m-Y');
                })
                ->addColumn('voucher_total', function($row) {
                    $total    = $row->total ?? 0;
                    $discount = $row->discount ?? 0;
                    $vatPerc  = $row->vat ?? 0;

                    // Calculate VAT on total after discount
                    $vatAmount = ($total - $discount) * ($vatPerc / 100);

                    return ($total - $discount) + $vatAmount;
                })
                ->addColumn('order_status', function($row) {
                    return match ($row->order_status) {
                        1 => '<span class="px-2 py-1 bg-yellow-200 text-yellow-800 rounded-full text-xs font-semibold">Pending</span>',
                        2 => '<span class="px-2 py-1 bg-green-200 text-green-800 rounded-full text-xs font-semibold">Approved</span>',
                        3 => '<span class="px-2 py-1 bg-blue-200 text-blue-800 rounded-full text-xs font-semibold">Delivered</span>',
                        4 => '<span class="px-2 py-1 bg-red-200 text-red-800 rounded-full text-xs font-semibold">Rejected</span>',
                        default => '<span class="px-2 py-1 bg-gray-200 text-gray-800 rounded-full text-xs font-semibold">N/A</span>',
                    };

                })
                ->addColumn('action', function ($row) {
                    $buttons = '<a href="' . route('admin.temp-distribute.show', $row->id) . '"
                   class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded mr-1">
                   <i class="fa fa-eye"></i>
                </a>';

                    if ($row->order_status == 1) {
                        $buttons .= '<a href="' . route('admin.temp-distribute.edit', $row->id) . '"
                        class="px-2 py-1 bg-green-500 hover:bg-green-600 text-white text-xs rounded">
                        <i class="fa fa-edit"></i>
                     </a>';
                    }

                    return $buttons;
                })


                ->rawColumns(['order_status','action'])
                ->make(true);
        }

        return view('admin.extends.temp_distribute.index');
    }
    public function create(Request $request)
    {
        if ($request->isMethod('POST')) {

            $validated = $request->validate([
                'depo_id' => 'required|exists:depos,id',
                'distribute_date' => 'required|date',
                'items' => 'required|array|min:1',
                'items.*.medicine_id' => 'required|exists:medicines,id',
                'items.*.unit_cost' => 'required|numeric|min:0',
                'items.*.quantity' => 'required|numeric|min:0',
                'items.*.free_quantity' => 'nullable|numeric|min:0',
            ], [], [
                'items.*.quantity' => 'Quantity',
                'items.*.free_quantity' => 'Free Quantity',
            ]);


            foreach ($request->items as $index => $item) {
                $totalRequested = ($item['quantity'] ?? 0) + ($item['free_quantity'] ?? 0);
                $stock = $item['stock'] ?? 0;

                if ($totalRequested > $stock) {
                    return back()->withErrors([
                        "items.$index.quantity" => "Your order (quantity + free quantity) exceeds available stock ($stock)."
                    ])->withInput();
                }
            }


            $exists = TempDistribute::where('depo_id', $request->depo_id)
                ->where('order_status', 1)
                ->exists();

            if ($exists) {
                return back()->with('error', 'This depo already has a pending distribution.');
            }



            try {

                DB::beginTransaction();
                // Total Calculation
                $total = collect($request->items)->sum(function ($item) {
                    return $item['unit_cost'] * $item['quantity'];
                });

                $discount    = $request->discount ?? 0;
                $vatPercent  = $request->vat ?? 0;
                $previousDue = $request->previous_due ?? 0;

                $vatAmount  = ($total * $vatPercent) / 100;
                $finalTotal = $total - $discount + $vatAmount;

                // Create Temp Distribute
                $distribute = TempDistribute::create([
                    'depo_id'           => $request->depo_id,
                    'distribute_date'   => $request->distribute_date,
                    'total'             => $total,
                    'discount'          => $discount,
                    'vat'               => $vatPercent,
                    'previous_due'      => $previousDue,
                    'final_total'       => $finalTotal,
                    'receivable_amount' => $request->receivable_amount,
                    'payment_status'    => 2,
                    'order_status'      => 1, // pending
                ]);

                // Items
                $items = [];
                foreach ($request->items as $item) {
                    $items[] = [
                        'temp_distribute_id' => $distribute->id,
                        'medicine_id'        => $item['medicine_id'],
                        'unit_cost'          => $item['unit_cost'],
                        'quantity'           => $item['quantity'],
                        'free_quantity'      => $item['free_quantity'] ?? 0,
                        'sub_total'          => $item['unit_cost'] * $item['quantity'],
                        'created_at'         => now(),
                    ];
                }

                TempDistributeItem::insert($items);

                DB::commit();

                return redirect('admin/temp-distribute/show/'.$distribute->id)->with('success', 'Distribution placed successfully.');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e->getMessage());
                return back()->with('error', 'Distribution Failed.');
            }
        }

        return view('admin.extends.temp_distribute.create');
    }



    public function show($id){

        try{
            $mainCompany=CompanySetting::first();
            $distribute = TempDistribute::with('items.medicine', 'depo')->where('id',$id)->first();

            if(empty($distribute)){
                Log::error('Distribute not found');
                return redirect()->back()->with('error', 'Distribute not found');
            }

            return view('admin.extends.temp_distribute.show', compact('distribute','mainCompany'));
        }
        catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Distribute Show Failed!');
        }

    }

    public function edit($id){
        try{
            $distribute = TempDistribute::with('items.medicine', 'depo')->where('id',$id)->first();

            if(empty($distribute)){
                Log::error('Distribute not found');
                return redirect()->back()->with('error', 'Distribute not found');
            }
            Log::info('Distribution Edited Successfully');
            return view('admin.extends.temp_distribute.edit', compact('distribute'));
        }
        catch(\Exception $e){
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Distribute Edited Failed!');
        }
    }


    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'depo_id' => 'required|exists:depos,id',
            'distribute_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.medicine_id' => 'required|exists:medicines,id',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.free_quantity' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'vat' => 'nullable|numeric|min:0',
            'previous_due' => 'nullable|numeric',
            'receivable_amount' => 'nullable|numeric',
        ]);

        DB::beginTransaction();

        try {
            $distribute = TempDistribute::find($id);

            // Total calculation
            $total = collect($request->items)->sum(function ($item) {
                return $item['unit_cost'] * $item['quantity'];
            });

            $discount    = $request->discount ?? 0;
            $vatPercent  = $request->vat ?? 0;
            $previousDue = $request->previous_due ?? 0;

            $vatAmount  = ($total * $vatPercent) / 100;
            $finalTotal = $total - $discount + $vatAmount + $previousDue;

            // Update main distribute
            $distribute->update([
                'depo_id'           => $request->depo_id,
                'distribute_date'   => $request->distribute_date,
                'total'             => $total,
                'discount'          => $discount,
                'vat'               => $vatPercent,
                'previous_due'      => $previousDue,
                'final_total'       => $finalTotal,
                'receivable_amount' => $finalTotal,
                'payment_status'    => 2,
                'order_status'      => 1,
            ]);

            // Replace items
            TempDistributeItem::where('temp_distribute_id', $distribute->id)->delete();

            $items = [];
            foreach ($request->items as $item) {
                $items[] = [
                    'temp_distribute_id' => $distribute->id,
                    'medicine_id'        => $item['medicine_id'],
                    'unit_cost'          => $item['unit_cost'],
                    'quantity'           => $item['quantity'],
                    'free_quantity'      => $item['free_quantity'] ?? 0,
                    'sub_total'          => $item['unit_cost'] * $item['quantity'],
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ];
            }

            TempDistributeItem::insert($items);

            DB::commit();
            Log::info('Distribute Updated Successfully');

            return redirect('admin/temp-distribute/show/'.$distribute->id)->with('success', 'Distribution Updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return back()->with('error', 'Distribution Failed.');
        }
    }



    public function print($id){

        try{
            $mainCompany=CompanySetting::first();
            $distribute = TempDistribute::with('items.medicine', 'depo')->where('id',$id)->first();

            if(empty($distribute)){
                Log::error('Distribute not found');
                return redirect()->back()->with('error', 'Distribute not found');
            }

            return view('admin.print.temp_distribute', compact('distribute','mainCompany'));
        }
        catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Distribute Show Failed!');
        }

    }


    public function posPrint($id){

        try{
            $mainCompany=CompanySetting::first();
            $distribute = TempDistribute::with('items.medicine', 'depo')->where('id',$id)->first();

            if(empty($distribute)){
                Log::error('Distribute not found');
                return redirect()->back()->with('error', 'Distribute not found');
            }

            return view('admin.print.temp_distribute_pos', compact('distribute','mainCompany'));
        }
        catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Distribute Show Failed!');
        }

    }




}
