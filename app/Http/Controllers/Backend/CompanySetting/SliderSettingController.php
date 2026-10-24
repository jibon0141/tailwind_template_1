<?php

namespace App\Http\Controllers\Backend\CompanySetting;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Traits\ManageImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class SliderSettingController extends Controller
{
    use ManageImage;

    private string $imagePath = 'image/slider';

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $sliders = Slider::latest();

            return DataTables::of($sliders)
                ->addIndexColumn()
                ->addColumn('image_preview', function ($row) {
                    if ($row->image) {
                        return '<img src="' . asset('image/slider/' . $row->image) . '" class="h-10 w-16 object-cover rounded-lg border">';
                    }
                    return '<span class="text-xs text-gray-400">—</span>';
                })
                ->addColumn('status_badge', function ($row) {
                    return $row->status
                        ? '<span class="px-3 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Active</span>'
                        : '<span class="px-3 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Inactive</span>';
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('admin.slider.edit', $row->id);
                    $action = '
                    <div class="flex gap-1.5">';
                    if (can_access('admin.slider.edit')) {
                        $action .= '
                        <a href="' . $editUrl . '"
                           class="w-8 h-8 flex items-center justify-center rounded-lg bg-gradient-to-br from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white shadow-sm shadow-sky-200 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200"
                           title="Edit">
                            <i class="fa fa-edit text-xs"></i>
                        </a>';
                    }
                    if (can_access('admin.slider.delete')) {
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

        return view('admin.extends.slider.index');
    }

    public function create(Request $request)
    {
        if ($request->isMethod('POST')) {
            $request->validate([
                'title'       => 'nullable|string|max:255',
                'heading'     => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'image'       => 'required|image|mimes:jpeg,png,jpg',
                'status'      => 'required|in:0,1',
            ]);

            try {
                $data = $request->only(['title', 'heading', 'description', 'status']);

                if ($request->hasFile('image')) {
                    $data['image'] = $this->storeImage($request->file('image'), $this->imagePath);
                }

                Slider::create($data);

                Log::info('Slider Created Successfully!');
                return redirect()->route('admin.slider.index')->with('success', 'Slider Created Successfully!');
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return redirect()->back()->with('error', 'Slider Create Failed!');
            }
        }

        return view('admin.extends.slider.create');
    }

    public function edit(Request $request, $id)
    {
        $slider = Slider::find($id);

        if (empty($slider)) {
            return redirect()->back()->with('error', 'Slider Not Found!');
        }

        if ($request->isMethod('PUT')) {
            $request->validate([
                'title'       => 'nullable|string|max:255',
                'heading'     => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'image'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'status'      => 'required|in:0,1',
            ]);

            try {
                $data = $request->only(['title', 'heading', 'description', 'status']);

                if ($request->hasFile('image')) {
                    if ($slider->image) $this->destroyImage($slider->image, $this->imagePath);
                    $data['image'] = $this->storeImage($request->file('image'), $this->imagePath);
                }

                $slider->update($data);

                Log::info('Slider Updated Successfully!');
                return redirect()->route('admin.slider.index')->with('success', 'Slider Updated Successfully!');
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return redirect()->back()->with('error', 'Slider Update Failed!');
            }
        }

        return view('admin.extends.slider.edit', compact('slider'));
    }

    public function destroy($id)
    {
        $slider = Slider::find($id);

        if (empty($slider)) {
            return response()->json(['success' => false, 'message' => 'Slider not found!'], 404);
        }

        try {
            if ($slider->image) $this->destroyImage($slider->image, $this->imagePath);
            $slider->delete();

            Log::info('Slider Deleted Successfully!');
            return response()->json(['success' => true, 'message' => 'Slider deleted successfully!']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['success' => false, 'message' => 'Slider delete failed!'], 500);
        }
    }
}
