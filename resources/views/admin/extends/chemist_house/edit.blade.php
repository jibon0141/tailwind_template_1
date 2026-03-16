@extends('admin.master')
@section('content')

    <div class="min-h-screen mb-5 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4">

            {{-- Page Header --}}
            @include('admin.include.message')
            <div class="mb-6 mt-2 pt-6 flex items-center justify-between">
                <h3 class="text-2xl font-bold text-gray-800">Edit Chemist House</h3>

                <a href="{{ route('chemist.house.index') }}"
                   class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
                    <i class="fa fa-reply"></i>
                    Chemist House List
                </a>
            </div>

            {{-- Form Card --}}
            <div class="mt-8 bg-white p-6 rounded-lg shadow">
                <form action="{{ route('chemist.house.update', $chemistHouse->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- ================= BASIC INFO ================= --}}
                    <h4 class="text-lg font-semibold text-gray-700 mb-4">Basic Information</h4>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- Shop Name --}}
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Shop Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="shop_name"
                                   value="{{ old('shop_name', $chemistHouse->shop_name) }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500" required>
                            @error('shop_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- Owner Name --}}
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Owner Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="owner_name"
                                   value="{{ old('owner_name', $chemistHouse->owner_name) }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500" required>
                            @error('owner_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- Email --}}
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="email"
                                   value="{{ old('email', $chemistHouse->email) }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500" required>
                            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- ================= DEPO & MPO ================= --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">

                        {{-- Depo --}}
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Depo <span class="text-red-500">*</span>
                            </label>
                            <select name="depo_id" id="depo_id"
                                    class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                    required>
                                <option value="" disabled>Select Depo</option>
                                @foreach($depos as $depo)
                                    <option value="{{ $depo->id }}" {{ old('depo_id', $chemistHouse->depo_id) == $depo->id ? 'selected' : '' }}>
                                        {{ $depo->depo_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('depo_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- MPO --}}
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                MPO <span class="text-red-500">*</span>
                            </label>
                            <select name="mpo_id" id="mpo_id"
                                    class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                    required>
                                <option value="">Select MPO</option>

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
                                   value="{{ old('bank_name', $chemistHouse->bank_name) }}"
                                   class="p-2 border border-gray-300 rounded">
                            @error('bank_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">Account Number</label>
                            <input type="text" name="account_number"
                                   value="{{ old('account_number', $chemistHouse->account_number) }}"
                                   class="p-2 border border-gray-300 rounded">
                            @error('account_number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">Contact</label>
                            <input type="text" name="contact"
                                   value="{{ old('contact', $chemistHouse->contact) }}"
                                   class="p-2 border border-gray-300 rounded">
                            @error('contact') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">Whats App</label>
                            <input type="text" name="whatsapp"
                                   value="{{ old('whatsapp', $chemistHouse->whatsapp) }}"
                                   class="p-2 border border-gray-300 rounded">
                            @error('whatsapp') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="mb-2 text-sm font-medium text-gray-600">Address</label>
                        <textarea name="address" rows="3" class="p-2 border border-gray-300 rounded w-full">{{ old('address', $chemistHouse->address) }}</textarea>
                        @error('address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- ================= LICENSE DETAILS ================= --}}
                    <h4 class="text-lg font-semibold text-gray-700 mt-8 mb-4">License Details</h4>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="mb-2 text-sm font-medium text-gray-600">Drug License No</label>
                            <input type="text" name="drug_license_number"
                                   value="{{ old('drug_license_number', $chemistHouse->chemistHouseDetail->drug_license_number ?? '') }}"
                                   class="p-2 border border-gray-300 rounded w-full">
                            @error('drug_license_number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="mb-2 text-sm font-medium text-gray-600">Drug License Expiry</label>
                            <input type="date" name="drug_license_expire_date"
                                   value="{{ old('drug_license_expire_date', isset($chemistHouse->chemistHouseDetail->drug_license_expire_date) ? \Carbon\Carbon::parse($chemistHouse->chemistHouseDetail->drug_license_expire_date)->format('Y-m-d') : '') }}"
                                   class="p-2 border border-gray-300 rounded w-full">
                            @error('drug_license_expire_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="mb-2 text-sm font-medium text-gray-600">Drug License Image</label>
                            <input type="file" name="drug_license_image" class="p-2 border border-gray-300 rounded w-full">
                            @if(!empty($chemistHouse->chemistHouseDetail->drug_license_image))
                                <img src="{{ asset('storage/' . $chemistHouse->chemistHouseDetail->drug_license_image) }}" alt="Drug License" class="mt-2 w-32 h-20 object-cover rounded">
                            @endif
                            @error('drug_license_image') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="mb-2 text-sm font-medium text-gray-600">Trade License</label>
                            <input type="text" name="trade_license"
                                   value="{{ old('trade_license', $chemistHouse->chemistHouseDetail->trade_license ?? '') }}"
                                   class="p-2 border border-gray-300 rounded w-full">
                            @error('trade_license') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="mb-2 text-sm font-medium text-gray-600">Trade License Expiry</label>
                            <input type="date" name="trade_license_expire_date"
                                   value="{{ old('trade_license_expire_date', isset($chemistHouse->chemistHouseDetail->trade_license_expire_date) ? \Carbon\Carbon::parse($chemistHouse->chemistHouseDetail->trade_license_expire_date)->format('Y-m-d') : '') }}"
                                   class="p-2 border border-gray-300 rounded w-full">
                            @error('trade_license_expire_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="mb-2 text-sm font-medium text-gray-600">Trade License Image</label>
                            <input type="file" name="trade_license_image" class="p-2 border border-gray-300 rounded w-full">
                            @if(!empty($chemistHouse->chemistHouseDetail->trade_license_image))
                                <img src="{{ asset('storage/' . $chemistHouse->chemistHouseDetail->trade_license_image) }}" alt="Trade License" class="mt-2 w-32 h-20 object-cover rounded">
                            @endif
                            @error('trade_license_image') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="mb-2 text-sm font-medium text-gray-600">TIN Number</label>
                            <input type="text" name="tin_number"
                                   value="{{ old('tin_number', $chemistHouse->chemistHouseDetail->tin_number ?? '') }}"
                                   class="p-2 border border-gray-300 rounded w-full">
                            @error('tin_number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="mb-2 text-sm font-medium text-gray-600">Status <span class="text-red-500">*</span></label>
                            <select name="status" class="p-2 border border-gray-300 rounded w-full">
                                <option value="1" {{ old('status', $chemistHouse->status) == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status', $chemistHouse->status) == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- ================= LOGIN CREDENTIALS ================= --}}
                    <h4 class="text-lg font-semibold text-gray-700 mt-8 mb-4">Login Credentials</h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Password --}}
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Password (leave blank to keep current)
                            </label>
                            <input type="password" name="password"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500">
                            @error('password') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Confirm Password --}}
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">Confirm Password</label>
                            <input type="password" name="password_confirmation"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500">
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="mt-8 text-right">
                        <button type="submit" class="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-8 rounded">
                            Update Chemist Shop
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const depoSelect = document.getElementById('depo_id');
            const mpoSelect  = document.getElementById('mpo_id');

            // Current selected MPO id from old() or database
            const currentMpoId = "{{ old('mpo_id', $chemistHouse->mpo_id ?? '') }}";

            function loadMpos(depoId) {
                mpoSelect.innerHTML = '<option disabled>Loading...</option>';

                fetch("{{ route('chemist.house.getMpo') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ depo_id: depoId })
                })
                    .then(res => res.json())
                    .then(res => {
                        mpoSelect.innerHTML = '<option value="">Select MPO</option>';

                        if (res.status && res.data.length > 0) {
                            res.data.forEach(mpo => {
                                const option = document.createElement('option');

                                option.value = mpo.user_id;
                                option.textContent = mpo.employee_code
                                    ? `${mpo.full_name} (${mpo.employee_code})`
                                    : mpo.full_name;

                                mpoSelect.appendChild(option);
                            });

                            // ✅ SET VALUE AFTER OPTIONS EXIST
                            if (currentMpoId) {
                                mpoSelect.value = String(currentMpoId);
                            }
                        } else {
                            mpoSelect.innerHTML = '<option disabled>No MPO found</option>';
                        }
                    })

            .catch(() => {
                        mpoSelect.innerHTML = '<option disabled>Error loading MPO</option>';
                    });
            }



            // Load MPOs on page load if a Depo is already selected
            if(depoSelect.value) {
                loadMpos(depoSelect.value);
            }

            // Reload MPOs when Depo changes
            depoSelect.addEventListener('change', function () {
                loadMpos(this.value);
            });
        });
    </script>
@endsection

