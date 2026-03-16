@extends("admin.master")
<!-- ADD THIS STYLE -->
<style>
    /* Match Select2 height with your date inputs */
    #supplier_select + .select2-container .select2-selection {
        height: 42px !important;          /* same as date inputs */
        line-height: 42px !important;     /* vertical text alignment */
        padding: 0 10px;                  /* optional padding */
        border-radius: 0.375rem;          /* match tailwind rounded */
        border: 1px solid #d1d5db;       /* same as date inputs */
    }
    #supplier_select + .select2-container .select2-selection__arrow {
        height: 42px !important;          /* make arrow same height */
    }
    #supplier_select + .select2-container .select2-selection__rendered {
        line-height: 42px !important;     /* align text vertically */
    }
</style>
@section("content")

    <div class="page-wrapper bg-white p-6 rounded shadow">
        <div class="content container mx-auto px-4 overflow-y-auto">

            <!-- Header -->
            <div class="flex flex-col md:flex-row items-center justify-between mb-6 mt-4">
                <h3 class="text-2xl font-semibold text-gray-800">
                    Supplier Ledger Report
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

                <!-- Supplier Select (ONE INPUT ONLY) -->
                <div class="w-full">
                    <select id="supplier_select" class="w-full border rounded p-2">
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
                        <th class="px-3 py-2">Supplier Name</th>
                        <th class="px-3 py-2">Purpose</th>
                        <th class="px-3 py-2">Voucher Amount</th>
                        <th class="px-3 py-2 text-left">Debit / Out</th>
                        <th class="px-3 py-2 text-right">Credit / In</th>
                        <th class="px-3 py-2 text-right">Balance</th>
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

            // SELECT2 (single input, searchable dropdown)
            $('#supplier_select').select2({
                placeholder: 'Select Supplier...',
                allowClear: true,
                minimumInputLength: 0,
                ajax: {
                    url: '{{ route("report.suppliers.ajax") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { q: params.term || '' };
                    },
                    processResults: function (data) {
                        return { results: data.results };
                    },
                    cache: true
                },

                // 👇 show name + code in dropdown
                templateResult: function (supplier) {
                    if (!supplier.id) return supplier.text;

                    return $(`
            <div class="flex justify-between">
                <span class="text-sm">${supplier.text}</span>
                <span class="text-sm">(${supplier.code ?? ''})</span>
            </div>
        `);
                },

                // 👇 show name + code when selected
                templateSelection: function (supplier) {
                    if (!supplier.id) return supplier.text;

                    return `${supplier.text} (${supplier.code ?? ''})`;
                }
            });



            // Initialize DataTable
            let table = $('#ledgerTable').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ordering: false,
                scrollX: true,
                autoWidth: false,
                ajax: {
                    url: '{{ route('report.supplier.ledger') }}',
                    data: function(d) {
                        d.custom_search = $('#supplier_select').val();
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex' },
                    { data: 'date' },
                    { data: 'invoice_id' },
                    { data: 'supplier' },
                    { data: 'purpose' },
                    { data: 'voucher_amount' },
                    { data: 'debit', className: 'text-left' },
                    { data: 'credit', className: 'text-left' },
                    { data: 'balance', className: 'text-left font-semibold' }
                ],
                drawCallback: function(settings) {
                    // Remove previous opening balance if exists
                    $('#ledgerTable tbody tr.opening-balance').remove();

                    // Get opening balance from DataTables AJAX response
                    let openingBalance = 0;
                    if (settings.json && settings.json.opening_balance !== undefined) {
                        openingBalance = parseFloat(settings.json.opening_balance);
                    }

                    // Add Opening Balance row at top, negative numbers allowed
                    let row = `<tr class="bg-gray-100 font-semibold opening-balance">
        <td colspan="8" class="px-3 py-2 text-left">Opening Balance</td>
        <td class="px-3 py-2 text-left">${openingBalance.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
    </tr>`;

                    $('#ledgerTable tbody').prepend(row);
                }

            });

            // FILTER button
            $('#date_filter').on('click', function () {
                if (!$('#supplier_select').val()) {
                    alert('Please select a supplier first');
                    return;
                }
                table.draw();
            });

            // CLEAR button
            $('#clear_filter').on('click', function () {
                $('#supplier_select').val(null).trigger('change');
                $('#start_date').val('');
                $('#end_date').val('');
                table.clear().draw();
            });

        });
    </script>
@endsection
