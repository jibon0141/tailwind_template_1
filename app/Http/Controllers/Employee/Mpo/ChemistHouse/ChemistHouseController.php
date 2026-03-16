<?php

namespace App\Http\Controllers\Employee\Mpo\ChemistHouse;

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

class ChemistHouseController extends Controller
{
    public function index(Request $request)
    {
        $userId=!empty(Session::get('userObj')) ? Session::get('userObj')->id : Auth::user()->id;
        $mpo = Employee::where('user_id', $userId)->first();

        if ($request->ajax()) {
            try {
                $chemistHouses = ChemistHouse::with(['depo','chemistHouseDueAccount'])
                    ->where('depo_id', $mpo->depo_id)
                ->where('mpo_id',$userId);

                // Global search
                if ($request->filled('search')) {
                    $search = $request->search['value']; // DataTables sends 'search.value'
                    $chemistHouses->where(function($q) use ($search) {
                        $q->where('shop_name', 'like', "%$search%")
                            ->orWhere('owner_name', 'like', "%$search%")
                            ->orWhere('bank_name', 'like', "%$search%")
                            ->orWhere('account_number', 'like', "%$search%")
                            ->orWhere('contact', 'like', "%$search%")
                            ->orWhere('whatsapp', 'like', "%$search%")
                            ->orWhere('address', 'like', "%$search%")
                            ->orWhereHas('depo', function($q2) use ($search) {
                                $q2->where('depo_name', 'like', "%$search%");
                            });
                    });
                }

                return DataTables::of($chemistHouses->latest()->get())
                    ->addIndexColumn()
                    ->addColumn('shop_name', fn ($row) => $row->shop_name ?? 'N/A')
                    ->addColumn('owner_name', fn ($row) => $row->owner_name ?? 'N/A')
                    ->addColumn('depo_name', fn ($row) => $row->depo->depo_name ?? 'N/A')
                    ->addColumn('bank_name', fn ($row) => $row->bank_name ?? 'N/A')
                    ->addColumn('account_number', fn ($row) => $row->account_number ?? 'N/A')
                    ->addColumn('contact', fn ($row) => $row->contact ?? 'N/A')
                    ->addColumn('whatsapp', fn ($row) => $row->whatsapp ?? 'N/A')
                    ->addColumn('address', fn ($row) => $row->address ?? 'N/A')
                    ->addColumn('receivable_amount', fn ($row) => $row->chemistHouseDueAccount->due_balance ?? 'N/A')
                    ->addColumn('status', function ($row) {
                        return $row->status
                            ? '<span class="px-4 py-1 text-xs font-semibold text-green-800 bg-green-200 rounded-full">Active</span>'
                            : '<span class="px-3 py-1 text-xs font-semibold text-red-800 bg-red-200 rounded-full">Inactive</span>';
                    })
                    ->addColumn('action', function ($row) {
                        $editUrl   = route('mpo.chemist-house.edit', $row->id);


                        return '
                        <div class="flex gap-2">
                            <a href="'.$editUrl.'" class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded">
                                <i class="fa fa-edit"></i>
                            </a>

                        </div>
                    ';
                    })
                    ->rawColumns(['status','action'])
                    ->make(true);

            } catch (\Exception $exception) {
                Log::error($exception->getMessage());
            }
        }

        return view('employee.mpo.extends.chemist_house.index');
    }


    public function create(Request $request)
    {
        $userId = Auth::user()->id;
        $mpo = Employee::where('user_id', $userId)->first();
        if ($request->isMethod('post')) {


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
//                'shop_type' => 'required|in:retail,wholesale,hospital_pharmacy,clinic_pharmacy',
                'depo_id' => 'required|exists:depos,id',
                'mpo_id' => 'required|exists:employees,user_id',
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
                'password' => 'required|string|min:6|confirmed',

            ]);

