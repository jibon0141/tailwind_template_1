@extends('depo.master')

@section('content')
    <div class="page-wrapper bg-gray-100 min-h-screen p-6">
        <div class="max-w-4xl mx-auto">

            <!-- Page Header -->
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-semibold text-gray-800">Depo Details</h2>
                <a href="{{ route('depo.list.index') }}"
                   class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow text-sm">
                    <i class="fa fa-arrow-left"></i> Back to List
                </a>
            </div>

            <!-- Depo Card -->
            <div class="bg-white rounded shadow p-6 space-y-4">

                <!-- Depo Name & Status -->
                <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-2">
                    <h3 class="text-xl font-semibold text-gray-700">{{ $data->depo_name ?? 'N/A' }}</h3>
                    <span class="px-3 py-1 text-sm font-medium rounded
                    {{ $data->status == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $data->status == 1 ? 'Active' : 'Inactive' }}
                </span>
                </div>

                <hr class="border-gray-200">

                <!-- Basic Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="font-medium text-gray-600">Person Name</h4>
                        <p class="text-gray-800">{{ $data->person_name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-600">Email</h4>
                        <p class="text-gray-800">{{ $data->email ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-600">Contact</h4>
                        <p class="text-gray-800">{{ $data->contact ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-600">Division</h4>
                        <p class="text-gray-800">{{ $data->division->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-600">District</h4>
                        <p class="text-gray-800">{{ $data->district->name ?? 'N/A' }}</p>
                    </div>
                </div>

                <hr class="border-gray-200">

                <!-- Address & Account -->
                <div class="space-y-4">
                    <div>
                        <h4 class="font-medium text-gray-600">Address</h4>
                        <p class="text-gray-800">{{ $data->address ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <h4 class="font-medium text-gray-600">Account Details</h4>
                        <pre class="bg-gray-50 border border-gray-200 p-3 rounded text-gray-800 whitespace-pre-wrap">
{{ $data->account_no ?? 'N/A' }}
                    </pre>
                    </div>
                </div>

                <hr class="border-gray-200">

                <!-- Timestamps -->
                <div class="text-sm text-gray-500 flex flex-col md:flex-row md:justify-between">
                    <p>Created at: {{ $data->created_at->format('d M, Y H:i') }}</p>
                    <p>Last updated: {{ $data->updated_at->format('d M, Y H:i') }}</p>
                </div>

            </div>
        </div>
    </div>
@endsection
