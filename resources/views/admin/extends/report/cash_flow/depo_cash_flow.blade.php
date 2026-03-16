@extends("admin.master")

<style>
    /* Depo dropdown styling */
    #depo_select + .select2-container .select2-selection,
    #account_select + .select2-container .select2-selection {
        height: 42px !important;
        line-height: 42px !important;
        padding: 0 10px;
        border-radius: 0.375rem;
        border: 1px solid #d1d5db;
    }
    #depo_select + .select2-container .select2-selection__arrow,
    #account_select + .select2-container .select2-selection__arrow {
        height: 42px !important;
    }
    #depo_select + .select2-container .select2-selection__rendered,
    #account_select + .select2-container .select2-selection__rendered {
        line-height: 42px !important;
    }
</style>

@section("content")

    <div class="page-wrapper bg-white p-6 rounded shadow">
        <div class="content container mx-auto px-4">

            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-semibold text-gray-800">Depo Cash Flow Ledger</h3>
                <a href="{{ route('admin.dashboard') }}" class="bg-teal-600 text-white px-4 py-2 rounded">
                    <i class="fa fa-reply"></i>
                    Back To Dashboard
                </a>
            </div>

            <!-- Filters -->
            <div class="bg-gray-100 p-4 rounded mb-6 grid grid-cols-1 md:grid-cols-6 gap-4">
                <select id="depo_select" class="w-full"></select>
                <select id="account_select" class="w-full"></select>
                <input type="date" id="start_date" class="border rounded p-2">
                <input type="date" id="end_date" class="border rounded p-2">

                <button id="date_filter" class="bg-teal-600 text-white rounded px-4 py-2">Filter</button>
                <button id="clear_filter" class="bg-gray-600 text-white rounded px-4 py-2">Clear</button>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table id="depoCashFlowTable" class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                    <tr class="uppercase text-xs text-gray-600">
                        <th>Sl</th>
                        <th>Date</th>
                        <th>Invoice</th>
                        <th>Depo</th>
                        <th>Account Name</th>
                        <th>Description</th>
                        <th>Debit / Out</th>
                        <th>Credit / In</th>
                        <th>Balance</th>
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
        $(function () {

            // Depo dropdown
            $('#depo_select').select2({
                placeholder: 'Select Depo',
                allowClear: true,
                ajax: {
                    url: '{{ route("report.depos.ajax") }}',
                    dataType: 'json',
                    delay: 250,
                    processResults: data => ({ results: data.results })
                }
            });

            // Account dropdown depends on selected Depo
            function loadAccounts(depoId) {
                $('#account_select').select2({
                    placeholder: 'Select Account',
                    allowClear: true,
                    ajax: {
                        url: '{{ route("report.depo.accounts.ajax") }}',
                        dataType: 'json',
                        delay: 250,
                        data: params => ({
                            q: params.term || '',
                            depo_id: depoId
                        }),
                        processResults: data => ({ results: data.results })
                    }
                });
            }

            $('#depo_select').on('change', function () {
                let depoId = $(this).val();
                $('#account_select').val(null).trigger('change');
                loadAccounts(depoId);
            });

            // Initialize DataTable
            let table = $('#depoCashFlowTable').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ordering: false,
                autoWidth: false,
                responsive: true,
                ajax: {
                    url: '{{ route("report.depo.cash-flow") }}',
                    data: d => {
                        d.depo_id = $('#depo_select').val();
                        d.account_id = $('#account_select').val();
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex' },
                    { data: 'date' },
                    { data: 'invoice_id' },
                    { data: 'depo_name' },
                    { data: 'account_name' },
                    { data: 'description' },
                    { data: 'debit', className: '' },
                    { data: 'credit', className: '' },
                    { data: 'balance', className: 'font-semibold' }
                ],
                drawCallback: function (settings) {
                    $('#depoCashFlowTable tbody tr.opening-balance').remove();

                    let opening = settings.json?.opening_balance ?? 0;
                    let row = `
            <tr class="bg-gray-100 font-semibold opening-balance">
                <td colspan="8">Opening Balance</td>
                <td>${parseFloat(opening).toFixed(2)}</td>
            </tr>`;
                    $('#depoCashFlowTable tbody').prepend(row);
                }
            });

            // Filter button
            $('#date_filter').click(function () {
                if (!$('#depo_select').val()) {
                    alert('Please select a depo');
                    return;
                }
                table.draw();
            });

            // Clear filters
            $('#clear_filter').click(function () {
                $('#depo_select, #account_select').val(null).trigger('change');
                $('#start_date, #end_date').val('');
                table.clear().draw();
            });

        });
    </script>
@endsection
