<?php

namespace App\Http\Controllers\Backend\Investor;
use App\Http\Controllers\Controller;
use App\Models\Investor;
use Illuminate\Http\Request;
use App\Traits\ManageImage;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class InvestorController extends Controller
{
    use ManageImage;
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $investors = Investor::latest()->get();



            return DataTables::of($investors)
                ->addIndexColumn()

                ->addColumn('name', fn ($row) => $row->name ?? 'N/A')
                ->addColumn('investor_code', fn ($row) => $row->investor_code ?? 'N/A')
                ->addColumn('contact', fn ($row) => $row->contact ?? 'N/A')
                ->addColumn('opening_balance', fn ($row) => number_format($row->opening_balance ?? 0, 2))
                ->addColumn('invest_amount', fn ($row) => number_format($row->invest_amount ?? 0, 2))

                ->addColumn('status', function ($row) {
                    return $row->status == 1
                        ? '<span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Active</span>'
                        : '<span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">Inactive</span>';
                })

                ->addColumn('action', function ($row) {
                    return '
                    <div class="flex gap-1">
                        <a href="' . route('admin.investor.show', $row->id) . '"
                           class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded"
                           title="View">
                            <i class="fa fa-eye"></i>
                        </a>

                        <a href="' . route('admin.investor.edit', $row->id) . '"
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

        return view('admin.extends.investor.index');
    }

    public function create(Request $request)
    {
        if($request->isMethod('post')){

            $request->validate([
                'name'                    => 'required|string|max:255',
                'nid'                     => 'required|string|max:100',

                'nid_front'               => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:2048',
                'nid_back'                => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:2048',

                'email'                   => 'required|email|max:255',
                'contact'                 => 'required|string|max:20',
                'bank_details'            => 'required|string',
                'address'                 => 'required|string',

                'nominee_name'            => 'nullable|string|max:255',
                'nominee_relation'        => 'nullable|string|max:100',
                'nominee_address'         => 'nullable|string',
                'nominee_contact'         => 'nullable|string|max:20',
                'nominee_bank_details'    => 'nullable|string',
                'nominee_nid'             => 'nullable|string|max:100',

                'nominee_nid_front'       => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:2048',
                'nominee_nid_back'        => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:2048',

                'status'                  => 'required',
            ]);


            try{
                $nidFront="";
                $nidBack="";

                $nomineeNidFront="";
                $nomineeNidBack="";

                if($request->hasFile('nid_front')){
                    $nidFront= $this->storeImage($request->nid_front,'image/investor/investor_image/nid_front/');
                }

                if($request->hasFile('nid_back')){
                    $nidBack= $this->storeImage($request->nid_back,'image/investor/investor_image/nid_back/');
                }

                if($request->hasFile('nominee_nid_front')){
                    $nomineeNidFront= $this->storeImage($request->nominee_nid_front,'image/investor/investor_nominee_image/nid_front/');
                }

                if($request->hasFile('nominee_nid_back')){
                    $nomineeNidBack= $this->storeImage($request->nominee_nid_back,'image/investor/investor_nominee_image/nid_back/');
                }
                $investor = [
                    'name'                    => $request->name,
                    'nid'                     => $request->nid,

                    'nid_front'               => $nidFront,
                    'nid_back'                => $nidBack,

                    'email'                   => $request->email,
                    'contact'                 => $request->contact,
                    'bank_details'            => $request->bank_details,
                    'address'                 => $request->address,

                    'invest_amount'           => 0,
                    'opening_balance'         => 0,

                    'nominee_name'            => $request->nominee_name,
                    'nominee_relation'        => $request->nominee_relation,
                    'nominee_address'         => $request->nominee_address,
                    'nominee_contact'         => $request->nominee_contact,
                    'nominee_bank_details'    => $request->nominee_bank_details,
                    'nominee_nid'             => $request->nominee_nid,

                    'nominee_nid_front'       => $nomineeNidFront,
                    'nominee_nid_back'        => $nomineeNidBack,

                    'status'                  => $request->status ?? 1,
                ];


                Investor::create($investor);

                Log::info('Investor Created Successfully.');
                return redirect()->back()->with('success','Investor Created Successfully.');

            }catch(\Exception $e){
                Log::error($e->getMessage());
                return redirect()->back()->with('error','Investor Create Failed.');
            }


        }

        return view('admin.extends.investor.create');
    }

    public function show($id)
    {
        try{
            $investor=Investor::where('id',$id)->first();

            if(empty($investor)){
                Log::info('Investor Not Found.');
                return redirect()->back()->with('error','Investor Not Found.');
            }

            Log::info('Investor Show Successfully.');
            return view('admin.extends.investor.show',compact('investor'));
        }catch (\Exception $e){
            Log::error($e->getMessage());
            return redirect()->back()->with('error','Investor Not Found.');
        }
    }

    public function edit($id)
    {
        try{
            $investor=Investor::where('id',$id)->first();

            if(empty($investor)){
                Log::info('Investor Not Found.');
                return redirect()->back()->with('error','Investor Not Found.');
            }

            Log::info('Investor Show Successfully.');
            return view('admin.extends.investor.edit',compact('investor'));
        }catch (\Exception $e){
            Log::error($e->getMessage());
            return redirect()->back()->with('error','Investor Not Found.');
        }

    }

    public function update(Request $request,$id)
    {

        $request->validate([
            'name'                    => 'required|string|max:255',
            'nid'                     => 'required|string|max:100',

            'nid_front'               => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:2048',
            'nid_back'                => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:2048',

            'email'                   => 'required|email|max:255',
            'contact'                 => 'required|string|max:20',
            'bank_details'            => 'required|string',
            'address'                 => 'required|string',

            'nominee_name'            => 'nullable|string|max:255',
            'nominee_relation'        => 'nullable|string|max:100',
            'nominee_address'         => 'nullable|string',
            'nominee_contact'         => 'nullable|string|max:20',
            'nominee_bank_details'    => 'nullable|string',
            'nominee_nid'             => 'nullable|string|max:100',

            'nominee_nid_front'       => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:2048',
            'nominee_nid_back'        => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:2048',

            'status'                  => 'required', // 0 = inactive, 1 = active
        ]);


        try{
            $investor=Investor::find($id);

            if(empty($investor)){
                Log::info('Investor Not Found.');
                return redirect()->back()->with('success','Investor Not Found');
            }

            $nidFront= $investor->nid_front;
            $nidBack=$investor->nid_back;

            $nomineeNidFront=$investor->nominee_nid_front;
            $nomineeNidBack=$investor->nominee_nid_back;

            if($request->hasFile('nid_front')){
                $this->destroyImage($nidFront,'image/investor/investor_image/nid_front/');
                $nidFront=$this->storeImage($request->nid_front,'image/investor/investor_image/nid_front/');
            }

            if($request->hasFile('nid_back')){
                $this->destroyImage($nidBack,'image/investor/investor_image/nid_back/');
                $nidBack= $this->storeImage($request->nid_back,'image/investor/investor_image/nid_back/');
            }

            if($request->hasFile('nominee_nid_front')){
                $this->destroyImage($nomineeNidFront,'image/investor/investor_nominee_image/nid_front/');
                $nomineeNidFront= $this->storeImage($request->nominee_nid_front,'image/investor/investor_nominee_image/nid_front/');
            }

            if($request->hasFile('nominee_nid_back')){
                $this->destroyImage($nomineeNidBack,'image/investor/investor_nominee_image/nid_back/');
                $nomineeNidBack= $this->storeImage($request->nominee_nid_back,'image/investor/investor_nominee_image/nid_back/');
            }


            $data = [
                'name'                 => $request->name,
                'nid'                  => $request->nid,
                'nid_front'            => $nidFront,
                'nid_back'             => $nidBack,
                'email'                => $request->email,
                'contact'              => $request->contact,
                'bank_details'         => $request->bank_details,
                'address'              => $request->address,

                'nominee_name'         => $request->nominee_name,
                'nominee_relation'     => $request->nominee_relation,
                'nominee_address'      => $request->nominee_address,
                'nominee_contact'      => $request->nominee_contact,
                'nominee_bank_details' => $request->nominee_bank_details,
                'nominee_nid'          => $request->nominee_nid,
                'nominee_nid_front'    => $nomineeNidFront,
                'nominee_nid_back'     => $nomineeNidBack,
                'status'               => $request->status,
            ];


            $investor->update($data);
            Log::info('Investor Successfully Updated.');
            return redirect()->back()->with('success','Investor Successfully Updated.');
        }catch(\Exception $e){
            Log::error($e->getMessage());
            return redirect()->back()->with('error','Investor Update Failed.');
        }
    }



}
