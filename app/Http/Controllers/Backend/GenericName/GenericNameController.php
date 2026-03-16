<?php

namespace App\Http\Controllers\Backend\GenericName;
use App\Http\Controllers\Controller;
use App\Models\GenericName;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class GenericNameController extends Controller
{
    public function index(Request $request){

        if ($request->ajax()) {

            try {

                $genericName = GenericName::get();


                return DataTables::of($genericName)
                    ->addIndexColumn()
                    ->addColumn('generic_name', fn ($row) => $row->generic_name ?? 'N/A')
                    ->addColumn('description', fn ($row) => $row->description ?? 'N/A')
                    ->addColumn('status', function ($row) {
                        return $row->status
                            ? '<span class="inline-block px-4 py-1 text-xs font-semibold text-green-800 bg-green-200 rounded-lg">Active</span>'
                            : '<span class="inline-block px-4 py-1 text-xs font-semibold text-red-800 bg-red-200 rounded-lg">Inactive</span>';
                    })
                    ->addColumn('action', function ($row) {
                        $editUrl = route('generic.edit', $row->id);

                        $btn  = '<div class="flex gap-2">';
                        $btn .= '<a href="'.$editUrl.'" class="inline-flex items-center px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded" title="Edit"><i class="fa fa-edit"></i></a>';
                        $btn .= '<button onclick="deleteItem('.$row->id.')" class="inline-flex items-center px-2 py-1 bg-red-500 hover:bg-red-600 text-white text-xs rounded" title="Delete"><i class="fa fa-trash"></i></button>';
                        $btn .= '</div>';

                        return $btn;
                    })
                    ->rawColumns(['status', 'action'])
                    ->make(true);

            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return response()->json(['error' => 'Something went wrong'], 500);
            }
        }

        return view('admin.extends.generic_name.index');

    }

    public function create(Request $request){

        if($request->isMethod('post')){

            $request->validate([
                'generic_name' => 'required|unique:generic_names,generic_name',
                'description'  => 'nullable',
                'status'       => 'required|boolean',
            ]);

            try{

                $data=[
                    'generic_name' =>$request->generic_name,
                    'description' => $request->description,
                    'status' =>$request->status,
                ];

                GenericName::insert($data);
                Log::info('Generic Name Stored Successfully.');
                return redirect()->back()->with('success','Generic Name Stored Successfully.');
            }
            catch(\Exception $e){
                Log::error($e->getMessage());
                return redirect()->back()->with('error','Generic Name Store Failed.');

            }
        }

        return view('admin.extends.generic_name.create');

    }

    public function show($id)
    {
        try {
            $data = GenericName::where('id', $id)->first();
            if (!$data) {
                return redirect()->back()->with('error', 'Generic Name not found.');
            }
            Log::info("Generic Name Show Successful for Id: {$id}");
            return view('admin.extends.generic_name.show', compact('data'));

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }


    public function edit($id)
    {
        try {
            $data = GenericName::where('id', $id)->first();
            if (!$data) {
                return redirect()->back()->with('error', 'Generic Name not found.');
            }
            Log::info("Generic Name Show Successful for Id: {$id}");
            return view('admin.extends.generic_name.edit', compact('data'));

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'generic_name' => 'required|unique:generic_names,generic_name,' . $id,
            'description'  => 'nullable',
            'status'       => 'required|boolean',
        ]);

        try {
            $genericName = GenericName::where('id', $id)->first();

            if (!$genericName) {
                return redirect()->back()->with('error', 'Generic Name not found.');
            }

            $data = [
                'generic_name' => $request->generic_name,
                'description'  => $request->description,
                'status'       => $request->status,
                'updated_at'   => now(),
            ];

            GenericName::where('id', $id)->update($data);

            Log::info("Generic Name updated successfully for Id: {$id}");
            return redirect()->back()->with('success', 'Generic Name updated successfully.');

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Generic Name update failed.');
        }
    }

    public function destroy($id)
    {
        try {
            $genericName = GenericName::find($id);

            if (!$genericName) {
                Log::info("Generic Name Not Found For Delete", ['id' => $id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Generic Name Not Found.'
                ], 404);
            }

            $hasMedicine = Medicine::where('generic_name_id', $id)->exists();

            if ($hasMedicine) {
                Log::info("Generic Name delete blocked due to medicines", ['generic_name_id' => $id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete generic name. Medicines exist under this generic name.'
                ], 422);
            }

            $genericName->delete();

            Log::info("Generic Name deleted successfully with Id: {$id}");
            return response()->json(['success' => true, 'message' => 'Generic Name deleted successfully.'], 200);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['success' => false, 'message' => 'Generic Name delete failed.'], 500);
        }
    }


}
