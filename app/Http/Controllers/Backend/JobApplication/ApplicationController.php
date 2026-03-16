<?php

namespace App\Http\Controllers\Backend\JobApplication;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class ApplicationController extends Controller
{


    public function index(Request $request)
    {
        if ($request->ajax()) {

            $applications = JobApplication::query();


            $datatable = DataTables::of($applications)
                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                    return $row->name ?? 'N/A';
                })
                ->addColumn('mobile', function ($row) {
                    return $row->mobile ?? 'N/A';
                })
                ->addColumn('email', function ($row) {
                    return $row->email ?? 'N/A';
                })  ->addColumn('designation', function ($row) {
                    return $row->designation ?? 'N/A';
                })
                ->addColumn('branch', function ($row) {
                    return $row->branch ?? 'N/A';
                })
                ->addColumn('religion', function ($row) {
                    $religions = [1 => 'Islam', 2 => 'Hinduism', 3 => 'Christianity', 4 => 'Others'];
                    return $religions[$row->religion] ?? 'N/A';
                })
                ->addColumn('marital_status', function ($row) {
                    $status = [1 => 'Single', 2 => 'Married', 3 => 'Divorced', 4 => 'Widow'];
                    return $status[$row->marital_status] ?? 'N/A';
                })
                ->addColumn('action', function ($row) {
                    $showUrl = route('admin.job.application.show', $row->id);
                    $editUrl = route('admin.job.application.edit', $row->id);
                    $deleteFunc = "deleteApplication({$row->id})";

                    $buttons = '<div class="flex gap-2">';

                    $buttons .= '<a href="' . $showUrl . '"
                     class="inline-flex items-center px-2 py-1 bg-green-500 hover:bg-green-600 text-white text-xs font-semibold rounded"
                     title="Show">
                     <i class="fa fa-eye"></i>
                 </a>';

                    $buttons .= '<a href="' . $editUrl . '"
                     class="inline-flex items-center px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold rounded"
                     title="Edit">
                     <i class="fa fa-edit"></i>
                 </a>';

                    $buttons .= '<button onclick="' . $deleteFunc . '"
                     class="inline-flex items-center px-2 py-1 bg-red-500 hover:bg-red-600 text-white text-xs font-semibold rounded"
                     title="Delete">
                     <i class="fa fa-trash"></i>
                 </button>';

                    $buttons .= '</div>';

                    return $buttons;
                })

                ->rawColumns(['action']);

