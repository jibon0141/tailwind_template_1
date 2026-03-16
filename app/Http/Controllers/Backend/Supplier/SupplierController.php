<?php

namespace App\Http\Controllers\Backend\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Traits\ManageImage;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{
    use ManageImage;

    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $suppliers = Supplier::with('company');

                return DataTables::of($suppliers)
                    ->addIndexColumn()

                    ->addColumn('supplier_name', fn ($row) => $row->supplier_name ?? 'N/A')

                    ->addColumn('supplier_code', fn ($row) =>
                        '<span class="px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded">'
                        . $row->supplier_code .
                        '</span>'
                    )
                    ->addColumn('company_name', fn ($row) => $row->company->company_name ?? 'N/A')
                    ->addColumn('phone', fn ($row) => $row->phone ?? 'N/A')

                    ->addColumn('email', fn ($row) => $row->email ?? 'N/A')

                    ->addColumn('opening_balance', fn ($row) =>
                    number_format($row->opening_balance, 2)
                    )

                    ->addColumn('balance', fn ($row) =>
                    number_format($row->balance, 2)
                    )

                    ->addColumn('payment_status', function ($row) {
                        if ($row->balance < 0) {
                            return '<span class="px-2 py-1 bg-green-300 text-black rounded text-xs">Receivable</span>';
                        } elseif($row->balance > 0) {
                            return '<span class="px-4 py-1 bg-yellow-300 text-black rounded text-xs">Payable</span>';
                        }
                        else{
                            return '<span class="px-7 py-1 bg-gray-300 text-black rounded text-xs">N/A</span>';
                        }
                    })

                    ->addColumn('created_at', fn ($row) =>
                    $row->created_at->format('d M Y')
                    )

                    ->addColumn('action', function ($row) {
                        $editUrl   = route('supplier.edit', $row->id);
                        $showUrl   = route('supplier.show', $row->id);

                        return '
                        <div class="flex gap-2">
                            <a href="' . $editUrl . '"
                               class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded">
                                <i class="fa fa-edit"></i>
                            </a>
                              <a href="' . $showUrl . '"
                               class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded">
                                <i class="fa fa-eye"></i>
                            </a>
                            <button onclick="deleteItem(' . $row->id . ')"
                                    class="px-2 py-1 bg-red-500 hover:bg-red-600 text-white text-xs rounded">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    ';
                    })

