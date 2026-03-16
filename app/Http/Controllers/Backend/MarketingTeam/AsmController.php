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

class AsmController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $employees =  Employee::with(['division', 'district'])
            ->where('employee_type', 'asm')->get();

            return datatables()->of($employees)
                ->addIndexColumn()
                ->addColumn('employee_code', fn($row) => $row->employee_code  ?? 'N/A')
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
                    $editUrl = route('asm.edit', $row->id);


                    return '<div class="flex gap-2">
                        <a href="' . $editUrl . '" class="inline-flex items-center px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold rounded" title="Edit"><i class="fa fa-edit"></i></a>
                    </div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.extends.marketing_team.asm.index');
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
                'parent_id' => 'required|exists:employees,id', // Parent must be SM
            ]);

            DB::beginTransaction();
            try {
                $data['employee_type'] = 'asm';


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
                    'parent_id' => $data['parent_id'],
                ]);

                DB::commit();
                return redirect()->back()->with('success', 'ASM created successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e->getMessage());
                return redirect()->back()->with('error', 'ASM creation failed.');
            }
        }

        // Pass only SMs as parent
        $sms = Employee::where('employee_type', 'sm')->get();
        $divisions=Division::all();
        return view('admin.extends.marketing_team.asm.create', compact('sms','divisions'));
    }

    public function edit($id)
    {
        try {
            $employee = Employee::findOrFail($id);
            $sms = Employee::where('employee_type', 'sm')->get();
            $divisions = Division::all();
            $districts = District::all();
            return view('admin.extends.marketing_team.asm.edit', compact('employee', 'sms','divisions','districts'));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'ASM not found.');
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
            'password' => 'nullable|string|min:6|confirmed',
            'parent_id' => 'required|exists:employees,id', // parent must be SM
        ]);

        try {

            DB::beginTransaction();

            $user = User::findOrFail($employee->user_id);



            $data['employee_type'] = 'asm';

            $user->update([
                'name' => $data['full_name']  ?? '',
                'email' => $data['email'],
                'user_type' => $data['employee_type'],
                'password' => !empty($data['password']) ? Hash::make($data['password']) : $user->password,
            ]);

            $employee->update([
                'full_name' => $data['full_name'],
                'last_name' => $data['last_name'] ?? null,
                'email' => $data['email'],
                'phone' => $data['phone'],
                'division_id' => $data['division_id'],
                'district_id' => $data['district_id'],
                'address' => $data['address'] ?? null,
                'employee_type' => $data['employee_type'],
                'parent_id' => $data['parent_id'],
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'ASM updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ASM update failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'ASM update failed.');
        }
    }

    public function destroy($id)
    {
        try {
            $employee = Employee::where('id',$id)->first();
            $user = User::findOrFail($employee->user_id);

            $employee->delete();
            $user->delete();

            return redirect()->back()->with('success', 'ASM deleted successfully.');
        } catch (\Exception $e) {
            Log::error('ASM delete failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'ASM delete failed.');
        }
    }
}
