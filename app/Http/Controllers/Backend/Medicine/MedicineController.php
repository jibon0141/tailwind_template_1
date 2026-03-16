<?php
namespace App\Http\Controllers\Backend\Medicine;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Company;
use App\Models\GenericName;
use App\Models\MedicineCategory;
use App\Models\MedicineDosageForm;
use App\Models\Strength;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Medicine;
use Yajra\DataTables\Facades\DataTables;

class MedicineController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $query = Medicine::with([
                    'company',
                    'brand',
                    'medicineCategory',
                    'genericName',
                    'dosageForm',
                ])->orderBy('id', 'desc');


                if ($request->has('search') && $request->search['value']) {

                    $search = $request->search['value'];

                    $query->where(function ($q) use ($search) {
                        $q->where('medicine_name', 'like', "%{$search}%")
                            ->orWhere('mrp', 'like', "%{$search}%")
                            ->orWhere('sale_price', 'like', "%{$search}%");
                    });
                }

                return DataTables::of($query)
                    ->addIndexColumn()

                    ->addColumn('medicine_name', fn ($row) => $row->medicine_name ?? 'N/A')

                    ->addColumn('company_name', fn ($row) =>
                        $row->company->company_name ?? 'N/A'
                    )

                    ->addColumn('generic_name', fn ($row) =>
                        $row->genericName->generic_name ?? 'N/A'
                    )

                    ->addColumn('brand', fn ($row) =>
                        $row->brand->brand_name ?? 'N/A'
                    )

                    ->addColumn('category', fn ($row) =>
                        $row->medicineCategory->category_name ?? 'N/A'
                    )

                    ->addColumn('dosage', fn ($row) =>
                        $row->dosageForm->dosage_name ?? 'N/A'
                    )

                    ->addColumn('strength', fn ($row) =>
                        $row->strength_name ?? 'N/A'
                    )
                    ->addColumn('mrp', fn ($row) => $row->mrp ?? 0.00)
                    ->addColumn('purchase_percentage', fn ($row) => $row->purchase_percentage ?? 0.00)
                    ->addColumn('purchase_price', fn ($row) => $row->purchase_price ?? 0.00)
                    ->addColumn('sale_percentage', fn ($row) => $row->sale_percentage ?? 0.00)
                    ->addColumn('sale_price', fn ($row) => $row->sale_price ?? 0.00)

                    ->addColumn('status', function ($row) {
                        return $row->status
                            ? '<span class="px-3 py-1 text-xs font-semibold text-green-800 bg-green-200 rounded-full">Active</span>'
                            : '<span class="px-3 py-1 text-xs font-semibold text-red-800 bg-red-200 rounded-full">Inactive</span>';
                    })

                    ->addColumn('action', function ($row) {
                        $editUrl = route('medicine.edit', $row->id);

                        return '
                        <div class="flex gap-2">
                            <a href="'.$editUrl.'" class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded">
                                <i class="fa fa-edit"></i>
                            </a>
                            <button onclick="deleteItem('.$row->id.')" class="px-2 py-1 bg-red-500 hover:bg-red-600 text-white text-xs rounded">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    ';
                    })

                    ->rawColumns(['status', 'action'])
                    ->make(true);

            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return response()->json(['error' => 'Something went wrong'], 500);
            }
        }

        return view('admin.extends.medicine.index');
    }


    public function create(Request $request)
    {


        if ($request->isMethod('post')) {

            $request->validate([
                'medicine_name' => 'required',
                'company_id' => 'required',
                'generic_name_id' => 'required',
                'brand_id' => 'required',
                'medicine_category_id' => 'required',
                'medicine_dosage_form_id' => 'required',
                'strength_name' => 'required',
                'purchase_percentage' => 'required',
                'purchase_price' => 'required',
                'sale_percentage' => 'required',
                'sale_price' => 'required',
                'mrp' => 'required',

            ]);

            try {
                $data = [
                    'medicine_name' => $request->medicine_name,
                    'company_id' =>$request->company_id,
                    'generic_name_id' => $request->generic_name_id,
                    'brand_id' => $request->brand_id,
                    'medicine_category_id' => $request->medicine_category_id,
                    'medicine_dosage_form_id' => $request->medicine_dosage_form_id,
                    'strength_name' => $request->strength_name,
                    'purchase_percentage' => $request->purchase_percentage,
                    'purchase_price' => $request->purchase_price,
                    'sale_percentage' => $request->sale_percentage,
                    'sale_price' => $request->sale_price,
                    'mrp' => $request->mrp,
                    'status' => $request->status ?? 1, // default to active (1)
                    'created_at' => date('Y-m-d H:i:s'),
                ];

                Medicine::insert($data);
                Log::info('Medicine Added Successfully');
                return redirect()->back()->with('success', 'Medicine Added Successfully');
            } catch (\Exception $e) {
                Log::info($e->getMessage());
                return redirect()->back()->with('error', 'Medicine Add Unsuccessful');
            }
        }

        $genericNames = GenericName::all();
        $categories= MedicineCategory::all();
        $brands = Brand::all();
        $dosageForms = MedicineDosageForm::all();
        $strengths = Strength::all();
        $companies=Company::all();
        return view('admin.extends.medicine.create',compact('genericNames','categories','brands','dosageForms','strengths','companies'));
    }

    public function show($id)
    {

        try {
            $data = Medicine::where('id', $id)->first();
            Log::info("Medicine Show Successfully for Id: {$id}");
            return view('admin.extends.medicine.show', compact('data'));
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }

    public function edit($id)
    {
        try {

            $medicine = Medicine::findOrFail($id);
            $genericNames = GenericName::where('status', 1)->get();
            $categories   = MedicineCategory::where('status', 1)->get();
            $brands       = Brand::where('status', 1)->get();
            $dosageForms  = MedicineDosageForm::where('status', 1)->get();
            $strengths    = Strength::where('status', 1)->get();
            $suppliers=Supplier::all();
            $companies=Company::all();

            Log::info("Medicine Edit Loaded for Id: {$id}");

            return view(
                'admin.extends.medicine.edit',
                compact('medicine','genericNames','categories','brands','dosageForms','strengths','suppliers','companies')
            );

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Data Fetch Failed!');
        }
    }


    public function update(Request $request, $id)
    {

        $request->validate([
            'medicine_name' => 'required',
            'company_id' => 'required',
            'generic_name_id' => 'required',
            'brand_id' => 'required',
            'medicine_category_id' => 'required',
            'medicine_dosage_form_id' => 'required',
            'strength_name' => 'required',
            'purchase_percentage' => 'required',
            'purchase_price' => 'required',
            'sale_percentage' => 'required',
            'sale_price' => 'required',
            'mrp' => 'required',
            'status' => 'required',
        ]);

        try {

            $data = [
                'medicine_name' => $request->medicine_name,
                'company_id'    =>$request->company_id,
                'generic_name_id' => $request->generic_name_id,
                'brand_id' => $request->brand_id,
                'medicine_category_id' => $request->medicine_category_id,
                'medicine_dosage_form_id' => $request->medicine_dosage_form_id,
                'strength_name' => $request->strength_name,
                'purchase_percentage' => $request->purchase_percentage,
                'purchase_price' => $request->purchase_price,
                'sale_percentage' => $request->sale_percentage,
                'sale_price' => $request->sale_price,
                'mrp' => $request->mrp,
                'status' => $request->status,
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            Medicine::where('id', $id)->update($data);
            Log::info('Medicine Updated Successfully');
            return redirect()->back()->with('success', 'Medicine Updated Successfully.');
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return redirect()->back()->with('error', 'Medicine Update Failed!');
        }

    }

    public function destroy($id)
    {
        try {
            $data = Medicine::where('id',$id)->first();

            if (!$data) {
                Log::error("Medicine Not Found For Id: {$id}");
                return response()->json([
                    'success' => false,
                    'message' => 'Medicine Not Found!'
                ], 404);
            }

            $data->delete();

            Log::info("Medicine Deleted Successfully for Id: {$id}");
            return response()->json([
                'success' => true,
                'message' => 'Medicine Deleted Successfully.'
            ], 200);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Medicine Delete Failed!'
            ], 500);
        }
    }


    public function getStrength($id)
    {
        $strength = Strength::where('medicine_dosage_form_id', $id)->where('status',1)->pluck('strength_name', 'id');

        return response()->json($strength);
    }

    public function getCategory(){
        try{
            $category=MedicineCategory::all();

        }catch(\Exception $e){
            Log::info($e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong.');
        }

    }

    public function getBrand(){

    }

    public function getDosageForm(){

    }

    public function  getGenericName(){

    }

}


