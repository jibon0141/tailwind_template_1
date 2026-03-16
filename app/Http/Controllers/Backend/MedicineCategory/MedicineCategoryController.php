<?php

namespace App\Http\Controllers\Backend\MedicineCategory;
use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\MedicineCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class MedicineCategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $categories = MedicineCategory::get();

                return DataTables::of($categories)
                    ->addIndexColumn()
                    ->addColumn('category_name', fn ($row) => $row->category_name ?? 'N/A')
                    ->addColumn('category_description', fn ($row) => $row->category_description ?? 'N/A')
                    ->addColumn('status', function ($row) {
                        return $row->status
                            ? '<span class="inline-block px-4 py-1 text-xs font-semibold text-green-800 bg-green-200 rounded-lg">Active</span>'
                            : '<span class="inline-block px-4 py-1 text-xs font-semibold text-red-800 bg-red-200 rounded-lg">Inactive</span>';
                    })
                    ->addColumn('action', function ($row) {
                        $editUrl   = route('category.edit', $row->id);
                        $deleteUrl = route('category.delete', $row->id);

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

        return view('admin.extends.medicine_category.index');
    }

    public function create(Request $request)
    {
        if ($request->isMethod('POST')) {

            $request->validate([
                'category_name'        => 'required|unique:medicine_categories,category_name',
                'category_description'=> 'nullable',
                'status'               => 'required|boolean',
            ]);

            try {
                $data = [
                    'category_name'         => $request->category_name,
                    'category_description'  => $request->category_description,
                    'status'                => $request->status,
                    'created_at'            => date('Y-m-d H:i:s'),
                ];

                MedicineCategory::insert($data);

                Log::info('Medicine category created successfully.');
                return redirect()->back()->with('success', 'Category created successfully.');

            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return redirect()->back()->with('error', 'Something went wrong.');
            }
        }

        return view('admin.extends.medicine_category.create');
    }

    public function show($id)
    {

    }

    public function edit($id)
    {
        try {
            $data = MedicineCategory::where('id', $id)->first();
            return view('admin.extends.medicine_category.edit', compact('data'));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'category_name'        => 'required|unique:medicine_categories,category_name,' . $id,
            'category_description'=> 'nullable',
            'status'               => 'required|boolean',
        ]);

        try {

            $data = [
                'category_name'        => $request->category_name,
                'category_description' => $request->category_description,
                'status'               => $request->status,
                'updated_at'           => date('Y-m-d H:i:s'),
            ];

            MedicineCategory::where('id', $id)->update($data);

            Log::info("Medicine category updated successfully for id: {$id}");
            return redirect()->route('medicine-category.index')
                ->with('success', 'Category updated successfully.');

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }

    public function delete($id)
    {
        try {
            $category = MedicineCategory::find($id);

            if (!$category) {
                Log::info("Medicine Category Not Found For Delete", ['id' => $id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Category Not Found.'
                ], 404);
            }

            $hasMedicine = Medicine::where('medicine_category_id', $id)->exists();

            if ($hasMedicine) {
                Log::info("Category delete blocked due to medicines", ['category_id' => $id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete category. Medicines exist under this category.'
                ], 422);
            }

            $category->delete();

            Log::info("Medicine category deleted successfully for id: {$id}");
            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully.'
            ], 200);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.'
            ], 500);
        }
    }
}
