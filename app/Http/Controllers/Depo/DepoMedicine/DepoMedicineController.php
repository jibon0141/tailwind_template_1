<?php

namespace App\Http\Controllers\Depo\DepoMedicine;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\GenericName;
use App\Models\MedicineCategory;
use App\Models\MedicineDosageForm;
use App\Models\Strength;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Medicine;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class DepoMedicineController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $medicines = Medicine::with([
                    'brand',
                    'medicineCategory',
                    'genericName',
                    'dosageForm',
                ])->get();

                return DataTables::of($medicines)
                    ->addIndexColumn()

                    ->addColumn('medicine_name', fn ($row) => $row->medicine_name ?? 'N/A')

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

                    ->addColumn('purchase_price', fn ($row) => $row->purchase_price)
                    ->addColumn('sale_price', fn ($row) => $row->sale_price)
                    ->addColumn('mrp', fn ($row) => $row->mrp)

                    ->addColumn('status', function ($row) {
                        return $row->status
                            ? '<span class="px-3 py-1 text-xs font-semibold text-green-800 bg-green-200 rounded-full">Active</span>'
                            : '<span class="px-3 py-1 text-xs font-semibold text-red-800 bg-red-200 rounded-full">Inactive</span>';
                    })

                    ->addColumn('action', function ($row) {
                        $editUrl = route('depo.medicine.edit', $row->id);

                        return '
                        <div class="flex gap-2">
                            <a href="'.$editUrl.'" class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded">
                                <i class="fa fa-edit"></i>
                            </a>

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

        return view('depo.extends.medicine.index');
    }


    public function create(Request $request)
    {
        if ($request->isMethod('post')) {

            $request->validate([
                'medicine_name' => 'required',
                'generic_name_id' => 'required',
                'brand_id' => 'required',
                'medicine_category_id' => 'required',
                'medicine_dosage_form_id' => 'required',
                'strength_name' => 'required',
                'purchase_price' => 'required',
                'sale_price' => 'required',
                'mrp' => 'required',
                'status' => 'required',
            ]);

            try {
                $data = [
                    'medicine_name' => $request->medicine_name,
                    'generic_name_id' => $request->generic_name_id,
                    'brand_id' => $request->brand_id,
                    'medicine_category_id' => $request->medicine_category_id,
                    'medicine_dosage_form_id' => $request->medicine_dosage_form_id,
                    'strength_name' => $request->strength_name,
                    'purchase_price' => $request->purchase_price,
                    'sale_price' => $request->sale_price,
                    'mrp' => $request->mrp,
                    'status' => $request->status ?? 1,
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
        $categories = MedicineCategory::all();
        $brands = Brand::all();
        $dosageForms = MedicineDosageForm::all();
        $strengths = Strength::all();
        return view('depo.extends.medicine.create', compact('genericNames', 'categories', 'brands', 'dosageForms', 'strengths'));
    }

    public function show($id)
    {

        try {
            $data = Medicine::where('id', $id)->first();
            Log::info("Medicine Show Successfully for Id: {$id}");
            return view('depo.extends.medicine.show', compact('data'));
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
            $categories = MedicineCategory::where('status', 1)->get();
            $brands = Brand::where('status', 1)->get();
            $dosageForms = MedicineDosageForm::where('status', 1)->get();
            $strengths = Strength::where('status', 1)->get();

            Log::info("Medicine Edit Loaded for Id: {$id}");

            return view(
                'depo.extends.medicine.edit',
                compact('medicine', 'genericNames', 'categories', 'brands', 'dosageForms', 'strengths')
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
            'generic_name_id' => 'required',
            'brand_id' => 'required',
            'medicine_category_id' => 'required',
            'medicine_dosage_form_id' => 'required',
            'strength_name' => 'required',
            'purchase_price' => 'required',
            'sale_price' => 'required',
            'mrp' => 'required',
            'status' => 'required',
        ]);

        try {
            $data = [
                'medicine_name' => $request->medicine_name,
                'generic_name_id' => $request->generic_name_id,
                'brand_id' => $request->brand_id,
                'medicine_category_id' => $request->medicine_category_id,
                'medicine_dosage_form_id' => $request->medicine_dosage_form_id,
                'strength_name' => $request->strength_name,
                'purchase_price' => $request->purchase_price,
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
            $medicine = Medicine::where('id',$id)->first();
            $medicine->delete();

            Log::info("Medicine Deleted Successfully for Id: {$id}");

            return response()->json([
                'success' => true,
                'message' => 'Medicine Deleted Successfully.'
            ]);

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
        $strength = Strength::where('medicine_dosage_form_id', $id)->where('status', 1)->pluck('strength_name', 'id');

        return response()->json($strength);
    }

}


