<?php

namespace App\Http\Controllers\Depo\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $suppliers = Supplier::all();

                return DataTables::of($suppliers)
                    ->addIndexColumn()

                    ->addColumn('supplier_name', fn ($row) => $row->supplier_name ?? 'N/A')

                    ->addColumn('supplier_code', fn ($row) =>
                        '<span class="px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded">'
                        . $row->supplier_code .
                        '</span>'
                    )

                    ->addColumn('phone', fn ($row) => $row->phone ?? 'N/A')

                    ->addColumn('email', fn ($row) => $row->email ?? 'N/A')

                    ->addColumn('address', fn ($row) =>
                    $row->address ? str($row->address)->limit(30) : 'N/A'
                    )

                    ->addColumn('balance', fn ($row) =>
                    number_format($row->balance, 2)
                    )

                    ->addColumn('created_at', fn ($row) =>
                    $row->created_at->format('d M Y')
                    )

                    ->addColumn('action', function ($row) {
                        $editUrl   = route('depo.supplier.edit', $row->id);

                        return '
                        <div class="flex gap-2">
                            <a href="' . $editUrl . '"
                               class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded">
                                <i class="fa fa-edit"></i>
                            </a>
                            <button onclick="deleteItem(' . $row->id . ')"
                                    class="px-2 py-1 bg-red-500 hover:bg-red-600 text-white text-xs rounded">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    ';
                    })

                    ->rawColumns(['supplier_code', 'action'])
                    ->make(true);

            } catch (\Exception $e) {
                Log::error($e->getMessage());

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to load suppliers'
                ], 500);
            }
        }

        return view('depo.extends.supplier.index');
    }

    public function create(Request $request){

        if($request->isMethod('POST')){

            $request->validate([
                'supplier_name' => 'required|string|max:255',
                'phone'         => 'required|string|max:20',
                'email'         => 'nullable|email',
                'address'       => 'nullable|string',
                'balance'       => 'required|numeric|min:0',
            ]);

            try{
                Supplier::create([
                    'supplier_name' => $request->supplier_name,
                    'phone'         => $request->phone,
                    'email'         => $request->email,
                    'address'       => $request->address,
                    'balance'       => $request->balance,
                    // 'supplier_code' is auto-generated in model
                ]);

                Log::info('Supplier Created Successfully.');
                return redirect()->back()->with('success','Supplier Created Successfully.');
            }
            catch(\Exception $e){
                Log::error($e->getMessage());
                return redirect()->back()->with('error','Supplier Created Failed.');
            }
        }
        return view('depo.extends.supplier.create');

    }

    public function edit($id){
        try{
            $supplier=Supplier::where('id',$id)->first();

            if(empty($supplier)){
                Log::info("Supplier Not Found for Id: {$id}");
                throw new \Exception("Supplier Not Found.");
            }
            Log::info('Supplier Edited Successfully.');
            return view('depo.extends.supplier.edit',compact('supplier'));
        }
        catch(\Exception $e){
            Log::error($e->getMessage());
            return redirect()->back()->with('error','Supplier Edited Failed.');
        }

    }
     public function update(Request $request,$id){

         $supplier=Supplier::where('id',$id)->first();

         $request->validate([
            'supplier_name' => 'required|string|max:255',
            'phone'         => 'required|string|max:20',
            'email'         => 'nullable|email',
            'address'       => 'nullable|string',
            'balance'       => 'required|numeric|min:0',
        ]);

        try{
            if(empty($supplier)){
                Log::info("Supplier Not Found for Id: {$id}");
                return redirect()->back()->with('error','Supplier Not Found.');
            }

            $supplier->update([
                'supplier_name' => $request->supplier_name,
                'phone'         => $request->phone,
                'email'         => $request->email,
                'address'       => $request->address,
                'balance'       => $request->balance,
            ]);
            Log::info('Supplier Updated Successfully.');
            return redirect()->back()->with('success','Supplier Updated Successfully.');
        }
        catch(\Exception $e){
            Log::error($e->getMessage());
            return redirect()->back()->with('error','Supplier Updated Failed.');
        }
     }

     public function destroy($id){
        try{
            $supplier=Supplier::where('id',$id)->first();
            if(empty($supplier)){
                Log::info("Supplier Not Found for Id: {$id}");
                return response()->json(['success'=>false,'message'=>'Supplier Not Found.'],200);
            }
            $supplier->delete();
            Log::info('Supplier Deleted Successfully.');
           return response()->json([
               'success' => true,
               'message' => 'Supplier Deleted Successfully.'
           ],200);
        }
        catch(\Exception $e){
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Supplier Deleted Failed.'
            ],500);
        }

     }

}
