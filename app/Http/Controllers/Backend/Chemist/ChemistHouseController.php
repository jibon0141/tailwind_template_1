<?php

namespace App\Http\Controllers\Backend\Chemist;

use App\Http\Controllers\Controller;
use App\Models\ChemistHouse;
use App\Models\ChemistHouseDetail;
use App\Models\ChemistHouseDueAccount;
use App\Models\Depo;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Traits\ManageImage;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class ChemistHouseController extends Controller
{
    use ManageImage;

    public function index(Request $request){

        if($request->ajax()){

            try{
                $chemistHouses = ChemistHouse::with(['depo','chemistHouseDueAccount','mpo'])->orderBy('id','DESC');

                return DataTables::of($chemistHouses)
                    ->addIndexColumn()

                    ->addColumn('shop_name', fn ($row) => $row->shop_name ?? 'N/A')

                    ->addColumn('owner_name', fn ($row) => $row->owner_name ?? 'N/A')

                    ->addColumn('depo_name', fn ($row) => $row->depo->depo_name ?? 'N/A')

                    ->addColumn('mpo_name', fn ($row) => $row->mpo->full_name ?? 'N/A')

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
                        $editUrl   = route('chemist.house.edit', $row->id);
                        $deleteId  = $row->id;

                        return '
                        <div class="flex gap-2">
                            <a href="'.$editUrl.'" class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded">
                                <i class="fa fa-edit"></i>
                            </a>

                        </div>
                    ';
                    })

                    ->rawColumns(['status', 'action'])
                    ->make(true);

            }catch (\Exception $exception){
                Log::error($exception->getMessage());
            }

        }
        return view('admin.extends.chemist_house.index');
    }


    public function create(Request $request)
    {
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
        $depos=Depo::all();
        return view('admin.extends.chemist_house.create',compact('depos'));
    }

    public function show($id)
    {
        try {
            $chemistHouse = ChemistHouse::with(['details', 'depo'])->where('id', $id)->first();

            if (empty($chemistHouse)) {
                Log::info("Show ChemistHouse Not Found for ID: {$id}");
                return redirect()->back()->with('error', 'Chemist House not found.');
            }

            Log::info("Show ChemistHouse Found for ID: {$id}");
            return view('admin.extends.chemist_house.show', compact('chemistHouse'));

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while fetching the Chemist House.');
        }
    }

    public function edit($id)
    {
        try {
            $chemistHouse = ChemistHouse::with(['chemistHouseDetail', 'depo'])->where('id', $id)->first();

            if(empty($chemistHouse)) {
                return redirect()->back()->with('error', 'Chemist House not found.');
            }

            $depos = Depo::all();

            // Preload MPOs for the current Depo
            $mpos = [];
            if ($chemistHouse->depo_id) {
                $mpos = Employee::where('employee_type','mpo')
                    ->where('depo_id', $chemistHouse->depo_id)
                    ->get();
            }

            return view('admin.extends.chemist_house.edit', compact('chemistHouse', 'depos','mpos'));

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

                    // Check email in users table, excluding this chemist's user
                    $existsInUsers = DB::table('users')
                        ->where('email', $value)
                        ->where('id', '<>', $chemistHouse->user_id)
                        ->exists();

                    // Check email in chemist_houses table, excluding current
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
            'mpo_id' => [
                'required',
                Rule::exists('employees', 'user_id')->where(function ($query) use ($request) {
                    $query->where('employee_type', 'mpo')
                        ->where('depo_id', $request->depo_id);
                }),
            ],
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

            $chemistHouse = ChemistHouse::with('user')->find($id);

            // Update user
            $userData = [
                'name' => $request->shop_name,
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $chemistHouse->user->update($userData);

            //Update Chemist House
            $chemistHouseData = [
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
                'updated_at'      => now(),
            ];

            $chemistHouse = ChemistHouse::findOrFail($id);
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

            return redirect()->back()->with('success', 'Chemist House updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Failed to update Chemist House');
        }
    }



    public function getMpo(Request $request)
    {
        try {
            $request->validate([
                'depo_id' => 'required|exists:depos,id',
            ]);

            $mpo = Employee::where('employee_type', 'mpo')
                ->where('depo_id', $request->depo_id)
                ->select('id', 'user_id', 'full_name','employee_code')
                ->orderBy('full_name')
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'success',
                'data' => $mpo
            ]);
        }

        catch (\Exception $e) {
            Log::error('loadMpo error: '.$e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong while loading MPO list.'
            ], 500);
        }
    }







}
