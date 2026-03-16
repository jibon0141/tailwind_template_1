@extends('admin.master')
@section('content')

    <div class="min-h-screen bg-gray-100 p-6">

        <!-- Back Button -->
        <div class="mb-4 flex justify-end">
            <a href="{{ route('admin.job.application.index') }}"
               class="px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                ← Back to Applications
            </a>
        </div>

        @include('admin.include.message')

        <!-- Application Header -->
        <div class="max-w-5xl mx-auto bg-white shadow rounded-lg p-6 mb-6">
            <div class="w-[60%] mx-auto text-center mb-6 ">
                <h1 class="text-3xl font-bold text-gray-800">{{ $mainCompany->company_name ?? 'Company Name' }}</h1>
                <p class="text-sm text-gray-600">{{ $mainCompany->address ?? '' }}</p>
                <p class="text-sm text-gray-600">
                    Phone: {{ $mainCompany->phone ?? 'N/A' }}
                    @if($mainCompany->email)
                        | Email: {{ $mainCompany->email }}
                    @endif
                </p>
                <p class="text-sm text-gray-600">{{ $mainCompany->website_url ?? '' }}</p>
                <p class="text-2xl font-semibold mt-2">Job Application</p>
            </div>

            <hr class="border-t border-gray-300 mb-6">

            <!-- Applicant Info -->
            <div class="flex justify-between text-sm text-gray-600">
                <div class="text-left">
                    <p><strong>Application ID:</strong> {{ $application->id }}</p>
                    <p><strong>Position:</strong> {{ $application->designation }}</p>
                    <p><strong>Branch:</strong> {{ $application->branch }}</p>
                    <p><strong>Date:</strong> {{ $application->application_date ? \Carbon\Carbon::parse($application->application_date)->format('d M Y') : 'N/A' }}</p>
                </div>

                <div class="text-left">
                    <p><strong>Applicant Name:</strong> {{ $application->name }}</p>
                    <p><strong>Mobile:</strong> {{ $application->mobile }}</p>
                    <p><strong>Email:</strong> {{ $application->email ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Personal Information -->
        <div class="max-w-5xl mx-auto bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-xl font-bold text-gray-800 border-b pb-2 mb-4">Personal Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-gray-500 font-semibold">Father / Husband Name</p>
                    <p class="text-gray-800">{{ $application->father_or_husband_name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 font-semibold">Mother Name</p>
                    <p class="text-gray-800">{{ $application->mother_name }}</p>
                </div>
                <div>
                    <p class="text-gray-500 font-semibold">NID Number</p>
                    <p class="text-gray-800">{{ $application->nid_no ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 font-semibold">Marital Status</p>
                    <p class="text-gray-800">{{ $maritalStatus[$application->marital_status] ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 font-semibold">Blood Group</p>
                    <p class="text-gray-800">{{ $bloodGroups[$application->blood_group] ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 font-semibold">Date of Birth</p>
                    <p class="text-gray-800">{{ $application->date_of_birth ? \Carbon\Carbon::parse($application->date_of_birth)->format('d M Y') : 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 font-semibold">Age</p>
                    <p class="text-gray-800">{{ $application->age }}</p>
                </div>
                <div>
                    <p class="text-gray-500 font-semibold">Nationality</p>
                    <p class="text-gray-800">{{ $application->nationality ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 font-semibold">Religion</p>
                    <p class="text-gray-800">{{ $religions[$application->religion] ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Contact & Address -->
        <div class="max-w-5xl mx-auto bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-xl font-bold text-gray-800 border-b pb-2 mb-4">Contact & Address</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-gray-500 font-semibold">Mobile</p>
                    <p class="text-gray-800">{{ $application->mobile ?? 'N/A' }}</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-gray-500 font-semibold">Current Address</p>
                    <p class="text-gray-800">{{ $application->current_address ?? 'N/A' }}</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-gray-500 font-semibold">Permanent Address</p>
                    <p class="text-gray-800">{{ $application->permanent_address ?? 'N/A'}}</p>
                </div>
            </div>
        </div>

        <!-- Experience -->
        <div class="max-w-5xl mx-auto bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-xl font-bold text-gray-800 border-b pb-2 mb-4">Experience</h3>
            <p class="text-gray-800">{{ $application->experience ?? 'N/A' }}</p>
        </div>

    </div>

@endsection
