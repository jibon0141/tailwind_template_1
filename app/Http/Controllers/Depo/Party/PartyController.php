<?php

namespace App\Http\Controllers\Depo\Party;

use App\Http\Controllers\Controller;
use App\Models\Party;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class PartyController extends Controller
{
    public function index(Request $request)
    {
        $userId=!empty(Session::get('userObj')) ? Session::get('userObj')->id : Auth::user()->id;

        if ($request->ajax()) {
            $parties = Party::where('user_id',$userId)->get();

            return Datatables::of($parties)
                ->addIndexColumn()
                ->addColumn('party_code',function($row){
                    return $row->party_code ?? 'N/A';
                })
                ->addColumn('party_name', function ($row) {
                    return $row->party_name ?? 'N/A';
                })
                ->addColumn('email', function ($row) {
                    return $row->email ?? 'N/A';
                })
                ->addColumn('phone', function ($row) {
                    return $row->phone ?? 'N/A';
                })
                ->addColumn('address', function ($row) {
                    return $row->address ?? 'N/A';
                })
                ->addColumn('action', function ($row) {
                    return $row->status
                        ? '<span class="px-3 py-1 text-xs font-semibold text-green-800 bg-green-200 rounded-full">Active</span>'
                        : '<span class="px-3 py-1 text-xs font-semibold text-red-800 bg-red-200 rounded-full">Inactive</span>';
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('depo.party.edit', $row->id);

                    return '
    <div class="flex gap-2">
        <a href="' . $editUrl . '" class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded">
            <i class="fa fa-edit"></i>
        </a>
        <button onclick="deleteItem(' . $row->id . ')" class="px-2 py-1 bg-red-500 hover:bg-red-600 text-white text-xs rounded">
            <i class="fa fa-trash"></i>
        </button>
    </div>
    ';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);

        }
        return view('depo.extends.party.index');
    }

    public function create(Request $request)
    {
        $userId=!empty(Session::get('userObj')) ? Session::get('userObj')->id : Auth::user()->id;

        if ($request->isMethod('POST')) {
            $request->validate([
                'party_name' => 'required',
                'email' => 'nullable',
                'phone' => 'required',
                'address' => 'nullable',
            ]);

            try {
                Party::create([
                    'user_id' => $userId,
                    'party_name' => $request->party_name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'created_at' => now(),
                ]);
                Log::info('Part Created Successfully');
                return redirect()->back()->with('success', 'Part Created Successfully.');
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return redirect()->back()->with('error', 'Party Create Failed.');

            }

        }
        return view('depo.extends.party.create');
    }

    public function edit($id)
    {
        $userId=!empty(Session::get('userObj')) ? Session::get('userObj')->id : Auth::user()->id;

        $party = Party::where('user_id',$userId)
        ->where('id', $id)->first();

        if (empty($party)) {
            Log::info('Party Not Found', ['party_id' => $id]);
            return redirect()->back()->with('error', 'Party Not Found.');
        }

        return view('depo.extends.party.edit', compact('party'));
    }


    public function update(Request $request, $id)
    {
        $userId=!empty(Session::get('userObj')) ? Session::get('userObj')->id : Auth::user()->id;

        $party = Party::where('user_id',$userId)
        ->where('id', $id)->first();

        if (empty($party)) {
            Log::info('Party Not Found', ['party_id' => $id]);
            return redirect()->back()->with('error', 'Party Not Found.');
        }

        $request->validate([
            'party_name' => 'required|string|max:100',
            'email'      => 'nullable',
            'phone'      => 'required|string|max:20',
            'address'    => 'nullable|string',
        ]);

        try {
            $party->update([
                'user_id' => Auth::id(),
                'party_name' => $request->party_name,
                'email'      => $request->email,
                'phone'      => $request->phone,
                'address'    => $request->address,
            ]);

            Log::info('Party Updated Successfully', ['party_id' => $party->id]);

            return redirect()->back()->with('success', 'Party Updated Successfully.');
        } catch (\Exception $e) {
            Log::error('Party Update Failed', [
                'party_id' => $id,
                'error'    => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Party Update Failed.');
        }
    }


    public function destroy($id)
    {
        try {
            $userId = !empty(Session::get('userObj')) ? Session::get('userObj')->id : Auth::user()->id;

            $party = Party::where('id', $id)
                ->where('user_id', $userId)
                ->first();

            if (empty($party)) {
                Log::info('Party Not Found', ['party_id' => $id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Party Not Found.'
                ], 404);
            }

            // Check debit vouchers
            $hasDebit = DB::table('debit_vouchers')
                ->where('party_id', $id)
                ->where('user_id', $userId)
                ->exists();

            // Check credit vouchers
            $hasCredit = DB::table('credit_vouchers')
                ->where('party_id', $id)
                ->where('user_id', $userId)
                ->exists();

            if ($hasDebit || $hasCredit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete party. Debit or Credit vouchers exist.'
                ], 422);
            }

            $party->delete();

            Log::info('Party Deleted Successfully', ['party_id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Party Deleted Successfully.'
            ], 200);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Party Delete Failed.'
            ], 500);
        }
    }


}
