@extends("depo.master")
@section("content")

    <div class="min-h-screen mb-5 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4">

            <!-- Page Header -->
            @include('admin.include.message')
            <div class="mb-6 mt-2 pt-6 flex items-center justify-between">
                <h3 class="text-2xl font-bold text-gray-800">
                    Edit Supplier
                </h3>

                <a href="{{ route('depo.supplier.index') }}"
                   class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
                    <i class="fa fa-reply"></i>
                    Supplier List
                </a>
            </div>

            <!-- Form Card -->
            <div class="mt-8 bg-white p-6 rounded-lg shadow">
                <form action="{{ route('depo.supplier.update', $supplier->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- Supplier Name -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Supplier Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="supplier_name" value="{{ old('supplier_name', $supplier->supplier_name) }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="Enter supplier name">
                            @error('supplier_name')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Supplier Code (readonly) -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Supplier Code
                            </label>
                            <input type="text" name="supplier_code" value="{{ $supplier->supplier_code }}"
                                   class="p-2 border border-gray-300 rounded bg-gray-100 cursor-not-allowed"
                                   readonly>
                        </div>

                        <!-- Phone -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Phone <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="phone" value="{{ old('phone', $supplier->phone) }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="Enter phone number">
                            @error('phone')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Email <span class="text-red-500"></span>
                            </label>
                            <input type="email" name="email" value="{{ old('email', $supplier->email) }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="Enter email">
                            @error('email')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="flex flex-col md:col-span-2">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Address
                            </label>
                            <textarea name="address" rows="3"
                                      class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                      placeholder="Enter address">{{ old('address', $supplier->address) }}</textarea>
                            @error('address')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Balance -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Opening Payable Due <span class="text-red-500">*</span>
                            </label>
                            <input type="number" step="0.01" name="balance" value="{{ old('balance', $supplier->balance) }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="Enter balance">
                            @error('balance')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

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