            return $datatable->make(true);
        }

        return view('admin.extends.job_application.index');
    }


    public function create(Request $request){

        if($request->isMethod('post')){

            $validated = $request->validate([
                'name'                  => 'required|string|max:255',
                'father_or_husband_name'=> 'required|string|max:255',
                'mother_name'           => 'required|string|max:255',
                'mobile'                => 'required|string|max:20|unique:job_applications,mobile',
                'email'                 => 'nullable|email|max:255|unique:job_applications,email',
                'marital_status'        => 'nullable',
                'date_of_birth'         => 'required|date|before:today',
                'age'                   => 'required|integer|min:0|max:100',
                'nationality'           => 'nullable|string|max:100',
                'religion'              => 'required|string|max:50',
                'experience'            => 'nullable|string|max:5000',
                'nid_no'                => 'nullable|string|max:50|unique:job_applications,nid_no',
                'blood_group'           => 'nullable',
                'current_address'       => 'required|string|max:1000',
                'permanent_address'     => 'required|string|max:1000',
                'designation'           => 'required|string|max:255',
                'branch'                => 'nullable|string|max:255',
                'application_date'      => 'nullable|date|before_or_equal:today',
            ]);

            try{

                $application = [
                    'name'                    => $request->name,
                    'father_or_husband_name'  => $request->father_or_husband_name,
                    'mother_name'             => $request->mother_name,
                    'mobile'                  => $request->mobile,
                    'email'                   => $request->email,
                    'marital_status'          => $request->marital_status,
                    'date_of_birth'           => $request->date_of_birth,
                    'age'                     => $request->age,
                    'nationality'             => $request->nationality,
                    'religion'                => $request->religion,
                    'experience'              => $request->experience,
                    'nid_no'                  => $request->nid_no,
                    'blood_group'             => $request->blood_group,
                    'current_address'         => $request->current_address,
                    'permanent_address'       => $request->permanent_address,
                    'designation'             => $request->designation,
                    'branch'                  => $request->branch,
                    'application_date'        => $request->application_date,
                ];

                JobApplication::create($application);
                Log::info("Job Application Created Successfully");
                return redirect()->route('admin.job.application.index')->with('success', 'Job Application Created Successfully!');
            }catch(\Exception $e){
                Log::error($e->getMessage());
                return redirect()->back()->with('error','Application Not Created Successfully!');

            }

        }
        return view('admin.extends.job_application.create');
    }

    public function edit($id)
    {
        try {
            $application = JobApplication::find($id);

            $maritalStatus = [1 => 'Single', 2 => 'Married', 3 => 'Divorced', 4 => 'Widow'];
            $bloodGroups = [1 => 'A+', 2 => 'A-', 3 => 'B+', 4 => 'B-', 5 => 'O+', 6 => 'O-', 7 => 'AB+', 8 => 'AB-'];
            $religions = [1 => 'Islam', 2 => 'Hinduism', 3 => 'Christianity', 4 => 'Others'];

            return view('admin.extends.job_application.edit', compact('application', 'maritalStatus', 'bloodGroups', 'religions'));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.job.application.index')->with('error', 'Job Application not found.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $application = JobApplication::findOrFail($id);

            $validated = $request->validate([
                'name'                  => 'required|string|max:255',
                'father_or_husband_name'=> 'required|string|max:255',
                'mother_name'           => 'required|string|max:255',
                'mobile'                => 'required|string|max:20|unique:job_applications,mobile,' . $id,
                'email'                 => 'nullable|email|max:255|unique:job_applications,email,' . $id,
                'marital_status'        => 'nullable',
                'date_of_birth'         => 'required|date|before:today',
                'age'                   => 'required|integer|min:0|max:100',
                'nationality'           => 'nullable|string|max:100',
                'religion'              => 'required|string|max:50',
                'experience'            => 'nullable|string|max:5000',
                'nid_no'                => 'nullable|string|max:50|unique:job_applications,nid_no,' . $id,
                'blood_group'           => 'nullable',
                'current_address'       => 'required|string|max:1000',
                'permanent_address'     => 'required|string|max:1000',
                'designation'           => 'required|string|max:255',
                'branch'                => 'nullable|string|max:255',
                'application_date'      => 'nullable|date|before_or_equal:today',
            ]);

            $application->update([
                'name'                    => $request->name,
                'father_or_husband_name'  => $request->father_or_husband_name,
                'mother_name'             => $request->mother_name,
                'mobile'                  => $request->mobile,
                'email'                   => $request->email,
                'marital_status'          => $request->marital_status,
                'date_of_birth'           => $request->date_of_birth,
                'age'                     => $request->age,
                'nationality'             => $request->nationality,
                'religion'                => $request->religion,
                'experience'              => $request->experience,
                'nid_no'                  => $request->nid_no,
                'blood_group'             => $request->blood_group,
                'current_address'         => $request->current_address,
                'permanent_address'       => $request->permanent_address,
                'designation'             => $request->designation,
                'branch'                  => $request->branch,
                'application_date'        => $request->application_date,
            ]);

            Log::info("Job Application Updated Successfully: ID $id");
            return redirect()->route('admin.job.application.index')->with('success', 'Job Application Updated Successfully!');

        } catch (\Exception $e) {
            Log::error("Update Error: " . $e->getMessage());
            return redirect()->back()->with('error', 'Application Not Updated Successfully!');
        }
    }



    public function show($id)
    {
        try {
            $mainCompany=CompanySetting::first();
            $application = JobApplication::find($id);

            $maritalStatus = [1 => 'Single', 2 => 'Married', 3 => 'Divorced', 4 => 'Widow'];
            $bloodGroups = [1 => 'A+', 2 => 'A-', 3 => 'B+', 4 => 'B-', 5 => 'O+', 6 => 'O-', 7 => 'AB+', 8 => 'AB-'];
            $religions = [1 => 'Islam', 2 => 'Hinduism', 3 => 'Christianity', 4 => 'Others'];

            return view('admin.extends.job_application.show', compact('application', 'maritalStatus', 'bloodGroups', 'religions','mainCompany'));

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('admin.job.application.index')->with('error', 'Job Application not found.');
        }
    }


    public function destroy($id)
    {
        try {
            $application = JobApplication::find($id);

            if (!$application) {
                Log::info("Job Application Not Found: ID $id");
                return response()->json([
                    'success' => false,
                    'message' => 'Job Application not found.'
                ], 404);
            }

            $application->delete();
            Log::info("Job Application Deleted Successfully: ID $id");

            return response()->json([
                'success' => true,
                'message' => 'Job Application Deleted Successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error("Delete Error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again.'
            ], 500);
        }
    }



}
