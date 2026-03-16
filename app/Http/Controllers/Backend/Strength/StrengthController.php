<?php

namespace App\Http\Controllers\Backend\Strength;

use App\Http\Controllers\Controller;
use App\Models\Strength;
use App\Models\MedicineDosageForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class StrengthController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $strengths = Strength::with('dosage')->get();

                return DataTables::of($strengths)
                    ->addIndexColumn()
                    ->addColumn('strength_name', fn($row) => $row->strength_name ?? 'N/A')
                    ->addColumn('dosage_name', fn($row) => $row->dosage->dosage_name ?? 'N/A')
                    ->addColumn('strength_description', fn($row) => $row->strength_description ?? 'N/A')
                    ->addColumn('status', function ($row) {
                        return $row->status
                            ? '<span class="inline-block px-5 py-1 text-xs font-semibold text-green-800 bg-green-200 rounded-lg">Active</span>'
                            : '<span class="inline-block px-4 py-1 text-xs font-semibold text-red-800 bg-red-200 rounded-lg">Inactive</span>';
                    })
                    ->addColumn('action', function ($row) {
                        $editUrl = route('strength.edit', $row->id);
                        $deleteUrl = route('strength.delete', $row->id);

                        $buttons = '<div class="flex gap-2">';
                        $buttons .= '<a href="' . $editUrl . '" class="inline-flex items-center px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold rounded" title="Edit"><i class="fa fa-edit"></i></a>';
                        $buttons .= '<button onclick="deleteItem(' . $row->id . ')" class="inline-flex items-center px-2 py-1 bg-red-500 hover:bg-red-600 text-white text-xs font-semibold rounded" title="Delete"><i class="fa fa-trash"></i></button>';
                        $buttons .= '</div>';

                        return $buttons;
                    })
                    ->rawColumns(['status', 'action'])
                    ->make(true);

            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return response()->json(['error' => 'Something went wrong'], 500);
            }
        }

        return view('admin.extends.strength.index');
    }

    // Show create form
    public function create(Request $request)
    {
        if ($request->isMethod('POST')) {

            $request->validate([
                'strength_name' => 'required|unique:strengths,strength_name',
                'medicine_dosage_form_id' => 'required',
                'strength_description' => 'nullable',
                'status' => 'required|boolean',
            ]);

            try {
                $data = [
                    'strength_name' => $request->strength_name,
                    'medicine_dosage_form_id' => $request->medicine_dosage_form_id,
                    'strength_description' => $request->strength_description,
                    'status' => $request->status,
                    'created_at' => date('Y-m-d H:i:s'),
                ];

                Strength::insert($data);

                Log::info('Strength created successfully.');
                return redirect()->back()->with('success', 'Strength created successfully.');

            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return redirect()->back()->with('error', 'Something went wrong.');
            }
        }

        $dosages=MedicineDosageForm::all();

        return view('admin.extends.strength.create',compact('dosages'));
    }

    // Edit form
    public function edit($id)
    {
        try {
            $data = Strength::where('id', $id)->first();
            $dosages=MedicineDosageForm::all();
            return view('admin.extends.strength.edit', compact('data','dosages'));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }

    // Update strength
    public function update(Request $request, $id)
    {

        $request->validate([
            'strength_name' => 'required|unique:strengths,strength_name,' . $id,
            'medicine_dosage_form_id' => 'required',
            'strength_description' => 'nullable',
            'status' => 'required|boolean',
        ]);

        try {
            $data = [
                'strength_name' => $request->strength_name,
                'medicine_dosage_form_id' => $request->medicine_dosage_form_id,
                'strength_description' => $request->strength_description,
                'status' => $request->status,
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            Strength::where('id', $id)->update($data);

            Log::info("Strength updated successfully for id: {$id}");
            return redirect()->back()->with('success', 'Strength updated successfully.');

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Strength update Failed.');
        }
    }

    // Delete strength
    public function delete($id)
    {
        try {
            $strength = Strength::where('id', $id)->first();
            $strength->delete();

            Log::info("Strength deleted successfully with id: {$id}");
            return response()->json(['success' => true, 'message' => 'Strength deleted successfully.']);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['success' => false, 'message' => 'Strength deleted Failed.']);
        }
    }
}
