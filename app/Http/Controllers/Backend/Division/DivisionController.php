<?php

namespace App\Http\Controllers\Backend\Division;

use App\Http\Controllers\Controller;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class DivisionController extends Controller
{
    public function index(Request $request){

        if($request->ajax()){
            $division=Division::all();

            return DataTables::of($division)
                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                    return $row->name  ?? 'N/A';
                })
                ->addColumn('status', function ($row) {
                    return $row->status
                        ? '<span class="inline-block px-5 py-2 text-xs font-semibold text-green-800 bg-green-200 rounded-lg">Active</span>'
                        : '<span class="inline-block px-4 py-2 text-xs font-semibold text-red-800 bg-red-200 rounded-lg">Inactive</span>';
                })

                ->addColumn('action', function ($row) {
                    $editUrl = route('division.edit', $row->id);
                    $deleteUrl = route('division.delete', $row->id);

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
        return view('admin.extends.division.index');
    }

    public function create(Request $request)
    {

        if($request->isMethod('POST')){
            $request->validate([
                'name' => 'required|string|max:255|unique:divisions,name',
                'status' => 'required|boolean',
            ]);


            try{
                $data=[
                    'name' => $request->name,
                    'status' => $request->status,
                ];

                Division::insert($data);
                Log::info('Division Added Successfully.');
                return redirect()->back()->with('success','Division Added Successfully');
            } catch (\Exception $e){
                Log::error($e->getMessage());
                return redirect()->back()->with('error','Division Add Failed');
            }
        }
        return view('admin.extends.division.create');
    }

    public function edit($id)
    {
        $division = Division::where('id',$id)->first();
        return view('admin.extends.division.edit', compact('division'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'status' => 'required|boolean',
        ]);

        try{

            $data=[
                'name' => $request->get('name'),
                'status' => $request->get('status'),
                'created_at' => now(),
            ];

            Division::where('id', $id)->update($data);
            Log::info("Division Updated Successfully for Id: {$id}");
            return redirect()->back()->with('success','Division Updated Successfully');
        } catch (\Exception $e){
            Log::error($e->getMessage());
            return redirect()->back()->with('error','Division Update Failed');
        }
    }

    public function destroy($id)
    {
        $hasDistrict = DB::table('districts')
            ->where('division_id', $id)
            ->exists();


        $hasEmployee = DB::table('employees')
            ->where('division_id', $id)
            ->exists();

        if ($hasDistrict || $hasEmployee) {
            return response()->json([
                'success' => false,
                'message' => 'Division has dependent districts or employees.'
            ]);
        }


        Division::where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Division deleted successfully.'
        ]);
    }



}
