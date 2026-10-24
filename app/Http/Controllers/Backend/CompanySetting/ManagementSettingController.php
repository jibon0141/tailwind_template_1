<?php

namespace App\Http\Controllers\Backend\CompanySetting;

use App\Http\Controllers\Controller;
use App\Models\ManagementSetting;
use App\Traits\ManageImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class ManagementSettingController extends Controller
{
    use ManageImage;

    private string $imagePath = 'image/management';

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of(ManagementSetting::latest())
                ->addIndexColumn()
                ->addColumn('image_preview', function ($row) {
                    if ($row->image) {
                        return '<img src="' . asset('image/management/' . $row->image) . '" class="h-10 w-10 object-cover rounded-full border">';
                    }
                    return '<span class="text-xs text-gray-400">—</span>';
                })
                ->addColumn('status_badge', function ($row) {
                    return $row->status
                        ? '<span class="px-3 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Active</span>'
                        : '<span class="px-3 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Inactive</span>';
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('admin.management.edit', $row->id);
                    $action = '
                    <div class="flex gap-1.5">';
                    if (can_access('admin.management.edit')) {
                        $action .= '
                        <a href="' . $editUrl . '"
                           class="w-8 h-8 flex items-center justify-center rounded-lg bg-gradient-to-br from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white shadow-sm shadow-sky-200 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200"
                           title="Edit">
                            <i class="fa fa-edit text-xs"></i>
                        </a>';
                    }
                    if (can_access('admin.management.delete')) {
                        $action .= '
                        <button onclick="deleteItem(' . $row->id . ')"
                                class="w-8 h-8 flex items-center justify-center rounded-lg bg-gradient-to-br from-rose-400 to-red-500 hover:from-rose-500 hover:to-red-600 text-white shadow-sm shadow-rose-200 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200"
                                title="Delete">
                            <i class="fa fa-trash text-xs"></i>
                        </button>';
                    }
                    $action .= '
                    </div>';

                    return $action;
                })
                ->rawColumns(['image_preview', 'status_badge', 'action'])
                ->make(true);
        }

        return view('admin.extends.management.index');
    }

    public function create(Request $request)
    {
        if ($request->isMethod('POST')) {
            $request->validate([
                'name'        => 'required|string|max:255',
                'designation' => 'required|string|max:255',
                'image'       => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'message'     => 'required|string',
                'serial_no'   => 'required|integer|min:0|unique:management_settings,serial_no',
                'status'      => 'required|in:0,1',
            ]);

            try {
                $data = $request->only(['name', 'designation', 'message', 'serial_no', 'status']);

                if ($request->hasFile('image')) {
                    $data['image'] = $this->storeImage($request->file('image'), $this->imagePath);
                }

                ManagementSetting::create($data);

                Log::info('Management Member Created Successfully!');
                return redirect()->route('admin.management.index')->with('success', 'Management Member Created Successfully!');
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return redirect()->back()->with('error', 'Create Failed!');
            }
        }

        return view('admin.extends.management.create');
    }

    public function edit(Request $request, $id)
    {
        $member = ManagementSetting::find($id);

        if (empty($member)) {
            return redirect()->back()->with('error', 'Member Not Found!');
        }

        if ($request->isMethod('PUT')) {
            $request->validate([
                'name'        => 'required|string|max:255',
                'designation' => 'required|string|max:255',
                'image'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'message'     => 'required|string',
                'serial_no'   => 'required|integer|min:0|unique:management_settings,serial_no,' . $id,
                'status'      => 'required|in:0,1',
            ]);

            try {
                $data = $request->only(['name', 'designation', 'message', 'serial_no', 'status']);

                if ($request->hasFile('image')) {
                    if ($member->image) $this->destroyImage($member->image, $this->imagePath);
                    $data['image'] = $this->storeImage($request->file('image'), $this->imagePath);
                }

                $member->update($data);

                Log::info('Management Member Updated Successfully!');
                return redirect()->route('admin.management.index')->with('success', 'Management Member Updated Successfully!');
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return redirect()->back()->with('error', 'Update Failed!');
            }
        }

        return view('admin.extends.management.edit', compact('member'));
    }

    public function destroy($id)
    {
        $member = ManagementSetting::find($id);

        if (empty($member)) {
            return response()->json(['success' => false, 'message' => 'Member not found!'], 404);
        }

        try {
            if ($member->image) $this->destroyImage($member->image, $this->imagePath);
            $member->delete();

            Log::info('Management Member Deleted Successfully!');
            return response()->json(['success' => true, 'message' => 'Member deleted successfully!']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['success' => false, 'message' => 'Delete failed!'], 500);
        }
    }
}
