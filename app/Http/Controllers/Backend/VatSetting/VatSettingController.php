<?php

namespace App\Http\Controllers\Backend\VatSetting;

use App\Http\Controllers\Controller;
use App\Models\Depo;
use App\Models\VatSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class VatSettingController extends Controller
{

    public function index(Request $request)
    {

        if ($request->ajax()) {
            try {

                $vat = VatSetting::with('depo')->get();

                return DataTables::of($vat)
                    ->addIndexColumn()
                    ->addColumn('depo_name', fn($row) => $row->depo->depo_name ?? 'N/A')
                    ->addColumn('vat_percentage', fn($row) => $row->vat_percentage.' %' ?? 'N/A')
                    ->addColumn('status', function ($row) {
                        return $row->status
                            ? '<span class="inline-block px-4 py-1 text-xs font-semibold text-green-800 bg-green-200 rounded-lg">Active</span>'
                            : '<span class="inline-block px-4 py-1 text-xs font-semibold text-red-800 bg-red-200 rounded-lg">Inactive</span>';
                    })
                    ->addColumn('action', function ($row) {
                        $editUrl = route('vat.edit', $row->id);
                        $showUrl = route('vat.delete', $row->id);

                        $btn = '<div class="flex gap-2">';
                        $btn .= '<a href="' . $editUrl . '" class="inline-flex items-center px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded" title="Edit"><i class="fa fa-edit"></i></a>';
                        $btn .= '<button onclick="deleteItem(' . $row->id . ')" class="inline-flex items-center px-2 py-1 bg-red-500 hover:bg-red-600 text-white text-xs rounded" title="Delete"><i class="fa fa-trash"></i></button>';
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

        return view('admin.extends.vat_setting.index');

    }

    public function create(Request $request)
    {

        if ($request->isMethod('POST')) {

            $request->validate([
                'depo_id' => 'required|unique:vat_settings,depo_id',
                'vat_percentage' => 'required',
                'status' => 'required',
            ]);

            try {

                $data = [
                    'depo_id' => $request->depo_id,
                    'vat_percentage' => $request->vat_percentage,
                    'status' => $request->status,
                ];

                VatSetting::insert($data);
                Log::info('Vat Added Successfully.');
                return redirect()->back()->with('success', 'Vat Added Successfully.');
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return redirect()->back()->with('error', 'Vat Add Failed!');
            }
        }
        $depos = Depo::all();
        return view('admin.extends.vat_setting.create', compact('depos'));
    }

    public function show($id)
    {

        try {
            $vat = VatSetting::where('id', $id)->first();
            if (empty($vat)) {
                Log::info("Vat Not Found For Id:{$id}");
                return redirect()->back()->with('error', 'Vat Not Found.');
            }
            Log::info("Vat Found for Id:{$id}");
            return redirect()->back()->with('success', 'Vat Found.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Vat Not Found.');
        }

    }

    public function edit($id)
    {
        try {
            $vat = VatSetting::with('depo')->find($id);

            if (empty($vat)) {
                Log::info("VAT Not Found For Id: {$id}");
                return redirect()->back()->with('error', 'VAT Not Found.');
            }

            // Get all depos to populate dropdown
            $depos = Depo::all();

            Log::info("VAT Found for Id: {$id}");
            return view('admin.extends.vat_setting.edit', compact('vat', 'depos'));

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'VAT Not Found.');
        }
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'depo_id' => 'required|unique:vat_settings,depo_id,' . $id,
            'vat_percentage' => 'required|numeric',
            'status' => 'required|boolean',
        ]);

        try {
            $data=[
                'depo_id' => $request->depo_id,
                'vat_percentage' => $request->vat_percentage,
                'status' => $request->status,
            ];
            VatSetting::where('id',$id)->update($data);
            Log::info('VAT Updated Successfully for Id:' . $id);
            return redirect()->back()->with('success', 'VAT Updated Successfully.');

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'VAT Update Failed!');
        }
    }


    public function destroy($id)
    {
        try {
            $vat = VatSetting::where('id', $id)->first();

            if (empty($vat)) {
                Log::info("Vat Not Found For Id: {$id}");

                return response()->json([
                    'success' => false,
                    'message' => 'Vat Not Found.'
                ], 404);
            }

            $vat->delete();

            Log::info("Vat Deleted Successfully for Id: {$id}");

            return response()->json([
                'success' => true,
                'message' => 'Vat Deleted Successfully.'
            ],200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Vat Delete Failed!'
            ], 500);
        }
    }



}