                    ->rawColumns(['supplier_code', 'action', 'payment_status'])
                    ->make(true);

            } catch (\Exception $e) {
                Log::error($e->getMessage());

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to load suppliers'
                ], 500);
            }
        }

        return view('admin.extends.supplier.index');
    }


    public function create(Request $request)
    {
        if ($request->isMethod('POST')) {

            $request->validate([
                'supplier_name'     => 'required|string|max:255',
                'company_id'        => 'nullable|integer',
                'phone'             => 'required|string|max:20',
                'email'             => 'nullable|email|unique:suppliers,email',
                'type'              => 'required|in:1,2', // 1=payable, 2=receivable
                'opening_balance'   => 'nullable|numeric',
                'balance'           => 'required|numeric',
                'bank'              => 'nullable|string|max:255',
                'nid'               => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
                'voucher_address'   => 'nullable|string',
                'address'           => 'nullable|string',
            ]);

            try {

                $nidFile = null;

                if ($request->hasFile('nid')) {
                    $nidFile = $this->storeImage($request->nid, 'image/suppliers/nid');
                }

                // Determine sign based on type
                $balanceValue = $request->balance ?? 0;
                $balanceValue = $request->type == 2 ? -abs($balanceValue) : abs($balanceValue);

                $openingBalance = $request->opening_balance ?? $balanceValue;
                $openingBalance = $request->type == 2 ? -abs($openingBalance) : abs($openingBalance);

                Supplier::create([
                    'supplier_name'     => $request->supplier_name,
                    'company_id'        => $request->company_id,
                    'phone'             => $request->phone,
                    'email'             => $request->email,
                    'type'              => $request->type,
                    'opening_balance'   => $openingBalance,
                    'balance'           => $balanceValue,
                    'bank'              => $request->bank,
                    'nid'               => $nidFile,
                    'voucher_address'   => $request->voucher_address,
                    'address'           => $request->address,
                ]);

                Log::info('Supplier Created Successfully');

                return redirect()->back()->with('success', 'Supplier Created Successfully.');

            } catch (\Exception $e) {

                Log::error('Supplier Create Error: ' . $e->getMessage());

                return redirect()->back()->with('error', 'Supplier Creation Failed.');
            }
        }
        $companies = Company::all();

        return view('admin.extends.supplier.create',compact('companies'));
    }


    public function show($id){
        try{
            $supplier=Supplier::where('id',$id)->first();
            if(empty($supplier)){
                Log::info("Supplier Not Found for Id: {$id}");
                throw new \Exception("Supplier Not Found.");
            }
            Log::info('Supplier Showed Successfully.');
            return view('admin.extends.supplier.show',compact('supplier'));
        }
        catch(\Exception $e){
            Log::error($e->getMessage());
            return redirect()->back()->with('error','Supplier Showed Failed.');
        }

    }


    public function edit($id){
        try{
            $supplier=Supplier::where('id',$id)->first();

            if(empty($supplier)){
                Log::info("Supplier Not Found for Id: {$id}");
                throw new \Exception("Supplier Not Found.");
            }
            $companies = Company::all();
            Log::info('Supplier Edited Successfully.');
            return view('admin.extends.supplier.edit',compact('supplier','companies'));
        }
        catch(\Exception $e){
            Log::error($e->getMessage());
            return redirect()->back()->with('error','Supplier Edited Failed.');
        }

    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::find($id);

        if (!$supplier) {
            Log::info("Supplier Not Found for Id: {$id}");
            return redirect()->back()->with('error', 'Supplier Not Found.');
        }

        // Validate input
        $request->validate([
            'supplier_name'     => 'required|string|max:255',
            'company_id'        => 'nullable|integer',
            'phone'             => 'required|string|max:20',
            'email'             => 'nullable|email|unique:suppliers,email,' . $supplier->id,
            'address'           => 'nullable|string',
            'voucher_address'   => 'nullable|string',
            'bank'              => 'nullable|string',
            'nid'               => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        try {

            $nidFile = $supplier->nid;
            // Handle NID file
            if ($request->hasFile('nid')) {
                if ($supplier->nid) {
                    $this->destroyImage($supplier->nid,'image/suppliers/nid');
                }
                $nidFile = $this->storeImage($request->nid, 'image/suppliers/nid');
            }

            $supplier->update([
                'supplier_name'     => $request->supplier_name,
                'company_id'        => $request->company_id,
                'phone'             => $request->phone,
                'email'             => $request->email,
                'bank'              => $request->bank,
                'voucher_address'   => $request->voucher_address,
                'address'           => $request->address,
                'nid'               => $nidFile,
            ]);

            Log::info('Supplier Updated Successfully. ID: ' . $supplier->id);
            return redirect()->back()->with('success', 'Supplier Updated Successfully.');

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Supplier Update Failed.');
        }
    }


    public function destroy($id)
    {
        try {
            $supplier = Supplier::find($id);

            if (!$supplier) {
                Log::info("Supplier Not Found for Id: {$id}");
                return response()->json([
                    'success' => false,
                    'message' => 'Supplier Not Found.'
                ], 404);
            }

            // Check if supplier has linked purchases
            $hasPurchases = Purchase::where('supplier_id', $id)->exists();
            if ($hasPurchases) {
                Log::info("Cannot delete Supplier {$id}: linked to existing purchases");
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete this supplier because it has associated purchases.'
                ], 409);
            }

            if ($supplier->nid) {
                $this->destroyImage($supplier->nid,'image/suppliers/nid');
            }

            $supplier->delete();
            Log::info("Supplier Deleted Successfully: {$id}");

            return response()->json([
                'success' => true,
                'message' => 'Supplier Deleted Successfully.'
            ], 200);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Supplier Delete Failed.'
            ], 500);
        }
    }



}
