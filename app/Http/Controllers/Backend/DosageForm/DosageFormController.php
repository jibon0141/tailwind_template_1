<?php

namespace App\Http\Controllers\Backend\DosageForm;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\MedicineDosageForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;


class DosageFormController extends Controller
{

    public function index(Request $request){


        if ($request->ajax()) {
            $medicine_dosage_form = MedicineDosageForm::get();

            return DataTables::of($medicine_dosage_form)
                ->addIndexColumn()
                ->addColumn('dosage_name', function ($row) {
                    return $row->dosage_name  ?? 'N/A';
                })
                ->addColumn('dosage_description', function($row){
                    return $row->dosage_description ?? 'N/A';
                })

                ->addColumn('status', function ($row) {
                    return $row->status
                        ? '<span class="inline-block px-5 py-2 text-xs font-semibold text-green-800 bg-green-200 rounded-lg">Active</span>'
                        : '<span class="inline-block px-4 py-2 text-xs font-semibold text-red-800 bg-red-200 rounded-lg">Inactive</span>';
                })


                ->addColumn('action', function ($row) {
                    $editUrl = route('dosage.edit', $row->id);       // Replace with your actual edit route
                    $deleteUrl = route('dosage.delete', $row->id);  // Replace with your actual delete route

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


        return view('admin.extends.dosage_form.index');

    }

    public function create(Request $request)
    {
        if($request->isMethod('POST')){

            $request->validate([
                'dosage_name'        => 'required|unique:medicine_dosage_forms,dosage_name',
                'dosage_description'=> 'nullable',
                'status'             => 'required|boolean',
            ]);

            try{
                $data=[
                    'dosage_name'        => $request->dosage_name,
                    'dosage_description'=> $request->dosage_description,
                    'status'             => $request->status,
                    'created_at'         => date('Y-m-d H:i:s'),
                ];

                MedicineDosageForm::insert($data);
                Log::info('Dosage form created successfully.');
                return redirect()->back()->with('success', 'Dosage form created successfully.');
            }
            catch (\Exception $e){
                Log::error($e->getMessage());
                return redirect()->back()->with('error', 'Dosage form update failed.');
            }

        }

        return view('admin.extends.dosage_form.create');


    }

    public function show($id){

    }

    public function edit($id){

        try{
           $data=MedicineDosageForm::where('id',$id)->first();
           return view('admin.extends.dosage_form.edit',compact('data'));
        }
        catch (\Exception $e){
            Log::error($e->getMessage());
        }


    }

    public function update(Request $request,$id){

        $request->validate([
            'dosage_name' => 'required|unique:medicine_dosage_forms,dosage_name,' . $id,
            'dosage_description'=> 'nullable',
            'status'             => 'required|boolean',
        ]);

        try{
            $data=[
                'dosage_name'        => $request->dosage_name,
                'dosage_description'=> $request->dosage_description,
                'status'             => $request->status,
                'updated_at'         => date('Y-m-d H:i:s'),
            ];
            MedicineDosageForm::where('id',$id)->update($data);
            Log::info("Dosage form updated successfully for id:{$id}");
            return redirect()->back()->with('success', 'Dosage form updated successfully.');

        }
        catch (\Exception $e){
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Dosage form update failed.');
        }

    }

    public function delete($id)
    {
        try {
            $dosage = MedicineDosageForm::find($id);

            if (!$dosage) {
                Log::info("Dosage Form Not Found For Delete", ['id' => $id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Dosage Form Not Found.'
                ], 404);
            }

            $hasMedicine = Medicine::where('medicine_dosage_form_id', $id)->exists();

            if ($hasMedicine) {
                Log::info("Dosage Form delete blocked due to medicines", ['dosage_form_id' => $id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete dosage form. Medicines exist under this dosage form.'
                ], 422);
            }

            $dosage->delete();

            Log::info("Dosage form deleted successfully. ID: {$id}");

            return response()->json([
                'success' => true,
                'message' => 'Dosage form deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            Log::error("Dosage delete failed for ID {$id}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete dosage form'
            ], 500);
        }
    }


}









