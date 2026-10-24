<?php

namespace App\Http\Controllers\Backend\User;

use App\Http\Controllers\Controller;
use App\Models\Sell;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\UserNominee;
use App\Models\UserWallet;
use App\Traits\ManageImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use App\Services\CustomerService;

class UserController extends Controller
{
    use ManageImage;

    public function show($id)
    {
        $user = User::with(['detail', 'nominees'])->where('id', $id)->firstOrFail();
        return view('admin.extends.user.show', compact('user'));
    }
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = User::with('designation')->where('user_type','!=','admin')->get();

            return Datatables::of($users)
                ->addIndexColumn()
                ->addColumn('user_code', function ($row) {
                    return $row->user_id ?? '-';
                })
                ->addColumn('ref_id', function ($row) {
                    return $row->ref_id ?? '-';
                })
                ->addColumn('name', function ($row) {
                    return $row->name ?? '-';
                })
                ->addColumn('designation', function ($row) {
                    if (!$row->designation) return '-';
                    $colors = [
                        1 => 'bg-blue-100 text-blue-800',
                        2 => 'bg-green-100 text-green-800',
                        3 => 'bg-purple-100 text-purple-800',
                        4 => 'bg-orange-100 text-orange-800',
                        5 => 'bg-indigo-100 text-indigo-800',
                        6 => 'bg-rose-100 text-rose-800',
                    ];
                    $class = $colors[$row->designation_id] ?? 'bg-gray-100 text-gray-800';
                    return '<span class="px-3 py-1 text-xs font-semibold rounded-full ' . $class . '">' . e($row->designation->name) . '</span>';
                })
                ->addColumn('phone', function ($row) {
                    return $row->phone ?? '-';
                })
                ->addColumn('status', function ($row) {
                    return $row->status
                        ? '<span class="px-3 py-1 text-xs font-semibold text-green-800 bg-green-200 rounded-full">Active</span>'
                        : '<span class="px-3 py-1 text-xs font-semibold text-red-800 bg-red-200 rounded-full">Inactive</span>';
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('admin.user.edit', $row->id);
                    $showUrl = route('admin.user.show', $row->id);
                    $accessUrl = route('admin.user.access', $row->id);

                    $action = '
    <div class="flex gap-1.5">';
                    if (can_access('admin.user.show')) {
                        $action .= '
        <a href="' . $showUrl . '"
           class="w-8 h-8 flex items-center justify-center rounded-lg bg-gradient-to-br from-emerald-400 to-teal-500 hover:from-emerald-500 hover:to-teal-600 text-white shadow-sm shadow-emerald-200 hover:shadow-md hover:shadow-emerald-300 hover:-translate-y-0.5 transition-all duration-200"
           title="View">
            <i class="fa fa-eye text-xs"></i>
        </a>';
                    }
                    if (can_access('admin.user.edit')) {
                        $action .= '
        <a href="' . $editUrl . '"
           class="w-8 h-8 flex items-center justify-center rounded-lg bg-gradient-to-br from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white shadow-sm shadow-sky-200 hover:shadow-md hover:shadow-sky-300 hover:-translate-y-0.5 transition-all duration-200"
           title="Edit">
            <i class="fa fa-edit text-xs"></i>
        </a>';
                    }
                    if (can_access('admin.user.delete')) {
                        $action .= '
        <button onclick="deleteItem(' . $row->id . ')"
                class="w-8 h-8 flex items-center justify-center rounded-lg bg-gradient-to-br from-rose-400 to-red-500 hover:from-rose-500 hover:to-red-600 text-white shadow-sm shadow-rose-200 hover:shadow-md hover:shadow-rose-300 hover:-translate-y-0.5 transition-all duration-200"
                title="Delete">
            <i class="fa fa-trash text-xs"></i>
        </button>';
                    }
                    if (can_access('admin.user.access')) {
                        $action .= '
          <a href="' . $accessUrl . '"
           class="w-8 h-8 flex items-center justify-center rounded-lg bg-gradient-to-br from-amber-400 to-orange-500 hover:from-amber-500 hover:to-orange-600 text-white shadow-sm shadow-amber-200 hover:shadow-md hover:shadow-amber-300 hover:-translate-y-0.5 transition-all duration-200"
           title="Login as User"
           onclick="return confirm(\'Login as this user?\')">
            <i class="fa fa-sign-in-alt text-xs"></i>
        </a>';
                    }
                    $action .= '
    </div>
    ';

                    return $action;
                })
                ->rawColumns(['status', 'designation', 'action'])
                ->make(true);
        }
        return view('admin.extends.user.index');
    }

    public function create(Request $request, CustomerService $customerService)
    {
        if ($request->isMethod('POST')) {
            $request->validate([
                'ref_id'    => 'required|string|max:50',
                'name'      => 'required|string|max:100',
                'phone'     => 'required|unique:users,phone|digits_between:1,11',
                'email'     => 'nullable|email|unique:users,email',
                'password'  => 'required|min:6|confirmed',
            ]);

            try {

                DB::beginTransaction();

                if($request->ref_id){
                    $check = User::where('user_id',$request->ref_id)->first();
                    if(empty($check)){
                        Log::info('Invalid Reference Id!');
                        return redirect()->back()->with('success','Invalid Reference Id!');
                    }
                }

                $user = User::create([
                    'ref_id'          => $request->ref_id,
                    'name'            => $request->name,
                    'phone'           => $request->phone,
                    'email'           => $request->email,
                    'user_type'       => "user",
                    'status'          => 0,
                    'designation_id'  => 1,
                    'password'        => Hash::make($request->password),
                    'created_at'      => now(),
                ]);

                // Create User Wallet

                UserWallet::create([
                    'user_id'            => $user->id,
                    'commission_balance' => 0,
                    'return_balance'     => 0,
                    'total_balance'      => 0,
                ]);

                // Check Designation and Update if it is not admin
                if($user->ref_id != 0){
                    $customerService->updateDesignation($user->id);
                }

                DB::commit();
                Log::info('User Created Successfully!');
                return redirect()->back()->with('success', 'User Created Successfully!');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e->getMessage());
                return redirect()->back()->with('error', 'User Create Failed!');
            }
        }
        return view('admin.extends.user.create');
    }

    public function edit($id)
    {
        $user = User::with(['detail', 'nominees'])->where('id', $id)->first();

        if (empty($user)) {
            Log::info('User Not Found!', ['user_id' => $id]);
            return redirect()->back()->with('error', 'User Not Found!');
        }

        return view('admin.extends.user.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::where('id', $id)->first();

        if (empty($user)) {
            Log::info('User Not Found', ['user_id' => $id]);
            return redirect()->back()->with('error', 'User Not Found.');
        }

        $request->validate([
            'name'            => 'required|string|max:100',
            'phone'           => 'required|unique:users,phone,' . $id . '|digits_between:1,11',
            'email'           => 'nullable|email|unique:users,email,' . $id,
            'status'          => 'required',
            'password'        => 'nullable|min:6|confirmed',
            'father_name'     => 'nullable|string|max:255',
            'mother_name'     => 'nullable|string|max:255',
            'age'             => 'nullable|integer|min:1|max:150',
            'gender'          => 'nullable|string|in:male,female,other',
            'address'         => 'nullable|string',
            'nid'             => 'nullable|string|max:50',
            'image'           => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'nid_front_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'nid_back_image'  => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {

            if($request->ref_id){
                $check = User::where('user_id',$request->ref_id)->first();
                if(empty($check)){
                    Log::info('Invalid Reference Id!');
                    return redirect()->back()->with('success','Invalid Reference Id!');
                }
            }

            $data = [
                'name'      => $request->name,
                'phone'     => $request->phone,
                'email'     => $request->email,
                'status'    => $request->status,
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            // Update user details
            $detailData = $request->only(['father_name', 'mother_name', 'age', 'gender', 'address']);
            if (!$user->detail || !$user->detail->nid) {
                $detailData['nid'] = $request->input('nid');
            }

            $imagePath = 'image/user';

            if ($request->hasFile('image')) {
                if ($user->detail && $user->detail->image) $this->destroyImage($user->detail->image, $imagePath);
                $detailData['image'] = $this->storeImage($request->file('image'), $imagePath);
            }

            if ($request->hasFile('nid_front_image')) {
                if ($user->detail && $user->detail->nid_front_image) $this->destroyImage($user->detail->nid_front_image, $imagePath);
                $detailData['nid_front_image'] = $this->storeImage($request->file('nid_front_image'), $imagePath);
            }

            if ($request->hasFile('nid_back_image')) {
                if ($user->detail && $user->detail->nid_back_image) $this->destroyImage($user->detail->nid_back_image, $imagePath);
                $detailData['nid_back_image'] = $this->storeImage($request->file('nid_back_image'), $imagePath);
            }

            $user->detail()->updateOrCreate(
                ['user_id' => $user->id],
                $detailData
            );

            Log::info('User Updated Successfully', ['user_id' => $user->id]);

            return redirect()->back()->with('success', 'User Updated Successfully.');
        } catch (\Exception $e) {
            Log::error('User Update Failed', [
                'user_id' => $id,
                'error'   => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'User Update Failed!');
        }
    }

    public function getReferrer(Request $request)
    {
        $refId = $request->get('ref_id', '');

        if (!$refId) {
            return response()->json(['success' => false, 'message' => 'No reference ID provided.']);
        }

        $referrer = User::where('user_id', $refId)->first();

        if (!$referrer) {
            return response()->json(['success' => false, 'message' => 'Invalid reference ID.']);
        }

        return response()->json([
            'success' => true,
            'name'    => $referrer->name,
            'phone'   => $referrer->phone,
            'user_id' => $referrer->user_id,
        ]);
    }

    public function destroy($id)
    {
        $user = User::where('id', $id)->first();

        if (empty($user)) {
            Log::info('User Not Found!', [
                'user_id' => $id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'User not found!'
            ], 404);
        }

        $ref_user = User::where('ref_id', $user->user_id)->exists();
        $sell = Sell::where('user_id', $id)->exists();

        if ($sell || $ref_user) {
            Log::info('Cannot delete user! User has associated sells or child users.', ['user_id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete user! User has associated sells or child users.'
            ], 409);
        }

        try {
            $user->delete();

            Log::info('User Deleted Successfully', [
                'user_id' => $id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully!'
            ], 200);
        } catch (\Exception $e) {
            Log::error('User Delete Failed', [
                'user_id' => $id,
                'error'   => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'User delete failed!'
            ], 500);
        }
    }

    public function storeNominee(Request $request, $id)
    {
        $user = User::where('id', $id)->first();

        if(empty($user)){
            Log::info('User Not Found!', ['user_id' => $id]);
            return redirect()->back()->with('error', 'User Not Found!');
        }

        $request->validate([
            'name'            => 'required|string|max:255',
            'phone'           => 'required|string|max:20',
            'email'           => 'required|email|max:255',
            'relation'        => 'required|string|max:100',
            'address'         => 'required|string',
            'image'           => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'nid_front_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'nid_back_image'  => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only(['name', 'phone', 'email', 'relation', 'address']);
        $imagePath = 'image/nominee';

        if ($request->hasFile('image')) {
            $data['image'] = $this->storeImage($request->file('image'), $imagePath);
        }
        if ($request->hasFile('nid_front_image')) {
            $data['nid_front_image'] = $this->storeImage($request->file('nid_front_image'), $imagePath);
        }
        if ($request->hasFile('nid_back_image')) {
            $data['nid_back_image'] = $this->storeImage($request->file('nid_back_image'), $imagePath);
        }

        $user->nominees()->create($data);

        return redirect()->route('admin.user.show', $id)->with('success', 'Nominee added successfully.');
    }

    public function updateNominee(Request $request, $id, UserNominee $nominee)
    {
        $user = User::where('id', $id)->firstOrFail();

        if ($nominee->user_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'name'             => 'required|string|max:255',
            'phone'            => 'required|string|max:20',
            'email'            => 'required|email|max:255',
            'relation'         => 'required|string|max:100',
            'address'          => 'required|string',
            'image'            => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'nid_front_image'  => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'nid_back_image'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only(['name', 'phone', 'email', 'relation', 'address']);
        $imagePath = 'image/nominee';

        if ($request->hasFile('image')) {
            if ($nominee->image) $this->destroyImage($nominee->image, $imagePath);
            $data['image'] = $this->storeImage($request->file('image'), $imagePath);
        }
        if ($request->hasFile('nid_front_image')) {
            if ($nominee->nid_front_image) $this->destroyImage($nominee->nid_front_image, $imagePath);
            $data['nid_front_image'] = $this->storeImage($request->file('nid_front_image'), $imagePath);
        }
        if ($request->hasFile('nid_back_image')) {
            if ($nominee->nid_back_image) $this->destroyImage($nominee->nid_back_image, $imagePath);
            $data['nid_back_image'] = $this->storeImage($request->file('nid_back_image'), $imagePath);
        }

        $nominee->update($data);

        return redirect()->route('admin.user.show', $id)->with('success', 'Nominee updated successfully.');
    }

    public function destroyNominee($id, UserNominee $nominee)
    {
        $user = User::where('id', $id)->firstOrFail();

        if ($nominee->user_id !== $user->id) {
            abort(403);
        }

        $imagePath = 'image/nominee';
        if ($nominee->image) $this->destroyImage($nominee->image, $imagePath);
        if ($nominee->nid_front_image) $this->destroyImage($nominee->nid_front_image, $imagePath);
        if ($nominee->nid_back_image) $this->destroyImage($nominee->nid_back_image, $imagePath);

        $nominee->delete();

        return redirect()->route('admin.user.show', $id)->with('success', 'Nominee deleted successfully.');
    }

    public function userAccess($id)
    {
        $user = User::find($id);

        if(empty($user)){
            Log::info('User not found!');
            abort(404);
        }

        Session::put('admin_impersonate', Auth::id(), now()->addHour());

        Auth::loginUsingId($user->id);

        return redirect()->route('user.dashboard')->with('success', 'Logged in as ' . $user->name);
    }

    public function switchBack()
    {
        $adminId = Session::pull('admin_impersonate');

        if ($adminId) {
            Auth::loginUsingId($adminId);
            return redirect()->route('admin.dashboard')->with('success', 'Switched back to admin.');
        }

        return redirect('/');
    }

}
