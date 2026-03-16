@extends('admin.master')

@section('content')

    <div class="min-h-screen bg-gray-100 py-6">
        <div class="max-w-5xl mx-auto bg-white p-6 rounded-lg shadow">

            <!-- Header -->
            @include('admin.include.message')
            <div class="mb-6 flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-800">Company Settings</h2>
            </div>

            <!-- Form -->
            <form action="{{ route('main.company.update', $company->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Company Name -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-600">Company Name</label>
                        <input type="text" name="company_name"
                               value="{{ old('company_name', $company->company_name) }}"
                               class="w-full p-2 border rounded focus:outline-none focus:border-teal-500">
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-600">Phone</label>
                        <input type="text" name="phone"
                               value="{{ old('phone', $company->phone) }}"
                               class="w-full p-2 border rounded focus:outline-none focus:border-teal-500">
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-600">Email</label>
                        <input type="email" name="email"
                               value="{{ old('email', $company->email) }}"
                               class="w-full p-2 border rounded focus:outline-none focus:border-teal-500">
                    </div>

                    <!-- Website -->
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-600">Website URL</label>
                        <input type="url" name="website_url"
                               value="{{ old('website_url', $company->website_url) }}"
                               class="w-full p-2 border rounded focus:outline-none focus:border-teal-500">
                    </div>

                    <!-- Address -->
                    <div class="md:col-span-2">
                        <label class="block mb-2 text-sm font-medium text-gray-600">Address</label>
                        <textarea name="address" rows="3"
                                  class="w-full p-2 border rounded focus:outline-none focus:border-teal-500">{{ old('address', $company->address) }}</textarea>
                    </div>

                    <!-- Logo -->
                    <div class="flex flex-col">
                        <label class="mb-2 text-sm font-medium text-gray-600">Company Logo</label>

                        <div class="h-20 flex items-center mb-2">
                            @if($company->logo)
                                <img src="{{ asset('/image/company_logo/'.$company->logo) }}"
                                     class="h-16 object-contain border rounded">
                            @endif
                        </div>

                        <input type="file" name="logo"
                               class="w-full p-2 border rounded">
                    </div>

                    <!-- Favicon -->
                    <div class="flex flex-col">
                        <label class="mb-2 text-sm font-medium text-gray-600">Favicon</label>

                        <div class="h-20 flex items-center mb-2">
                            @if($company->favicon)
                                <img src="{{ asset('/image/company_favicon/'.$company->favicon) }}"
                                     class="h-10 object-contain border rounded">
                            @endif
                        </div>

                        <input type="file" name="favicon"
                               class="w-full p-2 border rounded">
                    </div>


                </div>

                <!-- Submit -->
                <div class="mt-6 text-right">
                    <button type="submit"
                            class="bg-teal-500 hover:bg-teal-600 text-white px-6 py-2 rounded font-semibold">
                        Update Company
                    </button>
                </div>

            </form>
        </div>
    </div>

@endsection
