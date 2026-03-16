@extends("admin.master")

<style>
    /* Match Select2 height with date inputs */
    #investor_select + .select2-container .select2-selection {
        height: 42px !important;
        line-height: 42px !important;
        padding: 0 10px;
        border-radius: 0.375rem;
        border: 1px solid #d1d5db;
    }
    #investor_select + .select2-container .select2-selection__arrow {
        height: 42px !important;
    }
    #investor_select + .select2-container .select2-selection__rendered {
        line-height: 42px !important;
    }
</style>

@section("content")

    <div class="page-wrapper bg-white p-6 rounded shadow">
        <div class="content container mx-auto px-4 overflow-y-auto">

            <!-- Header -->
            <div class="flex flex-col md:flex-row items-center justify-between mb-6 mt-4">
                <h3 class="text-2xl font-semibold text-gray-800">
                    Investor Ledger Report
                </h3>

                <a href="{{ route('admin.dashboard') }}"
                   class="mt-3 md:mt-0 inline-flex items-center gap-2
               bg-teal-600 hover:bg-teal-700
               text-white text-sm font-medium px-4 py-2
               rounded shadow transition">
                    <i class="fa fa-reply"></i>
                    Back to Dashboard
                </a>
            </div>

            <!-- Filter Section -->
            <div class="bg-gray-100 p-4 rounded mb-6 grid grid-cols-1 md:grid-cols-6 gap-4 items-end">

                <!-- Investor Select -->
                <div class="w-full">
                    <select id="investor_select" class="w-full border rounded p-2">
                        <option value=""></option>
                    </select>
                </div>

                <div>
                    <input type="date" id="start_date" class="border rounded p-2 w-full">
                </div>

                <div>
                    <input type="date" id="end_date" class="border rounded p-2 w-full">
                </div>

                <div>
                    <button id="date_filter"
                            class="w-full bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded shadow">
                        Filter
                    </button>
                </div>

                <div>
                    <button id="clear_filter"
                            class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded shadow">
                        Clear
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white p-6 rounded shadow overflow-x-auto">
                <table id="ledgerTable" class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                    <tr class="text-left text-gray-600 uppercase text-xs font-semibold tracking-wider">
                        <th class="px-3 py-2">Sl.</th>
                        <th class="px-3 py-2">Date</th>
                        <th class="px-3 py-2">Invoice ID</th>
                        <th class="px-3 py-2">Investor Name</th>
                        <th class="px-3 py-2">Purpose</th>
                        <th class="px-3 py-2">Voucher Amount</th>
                        <th class="px-3 py-2">Debit (Withdraw)</th>
                        <th class="px-3 py-2">Credit (Invest)</th>
                        <th class="px-3 py-2">Balance</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

        </div>
    </div>

@endsection

@section('scripts')

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {

            // Investor Select2
            $('#investor_select').select2({
                placeholder: 'Select Investor...',
                allowClear: true,
                ajax: {
                    url: '{{ route("report.investors.ajax") }}',
                    dataType: 'json',
                    delay: 250,
                    data: params => ({ q: params.term || '' }),
                    processResults: data => ({ results: data.results }),
                    cache: true
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


            // DataTable
            let table = $('#ledgerTable').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ordering: false,
                scrollX: true,
                autoWidth: false,
                ajax: {
                    url: '{{ route("report.investor.ledger") }}',
                    data: function (d) {
                        d.custom_search = $('#investor_select').val();
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex' },
                    { data: 'date' },
                    { data: 'invoice_id' },
                    { data: 'investor' },
                    { data: 'purpose' },
                    { data: 'voucher_amount' },
                    { data: 'debit' },
                    { data: 'credit' },
                    { data: 'balance', className: 'font-semibold' }
                ],
                drawCallback: function (settings) {

                    $('#ledgerTable tbody tr.opening-balance').remove();

                    let openingBalance = 0;
                    if (settings.json && settings.json.opening_balance !== undefined) {
                        openingBalance = parseFloat(settings.json.opening_balance);
                    }

                    let row = `
                <tr class="bg-gray-100 font-semibold opening-balance">
                    <td colspan="8" class="px-3 py-2 text-left">Opening Balance</td>
                    <td class="px-3 py-2">${openingBalance.toFixed(2)}</td>
                </tr>
            `;

                    $('#ledgerTable tbody').prepend(row);
                }
            });

            // Filter
            $('#date_filter').on('click', function () {
                if (!$('#investor_select').val()) {
                    alert('Please select an investor first');
                    return;
                }
                table.draw();
            });

            // Clear
            $('#clear_filter').on('click', function () {
                $('#investor_select').val(null).trigger('change');
                $('#start_date').val('');
                $('#end_date').val('');
                table.clear().draw();
            });

        });
    </script>
@endsection
