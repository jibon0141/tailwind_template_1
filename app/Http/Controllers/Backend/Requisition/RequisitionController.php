<?php

namespace App\Http\Controllers\Backend\Requisition;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use App\Models\Medicine;
use App\Models\Company;
use App\Models\MedicineCategory;
use App\Models\Purchase;
use App\Models\Requisition;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class RequisitionController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $requisitions = Requisition::query()
                ->orderBy('id', 'desc');

            // Company filter
            if ($request->filled('company_name')) {
                $requisitions->where('company_name', $request->company_name);
            }

            // Date range filter
            if ($request->filled('start_date')) {
                $startDate = Carbon::parse($request->start_date)->startOfDay();
                $requisitions->where('requisition_date', '>=', $startDate);
            }

            if ($request->filled('end_date')) {
                $endDate = Carbon::parse($request->end_date)->endOfDay();
                $requisitions->where('requisition_date', '<=', $endDate);
            }

            $requisitions = $requisitions->get();

            return DataTables::of($requisitions)
                ->addIndexColumn()
                ->addColumn('requisition_voucher', fn($row) => $row->requisition_voucher  ?? 'N/A')
                ->addColumn('company_name', fn($row) => $row->company_name)
                ->addColumn('requisition_date', fn($row) => Carbon::parse($row->requisition_date)->format('d-m-Y'))
                ->addColumn('final_total', fn($row) => number_format($row->final_total, 2))
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('admin.requisition.show', $row->id) . '"
                    class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded">
                    <i class="fa fa-eye"></i>
                </a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.extends.requisition.index');
    }


    public function create(Request $request)
    {
        if ($request->isMethod('POST')) {

            $request->validate([
                'company_name' => 'required|string|max:255',
                'final_total' => 'required|numeric',
                'medicine_name.*' => 'required|string|max:255',
                'medicine_id.*' => 'required|integer',
                'mrp.*' => 'required|numeric',
                'discount.*' => 'required|integer',
                'purchase_price.*' => 'required|numeric',
                'quantity.*' => 'required|integer',
                'sub_total_price.*' => 'required|numeric',
            ]);

            try {
                DB::beginTransaction();

                $requisition = Requisition::create([
                    'company_name' => $request->company_name,
                    'requisition_date' => now(),
                    'final_total' => $request->final_total,
                ]);

                $items = [];
                $medicineCount = count($request->medicine_name);

                for ($i = 0; $i < $medicineCount; $i++) {
                    $qty = (int)$request->quantity[$i];
                    if ($qty < 1) continue; // Skip zero qty

                    $items[] = [
                        'requisition_id' => $requisition->id,
                        'medicine_id'    => $request->medicine_id[$i],
                        'medicine_name'  => $request->medicine_name[$i],
                        'mrp'            => $request->mrp[$i] ?: 0,
                        'discount'       => $request->discount[$i] ?: 0,
                        'purchase_price' => $request->purchase_price[$i] ?: 0,
                        'quantity'       => $qty,
                        'sub_total'      => $request->sub_total_price[$i] ?: 0,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ];
                }

                if (!empty($items)) {
                    DB::table('requisition_items')->insert($items);
                } else {
                    return redirect()->back()->with('error', 'Please enter quantity for at least one medicine.');
                }

                DB::commit();
                Log::info('Requisition created successfully!');
                return redirect()->route('admin.requisition.show', $requisition->id)
                    ->with('success', 'Requisition created successfully!');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e->getMessage());
                return redirect()->back()->with('error', 'Requisition creation failed.');
            }
        }

        return view('admin.extends.requisition.create');
    }




    public function show($id){

        try{
            $mainCompany=CompanySetting::first();
            $requisition=Requisition::with('requisitionItems')->find($id);

            if(empty($requisition)){
                Log::info('Requisition not found');
                return redirect()->back()->with('error', 'Requisition not found.');
            }
            Log::info('Requisition Successfully Showed.');
            return view('admin.extends.requisition.show',compact('mainCompany','requisition'));
        }
        catch(\Exception $e){
            Log::info('Requisition Successfully Showed.');
            return redirect()->back()->with('error', 'Requisition Not Showed.');
        }

    }


    public function print($id){

        try{
            $mainCompany=CompanySetting::first();
            $requisition=Requisition::with('requisitionItems')->find($id);

            if(empty($requisition)){
                Log::info('Requisition not found');
                return redirect()->back()->with('error', 'Requisition not found.');
            }
            Log::info('Requisition Successfully Showed.');
            return view('admin.print.requisition_voucher_print',compact('mainCompany','requisition'));
        }
        catch(\Exception $e){
            Log::info('Requisition Successfully Showed.');
            return redirect()->back()->with('error', 'Requisition Not Showed.');
        }

    }



    public function getMedicine(Request $request)
    {
        /* ---------------------------------------
           1. COMPANY SEARCH (Select2)
        ----------------------------------------*/
        if ($request->filled('q')) {

            $term = $request->q;

            $companies = Company::where('company_name', 'LIKE', "%{$term}%")->get();

            return response()->json([
                'results' => $companies->map(fn ($c) => [
                    'id'   => $c->id,
                    'text' => $c->company_name,
                ])
            ]);
        }

        /* ---------------------------------------
           2. MEDICINE QUERY (DYNAMIC FILTERING)
        ----------------------------------------*/
        $query = Medicine::query();

        // CASE 1 & 2: Company selected
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // CASE 2 & 3: Category selected
        if ($request->filled('category_id')) {
            $query->where('medicine_category_id', $request->category_id);
        }

        // If neither company nor category selected → return empty
        if (!$request->filled('company_id') && !$request->filled('category_id')) {
            return response()->json([]);
        }

        $medicines = $query->get();

        return response()->json([
            'company_name' => optional($medicines->first()?->company)->company_name ?? '',
            'medicines' => $medicines->map(fn ($med) => [
                'id'             => $med->id,
                'name'           => $med->medicine_name,
                'purchase_price' => $med->purchase_price,
                'mrp'            => $med->mrp,
            ])
        ]);
    }




    public function getCompanyAjax(Request $request)
    {
        $search = $request->q;

        $companies = Company::where('company_name', 'LIKE', "%{$search}%")
            ->get();

        $results = $companies->map(function ($company) {
            return [
                'id' => $company->id,
                'text' => $company->company_name
            ];
        });

        return response()->json(['results' => $results]);
    }


    public function getCategoryAjax(Request $request)
    {
        $search = $request->q;

        $categories = MedicineCategory::where('category_name', 'LIKE', "%{$search}%")
            ->get();

        $results = $categories->map(function ($category) {
            return [
                'id'   => $category->id,
                'text' => $category->category_name,
            ];
        });

        return response()->json(['results' => $results]);
    }
}
