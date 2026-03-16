<?php

namespace App\Http\Controllers\Depo\Depo;

use App\Http\Controllers\Controller;
use App\Models\Depo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class DepoController extends Controller
{
    public function index(Request $request)
    {
        $userId=!empty(Session::get('userObj')) ? Session::get('userObj')->id : Auth::user()->id;
        if ($request->ajax()) {

            $data=Depo::with(['division','district'])
                ->where('user_id','!=',$userId)
                ->latest()->get();

          return Datatables::of($data)
              ->addIndexColumn()
              ->addColumn('depo_name',function($row){
                  return $row->depo_name ?? 'N/A';
              })
              ->addColumn('person_name',function($row){
                  return $row->person_name ?? 'N/A';
              })
              ->addColumn('email',function($row){
                  return $row->email ?? 'N/A';
              })
              ->addColumn('contact',function($row){
                  return $row->contact ?? 'N/A';
              })
              ->addColumn('division',function($row){
                  return $row->division->name ?? 'N/A';
              })
              ->addColumn('district',function($row){
                  return $row->district->name ?? 'N/A';
              })
              ->addColumn('status', function ($row) {
                  return $row->status
                      ? '<span class="inline-block px-5 py-2 text-xs font-semibold text-green-800 bg-green-200 rounded-lg">Active</span>'
                      : '<span class="inline-block px-4 py-2 text-xs font-semibold text-red-800 bg-red-200 rounded-lg">Inactive</span>';
              })
              ->addColumn('action', function($row){
                  $showUrl = route('depo.view', $row->id);
                  return '<div class="flex gap-2">
                    <a href="'.$showUrl.'" class="inline-flex items-center px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold rounded" title="View"><i class="fa fa-eye"></i></a>
                </div>';
              })
              ->rawColumns(['status','action'])
              ->make(true);
        }
        return view('depo.extends.depo.index');
    }


    public function show($id){

        try{
            $data=Depo::with(['division','district'])->find($id);
            if(empty($data)){
                Log::info('Depo Not Found');
                return redirect()->back()->with('error','Depo not found.');
            }

            Log::info('Depo Data Successfully Showed.');
            return view('depo.extends.depo.show',compact('data'));
        }catch (\Exception $exception){
            Log::error($exception->getMessage());
            return redirect()->back()->with('error','Depo Data Showed Unsuccessfully.');
        }
    }

}