            try {

                DB::beginTransaction();

                $user = User::create([
                    'name' => $request->shop_name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'user_type' => 'chemist_house',
                    'status' => 1,
                ]);

                // Store Chemist House
                $chemistHouseData = [
                    'user_id'         => $user->id,
                    'shop_name'       => $request->shop_name,
                    'owner_name'      => $request->owner_name,
                    'email' => $request->email,
//                    'shop_type'       => $request->shop_type,
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

                $chemistHouseInitialDue=[
                    'chemist_house_id'        => $chemistHouseId,
                    'due_balance'             =>0,
                    'created_at'              => now(),
                ];

                ChemistHouseDueAccount::insert($chemistHouseInitialDue);

                DB::commit();
                Log::info('Chemist House Created Successfully.');
                return redirect()->back()->with('success', 'Chemist House Created Successfully.');
            } catch (\Exception $e) {

                DB::rollBack();
                Log::error($e->getMessage());

                return redirect()->back()->with('error', 'Failed to create Chemist House');
            }

        }
        $depo=Depo::find($mpo->depo_id);
        return view('employee.mpo.extends.chemist_house.create',compact('depo','mpo'));
    }


    public function edit($id)
    {
        try {
            $userId = Auth::user()->id;
            $mpo = Employee::where('user_id', $userId)->first();

            $chemistHouse = ChemistHouse::with(['chemistHouseDetail', 'depo'])
                ->where('id', $id)
                ->where('mpo_id', $userId) // MPO ownership check
                ->where('depo_id', $mpo->depo_id)
                ->first();

            if (!$chemistHouse) {
                return redirect()->back()->with('error', 'Chemist House not found.');
            }

            $depo = Depo::find($mpo->depo_id);

            return view(
                'employee.mpo.extends.chemist_house.edit',
                compact('chemistHouse', 'depo', 'mpo')
            );

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Failed to load Chemist House.');
        }
    }



    public function update(Request $request, $id)
    {
        $request->validate([
            'shop_name' => "required|string|unique:chemist_houses,shop_name,{$id}",
            'email' => [
                'required',
                'email',
                function ($attribute, $value, $fail) use ($id) {
                    $chemistHouse = ChemistHouse::find($id);

                    if (
                        DB::table('users')
                            ->where('email', $value)
                            ->where('id', '<>', $chemistHouse->user_id)
                            ->exists()
                        ||
                        DB::table('chemist_houses')
                            ->where('email', $value)
                            ->where('id', '<>', $id)
                            ->exists()
                    ) {
                        $fail('This email is already taken.');
                    }
                }
            ],
            'owner_name' => 'required|string',
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
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        try {
            DB::beginTransaction();

            $chemistHouse = ChemistHouse::with(['user', 'chemistHouseDetail'])->findOrFail($id);

            /* =========================
             * Update User
             * ========================= */
            $userData = [
                'name'  => $request->shop_name,
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $chemistHouse->user->update($userData);

            /* =========================
             * Update Chemist House
             * ========================= */
            $chemistHouse->update([
                'shop_name'      => $request->shop_name,
                'owner_name'     => $request->owner_name,
                'email'          => $request->email,
                'status'         => $request->status,
                'bank_name'      => $request->bank_name,
                'account_number' => $request->account_number,
                'contact'        => $request->contact,
                'whatsapp'       => $request->whatsapp,
                'address'        => $request->address,
            ]);

            /* =========================
             * Handle Images
             * ========================= */
            $drugImage  = $chemistHouse->chemistHouseDetail->drug_license_image ?? null;
            $tradeImage = $chemistHouse->chemistHouseDetail->trade_license_image ?? null;

            if ($request->hasFile('drug_license_image')) {
                if ($drugImage) {
                    $this->destroyImage($drugImage, 'image/drug_license_image');
                }
                $drugImage = $this->storeImage(
                    $request->drug_license_image,
                    'image/drug_license_image'
                );
            }

            if ($request->hasFile('trade_license_image')) {
                if ($tradeImage) {
                    $this->destroyImage($tradeImage, 'image/trade_license_image');
                }
                $tradeImage = $this->storeImage(
                    $request->trade_license_image,
                    'image/trade_license_image'
                );
            }

            /* =========================
             * Update / Insert Details
             * ========================= */
            $detailsData = [
                'chemist_house_id'          => $chemistHouse->id,
                'drug_license_number'       => $request->drug_license_number,
                'drug_license_expire_date'  => $request->drug_license_expire_date,
                'drug_license_image'        => $drugImage,
                'trade_license'             => $request->trade_license,
                'trade_license_expire_date' => $request->trade_license_expire_date,
                'trade_license_image'       => $tradeImage,
                'tin_number'                => $request->tin_number,
            ];

            if ($chemistHouse->chemistHouseDetail) {
                $chemistHouse->chemistHouseDetail->update($detailsData);
            } else {
                ChemistHouseDetail::create($detailsData);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Chemist House updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Failed to update Chemist House.');
        }
    }




}
