<?php

namespace App\Http\Controllers\Depo\Account;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Depo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class AccountController extends Controller
{
    public function index(Request $request){
        $userId=!empty(Session::get('userObj')) ? Session::get('userObj')->id : Auth::user()->id;

        if($request->ajax()){
            $userId=!empty(Session::get('userObj')) ? Session::get('userObj')->id : Auth::user()->id;
            $accounts = Account::with(['user', 'depo'])->where('user_id',$userId)->get();

            return DataTables::of($accounts)
                ->addIndexColumn()
                ->addColumn('account_no', function ($row) {
                    return $row->account_no ?? 'N/A';
                })
                ->addColumn('account_name', function ($row) {
                    return $row->account_name ?? 'N/A';
                })
                ->addColumn('depo_name', function ($row) {
                    return $row->depo->depo_name ?? 'N/A';
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
                    $editUrl = route('depo.account.edit', $row->id);
                    $deleteUrl = route('depo.account.delete', $row->id);

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
        return view('depo.extends.account.index');
    }

    public function create(Request $request)
    {
        $userId = !empty(Session::get('userObj'))
            ? Session::get('userObj')->id
            : Auth::user()->id;

        if ($request->isMethod('POST')) {

            $request->validate([
                'account_no'      => 'required|string|max:50|unique:accounts,account_no',
                'account_name'    => 'required|string|max:100',
                'opening_balance' => 'required|numeric|min:0',
                'status'          => 'nullable|boolean',
                'is_default'      => 'nullable|boolean',
            ]);

            try {
                DB::beginTransaction();

                $depo = Depo::where('user_id', $userId)->firstOrFail();

                // If this account is marked as default
                if ($request->has('is_default') && $request->is_default == 1) {

                    // Remove default from all other accounts of this depo
                    Account::where('depo_id', $depo->id)
                        ->update(['is_default' => 0]);
                }

                // Create account
                Account::create([
                    'account_no'      => $request->account_no,
                    'account_name'    => $request->account_name,
                    'user_id'         => $userId,
                    'depo_id'         => $depo->id,
                    'opening_balance' => $request->opening_balance,
                    'balance'         => $request->opening_balance,
                    'status'          => $request->status ?? 1,
                    'is_default'      => $request->is_default ? 1 : 0,
                ]);

                DB::commit();

                Log::info('Depo Account Added Successfully.');
                return redirect()->back()->with('success', 'Account Added Successfully');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e->getMessage());
                return redirect()->back()->with('error', 'Account Add Failed');
            }
        }

        return view('depo.extends.account.create');
    }



    public function edit($id)
    {
        $userId=!empty(Session::get('userObj')) ? Session::get('userObj')->id : Auth::user()->id;
        $account = Account::with(['user', 'depo'])->where('id',$id)->where('user_id', $userId)->first();

        if (!$account) {
            Log::info('Account Not Found.');
            return redirect()->back()->with('error', 'Account not found or access denied.');
        }
        Log::info('Account Edited Successfully.');
        return view('depo.extends.account.edit', compact('account'));
    }

    public function update(Request $request, $id)
    {
        $userId = !empty(Session::get('userObj'))
            ? Session::get('userObj')->id
            : Auth::user()->id;

        $account = Account::where('id', $id)
            ->where('user_id', $userId)
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


            if ($request->has('is_default') && $request->is_default == 1) {

                // Remove default from other accounts of this depo
                Account::where('depo_id', $account->depo_id)
                    ->where('id', '!=', $account->id)
                    ->update(['is_default' => 0]);
            }


            $account->update([
                'account_no'      => $request->account_no,
                'account_name'    => $request->account_name,
                'opening_balance' => $request->opening_balance,
                'status'          => $request->status ?? 1,
                'is_default'      => $request->is_default ? 1 : 0,
            ]);

            DB::commit();

            Log::info("Account Updated Successfully for Id: {$id}");
            return redirect()->back()->with('success', 'Account Updated Successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Account Update Failed');
        }
    }



    public function destroy($id)
    {
        $userId=!empty(Session::get('userObj')) ? Session::get('userObj')->id : Auth::user()->id;
        try{
            $deleted = Account::where('id',$id)->where('user_id', $userId)->delete();

            if (!$deleted) {
                return response()->json(['success'=>false,'message'=>'Account not found or access denied']);
            }

            Log::info("Account Deleted Successfully for Id: {$id}");
            return response()->json(['success'=>true,'message'=>'Account Deleted Successfully']);
        } catch (\Exception $e){
            Log::error($e->getMessage());
            return response()->json(['success'=>false,'message'=>'Account Delete Failed']);
        }
    }
}
