@extends('depo.master')
@section('content')

    <div class="min-h-screen mb-5 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4">

            {{-- Page Header --}}
            @include('depo.include.message')
            <div class="mb-6 mt-2 pt-6 flex items-center justify-between">
                <h3 class="text-2xl font-bold text-gray-800">
                    Create Chemist Shop
                </h3>

                <a href="{{ route('depo.chemist-house.index') }}"
                   class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
                    <i class="fa fa-reply"></i>
                    Chemist House List
                </a>
            </div>

            {{-- Form Card --}}
            <div class="mt-8 bg-white p-6 rounded-lg shadow">
                <form action="{{ route('depo.chemist-house.create') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- ================= BASIC INFO ================= --}}
                    <h4 class="text-lg font-semibold text-gray-700 mb-4">Basic Information</h4>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                        {{-- Shop Name --}}
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Shop Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="shop_name"
                                   value="{{ old('shop_name') }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   required>
                            @error('shop_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- Owner Name --}}
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Owner Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="owner_name"
                                   value="{{ old('owner_name') }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   required>
                            @error('owner_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- Email --}}
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email"
                                   value="{{ old('email') }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   required>
                            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">

                        {{-- Depo (Readonly) --}}
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Depo <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   value="{{ $depo->depo_name }}"
                                   readonly
                                   class="p-2 border border-gray-300 rounded bg-gray-100 cursor-not-allowed">
                            <input type="hidden" name="depo_id" value="{{ $depo->id }}">
                        </div>

                        {{-- MPO --}}
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                MPO <span class="text-red-500">*</span>
                            </label>
                            <select name="mpo_id"
                                    class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                    required>
                                <option value="" disabled selected>Select MPO</option>
                                @foreach($mpos as $mpo)
                                    <option value="{{ $mpo->user_id }}" {{ old('mpo_id') == $mpo->user_id ? 'selected' : '' }}>
                                        {{ $mpo->full_name }} ({{$mpo->employee_code}})
                                    </option>
                                @endforeach
                            </select>
                            @error('mpo_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>


                    </div>

                    {{-- ================= BANK & CONTACT ================= --}}
                    <h4 class="text-lg font-semibold text-gray-700 mt-8 mb-4">Bank & Contact</h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">Bank Name</label>
                            <input type="text" name="bank_name"
                                   value="{{ old('bank_name') }}"
                                   class="p-2 border border-gray-300 rounded">
                        </div>

                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">Account Number</label>
                            <input type="text" name="account_number"
                                   value="{{ old('account_number') }}"
                                   class="p-2 border border-gray-300 rounded">
                        </div>

                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">Mobile</label>
                            <input type="text" name="contact"
                                   value="{{ old('contact') }}"
                                   class="p-2 border border-gray-300 rounded">
                        </div>

                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">What's App</label>
                            <input type="text" name="whatsapp"
                                   value="{{ old('whatsapp') }}"
                                   class="p-2 border border-gray-300 rounded">
                        </div>

                    </div>

                    <div class="mt-4">
                        <label class="mb-2 text-sm font-medium text-gray-600">Address</label>
                        <textarea name="address" rows="3"
                                  class="p-2 border border-gray-300 rounded w-full">{{ old('address') }}</textarea>
                    </div>

                    {{-- ================= LICENSE DETAILS ================= --}}
                    <h4 class="text-lg font-semibold text-gray-700 mt-8 mb-4">License Details</h4>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                        <div>
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Drug License No
                            </label>
                            <input type="text" name="drug_license_number"
                                   value="{{ old('drug_license_number') }}"
                                   class="p-2 border border-gray-300 rounded w-full">
                        </div>

                        <div>
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Drug License Expiry
                            </label>
                            <input type="date" name="drug_license_expire_date"
                                   value="{{ old('drug_license_expire_date') }}"
                                   class="p-2 border border-gray-300 rounded w-full" >
                        </div>

                        <div>
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Drug License Image
                            </label>
                            <input type="file" name="drug_license_image"
                                   class="p-2 border border-gray-300 rounded w-full">
                        </div>

                        <div>
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Trade License
                            </label>
                            <input type="text" name="trade_license"
                                   value="{{ old('trade_license') }}"
                                   class="p-2 border border-gray-300 rounded w-full" >
                        </div>

                        <div>
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Trade License Expiry
                            </label>
                            <input type="date" name="trade_license_expire_date"
                                   value="{{ old('trade_license_expire_date') }}"
                                   class="p-2 border border-gray-300 rounded w-full">
                        </div>

                        <div>
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Trade License Image
                            </label>
                            <input type="file" name="trade_license_image"
                                   class="p-2 border border-gray-300 rounded w-full">
                        </div>

                        <div>
                            <label class="mb-2 text-sm font-medium text-gray-600">TIN Number</label>
                            <input type="text" name="tin_number"
                                   value="{{ old('tin_number') }}"
                                   class="p-2 border border-gray-300 rounded w-full">
                        </div>




                        {{-- Status --}}
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status" class="p-2 border border-gray-300 rounded w-full">
                                <option value="1" selected>Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>


                    </div>

                    {{-- ================= LOGIN CREDENTIALS ================= --}}
                    <h4 class="text-lg font-semibold text-gray-700 mt-8 mb-4">Login Credentials</h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Password --}}
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   required>
                            @error('password')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Confirm Password --}}
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Confirm Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password_confirmation"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   required>
                        </div>

                    </div>


                    {{-- Submit --}}
                    <div class="mt-8 text-right">
                        <button type="submit"
                                class="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-8 rounded">
                            Save Chemist Shop
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>

@endsection
