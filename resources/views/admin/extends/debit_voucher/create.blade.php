@extends('admin.master')
@section('content')

    <div class="min-h-screen bg-gray-100 p-10">
        <div class="max-w-7xl mx-auto">

            <!-- Page Header -->
            @include('admin.include.message')
            <div class="mb-6 mt-2 pt-6 flex items-center justify-between">
                <h3 class="text-2xl font-bold text-gray-800">Create Debit Voucher</h3>
                <a href="{{ route('admin.debit-voucher.index') }}" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
                    <i class="fa fa-reply"></i> Debit Voucher List
                </a>
            </div>

            {{-- Form Card --}}
            <div class="bg-white rounded-lg shadow-lg p-8">
                <form action="{{ route('admin.debit-voucher.create') }}" method="POST" id="debit-voucher-form">
                    @csrf

                    {{-- MASTER DATA --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 border-b pb-6 mb-6">
                        <div class="flex flex-col">
                            <label class="mb-2 font-semibold text-gray-700">Pay to (Party) <span class="text-red-500">*</span></label>
                            <select id="party_id" name="party_id" class="border border-gray-300 rounded p-3 focus:ring-2 focus:ring-teal-400" required></select>
                        </div>

                        <div class="flex flex-col">
                            <label class="mb-2 font-semibold text-gray-700">Payment Date <span class="text-red-500">*</span></label>
                            <input type="date" id="payment_date" name="payment_date" value="{{ date('Y-m-d') }}" class="border border-gray-300 rounded p-3 focus:ring-2 focus:ring-teal-400" required>
                        </div>

                        <div class="flex flex-col">
                            <label class="mb-2 font-semibold text-gray-700">Select Account <span class="text-red-500">*</span></label>
                            <select id="account_id" name="account_id" class="border border-gray-300 rounded p-3 focus:ring-2 focus:ring-teal-400" required></select>
                        </div>
                    </div>

                    {{-- DETAIL ITEMS --}}
                    <div class="mb-4">
                        <div class="grid grid-cols-12 gap-4 font-semibold text-gray-600 border-b pb-2 mb-2">
                            <div class="col-span-4">Select Chart of Account <span class="text-red-500">*</span></div>
                            <div class="col-span-4">Description</div>
                            <div class="col-span-3">Paid Amount <span class="text-red-500">*</span></div>
                            <div class="col-span-1 text-right">
                                <button type="button" id="add-detail-row" class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-sm">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div id="voucher-details"></div>
                    </div>

                    {{-- TOTAL --}}
                    <div class="grid grid-cols-12 gap-4 mt-4">
                        <div class="col-span-8 text-right font-bold text-lg">Total:</div>
                        <div class="col-span-3">
                            <div class="flex items-center border rounded p-2">
                                <input type="text" id="total_amount_display" class="w-full text-right font-bold text-lg border-none focus:ring-0 bg-gray-100" value="0.00" readonly>
                                <span class="ml-2 font-bold text-lg">TK</span>
                            </div>
                        </div>
                    </div>

                    {{-- SUBMIT --}}
                    <div class="mt-6 border-t pt-4 text-right">
                        <button type="submit" class="bg-teal-500 hover:bg-teal-600 text-white font-semibold px-6 py-2 rounded transition">
                            <i class="fas fa-plus-circle me-2"></i> Create New
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>

    <style>
        .select2-container { width: 100% !important; }
        .select2-selection--single {
            height: 44px !important;
            border: 1px solid #d1d5db !important;
            border-radius: 0.375rem !important;
        }
        .select2-selection__rendered {
            line-height: 44px !important;
        }
    </style>

    <script>
        $(document).ready(function () {
            let rowCount = 0;

            const partyUrl   = "{{ route('admin.debit-voucher.get-parties') }}";
            const accountUrl = "{{ route('admin.debit-voucher.get-bank-accounts') }}";
            const coaUrl     = "{{ route('admin.debit-voucher.get-expense-coa') }}";

            /* ================= PARTY ================= */
            $('#party_id').select2({
                placeholder: 'Select Party',
                allowClear: true,
                minimumInputLength: 0,
                ajax: {
                    url: partyUrl,
                    dataType: 'json',
                    delay: 250,
                    data: params => ({ q: params.term || '' }),
                    processResults: data => ({ results: data.results })
                }
            });

            /* ================= ACCOUNT ================= */
            $('#account_id').select2({
                placeholder: 'Select Account',
                allowClear: true,
                minimumInputLength: 0,
                ajax: {
                    url: accountUrl,
                    dataType: 'json',
                    delay: 250,
                    data: params => ({ q: params.term || '' }),
                    processResults: data => ({
                        results: data.results.map(item => ({
                            id: item.id,
                            text: `${item.text} (Balance: ${item.balance})`
                        }))
                    })
                }
            });

            /* ================= ADD DETAIL ROW ================= */
            function addDetailRow() {
                const rowId = rowCount++;

                const row = `
        <div class="grid grid-cols-12 gap-4 mb-3 detail-row">
            <div class="col-span-4">
                <select name="chart_of_account_id[]" class="coa-select border rounded p-2 w-full" required></select>
            </div>
            <div class="col-span-4">
                <input type="text" name="description[]" class="border rounded p-2 w-full">
            </div>
            <div class="col-span-3">
                <input type="number" step="any" min="0.01" name="paid_amount[]" class="paid-amount border rounded p-2 w-full text-right" required>
            </div>
            <div class="col-span-1 text-end">
                ${rowId > 0 ? `<button type="button" class="remove-row bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded"><i class="fa fa-times"></i></button>` : ''}
            </div>
        </div>`;

                $('#voucher-details').append(row);

                /* ================= COA SELECT2 ================= */
                $('.coa-select').last().select2({
                    placeholder: 'Select Expense',
                    minimumInputLength: 0,
                    ajax: {
                        url: coaUrl,
                        dataType: 'json',
                        delay: 250,
                        data: params => ({ q: params.term || '' }),
                        processResults: data => ({ results: data.results })
                    }
                });
            }

            addDetailRow();

            $('#add-detail-row').on('click', addDetailRow);

            $('#voucher-details').on('click', '.remove-row', function () {
                $(this).closest('.detail-row').remove();
                calculateTotal();
            });

            $('#voucher-details').on('input', '.paid-amount', calculateTotal);

            function calculateTotal() {
                let total = 0;
                $('.paid-amount').each(function () {
                    total += parseFloat($(this).val()) || 0;
                });
                $('#total_amount_display').val(total.toFixed(2));
            }
        });
    </script>
@endsection
