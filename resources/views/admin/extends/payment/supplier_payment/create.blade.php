@extends('admin.master')

<!-- ADD THIS STYLE -->
<style>
    /* Match Select2 height with your other inputs */
    #supplier_select + .select2-container .select2-selection {
        height: 42px !important;
        line-height: 42px !important;
        padding: 0 10px;
        border-radius: 0.375rem;
        border: 1px solid #d1d5db;
    }
    #supplier_select + .select2-container .select2-selection__arrow {
        height: 42px !important;
    }
    #supplier_select + .select2-container .select2-selection__rendered {
        line-height: 42px !important;
    }
</style>

@section('content')

    <div class="min-h-screen mb-5 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4">

            <!-- Page Header -->
            @include('admin.include.message')
            <div class="mb-6 mt-2 pt-6 flex flex-col items-center md:flex-row md:justify-between gap-5">
                <h3 class="text-2xl font-bold text-gray-800">
                    Create New Payment
                </h3>

                <a href="{{ route('admin.supplier-due-payment.index') }}"
                   class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
                    <i class="fa fa-reply"></i>
                    Payment List
                </a>
            </div>

            <div class="mt-8 bg-white p-6 rounded-lg shadow">
                <form action="{{ route('admin.supplier-due-payment.create') }}" method="POST">
                    @csrf

                    <input type="hidden" name="form_token" value="{{ session('supplier_payment_form_token') }}">
                    <input type="hidden" name="supplier_id" id="supplier_id">
                    <input type="hidden" name="supplier_name" id="hidden_supplier_name"> {{-- HIDDEN NAME --}}

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- Supplier Name (Select2) -->
                        <div class="flex flex-col relative">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Supplier Name <span class="text-red-500">*</span>
                            </label>
                            <select id="supplier_select" style="width: 100%;">
                                <option value="">Select Supplier...</option>
                            </select>
                            @error('supplier_name')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Supplier Code -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Supplier Code
                            </label>
                            <input type="text" name="supplier_code" id="supplier_code"
                                   value="{{ old('supplier_code') }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="Supplier Code" readonly>
                        </div>

                        <!-- Supplier Phone -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Supplier Phone
                            </label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="Supplier Phone" readonly>
                        </div>

                        <!-- Payment Date -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Payment Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="payment_date" id="payment_date"
                                   value="{{ old('payment_date', now()->format('Y-m-d')) }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500">
                            @error('payment_date')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Company Account -->
                        <div class="flex flex-col mb-4">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Company Account <span class="text-red-500">*</span>
                            </label>
                            <select name="account_id"
                                    class="p-2 border rounded focus:outline-none focus:border-teal-500 @error('account_id') border-red-500 @enderror">
                                <option value="">-- Select Company Account --</option>
                                @foreach($companyAccounts as $account)
                                    <option value="{{ $account->id }}"
                                        {{ old('account_id') == $account->id ? 'selected' : '' }}>
                                        {{ $account->account_name }} ({{ $account->balance }})
                                    </option>
                                @endforeach
                            </select>
                            @error('account_id')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Supplier Payable -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Amount
                            </label>
                            <input type="number" step="0.01" id="balance" name="balance" value="{{ old('balance') }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="Payable Balance" readonly>
                            @error('mrp')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Paying Amount -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Paying Amount <span class="text-red-500">*</span>
                            </label>
                            <input type="number" step="0.01" name="paying_amount"
                                   value="{{ old('paying_amount') }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="Enter Paying Amount">
                            @error('paying_amount')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Refund Amount -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Refund Amount <span class="text-red-500">*</span>
                            </label>
                            <input type="number" step="0.01" name="refund_amount"
                                   value="{{ old('refund_amount') }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="Enter Refund Amount">
                            @error('refund_amount')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>

                    <!-- Submit -->
                    <div class="mt-6 text-right">
                        <button type="submit" id="savePaymentBtn"
                                class="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-6 rounded transition">
                            Save Payment
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
        $(document).ready(function() {
            // Initialize Select2 for supplier
            $('#supplier_select').select2({
                placeholder: 'Select Supplier...',
                allowClear: true,
                ajax: {
                    url: '/admin/getSupplierData', // your existing AJAX route
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return { query: params.term || '' };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(s => ({
                                id: s.id,
                                text: s.supplier_name,
                                code: s.supplier_code,
                                phone: s.phone,
                                balance: s.balance
                            }))
                        };
                    },
                    cache: true
                },
                templateResult: function(supplier) {
                    if (!supplier.id) return supplier.text;
                    return $('<div class="flex justify-between"><span>' + supplier.text + '</span><span>(' + (supplier.code || '') + ')</span></div>');
                },
                templateSelection: function(supplier) {
                    if (!supplier.id) return supplier.text;
                    return supplier.text + ' (' + (supplier.code || '') + ')';
                }
            });

            // Auto-fill fields when a supplier is selected
            $('#supplier_select').on('select2:select', function(e) {
                const supplier = e.params.data;
                $('#supplier_id').val(supplier.id || '');
                $('#hidden_supplier_name').val(supplier.text || ''); // hidden supplier_name
                $('#supplier_code').val(supplier.code || '');
                $('#phone').val(supplier.phone || '');
                $('#balance').val(supplier.balance || '');
            });

            // Clear fields when select2 cleared
            $('#supplier_select').on('select2:clear', function() {
                $('#supplier_id').val('');
                $('#hidden_supplier_name').val(''); // clear hidden name
                $('#supplier_code').val('');
                $('#phone').val('');
                $('#balance').val('');
            });

            // Hide submit button on submit
            $('form').on('submit', function() {
                $('#savePaymentBtn').hide();
            });
        });
    </script>
@endsection
