@extends('admin.master')

{{-- ===================== STYLES ===================== --}}
<style>
    .select2-container {
        width: 100% !important;
    }

    #investor_select + .select2-container .select2-selection--single {
        height: 42px !important;
        border-radius: 0.375rem !important;
        border: 1px solid #d1d5db !important;
        background-color: #fff;
    }

    #investor_select + .select2-container .select2-selection__rendered {
        line-height: 42px !important;
        padding-left: 10px !important;
        padding-right: 45px !important;
        color: #111827;
    }

    #investor_select + .select2-container .select2-selection__arrow {
        height: 42px !important;
        top: 0;
        right: 5px;
    }

    #investor_select + .select2-container .select2-selection__clear {
        position: absolute;
        right: 30px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 18px;
        color: #9ca3af;
        cursor: pointer;
    }

    #investor_select + .select2-container .select2-selection__clear:hover {
        color: #ef4444;
    }
</style>

@section('content')
    <div class="min-h-screen mb-5 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4">

            {{-- Messages --}}
            @include('admin.include.message')

            {{-- Header --}}
            <div class="mb-6 mt-2 pt-6 flex items-center justify-between">
                <h3 class="text-2xl font-bold text-gray-800">Create New Withdraw</h3>

                <a href="{{ route('admin.investor.withdraw.index') }}"
                   class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
                    <i class="fa fa-reply"></i>
                    Withdraw List
                </a>
            </div>

            {{-- Form --}}
            <div class="mt-8 bg-white p-6 rounded-lg shadow">
                <form action="{{ route('admin.investor.withdraw.create') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Hidden Investor ID --}}
                        <input type="hidden" name="investor_id" id="investor_id">

                        {{-- Hidden Investor Name --}}
                        <input type="hidden" name="investor_name" id="investor_name">

                        {{-- Investor Select --}}
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Investor Name <span class="text-red-500">*</span>
                            </label>

                            <select id="investor_select" class="w-full" required>
                                <option></option>
                            </select>

                            @error('investor_id')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Investor Code --}}
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">Investor Code</label>
                            <input type="text" name="investor_code" id="investor_code"
                                   class="p-2 border border-gray-300 rounded bg-gray-100"
                                   readonly>
                        </div>

                        {{-- Phone --}}
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">Phone</label>
                            <input type="text" name="phone" id="phone"
                                   class="p-2 border border-gray-300 rounded bg-gray-100"
                                   readonly>
                        </div>

                        {{-- Payment Date --}}
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Payment Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="payment_date"
                                   value="{{ now()->format('Y-m-d') }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   required>
                        </div>

                        {{-- Company Account --}}
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Company Account <span class="text-red-500">*</span>
                            </label>

                            <select name="account_id"
                                    class="p-2 border rounded focus:outline-none focus:border-teal-500"
                                    required>
                                <option value="">-- Select Company Account --</option>
                                @foreach($companyAccounts as $account)
                                    <option value="{{ $account->id }}">
                                        {{ $account->account_name }} ({{ $account->balance }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Total Invest Amount --}}
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Total Invest Amount
                            </label>
                            <input type="number" name="invest_amount" id="invest_amount"
                                   class="p-2 border border-gray-300 rounded bg-gray-100"
                                   readonly>
                        </div>

                        {{-- Withdraw Amount --}}
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Withdraw Amount <span class="text-red-500">*</span>
                            </label>
                            <input type="number" step="0.01" name="withdraw_amount"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   required>
                        </div>

                    </div>

                    {{-- Submit --}}
                    <div class="mt-6 text-right">
                        <button type="submit"
                                class="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-6 rounded transition">
                            Save Withdraw
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
@endsection

{{-- ===================== SCRIPTS ===================== --}}
@section('scripts')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {

            $('#investor_select').select2({
                placeholder: 'Search Investor...',
                allowClear: true,
                ajax: {
                    url: '/admin/get-investor-data',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { query: params.term };
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(item => ({
                                id: item.id,
                                text: item.name, // text = investor name
                                investor_code: item.investor_code,
                                phone: item.contact,
                                invest_amount: item.invest_amount
                            }))
                        };
                    }
                },
                templateResult: function (data) {
                    if (!data.id) return data.text;
                    return `${data.text} (${data.investor_code})`;
                },
                templateSelection: function (data) {
                    if (!data.id) return data.text;
                    return `${data.text} (${data.investor_code})`;
                }
            });

            $('#investor_select').on('select2:select', function (e) {
                const d = e.params.data;
                $('#investor_id').val(d.id);
                $('#investor_name').val(d.text); // <-- hidden investor_name
                $('#investor_code').val(d.investor_code);
                $('#phone').val(d.phone);
                $('#invest_amount').val(d.invest_amount);
            });

            $('#investor_select').on('select2:clear', function () {
                $('#investor_id, #investor_name, #investor_code, #phone, #invest_amount').val('');
            });

        });
    </script>
@endsection
