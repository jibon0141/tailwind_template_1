@extends('admin.master')
@section('content')
<div class="min-h-screen p-4 sm:p-6 lg:p-8" style="background: linear-gradient(135deg, #f0f4ff 0%, #e8ecf8 100%);">
    <div class="max-w-4xl mx-auto">
        @include('admin.include.message')
        <div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white shadow-lg shadow-amber-200">
                        <i class="fa fa-cog text-sm"></i>
                    </span>
                    Company Settings
                </h3>
                <p class="text-sm text-gray-500 mt-1 ml-13">Update company information</p>
            </div>
            <a href="{{ url('/admin/dashboard') }}"
               class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white text-sm font-medium px-5 py-2.5 rounded-xl shadow-lg shadow-indigo-200 transition-all duration-200 hover:shadow-xl hover:-translate-y-0.5">
                <i class="fa fa-reply"></i>
                Dashboard
            </a>
        </div>
        <div class="bg-white/80 rounded-2xl shadow-lg shadow-indigo-100/50 border border-white/50 overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex items-center gap-2">
                <i class="fa fa-info-circle text-amber-500"></i>
                <span class="font-semibold text-gray-700">Company Information</span>
            </div>
            <div class="p-6">

            <form action="{{ route('main.company.update', $company->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Company Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="company_name" value="{{ old('company_name', $company->company_name) }}"
                                   class="form-input !rounded-xl !border-gray-200 !bg-gray-50 focus:!bg-white focus:!border-indigo-400 focus:!ring focus:!ring-indigo-200/50 !transition-all"
                                   placeholder="Enter company name">
                            @error('company_name')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Phone
                            </label>
                            <input type="text" name="phone" value="{{ old('phone', $company->phone) }}"
                                   class="form-input !rounded-xl !border-gray-200 !bg-gray-50 focus:!bg-white focus:!border-indigo-400 focus:!ring focus:!ring-indigo-200/50 !transition-all"
                                   placeholder="Enter phone number">
                            @error('phone')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Email
                            </label>
                            <input type="email" name="email" value="{{ old('email', $company->email) }}"
                                   class="form-input !rounded-xl !border-gray-200 !bg-gray-50 focus:!bg-white focus:!border-indigo-400 focus:!ring focus:!ring-indigo-200/50 !transition-all"
                                   placeholder="Enter email address">
                            @error('email')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Website URL
                            </label>
                            <input type="url" name="website_url" value="{{ old('website_url', $company->website_url) }}"
                                   class="form-input !rounded-xl !border-gray-200 !bg-gray-50 focus:!bg-white focus:!border-indigo-400 focus:!ring focus:!ring-indigo-200/50 !transition-all"
                                   placeholder="Enter website URL">
                            @error('website_url')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="flex flex-col md:col-span-2">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Address
                            </label>
                            <textarea name="address" rows="3"
                                      class="form-input !rounded-xl !border-gray-200 !bg-gray-50 focus:!bg-white focus:!border-indigo-400 focus:!ring focus:!ring-indigo-200/50 !transition-all"
                                      placeholder="Enter company address">{{ old('address', $company->address) }}</textarea>
                            @error('address')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Company Logo
                            </label>
                            <div class="relative">
                                <input type="file" name="logo"
                                       class="form-input !rounded-xl !border-gray-200 !bg-gray-50 focus:!bg-white focus:!border-indigo-400 focus:!ring focus:!ring-indigo-200/50 !transition-all !pl-10 file:!rounded-lg file:!border-0 file:!bg-indigo-50 file:!text-indigo-700 file:!text-sm file:!font-medium file:!px-3 file:!py-1.5 hover:file:!bg-indigo-100">
                            </div>
                            @if($company->logo)
                                <img src="{{ asset('/image/company_logo/'.$company->logo) }}"
                                     class="mt-2 h-20 w-48 object-contain border border-gray-200 rounded-xl p-1">
                            @endif
                            @error('logo')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Favicon
                            </label>
                            <div class="relative">
                                <input type="file" name="favicon"
                                       class="form-input !rounded-xl !border-gray-200 !bg-gray-50 focus:!bg-white focus:!border-indigo-400 focus:!ring focus:!ring-indigo-200/50 !transition-all !pl-10 file:!rounded-lg file:!border-0 file:!bg-indigo-50 file:!text-indigo-700 file:!text-sm file:!font-medium file:!px-3 file:!py-1.5 hover:file:!bg-indigo-100">
                            </div>
                            @if($company->favicon)
                                <img src="{{ asset('/image/company_favicon/'.$company->favicon) }}"
                                     class="mt-2 h-20 w-48 object-contain border border-gray-200 rounded-xl p-1">
                            @endif
                            @error('favicon')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="mt-8 pt-5 border-t border-gray-100 flex justify-end gap-3">
                        <a href="{{ url('/admin/dashboard') }}"
                           class="px-6 py-2.5 text-sm font-medium text-gray-600 bg-gray-50 hover:bg-gray-100 rounded-xl border border-gray-200 transition-all w-full sm:w-auto text-center">
                            Cancel
                        </a>
                        <button type="submit"
                                class="px-6 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 rounded-xl shadow-lg shadow-amber-200 transition-all duration-200 hover:shadow-xl hover:-translate-y-0.5 w-full sm:w-auto">
                            <i class="fa fa-save mr-1.5"></i>
                            Update Company
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
