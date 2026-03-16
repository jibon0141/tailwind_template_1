@extends('admin.master')
@section('content')
    <div class="page-wrapper bg-white p-6 rounded shadow">
        <div class="content container mx-auto px-4">

            <div class="mb-6 flex justify-between items-center">
                <h3 class="text-2xl font-semibold text-gray-800">Purchase Admin Details</h3>
                <a href="{{ route('purchase.admin.index') }}"
                   class="inline-flex items-center gap-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium px-4 py-2 rounded shadow transition">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
            </div>

            <div class="bg-gray-50 p-6 rounded shadow">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Name</p>
                        <p class="text-lg font-semibold">{{ $admin->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Email</p>
                        <p class="text-lg font-semibold">{{ $admin->email }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Status</p>
                        <p class="text-lg font-semibold">
                            @if($admin->status == 1)
                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs">Active</span>
                            @else
                                <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs">Inactive</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Created At</p>
                        <p class="text-lg font-semibold">{{ $admin->created_at->format('d M Y') }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
