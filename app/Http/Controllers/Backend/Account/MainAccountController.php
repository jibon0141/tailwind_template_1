<?php

namespace App\Http\Controllers\Backend\Account;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Depo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class MainAccountController extends Controller
{
    public function index(Request $request){


        if($request->ajax()){
            $accounts = Account::where('user_id',Auth::id())
                ->where('depo_id',0)->get();

            return DataTables::of($accounts)
                ->addIndexColumn()
                ->addColumn('account_no', function ($row) {
                    return $row->account_no ?? 'N/A';
                })
                ->addColumn('account_name', function ($row) {
                    return $row->account_name ?? 'N/A';
                })

                ->addColumn('opening_balance', function ($row) {
                    return number_format($row->opening_balance, 2);
                })
                ->addColumn('balance', function ($row) {
                    return number_format($row->balance, 2);
                })
                ->addColumn('status', function ($row) {
                    return $row->status
                        ? '<span class="inline-block px-5 py-2 text-xs font-semibold text-green-800 bg-green-200 rounded-lg">Active</span>'
                        : '<span class="inline-block px-4 py-2 text-xs font-semibold text-red-800 bg-red-200 rounded-lg">Inactive</span>';
                })
                ->addColumn('is_default', function ($row) {
                    return $row->is_default
                        ? '<span class="inline-block px-3 py-1 text-xs font-semibold text-blue-800 bg-blue-200 rounded-lg">Yes</span>'
                        : '<span class="inline-block px-3 py-1 text-xs font-semibold text-gray-800 bg-gray-200 rounded-lg">No</span>';
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('admin.account.edit', $row->id);
                    $deleteUrl = route('admin.account.delete', $row->id);

                    $buttons = '<div class="flex gap-2">';

                    // Edit button
                    $buttons .= '<a href="' . $editUrl . '"
                     class="inline-flex items-center px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold rounded"
                     title="Edit">
                     <i class="fa fa-edit"></i>
                 </a>';

                    // Delete button
//                    $buttons .= '<button onclick="deleteItem(' . $row->id . ')"
//                         class="inline-flex items-center px-2 py-1 bg-red-500 hover:bg-red-600 text-white text-xs font-semibold rounded"
//                         title="Delete">
//                     <i class="fa fa-trash"></i>
//                 </button>';

                    $buttons .= '</div>';

                    return $buttons;
                })
                ->rawColumns(['status', 'is_default', 'action'])
                ->make(true);
        }
        return view('admin.extends.main_account.index');
    }

    public function create(Request $request)
    {
        if ($request->isMethod('POST')) {

            $request->validate([
                'account_no'      => 'required|string|max:50|unique:accounts,account_no',
                'account_name'    => 'required|string|max:100',
                'opening_balance' => 'required|numeric|min:0',
                'status'          => 'required|boolean',
                'is_default'      => 'nullable|boolean',
            ]);

            try {
                DB::beginTransaction();

                $userId = Auth::id(); // Admin ID
                $depoId = 0;          // Main branch

                // If new account is marked as default
                if ($request->has('is_default') && $request->is_default == 1) {

                    // Remove default from all existing accounts
                    Account::where('user_id', $userId)
                        ->where('depo_id', $depoId)
                        ->update(['is_default' => 0]);
                }

                // Create account
                Account::create([
                    'account_no'      => $request->account_no,
                    'account_name'    => $request->account_name,
                    'user_id'         => $userId,
                    'depo_id'         => $depoId,
                    'opening_balance' => $request->opening_balance,
                    'balance'         => $request->opening_balance,
                    'status'          => $request->status,
                    'is_default'      => $request->is_default ? 1 : 0,
                ]);

                DB::commit();

                Log::info('Main Branch Account Added Successfully');
                return redirect()->back()->with('success', 'Account Added Successfully');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Account Add Failed: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Account Add Failed');
            }
        }

        return view('admin.extends.main_account.create');
    }





    public function edit($id)
    {
        $account = Account::with(['user'])->where('id',$id)->where('user_id',Auth::id())->first();

        if (!$account) {
            Log::info('Account Not Found.');
            return redirect()->back()->with('error', 'Account not found or access denied.');
        }
        Log::info('Account Edited Successfully.');
        return view('admin.extends.main_account.edit', compact('account'));
    }

    public function update(Request $request, $id)
    {
        $account = Account::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$account) {
            return redirect()->back()->with('error', 'Account not found or access denied.');
        }

        $request->validate([
            'account_no'      => 'required|string|max:50|unique:accounts,account_no,' . $account->id,
            'account_name'    => 'required|string|max:100',
            'opening_balance' => 'required|numeric|min:0',
            'status'          => 'nullable|boolean',
            'is_default'      => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            // If user wants to make this account default
            if ($request->has('is_default') && $request->is_default == 1) {

                // Remove default from all other accounts of this user + depo
                Account::where('user_id', Auth::id())
                    ->where('depo_id', $account->depo_id)
                    ->where('id', '!=', $account->id)
                    ->update(['is_default' => 0]);
            }

            // Update current account
            $account->update([
                'account_no'      => $request->account_no,
                'account_name'    => $request->account_name,
                'opening_balance' => $request->opening_balance,
                'status'          => $request->status ?? 1,
                'is_default'      => $request->is_default ? 1 : 0,
            ]);

            DB::commit();

            Log::info("Main Account Updated Successfully for Id: {$id}");
            return redirect()->back()->with('success', 'Main Account Updated Successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Main Account Update Failed');
        }
    }



    public function destroy($id)
    {
        try{
            $deleted = Account::where('id',$id)->where('user_id', Auth::id())->delete();

            if (!$deleted) {
                return response()->json(['success'=>false,'message'=>'Main Account not found or access denied']);
            }

            Log::info("Main Account Deleted Successfully for Id: {$id}");
            return response()->json(['success'=>true,'message'=>'Main Account Deleted Successfully.']);
        } catch (\Exception $e){
            Log::error($e->getMessage());
            return response()->json(['success'=>false,'message'=>'Main Account Delete Failed']);
        }
    }
}
