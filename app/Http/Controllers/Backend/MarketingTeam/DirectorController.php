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
use Illuminate\Validation\Rule;
use PhpParser\Node\Stmt\Return_;

class DirectorController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $employees = Employee::with(['division', 'district'])
                ->where('employee_type', 'director')
                ->get();


            return datatables()->of($employees)
                ->addIndexColumn()
                ->addColumn('employee_code', fn($row) => $row->employee_code ?? 'N/A')
                ->addColumn('full_name', fn($row) => $row->full_name ?? '')
                ->editColumn('division', function ($row) {
                    return $row->division->name ?? 'N/A';
                }) ->editColumn('district', function ($row) {
                    return $row->district->name ?? 'N/A';
                })
                ->addColumn('email', fn($row) => $row->email ?? 'N/A')
                ->addColumn('phone', fn($row) => $row->phone ?? 'N/A')
                ->addColumn('address', fn($row) => $row->address ?? 'N/A')
                ->addColumn('employee_type', fn($row) => strtoupper($row->employee_type ?? 'N/A'))
                ->addColumn('action', function ($row) {
                    $editUrl = route('director.edit', $row->id);
                    $accessUrl = route('director.access', $row->id);

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


        return view('admin.extends.marketing_team.director.index');
    }


    public function create(Request $request)
    {
        if ($request->isMethod('POST')) {
            $data = $request->validate([
                'full_name' => 'required|string|max:255',
                'email' => [
                    'required',
                    'email',
                    Rule::unique('employees', 'email'),
                    Rule::unique('users', 'email'),
                ],
                'phone' => 'required|string|max:20',
                'division_id' => 'required',
                'district_id' => 'required',
                'address' => 'nullable|string|max:255',
                'password' => 'required|string|min:6|confirmed',
            ]);

            try {

                DB::beginTransaction();
                $data['employee_type'] = 'director';
                $data['parent_id'] = null;


                $user = User::create([
                    'name' => $data['full_name']  ?? '',
                    'email' => $data['email'],
                    'user_type' => $data['employee_type'],
                    'status' => 1,
                    'password' => Hash::make($data['password']),
                ]);

                Employee::create([
                    'user_id' => $user->id,
                    'full_name' => $data['full_name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'division_id' => $data['division_id'],
                    'district_id' => $data['district_id'],
                    'address' => $data['address'] ?? null,
                    'employee_type' => $data['employee_type'],
                    'parent_id' => null,
                ]);

                DB::commit();
                Log::info('Director created successfully.');
                return redirect()->back()->with('success', 'Director created successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e->getMessage());
                return redirect()->back()->with('error', 'Director creation failed.');
            }
        }

        $divisions=Division::all();
        $districts=District::all();
        return view('admin.extends.marketing_team.director.create',compact('divisions','districts'));
    }

    public function edit($id)
    {
        try {
            $employee = Employee::where('id',$id)->first();
            $divisions = Division::all();
            $districts = District::all();
            return view('admin.extends.marketing_team.director.edit', compact('employee','divisions','districts'));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Director not found.');
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
            ]);

            // Director is automatic, no parent
            $data['employee_type'] = 'director';
            $data['parent_id'] = null;

            // Update user
            $user->update([
                'name' => $data['full_name'] ?? '',
                'email' => $data['email'],
                'user_type' => $data['employee_type'],
                'password' => !empty($data['password']) ? Hash::make($data['password']) : $user->password,
            ]);

            // Update employee
            $employee->update([
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'division_id' => $data['division_id'],
                'district_id' => $data['district_id'],
                'address' => $data['address'] ?? null,
                'employee_type' => $data['employee_type'],
                'parent_id' => null,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Director updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Director update failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Director update failed.');
        }
    }

    public function destroy($id)
    {
        try {
            $employee = Employee::findOrFail($id);
            $user = User::findOrFail($employee->user_id);

            $employee->delete();
            $user->delete();

            return redirect()->back()->with('success', 'Director deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Director delete failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Director delete failed.');
        }
    }


    public function getDistrict($divisionId)
    {
        try {
            $districts = District::where('division_id', $divisionId)->get();

            if ($districts->isEmpty()) {
                throw new \Exception('District not found.');
            }

            Log::info("District found for division_id: {$divisionId}");

            return response()->json([
                'status' => true,
                'data' => $districts
            ], 200);

        } catch (\Exception $e) {

            Log::error($e->getMessage());

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function directorAccess($id)
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

        return redirect()->route('director.dashboard');
    }

}
