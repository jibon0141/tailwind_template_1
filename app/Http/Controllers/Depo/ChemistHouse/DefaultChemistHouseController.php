<?php

namespace App\Http\Controllers\Depo\ChemistHouse;

use App\Http\Controllers\Controller;
use App\Models\ChemistHouse;
use App\Models\ChemistHouseDetail;
use App\Models\ChemistHouseDueAccount;
use App\Models\Depo;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class DefaultChemistHouseController extends Controller
{


    public function index(Request $request)
    {
        $userId = !empty(Session::get('userObj')) ? Session::get('userObj')->id : Auth::user()->id;
        $depoId = Depo::where('user_id', $userId)->first()->id;

        if ($request->ajax()) {
            $chemistHouses = ChemistHouse::with(['depo','mpo','chemistHouseDueAccount'])
                ->where('depo_id', $depoId)
                ->whereNull('mpo_id')
                ->select('chemist_houses.*');


            return DataTables::of($chemistHouses)
                ->addIndexColumn()
                ->addColumn('shop_name', fn($row) => $row->shop_name ?? 'N/A')
                ->addColumn('owner_name', fn($row) => $row->owner_name ?? 'N/A')
                ->addColumn('depo_name', fn($row) => $row->depo->depo_name ?? 'N/A')
                ->addColumn('bank_name', fn($row) => $row->bank_name ?? 'N/A')
                ->addColumn('account_number', fn($row) => $row->account_number ?? 'N/A')
                ->addColumn('contact', fn($row) => $row->contact ?? 'N/A')
                ->addColumn('whatsapp', fn($row) => $row->whatsapp ?? 'N/A')
                ->addColumn('address', fn($row) => $row->address ?? 'N/A')
                ->addColumn('receivable_amount', fn($row) => $row->chemistHouseDueAccount->due_balance ?? 'N/A')
                ->addColumn('status', function ($row) {
                    return $row->status
                        ? '<span class="px-4 py-1 text-xs font-semibold text-green-800 bg-green-200 rounded-full">Active</span>'
                        : '<span class="px-3 py-1 text-xs font-semibold text-red-800 bg-red-200 rounded-full">Inactive</span>';
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('depo.default-chemist-house.edit', $row->id);
                    return '<div class="flex gap-2">
                    <a href="'.$editUrl.'" class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded">
                        <i class="fa fa-edit"></i>
                    </a>
                </div>';
                })
                ->filter(function ($query) use ($request) {
                    if ($search = $request->search['value'] ?? false) {
                        $query->where(function($q) use ($search) {
                            $q->where('shop_name', 'like', "%{$search}%")
                                ->orWhere('owner_name', 'like', "%{$search}%")
                                ->orWhere('bank_name', 'like', "%{$search}%")
                                ->orWhere('account_number', 'like', "%{$search}%")
                                ->orWhere('contact', 'like', "%{$search}%")
                                ->orWhere('whatsapp', 'like', "%{$search}%")
                                ->orWhere('address', 'like', "%{$search}%")
                                ->orWhereHas('depo', function($q2) use ($search) {
                                    $q2->where('depo_name', 'like', "%{$search}%");
                                });
                        });
                    }
                })
                ->rawColumns(['status','action'])
                ->make(true);
        }

        return view('depo.extends.default_chemist_house.index');
    }




    public function create(Request $request)
    {
        $userId = !empty(Session::get('userObj')) ? Session::get('userObj')->id : Auth::user()->id;

        if ($request->isMethod('post')) {

            // Check if this depo already has a direct buyer (mpo_id is null)
            $existingDirectBuyer = ChemistHouse::where('depo_id', $request->depo_id)
                ->whereNull('mpo_id') // no MPO assigned
                ->first();

            if ($existingDirectBuyer) {
                return redirect()->back()
                    ->with('error', 'This Depo already has a Direct Customer. You cannot create another one.');
            }

            $request->validate([
                'shop_name' => 'required|string',
                'email' => [
                    'required',
                    'string',
                    'email',
                    function ($attribute, $value, $fail) {
                        if (
                            DB::table('users')->where('email', $value)->exists() ||
                            DB::table('chemist_houses')->where('email', $value)->exists()
                        ) {
                            $fail('This email is already taken.');
                        }
                    },
                ],
                'owner_name' => 'required|string',
                'depo_id' => 'required|exists:depos,id',
                'mpo_id' => 'nullable',
                'status' => 'required|boolean',
                'bank_name' => 'nullable|string',
                'account_number' => 'nullable|string',
                'contact' => 'nullable|string',
                'whatsapp' => 'nullable|string',
                'address' => 'nullable|string',

                'drug_license_number' => 'nullable|string',
                'drug_license_expire_date' => 'nullable|date',
                'drug_license_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'trade_license' => 'nullable|string',
                'trade_license_expire_date' => 'nullable|date',
                'trade_license_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'tin_number' => 'nullable|string',
//                'password' => 'required|string|min:6|confirmed',
            ]);

            try {
                DB::beginTransaction();


//                $user = User::create([
//                    'name' => $request->shop_name,
//                    'email' => $request->email,
//                    'password' => Hash::make($request->password),
//                    'user_type' => 'chemist_house',
//                    'status' => 1,
//                ]);

                $chemistHouseData = [
//                    'user_id'         => $user->id,
                    'shop_name'       => $request->shop_name,
                    'owner_name'      => $request->owner_name,
                    'email'           => $request->email,
                    'depo_id'         => $request->depo_id,
                    'mpo_id'          => $request->mpo_id,
                    'status'          => $request->status,
                    'bank_name'       => $request->bank_name,
                    'account_number'  => $request->account_number,
                    'contact'         => $request->contact,
                    'whatsapp'         => $request->whatsapp,
                    'address'         => $request->address,
                    'created_at'      => now(),
                ];



                // Store Chemist House Get Id and Link Image

                $chemistHouseId = ChemistHouse::insertGetId($chemistHouseData);

                $drugLicenseImageName  = null;
                $tradeLicenseImageName = null;


                if ($request->hasFile('drug_license_image')) {
                    $drugLicenseImageName= $this->storeImage($request->drug_license_image,'image/drug_license_image');
                }

                if ($request->hasFile('trade_license_image')) {
                    $tradeLicenseImageName= $this->storeImage($request->trade_license_image,'image/trade_license_image');
                }


                $shopDetailsData = [
                    'chemist_house_id'        => $chemistHouseId,
                    'drug_license_number'     => $request->drug_license_number,
                    'drug_license_expire_date'=> $request->drug_license_expire_date,
                    'drug_license_image'      => $drugLicenseImageName,
                    'trade_license'           => $request->trade_license,
                    'trade_license_expire_date'=> $request->trade_license_expire_date,
                    'trade_license_image'     => $tradeLicenseImageName,
                    'tin_number'              => $request->tin_number,
                    'created_at'              => now(),
                ];

                ChemistHouseDetail::insert($shopDetailsData);

//                $chemistHouseInitialDue=[
//                    'chemist_house_id'        => $chemistHouseId,
//                    'due_balance'             =>0,
//                    'created_at'              => now(),
//                ];
//
//                ChemistHouseDueAccount::insert($chemistHouseInitialDue);

                DB::commit();
                Log::info('Chemist House Created Successfully.');
                return redirect()->back()->with('success', 'Direct Buyer Created Successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error($e->getMessage());
                return redirect()->back()->with('error', 'Direct Buyer creation failed.');
            }
        }

        $depo = Depo::where('user_id', $userId)->first();
        return view('depo.extends.default_chemist_house.create', compact('depo'));
    }


    public function edit($id)
    {
        $userId=!empty(Session::get('userObj')) ? Session::get('userObj')->id : Auth::user()->id;

        try {
            $chemistHouse = ChemistHouse::with(['chemistHouseDetail', 'depo'])->where('id', $id)->first();

            if(empty($chemistHouse)) {
                return redirect()->back()->with('error', 'Chemist House not found.');
            }

            return view('depo.extends.default_chemist_house.edit', compact('chemistHouse'));

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while fetching the Chemist House.');
        }
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'shop_name' => "required|string|unique:chemist_houses,shop_name,{$id}",
            'email' => [
                'required',
                'string',
                'email',
                function ($attribute, $value, $fail) use ($id) {
                    $chemistHouse = ChemistHouse::find($id);

                    $existsInUsers = DB::table('users')
                        ->where('email', $value)
                        ->where('id', '<>', $chemistHouse->user_id)
                        ->exists();

                    $existsInChemist = DB::table('chemist_houses')
                        ->where('email', $value)
                        ->where('id', '<>', $id)
                        ->exists();

                    if ($existsInUsers || $existsInChemist) {
                        $fail('This email is already taken.');
                    }
                },
            ],
            'owner_name' => 'required|string',
            'depo_id' => 'required|exists:depos,id',
            'status' => 'required|boolean',
            'bank_name' => 'nullable|string',
            'account_number' => 'nullable|string',
            'contact' => 'nullable|string',
            'whatsapp' => 'nullable|string',
            'address' => 'nullable|string',

            'drug_license_number' => 'nullable|string',
            'drug_license_expire_date' => 'nullable|date',
            'drug_license_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'trade_license' => 'nullable|string',
            'trade_license_expire_date' => 'nullable|date',
            'trade_license_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'tin_number' => 'nullable|string',
//            'password' => 'nullable|string|min:6|confirmed',
        ]);

        try {
            DB::beginTransaction();

            $chemistHouse = ChemistHouse::with('user')->find($id);

//            $userData = [
//                'name' => $request->shop_name,
//                'email' => $request->email,
//            ];
//
//            if ($request->filled('password')) {
//                $userData['password'] = Hash::make($request->password);
//            }
//
//            $chemistHouse->user->update($userData);

            $chemistHouseData = [
                'shop_name'       => $request->shop_name,
                'owner_name'      => $request->owner_name,
                'email'           => $request->email,
                'depo_id'         => $request->depo_id,
                'status'          => $request->status,
                'bank_name'       => $request->bank_name,
                'account_number'  => $request->account_number,
                'contact'         => $request->contact,
                'whatsapp'         => $request->whatsapp,
                'address'         => $request->address,
                'updated_at'      => now(),
            ];

            $chemistHouse = ChemistHouse::find($id);
            $chemistHouse->update($chemistHouseData);

            // ==============================
            // 2. Handle Images
            // ==============================
            $drugLicenseImageName  = $chemistHouse->chemistHouseDetail->drug_license_image ?? null;
            $tradeLicenseImageName = $chemistHouse->chemistHouseDetail->trade_license_image ?? null;

            if ($request->hasFile('drug_license_image')) {
                if ($drugLicenseImageName) {
                    $this->destroyImage($drugLicenseImageName, 'image/drug_license_image');
                }
                $drugLicenseImageName = $this->storeImage($request->drug_license_image, 'image/drug_license_image');
            }

            if ($request->hasFile('trade_license_image')) {
                if ($tradeLicenseImageName) {
                    $this->destroyImage($tradeLicenseImageName, 'image/trade_license_image');
                }
                $tradeLicenseImageName = $this->storeImage($request->trade_license_image, 'image/trade_license_image');
            }


            // Update or Insert ChemistHouseDetail

            $shopDetailsData = [
                'chemist_house_id'        => $chemistHouse->id,
                'drug_license_number'     => $request->drug_license_number,
                'drug_license_expire_date'=> $request->drug_license_expire_date,
                'drug_license_image'      => $drugLicenseImageName,
                'trade_license'           => $request->trade_license,
                'trade_license_expire_date'=> $request->trade_license_expire_date,
                'trade_license_image'     => $tradeLicenseImageName,
                'tin_number'              => $request->tin_number,
                'updated_at'              => now(),
            ];

            if ($chemistHouse->chemistHouseDetail) {
                $chemistHouse->chemistHouseDetail->update($shopDetailsData);
            } else {
                ChemistHouseDetail::insert($shopDetailsData);
            }

            DB::commit();
            Log::info('Chemist House Updated.');
            return redirect()->back()->with('success', 'Direct Buyer House updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Direct Buyer Update Failed.');
        }
    }

}
