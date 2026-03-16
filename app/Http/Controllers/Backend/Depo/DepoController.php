<?php

namespace App\Http\Controllers\Backend\Depo;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use App\Models\Depo;
use App\Models\DepoDueAccount;
use App\Models\User;
use App\Models\Division;
use App\Models\District;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class DepoController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $depo = Depo::with(['user', 'companySetting','depoDueAccount'])->get();

            return DataTables::of($depo)
                ->addIndexColumn()
                ->addColumn('depo_name', function ($row) {
                    return $row->depo_name ?? 'N/A';
                })
                ->addColumn('area_code', function ($row) {
                    return $row->area_code ?? 'N/A';
                })
                ->addColumn('user_name', function ($row) {
                    return $row->user->name ?? 'N/A';
                })
                ->addColumn('person_name', function ($row) {
                    return $row->person_name ?? 'N/A';
                })
                ->addColumn('email', function ($row) {
                    return $row->user->email ?? 'N/A';
                })
                ->addColumn('contact', function ($row) {
                    return $row->contact ?? 'N/A';
                })
                ->addColumn('address', function ($row) {
                    return $row->address ?? 'N/A';
                })
                ->addColumn('due_balance', function ($row) {
                    return $row->depoDueAccount->due_balance ?? 'N/A';
                })
                ->addColumn('status', function ($row) {
                    return $row->status
                        ? '<span class="inline-block px-5 py-2 text-xs font-semibold text-green-800 bg-green-200 rounded-lg">Active</span>'
                        : '<span class="inline-block px-4 py-2 text-xs font-semibold text-red-800 bg-red-200 rounded-lg">Inactive</span>';
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('depo.edit', $row->id);
                    $accessUrl = route('depo.access', $row->id);

                    $buttons = '<div class="flex gap-2">';

                    // Edit button
                    $buttons .= '<a href="' . $editUrl . '"
                     class="inline-flex items-center px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold rounded"
                     title="Edit">
                     <i class="fa fa-edit"></i>
                 </a>';

                    $buttons .= '<a href="' . $accessUrl . '"
    class="inline-flex items-center px-2 py-1 bg-green-500 hover:bg-green-600
           text-white text-xs font-semibold rounded"
    title="Login as Depo">
    <i class="fa fa-sign-in-alt"></i>
</a>';

                    $buttons .= '</div>';

                    return $buttons;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('admin.extends.depo.index');
    }

    public function create(Request $request)
    {
        if ($request->isMethod('POST')) {


            $request->validate([
                'depo_name' => 'required|string|max:255|unique:depos,depo_name',
                'person_name' => 'required|string',
                'area_code' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'contact' => 'required|string|max:255',
                'division_id' => 'required',
                'district_id' => 'required',
                'account_no'  => 'required',
                'address' => 'required|string|max:255',
                'password' => 'required|string|min:6|confirmed',

            ]);


            try {

                DB::beginTransaction();
                // Create user
                $user = User::create([
                    'name' => $request->depo_name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'user_type' => 'depo',
                    'status' => 1,
                ]);

                // Create depo
                $depo = Depo::create([
                    'user_id' => $user->id,
                    'depo_name' => $request->depo_name,
                    'area_code' => $request->area_code,
                    'person_name' => $request->person_name,
                    'email' => $request->email,
                    'contact' => $request->contact,
                    'division_id' => $request->division_id,
                    'district_id' => $request->district_id,
                    'account_no' => $request->account_no,
                    'address' => $request->address,
                    'status' => 1,
                ]);

                DepoDueAccount::create([
                    'depo_id' => $depo->id,
                    'due_balance' => 0,
                    'created_at' => now(),
                ]);

                DB::commit();
                Log::info('Depo and User Added Successfully.');
                return redirect()->back()->with('success', 'Depo Added Successfully');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e->getMessage());
                return redirect()->back()->with('error', 'Depo Add Failed');
            }
        }
        $divisions = Division::where('status', 1)->get();
        return view('admin.extends.depo.create', compact('divisions'));
    }

    public function edit($id)
    {
        $depo = Depo::with(['user', 'companySetting'])->where('id', $id)->first();
        $divisions = Division::where('status', 1)->get();
        $districts = District::where('status', 1)->get();
        return view('admin.extends.depo.edit', compact('depo', 'divisions', 'districts'));
    }

    public function depoAccess($id)
    {
        session()->forget('userObj');
//        $depo = Depo::with('user')->find($id);
        $users = DB::table('depos')
            ->leftJoin('users', 'users.id', '=', 'depos.user_id')
            ->select('depos.id as depo_primary_id', 'depos.depo_name', 'users.id', 'users.name', 'users.email', 'users.user_type', 'users.status')
            ->where('depos.id', $id)
            ->first();
        Session::put('userObj', $users);


        return redirect()->route('depo.dashboard');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'depo_name' => 'required|string|max:255',
            'person_name' => 'required|string',
            'area_code' => 'required|string',
            'email' => 'required|email',
            'contact' => 'required|string|max:255',
            'division_id' => 'required',
            'district_id' => 'required',
            'account_no'  => 'required',
            'address' => 'required|string|max:255',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        DB::beginTransaction();
        try {
            $depo = Depo::with('user')->findOrFail($id);

            // Update user
            $userData = [
                'name' => $request->depo_name,
                'email' => $request->email,
                'user_type' => 'depo',
                'status' => 1,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $depo->user->update($userData);

            // Update depo
            $depo->update([
                'company_setting_id' => 1,
                'depo_name' => $request->depo_name,
                'area_code' => $request->area_code,
                'person_name' => $request->person_name,
                'email' => $request->email,
                'contact' => $request->contact,
                'division_id' => $request->division_id,
                'district_id' => $request->district_id,
                'account_no'  => $request->account_no,
                'address' => $request->address,
                'status' => 1,
            ]);

            DB::commit();
            Log::info("Depo and User Updated Successfully for Id: {$id}");
            return redirect()->back()->with('success', 'Depo Updated Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Depo Update Failed');
        }
    }

    public function getDistricts($divisionId)
    {
        // Route: /depo/districts/{divisionId}
        // You can implement your district fetching logic here
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $depo = Depo::with('user')->findOrFail($id);

            // Delete user (this will cascade delete depo due to foreign key)
            $depo->user->delete();

            DB::commit();
            Log::info("Depo and User Deleted Successfully for Id: {$id}");
            return response()->json(['success' => true, 'message' => 'Depo Deleted Successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json(['success' => false, 'message' => 'Depo Delete Failed']);
        }
    }

}
