<?php

namespace App\Http\Controllers\Backend\MarketingTeam;

use App\Http\Controllers\Controller;
use App\Models\Depo;
use App\Models\District;
use App\Models\Division;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class MpoController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $employees = Employee::with(['division', 'district','depo'])
                ->where('employee_type', 'mpo')
                ->get();

            return datatables()->of($employees)
                ->addIndexColumn()
                ->addColumn('employee_code', fn($row) => $row->employee_code ?? 'N/A')
                ->addColumn('full_name', fn($row) => $row->full_name ?? '')
                ->addColumn('email', fn($row) => $row->email ?? 'N/A')
                ->addColumn('phone', fn($row) => $row->phone ?? 'N/A')
                ->addColumn('division', fn($row) => $row->division->name ?? 'N/A')
                ->addColumn('district', fn($row) => $row->district->name ?? 'N/A')
                ->addColumn('address', fn($row) => $row->address ?? 'N/A')
                ->addColumn('depo_name', fn($row) => $row->depo->depo_name ?? 'N/A')
                ->addColumn('employee_type', fn($row) => strtoupper($row->employee_type ?? 'N/A'))
                ->addColumn('action', function ($row) {
                    $editUrl = route('mpo.edit', $row->id);
                    $addDepo = route('mpo.assign.depo', $row->id);
                    $accessUrl = route('mpo.access', $row->id);
                    return '<div class="flex gap-2">
                        <a href="' . $editUrl . '" class="inline-flex items-center px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold rounded" title="Edit"><i class="fa fa-edit"></i></a>
                           <a href="' . $addDepo . '" class="inline-flex items-center px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold rounded" title="Edit"><i class="fa fa-plus"></i></a>
                    <a href="' . $accessUrl . '" class="inline-flex items-center px-2 py-1 bg-green-500 hover:bg-green-600 text-white text-xs font-semibold rounded"
    title="Login as Mpo">
    <i class="fa fa-sign-in-alt"></i>
</a>
                    </div>';

                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.extends.marketing_team.mpo.index');
    }

    public function create(Request $request)
    {


        if ($request->isMethod('POST')) {

            $request->validate([
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|unique:employees,email',
                'phone' => 'required|string|max:20',
                'division_id' => 'required',
                'district_id' => 'required',
                'address' => 'nullable|string|max:255',
                'password' => 'required|string|min:6|confirmed',
            ]);

            try {
                DB::beginTransaction();

                $employee_type = 'mpo';

                $user = User::create([
                    'name' => $request->full_name,
                    'email' => $request->email,
                    'user_type' => $employee_type,
                    'status' => 1,
                    'password' => Hash::make($request->password),

                ]);

                Employee::create([
                    'user_id' => $user->id,
                    'full_name' => $request->full_name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'division_id' => $request->division_id,
                    'district_id' => $request->district_id,
                    'address' => $request->address,
                    'employee_type' => $request->employee_type,
                    'parent_id' => $request->parent_id,
                ]);
                DB::commit();
                Log::info('Mpo Created Successfully.');
                return redirect()->back()->with('success', 'Mpo Created Successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e->getMessage());
                return redirect()->back()->with('error', 'Mpo Created Failed.');
            }
        }
        $asms = Employee::where('employee_type', 'asm')->get();
        $divisions = Division::all();
        return view('admin.extends.marketing_team.mpo.create', compact('divisions', 'asms'));
    }

    public function edit($id)
    {
        $asms = Employee::where('employee_type', 'asm')->get();
        try {
            $employee = Employee::where('id', $id)->first();
            $divisions = Division::all();
            $districts = District::all();
            return view('admin.extends.marketing_team.mpo.edit', compact('asms', 'employee', 'divisions', 'districts'));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Mpo Update Failed.');

        }

    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $id,
            'phone' => 'required|string|max:20',
            'division_id' => 'required',
            'district_id' => 'required',
            'address' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6|confirmed',
            'password_confirmation' => 'nullable|string|min:6',
            'parent_id' => 'required|exists:employees,id',
        ]);

        try {
            DB::beginTransaction();

            $employee = Employee::findOrFail($id);
            $user = User::findOrFail($employee->user_id);

            $employee_type = 'mpo';

            /* ---------- Update User ---------- */
            $userData = [
                'name' => $request->full_name,
                'email' => $request->email,
                'user_type' => $employee_type,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);


            $employee->update([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'division_id' => $request->division_id,
                'district_id' => $request->district_id,
                'address' => $request->address,
                'parent_id' => $request->parent_id,
            ]);

            DB::commit();
            Log::info('MPO Updated Successfully.');

            return redirect()->back()->with('success', 'MPO Updated Successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'MPO Update Failed.');
        }
    }


//    Depo assign to  Mpo
    public function assignDepo($id)
    {
        $mpo = Employee::where('id', $id)->first();
        $depos = Depo::all();
        return view('admin.extends.marketing_team.mpo.assign_depo', compact('mpo', 'depos'));
    }

    public function addDepo(Request $request, $id)
    {
        $request->validate([
            'depo_id' => 'required|exists:depos,id',
        ]);

        try {
            DB::beginTransaction();

            $mpo = Employee::where('id',$id);
            if(empty($mpo)){
                Log::error('MPO Not Found.');
                return back()->with('error', 'MPO Not Found.');
            }
            $data=[
                'depo_id' => $request->depo_id,
            ];
            $mpo->update($data);

            DB::commit();
            Log::info('Depo Assigned Successfully.');
            return back()->with('success', 'Depo Assigned Successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return back()->with('error', 'Depo Assignment Failed.');
        }
    }


    public function mpoAccess($id)
    {
        session()->forget('userObj');

        $users = DB::table('employees')
            ->leftJoin('users', 'users.id', '=', 'employees.user_id')
            ->select(
                'employees.id as employee_primary_id',
                'employees.full_name',
                'users.id',
                'users.name',
                'users.email',
                'users.user_type',
                'users.status',
                'users.created_at',
            )
            ->where('employees.id', $id)
            ->first();

        if (!$users) {
            abort(404);
        }

        Session::put('userObj', $users);

        return redirect()->route('mpo.dashboard');
    }





}
