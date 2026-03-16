<?php

namespace App\Http\Controllers\Backend\Employee;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $employees = Employee::get();

            return datatables()->of($employees)
                ->addIndexColumn()
                ->addColumn('full_name', function ($row) {
                    return $row->full_name ?? 'N/A';
                })
                ->addColumn('email', function ($row) {
                    return $row->email ?? 'N/A';
                })
                ->addColumn('phone', function ($row) {
                    return $row->phone ?? 'N/A';
                })
                ->addColumn('division', function ($row) {
                    return $row->division ?? 'N/A';
                })
                ->addColumn('district', function ($row) {
                    return $row->district ?? 'N/A';
                })
                ->addColumn('address', function ($row) {
                    return $row->address ?? 'N/A';
                })
                ->addColumn('employee_type', function ($row) {
                    return strtoupper($row->employee_type ?? 'N/A');
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('employee.edit', $row->id);
                    $deleteUrl = route('employee.delete', $row->id);

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
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.extends.employee.index');
    }

    public function create(Request $request)
    {
        if ($request->isMethod('POST')) {

            // Validate all fields
            $data = $request->validate([
                'full_name'    => 'required|string|max:255',
                'email'         => 'required|email|unique:employees,email',
                'phone'         => 'required|string|max:20',
                'division'      => 'required|string|max:100',
                'district'      => 'required|string|max:100',
                'address'       => 'required|string|max:100',
                'password'      => 'required|string|min:6',
                'employee_type' => 'required|string|in:director,nsm,rsm,sm,asm,mpo',
                'parent_id'     => 'nullable|exists:employees,id',
            ]);
            try{
                DB::beginTransaction();

                // Create User first
                $user = User::create([
                    'name'     => $data['full_name']  ?? '',
                    'email'    => $data['email'],
                    'user_type'=> $data['employee_type'],
                    'status'   => 1,
                    'password' => Hash::make($data['password']),
                ]);

                if(empty($user)){
                    Log::info('User Create Failed');
                    return redirect()->back()->with('error', 'User Create Failed.');
                }

                // Director has no parent
                if ($data['employee_type'] === 'director') {
                    $data['parent_id'] = null;
                }

                $employee = Employee::create([
                    'user_id'       => $user->id,
                    'full_name'    => $data['full_name'],
                    'email'         => $data['email'],
                    'phone'         => $data['phone'],
                    'division'      => $data['division'],
                    'district'      => $data['district'],
                    'address'       => $data['address'] ?? null,
                    'password'      => Hash::make($data['password']),
                    'employee_type' => $data['employee_type'],
                    'parent_id'     => $data['parent_id'],
                ]);
                DB::commit();
                Log::info('Employee Created Successfully.');
                return redirect()->back()->with('success', 'Employee created successfully.');
            }
            catch(\Exception $e){
                DB::rollBack();
                Log::error($e->getMessage());
                return redirect()->back()->with('error', 'Employee Create Failed.');
            }

        }

        $employees = Employee::all();
        return view('admin.extends.employee.create', compact('employees'));
    }


    public function show($id)
    {
        try {
            $employee = Employee::with('user')->where('id', $id)->first();
            return view('admin.extends.employee.show', compact('employee'));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Employee not found.');
        }
    }

    public function edit($id)
    {
        try {
            $employee = Employee::where('id', $id)->first();
            $employees = Employee::where('id', '!=', $id)->get(); // for parent dropdown
            return view('admin.extends.employee.edit', compact('employee', 'employees'));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Employee not found.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $employee = Employee::findOrFail($id);

            $user = User::findOrFail($employee->user_id);

            $data = $request->validate([
                'full_name' => 'required|string|max:255',
                'email'      => 'required|email|unique:employees,email,' . $employee->id,
                'phone'      => 'required|string|max:20',
                'division'   => 'required|string|max:100',
                'district'   => 'required|string|max:100',
                'address'    => 'required|string|max:255',
                'password'   => 'nullable|string|min:6',
                'parent_id'  => 'nullable|exists:employees,id',
            ]);

            $request->validate([
                'email' => 'unique:users,email,' . $user->id,
            ]);

            if ($employee->employee_type === 'director') {
                $data['parent_id'] = null;
            }

            $user->update([
                'name'      => $data['full_name']  ?? '',
                'email'     => $data['email'],
                'user_type' => $employee->employee_type,
                'password'  => !empty($data['password'])
                    ? Hash::make($data['password'])
                    : $user->password,
            ]);

            $employee->update([
                'full_name' => $data['full_name'],
                'email'      => $data['email'],
                'phone'      => $data['phone'],
                'division'   => $data['division'],
                'district'   => $data['district'],
                'address'    => $data['address'] ?? null,
                'password'   => !empty($data['password'])
                    ? Hash::make($data['password'])
                    : $employee->password,
                'parent_id'  => $data['parent_id'],
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Employee & User updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Employee update failed: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Employee update failed.');
        }
    }


    public function destroy(Request $request,$id){

    }

    public function getParentEmployee(Request $request)
    {
        $type = $request->type;

        $map = [
            'nsm' => 'director',
            'rsm' => 'nsm',
            'sm'  => 'rsm',
            'asm' => 'sm',
            'mpo' => 'asm',
        ];

        if (!isset($map[$type])) {
            return response()->json([]);
        }

        $parents = Employee::where('employee_type', $map[$type])
            ->select('id', 'full_name','employee_code','employee_type')
            ->get();

        return response()->json($parents);
    }



}
