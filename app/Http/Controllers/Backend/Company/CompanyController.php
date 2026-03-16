<?php

namespace App\Http\Controllers\Backend\Company;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $companies = Company::all();

                return DataTables::of($companies)
                    ->addIndexColumn()
                    ->addColumn('company_name', fn($row) => $row->company_name ?? 'N/A')
                    ->addColumn('created_at', function($row) {
                        return $row->created_at
                            ? \Carbon\Carbon::parse($row->created_at)->format('j F Y')
                            : 'N/A';
                    })


                    ->addColumn('action', function ($row) {
                        $editUrl = route('company.edit', $row->id);
                        $deleteUrl = route('company.delete', $row->id);

                        $btn = '<div class="flex gap-2">';
                        $btn .= '<a href="' . $editUrl . '" class="inline-flex items-center px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded" title="Edit"><i class="fa fa-edit"></i></a>';
                        $btn .= '<button onclick="deleteItem(' . $row->id . ')" class="inline-flex items-center px-2 py-1 bg-red-500 hover:bg-red-600 text-white text-xs rounded" title="Delete"><i class="fa fa-trash"></i></button>';
                        $btn .= '</div>';

                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);

            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return response()->json(['error' => 'Something went wrong'], 500);
            }
        }

        return view('admin.extends.medicine_company.index');
    }


    public function create(Request $request){

        if($request->isMethod('POST')){

            $request->validate([
                'company_name' => 'required',
            ]);
            try{
                $data=[
                    'company_name'=>$request->company_name,
                    'created_at'=>date('Y-m-d H:i:s'),
                ];
                Company::create($data);

                Log::info("Company Added Successfully.");
                return redirect()->back()->with('success','Company Added Successfully.');
            }
            catch(\Exception $e){
                Log::info($e->getMessage());
                return redirect()->back()->with('error',"Company Added Failed.");
            }

        }
        return view('admin.extends.medicine_company.create');

    }

    public function edit($id){
        try{
            $company=Company::where('id',$id)->first();

            if(empty($company)){
                Log::info("Company Not Found.");
                return redirect()->back()->with('error',"Company Not Found.");
            }
            Log::info("Company Edited Successfully For Id:${id}");
            return view('admin.extends.medicine_company.edit',compact('company'));
        }catch(\Exception $e){
            Log::info($e->getMessage());
            return redirect()->back()->with('error',"Company Edited Failed For Id:${id}");
        }

    }

    public function update(Request $request, $id){

        $request->validate([
            'company_name' => 'required',
        ]);
        try{
            $company=Company::where('id',$id)->first();

            if(empty($company)){
                Log::info("Company Not Found For Update On Id:${id}");
                return redirect()->back()->with('error',"Company Not Found.");
            }

            $company->update([
                'company_name'=>$request->company_name,
                'updated_at'=>date('Y-m-d H:i:s'),
            ]);

            Log::info("Company Updated Successfully.");
            return redirect()->back()->with('success','Company Updated Successfully.');

        }catch(\Exception $e){
            Log::info($e->getMessage());
            return redirect()->back()->with('error',"Company Updated Failed.");
        }

    }

    public function destroy($id)
    {
        try {
            $company = Company::find($id);

            if (!$company) {
                Log::info("Company Not Found For Delete", ['id' => $id]);

                return response()->json([
                    'success' => false,
                    'message' => 'Company Not Found.'
                ], 404);
            }

            // 🔒 Check medicines by company_id
            $hasMedicine = Medicine::where('company_id', $id)->exists();

            if ($hasMedicine) {
                Log::info("Company delete blocked due to medicines", ['company_id' => $id]);

                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete company. Medicines exist under this company.'
                ], 422);
            }

            $company->delete();

            Log::info("Company Deleted Successfully", ['id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Company Deleted Successfully.'
            ], 200);

        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Company Delete Failed.'
            ], 500);
        }
    }



}
