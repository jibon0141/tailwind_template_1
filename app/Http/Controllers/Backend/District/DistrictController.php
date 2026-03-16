<?php

namespace App\Http\Controllers\Backend\District;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class DistrictController extends Controller
{
    public function index(Request $request){

        if($request->ajax()){
            $district=District::with('division')->get();

            return DataTables::of($district)
                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                    return $row->name  ?? 'N/A';
                })
                ->addColumn('division_name', function ($row) {
                    return $row->division->name  ?? 'N/A';
                })
                ->addColumn('status', function ($row) {
                    return $row->status
                        ? '<span class="inline-block px-5 py-2 text-xs font-semibold text-green-800 bg-green-200 rounded-lg">Active</span>'
                        : '<span class="inline-block px-4 py-2 text-xs font-semibold text-red-800 bg-red-200 rounded-lg">Inactive</span>';
                })

                ->addColumn('action', function ($row) {
                    $editUrl = route('district.edit', $row->id);
                    $deleteUrl = route('district.delete', $row->id);

                    $buttons = '<div class="flex gap-2">';

                    // Edit button
                    $buttons .= '<a href="' . $editUrl . '"
                     class="inline-flex items-center px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold rounded"
                     title="Edit">
                     <i class="fa fa-edit"></i>
                 </a>';

                    // Delete button
                    $buttons .= '<button onclick="deleteItem(' . $row->id . ')"
                         class="inline-flex items-center px-2 py-1 bg-red-500 hover:bg-red-600 text-white text-xs font-semibold rounded"
                         title="Delete">
                     <i class="fa fa-trash"></i>
                 </button>';

                    $buttons .= '</div>';

                    return $buttons;
                })
                ->rawColumns(['status', 'action']) // allow HTML rendering
                ->make(true);


        }
        return view('admin.extends.district.index');
    }

    public function create(Request $request)
    {

        if($request->isMethod('POST')){
            $request->validate([
                'name' => 'required|string|max:255',
                'division_id' => 'required|exists:divisions,id',
                'status' => 'required|boolean',
            ]);


            try{
                $data=[
                    'name' => $request->name,
                    'division_id' => $request->division_id,
                    'status' => $request->status,
                ];

                District::insert($data);
                Log::info('District Added Successfully.');
                return redirect()->back()->with('success','District Added Successfully');
            } catch (\Exception $e){
                Log::error($e->getMessage());
                return redirect()->back()->with('error','District Add Failed');
            }
        }
        $divisions = Division::where('status', 1)->get();
        return view('admin.extends.district.create', compact('divisions'));
    }

    public function edit($id)
    {
        $district = District::with('division')->where('id',$id)->first();
        $divisions = Division::where('status', 1)->get();
        return view('admin.extends.district.edit', compact('district', 'divisions'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'division_id' => 'required|exists:divisions,id',
            'status' => 'required|boolean',
        ]);

        try{

            $data=[
                'name' => $request->get('name'),
                'division_id' => $request->get('division_id'),
                'status' => $request->get('status'),
                'created_at' => now(),
            ];

            District::where('id', $id)->update($data);
            Log::info("District Updated Successfully for Id: {$id}");
            return redirect()->back()->with('success','District Updated Successfully');
        } catch (\Exception $e){
            Log::error($e->getMessage());
            return redirect()->back()->with('error','District Update Failed');
        }
    }

    public function destroy($id)
    {
        try {

            $hasEmployee = DB::table('employees')
                ->where('district_id', $id)
                ->exists();

            if ($hasEmployee) {
                return response()->json([
                    'success' => false,
                    'message' => 'District has employees.'
                ]);
            }

            District::where('id', $id)->delete();
            Log::info("District Deleted Successfully for Id: {$id}");

            return response()->json([
                'success' => true,
                'message' => 'District deleted successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error("District Delete Failed for Id {$id}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'District Delete Failed'
            ]);
        }
    }


    public function getDistrict($divisionId)
    {
        try {
            $districts = District::where('division_id', $divisionId)->get();

            if ($districts->isEmpty()) {
                throw new \Exception('District not found.');
            }

            Log::info("District found for division_id: {$divisionId}");

            return response()->json([
                'status' => true,
                'data' => $districts
            ], 200);

        } catch (\Exception $e) {

            Log::error($e->getMessage());

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

}
