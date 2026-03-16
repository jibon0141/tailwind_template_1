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

class SmController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $employees = Employee::with(['division', 'district'])
            ->where('employee_type', 'sm')->get();

            return datatables()->of($employees)
                ->addIndexColumn()
                ->addColumn('employee_code', fn($row) => $row->employee_code ?? 'N/A')
                ->addColumn('full_name', fn($row) => $row->full_name  ?? 'N/A')
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
                    $editUrl = route('sm.edit', $row->id);
//                    $deleteUrl = route('sm.destroy', $row->id);
                    $accessUrl = route('sm.access', $row->id);

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


        return view('admin.extends.marketing_team.sm.index');

    }

    public function create(Request $request)
    {
        if ($request->isMethod('POST')) {
            $data = $request->validate([
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|unique:employees,email',
                'phone' => 'required|string|max:20',
                'division_id' => 'required',
                'district_id' => 'required',
                'address' => 'nullable|string|max:255',
                'password' => 'required|string|min:6|confirmed',
                'parent_id' => 'required|exists:employees,id',
            ]);

            DB::beginTransaction();
            try {

                $data['employee_type'] = 'sm';

                // Create User
                $user = User::create([
                    'name' => $data['full_name']  ?? '',
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
                    'parent_id' => $data['parent_id'], // must be RSM
                ]);

                DB::commit();
                return redirect()->back()->with('success', 'SM created successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e->getMessage());
                return redirect()->back()->with('error', 'SM creation failed.');
            }
        }

        // Pass only RSMs as parent
        $rsms = Employee::where('employee_type', 'rsm')->get();
        $divisions=Division::all();
        return view('admin.extends.marketing_team.sm.create', compact('rsms','divisions'));
    }

    public function edit($id)
    {
        try {
            $employee = Employee::findOrFail($id);
            $rsms = Employee::where('employee_type', 'rsm')->get(); // parent RSMs\
            $divisions = Division::all();
            $districts = District::all();
            return view('admin.extends.marketing_team.sm.edit', compact('employee', 'rsms','divisions','districts'));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'SM not found.');
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $employee = Employee::findOrFail($id);
            $user = User::findOrFail($employee->user_id);

            $data = $request->validate([
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|unique:employees,email,' . $employee->id,
                'phone' => 'required|string|max:20',
                'division_id' => 'required',
                'district_id' => 'required',
                'address' => 'nullable|string|max:255',
                'password' => 'nullable|string|min:6|confirmed',
                'parent_id' => 'required|exists:employees,id', // parent must be RSM
            ]);

            $data['employee_type'] = 'sm';

            // Update User
            $user->update([
                'name' => $data['full_name'] . ' ' . ($data['last_name'] ?? ''),
                'email' => $data['email'],
                'user_type' => $data['employee_type'],
                'password' => !empty($data['password']) ? Hash::make($data['password']) : $user->password,
            ]);

            // Update Employee
            $employee->update([
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'division_id' => $data['division_id'],
                'district_id' => $data['district_id'],
                'address' => $data['address'] ?? null,
                'employee_type' => $data['employee_type'],
                'parent_id' => $data['parent_id'],
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'SM updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SM update failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'SM update failed.');
        }
    }

    public function destroy($id)
    {
        try {
            $employee = Employee::findOrFail($id);
            $user = User::findOrFail($employee->user_id);

            $employee->delete();
            $user->delete();

            return redirect()->back()->with('success', 'SM deleted successfully.');
        } catch (\Exception $e) {
            Log::error('SM delete failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'SM delete failed.');
        }
    }

    public function smAccess($id)
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

        return redirect()->route('sm.dashboard');
    }
}
