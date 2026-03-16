<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class PurchaseAdminController extends Controller
{
    public function index(Request $request){
        if ($request->ajax()) {
            $admins = User::where('role', 'purchase_admin')->latest()->get();

            return datatables()->of($admins)
                ->addIndexColumn()
                ->addColumn('name', fn ($row) => $row->name ?? 'N/A')
                ->addColumn('email', fn ($row) => $row->email ?? 'N/A')
                ->addColumn('status', function ($row) {
                    return $row->status == 1
                        ? '<span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Active</span>'
                        : '<span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">Inactive</span>';
                })
                ->addColumn('action', function ($row) {
                    return '
                    <div class="flex gap-1">
                        <a href="' . route('purchase.admin.show', $row->id) . '"
                           class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded"
                           title="View">
                            <i class="fa fa-eye"></i>
                        </a>

                        <a href="' . route('purchase.admin.edit', $row->id) . '"
                           class="px-2 py-1 bg-green-500 hover:bg-green-600 text-white text-xs rounded"
                           title="Edit">
                            <i class="fa fa-edit"></i>
                        </a>
                    </div>
                ';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('admin.extends.purchase_admin.index');
    }

    public function create(Request $request){

        if($request->isMethod('POST')){


            $validated = $request->validate([
                'name'                  => 'required|string|max:255',
                'email'                 => 'required|email|unique:users,email',
                'password'              => 'required|string|min:6|confirmed',
            ]);

            try{
                $data=[
                    'name'      => $validated['name'],
                    'email'     => $validated['email'],
                    'password'  => Hash::make($validated['password']),
                    'user_type' => 'admin',
                    'role'      => 'purchase_admin',
                    'status'    => '1',
                ];

                User::create($data);
                Log::info("Admin create successfully for Purchase.");
                return redirect()->back()->with('success','Admin create successfully for Purchase.');
            }
            catch(\Exception $e){
                Log::info("User create failed");
                return redirect()->back()->with('error',$e->getMessage());
            }

        }
        return view('admin.extends.purchase_admin.create');
    }

    public function show($id){
        try{
            $admin = User::where('id', $id)->where('role', 'purchase_admin')->first();

            if(empty($admin)){
                Log::info('Purchase Admin Not Found.');
                return redirect()->back()->with('error','Purchase Admin Not Found.');
            }

            Log::info('Purchase Admin Show Successfully.');
            return view('admin.extends.purchase_admin.show', compact('admin'));
        }catch (\Exception $e){
            Log::error($e->getMessage());
            return redirect()->back()->with('error','Purchase Admin Not Found.');
        }
    }

    public function edit($id){
        try{
            $admin = User::where('id', $id)->where('role', 'purchase_admin')->first();

            if(empty($admin)){
                Log::info('Purchase Admin Not Found.');
                return redirect()->back()->with('error','Purchase Admin Not Found.');
            }

            Log::info('Purchase Admin Edit Successfully.');
            return view('admin.extends.purchase_admin.edit', compact('admin'));
        }catch (\Exception $e){
            Log::error($e->getMessage());
            return redirect()->back()->with('error','Purchase Admin Not Found.');
        }
    }

    public function update(Request $request, $id){
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6|confirmed',
            'status'   => 'required|in:0,1',
        ]);

        try{
            $admin = User::find($id);

            if(empty($admin) || $admin->role !== 'purchase_admin'){
                Log::info('Purchase Admin Not Found.');
                return redirect()->back()->with('error','Purchase Admin Not Found.');
            }

            $data = [
                'name'   => $validated['name'],
                'email'  => $validated['email'],
                'status' => $validated['status'],
            ];

            if(!empty($validated['password'])){
                $data['password'] = Hash::make($validated['password']);
            }

            $admin->update($data);
            Log::info('Purchase Admin Successfully Updated.');
            return redirect()->back()->with('success','Purchase Admin Successfully Updated.');
        }catch(\Exception $e){
            Log::error($e->getMessage());
            return redirect()->back()->with('error','Purchase Admin Update Failed.');
        }
    }

    public function destroy($id){
        try{
            $admin = User::where('id', $id)->where('role', 'purchase_admin')->first();

            if(empty($admin)){
                return response()->json(['success' => false, 'message' => 'Purchase Admin Not Found.']);
            }

            $admin->delete();
            Log::info('Purchase Admin Deleted Successfully.');
            return response()->json(['success' => true, 'message' => 'Purchase Admin Deleted Successfully.']);
        }catch(\Exception $e){
            Log::error($e->getMessage());
            return response()->json(['success' => false, 'message' => 'Purchase Admin Delete Failed.']);
        }
    }

}
