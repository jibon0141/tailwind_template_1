@extends('employee.mpo.master')

@section('content')
    <div class="min-h-screen mb-5 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4">

            @include('admin.include.message')

            {{-- Header --}}
            <div class="mb-6 mt-2 pt-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <h3 class="text-2xl font-bold text-gray-800">Edit Chemist Shop</h3>

                <a href="{{ route('mpo.chemist-house.index') }}"
                   class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700">
                    <i class="fa fa-reply"></i>
                    Chemist House List
                </a>
            </div>

            {{-- Form Card --}}
            <div class="mt-8 bg-white p-6 rounded-lg shadow">
                <form action="{{ route('mpo.chemist-house.update', $chemistHouse->id) }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- ================= BASIC INFO ================= --}}
                    <h4 class="text-lg font-semibold text-gray-700 mb-4">Basic Information</h4>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="text-sm font-medium">Shop Name <span class="text-red-500">*</span></label>
                            <input type="text" name="shop_name"
                                   value="{{ old('shop_name', $chemistHouse->shop_name) }}"
                                   class="p-2 border rounded w-full" required>
                        </div>

                        <div>
                            <label class="text-sm font-medium">Owner Name <span class="text-red-500">*</span></label>
                            <input type="text" name="owner_name"
                                   value="{{ old('owner_name', $chemistHouse->owner_name) }}"
                                   class="p-2 border rounded w-full" required>
                        </div>

                        <div>
                            <label class="text-sm font-medium">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email"
                                   value="{{ old('email', $chemistHouse->user?->email) }}"
                                   class="p-2 border rounded w-full" required>
                        </div>
                    </div>

                    {{-- ================= FIXED DEPO & MPO ================= --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div>
                            <label class="text-sm font-medium">Depo</label>
                            <input type="text"
                                   value="{{ $depo->depo_name }}"
                                   readonly
                                   class="p-2 border rounded bg-gray-100 w-full">
                        </div>

                        <div>
                            <label class="text-sm font-medium">MPO</label>
                            <input type="text"
                                   value="{{ $mpo->full_name }} ({{$mpo->employee_code}})"
                                   readonly
                                   class="p-2 border rounded bg-gray-100 w-full">
                        </div>
                    </div>

                    {{-- ================= BANK & CONTACT ================= --}}
                    <h4 class="text-lg font-semibold text-gray-700 mt-8 mb-4">Bank & Contact</h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <input type="text" name="bank_name"
                               value="{{ old('bank_name', $chemistHouse->chemistHouseDetail?->bank_name) }}"
                               class="p-2 border rounded" placeholder="Bank Name">

                        <input type="text" name="account_number"
                               value="{{ old('account_number', $chemistHouse->chemistHouseDetail?->account_number) }}"
                               class="p-2 border rounded" placeholder="Account Number">

                        <input type="text" name="contact"
                               value="{{ old('contact', $chemistHouse->chemistHouseDetail?->contact) }}"
                               class="p-2 border rounded" placeholder="Mobile">

                        <input type="text" name="whatsapp"
                               value="{{ old('whatsapp', $chemistHouse->chemistHouseDetail?->whatsapp) }}"
                               class="p-2 border rounded" placeholder="WhatsApp">
                    </div>

                    <div class="mt-4">
                    <textarea name="address" rows="3"
                              class="p-2 border rounded w-full"
                              placeholder="Address">{{ old('address', $chemistHouse->chemistHouseDetail?->address) }}</textarea>
                    </div>

                    {{-- ================= LICENSE DETAILS ================= --}}
                    <h4 class="text-lg font-semibold text-gray-700 mt-8 mb-4">License Details</h4>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="text-sm font-medium">Drug License No</label>
                            <input type="text" name="drug_license_number"
                                   value="{{ old('drug_license_number', $chemistHouse->chemistHouseDetail?->drug_license_number) }}"
                                   class="p-2 border rounded w-full">
                        </div>

                        <div>
                            <label class="text-sm font-medium">Drug License Expiry</label>
                            <input type="date" name="drug_license_expire_date"
                                   value="{{ old('drug_license_expire_date', isset($chemistHouse->chemistHouseDetail->drug_license_expire_date) ? \Carbon\Carbon::parse($chemistHouse->chemistHouseDetail->drug_license_expire_date)->format('Y-m-d') : '') }}"
                                   class="p-2 border rounded w-full">
                        </div>

                        <div>
                            <label class="text-sm font-medium">Drug License Image</label>
                            <input type="file" name="drug_license_image" class="p-2 border rounded w-full">
                            @if($chemistHouse->chemistHouseDetail?->drug_license_image)
                                <a href="{{ asset('storage/'.$chemistHouse->chemistHouseDetail->drug_license_image) }}"
                                   target="_blank" class="text-blue-600 text-sm">View Current</a>
                            @endif
                        </div>

                        <div>
                            <label class="text-sm font-medium">Trade License </label>
                            <input type="text" name="trade_license"
                                   value="{{ old('trade_license', $chemistHouse->chemistHouseDetail?->trade_license) }}"
                                   class="p-2 border rounded w-full" >
                        </div>

                        <div>
                            <label class="text-sm font-medium">Trade License Expiry </label>
                            <input type="date" name="trade_license_expire_date"
                                   value="{{ old('trade_license_expire_date', isset($chemistHouse->chemistHouseDetail->trade_license_expire_date) ? \Carbon\Carbon::parse($chemistHouse->chemistHouseDetail->trade_license_expire_date)->format('Y-m-d') : '') }}"
                                   class="p-2 border rounded w-full" >
                        </div>

                        <div>
                            <label class="text-sm font-medium">Trade License Image</label>
                            <input type="file" name="trade_license_image" class="p-2 border rounded w-full">
                            @if($chemistHouse->chemistHouseDetail?->trade_license_image)
                                <a href="{{ asset('storage/'.$chemistHouse->chemistHouseDetail->trade_license_image) }}"
                                   target="_blank" class="text-blue-600 text-sm">View Current</a>
                            @endif
                        </div>

                        <div>
                            <label class="text-sm font-medium">TIN Number</label>
                            <input type="text" name="tin_number"
                                   value="{{ old('tin_number', $chemistHouse->chemistHouseDetail?->tin_number) }}"
                                   class="p-2 border rounded w-full">
                        </div>

                        <div>
                            <label class="text-sm font-medium">Status <span class="text-red-500">*</span></label>
                            <select name="status" class="p-2 border rounded w-full">
                                <option value="1" @selected($chemistHouse->status == 1)>Active</option>
                                <option value="0" @selected($chemistHouse->status == 0)>Inactive</option>
                            </select>
                        </div>
                    </div>

                    {{-- ================= LOGIN ================= --}}
                    <h4 class="text-lg font-semibold text-gray-700 mt-8 mb-4">Login Credentials</h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm font-medium">Password (leave blank to keep current)</label>
                            <input type="password" name="password" class="p-2 border rounded w-full">
                        </div>

                        <div>
                            <label class="text-sm font-medium">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="p-2 border rounded w-full">
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="mt-8 text-right">
                        <button type="submit"
                                class="bg-teal-600 hover:bg-teal-700 text-white px-8 py-2 rounded">
                            Update Chemist Shop
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection
