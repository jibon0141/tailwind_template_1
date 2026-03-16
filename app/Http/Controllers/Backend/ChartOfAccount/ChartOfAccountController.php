<?php

namespace App\Http\Controllers\Backend\ChartOfAccount;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\GlAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class ChartOfAccountController extends Controller
{
    public function index(Request $request){
        $userId=Auth::user()->id;
        if($request->ajax()){
            $chartOfAccounts = ChartOfAccount::with('glAccount')->where('user_id', $userId)->get();

            return DataTables::of($chartOfAccounts)
                ->addIndexColumn()
                ->addColumn('gl_account_name', function ($row) {
                    return $row->glAccount->account_name ?? 'N/A';
                })
                ->addColumn('head_type', function ($row) {
                    return $row->head_type ?? 'N/A';
                })
                ->addColumn('head_name', function ($row) {
                    return $row->head_name ?? 'N/A';
                })
                ->addColumn('status', function ($row) {
                    return $row->status
                        ? '<span class="inline-block px-5 py-2 text-xs font-semibold text-green-800 bg-green-200 rounded-lg">Active</span>'
                        : '<span class="inline-block px-4 py-2 text-xs font-semibold text-red-800 bg-red-200 rounded-lg">Inactive</span>';
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('admin.chart-of-account.edit', $row->id);
                    $deleteUrl = route('admin.chart-of-account.delete', $row->id);

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
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('admin.extends.chart_of_account.index');
    }

    public function create(Request $request)
    {
        $userId= Auth::user()->id;

        if($request->isMethod('POST')){
            $request->validate([
                'gl_account_id' => 'required|exists:gl_accounts,id',
                'head_type' => 'required|string|max:255',
                'head_name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('chart_of_accounts')->where(function ($query) use ($userId) {
                        return $query->where('user_id', $userId);
                    }),
                ],
                'status' => 'required|boolean',
            ]);

            try{
                $data=[
                    'gl_account_id' => $request->gl_account_id,
                    'head_type' => $request->head_type,
                    'head_name' => $request->head_name,
                    'status' => $request->status,
                    'user_id' =>$userId,
                ];

                ChartOfAccount::insert($data);
                Log::info('Chart of Account Added Successfully.');
                return redirect()->back()->with('success','Chart of Account Added Successfully');
            } catch (\Exception $e){
                Log::error($e->getMessage());
                return redirect()->back()->with('error','Chart of Account Add Failed');
            }
        }
        $glAccounts = GlAccount::all();
        return view('admin.extends.chart_of_account.create', compact('glAccounts'));
    }

    public function edit($id)
    {
        $userId=Auth::user()->id;
        $chartOfAccount = ChartOfAccount::with('glAccount')->where('id',$id)->where('user_id', $userId)->first();

        if (!$chartOfAccount) {
            return redirect()->back()->with('error', 'Chart of Account not found or access denied.');
        }

        $glAccounts = GlAccount::all();
        return view('admin.extends.chart_of_account.edit', compact('chartOfAccount', 'glAccounts'));
    }

    public function update(Request $request, $id)
    {
        $userId=Auth::user()->id;
        $chartOfAccount = ChartOfAccount::where('id', $id)->where('user_id', $userId)->first();

        if (!$chartOfAccount) {
            return redirect()->back()->with('error', 'Chart of Account not found or access denied.');
        }

        $request->validate([
            'gl_account_id' => 'required|exists:gl_accounts,id',
            'head_type' => 'required|string|max:255',
            'head_name' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        try{
            $data=[
                'gl_account_id' => $request->get('gl_account_id'),
                'head_type' => $request->get('head_type'),
                'head_name' => $request->get('head_name'),
                'status' => $request->get('status'),
                'user_id' => $userId,
                'updated_at' => now(),
            ];

            $chartOfAccount->update($data);
            Log::info("Chart of Account Updated Successfully for Id: {$id}");
            return redirect()->back()->with('success','Chart of Account Updated Successfully');
        } catch (\Exception $e){
            Log::error($e->getMessage());
            return redirect()->back()->with('error','Chart of Account Update Failed');
        }
    }

    public function destroy($id)
    {
        try {
            // Dependency check
            $hasDependency = DB::table('debit_voucher_items')
                    ->where('chart_of_account_id', $id)
                    ->exists()
                ||
                DB::table('credit_voucher_items')
                    ->where('chart_of_account_id', $id)
                    ->exists();

            if ($hasDependency) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete. Chart of Account has debit or credit vouchers.'
                ]);
            }

            $coa = ChartOfAccount::find($id);

            if (!$coa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chart of Account not found.'
                ]);
            }

            $coa->delete();

            return response()->json([
                'success' => true,
                'message' => 'Chart of Account deleted successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Delete failed.'
            ]);
        }
    }


}
