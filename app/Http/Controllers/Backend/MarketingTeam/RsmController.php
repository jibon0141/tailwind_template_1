<?php

namespace App\Http\Controllers\Backend\MarketingTeam;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Division;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class RsmController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $employees = Employee::with(['division', 'district'])
            ->where('employee_type', 'rsm')->get();

            return datatables()->of($employees)
                ->addIndexColumn()
                ->addColumn('employee_code', fn($row) => $row->employee_code ?? 'N/A')
                ->addColumn('full_name', fn($row) => $row->full_name ?? 'N/A')
                ->addColumn('email', fn($row) => $row->email ?? 'N/A')
                ->addColumn('phone', fn($row) => $row->phone ?? 'N/A')
                ->editColumn('division', function ($row) {
                    return $row->division->name ?? 'N/A';
                }) ->editColumn('district', function ($row) {
                    return $row->district->name ?? 'N/A';
                })
                ->addColumn('address', fn($row) => $row->address ?? 'N/A')
                ->addColumn('employee_type', fn($row) => strtoupper($row->employee_type ?? 'N/A'))
                ->addColumn('action', function ($row) {
                    $editUrl = route('rsm.edit', $row->id);
                    $deleteUrl = route('rsm.delete', $row->id);
                    $accessUrl = route('rsm.access', $row->id);


                    return '<div class="flex gap-2">
        <a href="' . $editUrl . '" class="inline-flex items-center px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold rounded" title="Edit">
            <i class="fa fa-edit"></i>
        </a>
        <a href="' . $accessUrl . '" class="inline-flex items-center px-2 py-1 bg-green-500 hover:bg-green-600 text-white text-xs font-semibold rounded" title="Login as MPO">
            <i class="fa fa-sign-in-alt"></i>
        </a>
    </div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.extends.marketing_team.rsm.index');
    }

    public function create(Request $request)
    {
        $nsms = Employee::where('employee_type', 'nsm')->get();

        if ($request->isMethod('POST')) {
            $data = $request->validate([
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|unique:employees,email',
                'phone' => 'required|string|max:20',
                'division_id' => 'required',
                'district_id' => 'required',
                'address' => 'nullable|string|max:255',
                'parent_id' => 'required|exists:employees,id', // Must select NSM
                'password' => 'required|string|min:6|confirmed',
            ]);


            try {

                DB::beginTransaction();

                $data['employee_type'] = 'rsm';

                // Create User
                $user = User::create([
                    'name' => $data['full_name'] ?? '',
                    'email' => $data['email'],
                    'user_type' => $data['employee_type'],
                    'status' => 1,
                    'password' => Hash::make($data['password']),
                ]);

                // Create Employee
                Employee::create([
                    'user_id' => $user->id,
                    'full_name' => $data['full_name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'division_id' => $data['division_id'],
                    'district_id' => $data['district_id'],
                    'address' => $data['address'] ?? null,
                    'employee_type' => $data['employee_type'],
                    'parent_id' => $data['parent_id'], // NSM parent
                ]);

                DB::commit();
                return redirect()->back()->with('success', 'RSM created successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e->getMessage());
                return redirect()->back()->with('error', 'RSM creation failed.');
            }
        }

        $divisions=Division::all();
        $districts=District::all();
        return view('admin.extends.marketing_team.rsm.create', compact('nsms','divisions','districts'));
    }

    public function edit($id)
    {
        try {
            $employee = Employee::findOrFail($id);
            $nsms = Employee::where('employee_type', 'nsm')->get();
            $divisions = Division::all();
            $districts = District::all();
            return view('admin.extends.marketing_team.rsm.edit', compact('employee', 'nsms','divisions','districts'));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'RSM not found.');
        }
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'phone' => 'required|string|max:20',
            'division_id' => 'required',
            'district_id' => 'required',
            'address' => 'nullable|string|max:255',
            'parent_id' => 'required|exists:employees,id',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        try {
            DB::beginTransaction();
            $user = User::findOrFail($employee->user_id);

            $data['employee_type'] = 'rsm';

            $user->update([
                'name' => $data['full_name']  ?? '',
                'email' => $data['email'],
                'user_type' => $data['employee_type'],
                'password' => !empty($data['password']) ? Hash::make($data['password']) : $user->password,
            ]);

            $employee->update([
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'division_id' => $data['division_id'],
                'district_id' => $data['district_id'],
                'address' => $data['address'] ?? null,
                'employee_type' => $data['employee_type'],
                'parent_id' => $data['parent_id'], // NSM parent
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'RSM updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('RSM update failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'RSM update failed.');
        }
    }

    public function destroy($id)
    {
        try {
            $employee = Employee::findOrFail($id);
            $user = User::findOrFail($employee->user_id);

            $employee->delete();
            $user->delete();

            return redirect()->back()->with('success', 'RSM deleted successfully.');
        } catch (\Exception $e) {
            Log::error('RSM delete failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'RSM delete failed.');
        }
    }

    public function rsmAccess($id)
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
                'users.status'
            )
            ->where('employees.id', $id)
            ->first();

        if (!$users) {
            abort(404);
        }

        Session::put('userObj', $users);

        return redirect()->route('rsm.dashboard');
    }
}
