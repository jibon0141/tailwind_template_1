<style>
    .select2-container .select2-selection--single {
        height: 42px !important;
    }

    .select2-container--default .select2-selection--single {
        border: 1px solid #d1d5db !important;
        border-radius: 0.375rem !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 40px !important;
        padding-left: 10px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
        top: 1px !important;
    }
</style>
@extends("admin.master")
@section("content")

    <div class="min-h-screen mb-5 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4">

            @include('admin.include.message')

            <!-- Header -->
            <div class="mb-6 mt-2 pt-6 flex items-center justify-between">
                <h3 class="text-2xl font-bold text-gray-800">Edit Supplier</h3>

                <a href="{{ route('supplier.index') }}"
                   class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
                    <i class="fa fa-reply"></i> Supplier List
                </a>
            </div>

            <!-- Form Card -->
            <div class="mt-8 bg-white p-6 rounded-lg shadow">
                <form action="{{ route('supplier.update', $supplier->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Row 1: Supplier Name + Supplier Code -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- Supplier Name -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Supplier Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="supplier_name"
                                   value="{{ old('supplier_name', $supplier->supplier_name) }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="Enter supplier name">
                            @error('supplier_name')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>


                        <!-- Phone -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Phone <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="phone"
                                   value="{{ old('phone', $supplier->phone) }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="Enter phone number">
                            @error('phone')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>


                    </div>

                    <!-- Row 2: Phone + Email + Type -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">

                        <!-- Email -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Email
                            </label>
                            <input type="email" name="email"
                                   value="{{ old('email', $supplier->email) }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="Enter email">
                            @error('email')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>


                        <!-- Supplier Code (readonly) -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">Supplier Code</label>
                            <input type="text" name="supplier_code"
                                   value="{{ $supplier->supplier_code }}"
                                   class="p-2 border border-gray-300 rounded bg-gray-100 cursor-not-allowed" readonly>
                        </div>
                    </div>

                    <!-- Company -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div class="flex flex-col">
                        <label class="mb-2 text-sm font-medium text-gray-600">
                            Company
                        </label>

                        <select name="company_id"
                                id="company_id"
                                class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500 select2-container">

                            <option value="">Select Company</option>

                            @foreach($companies as $company)
                                <option value="{{ $company->id }}"
                                    {{ old('company_id', $supplier->company_id) == $company->id ? 'selected' : '' }}>
                                    {{ $company->company_name }}
                                </option>
                            @endforeach

                        </select>

                        @error('company_id')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    </div>

                    <!-- Row 3: Balance + NID + Bank -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">

                        <!-- Opening Balance -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">Balance</label>
                            <input type="number" step="0.01" name="balance"
                                   value="{{ old('balance', $supplier->balance) }}"
                                   class="p-2 border border-gray-300 rounded  bg-gray-100 cursor-not-allowed"
                                   placeholder="Enter Balance" readonly>
                            @error('balance')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>


                        <!-- NID FILE -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">NID (Upload File)</label>
                            <input type="file" name="nid"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500">
                            @if($supplier->nid)
                                <small class="text-gray-500 mt-1">Current: <a href="{{ asset('storage/'.$supplier->nid) }}" target="_blank">View NID</a></small>
                            @endif
                            @error('nid')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>



                    </div>

                    <!-- Bank (TEXTAREA) -->
                    <div class="flex flex-col mt-6">
                        <label class="mb-2 text-sm font-medium text-gray-600">Bank Account Details</label>
                        <textarea name="bank" rows="3"
                                  class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                  placeholder="Bank name, branch, account no">{{ old('bank', $supplier->bank) }}</textarea>
                        @error('bank')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Row 4: Voucher Address + Address (full width) -->
                    <div class="flex flex-col mt-6">
                        <label class="mb-2 text-sm font-medium text-gray-600">Voucher Address</label>
                        <textarea name="voucher_address" rows="3"
                                  class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                        placeholder="Voucher Address">{{ old('voucher_address', $supplier->voucher_address) }}</textarea>
                        @error('voucher_address')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex flex-col mt-6">
                        <label class="mb-2 text-sm font-medium text-gray-600">Address</label>
                        <textarea name="address" rows="3"
                                  class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                        placeholder="Address">{{ old('address', $supplier->address) }}</textarea>
                        @error('address')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Submit -->
                    <div class="mt-6 text-right">
                        <button type="submit"
                                class="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-6 rounded transition">
                            Update Supplier
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>

@endsection


@section('scripts')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {

            $('#company_id').select2({
                width: '100%',
                placeholder: "Select Company",
                allowClear: true
            });

        });
    </script>

@endsection
