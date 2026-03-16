@extends("depo.master")

<style>
    #account_select + .select2-container .select2-selection {
        height: 42px !important;
        line-height: 42px !important;
        padding: 0 10px;
        border-radius: 0.375rem;
        border: 1px solid #d1d5db;
    }
    #account_select + .select2-container .select2-selection__arrow {
        height: 42px !important;
    }
    #account_select + .select2-container .select2-selection__rendered {
        line-height: 42px !important;
    }
</style>

@section("content")

    <div class="page-wrapper bg-white p-6 rounded shadow">
        <div class="content container mx-auto px-4">

            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-semibold text-gray-800">
                    Cash Flow Ledger
                </h3>

                <a href="{{ route('depo.dashboard') }}" class="bg-teal-600 text-white px-4 py-2 rounded">
                    <i class="fa fa-reply"></i>
                    Back To Dashboard
                </a>
            </div>

            <!-- Filters -->
            <div class="bg-gray-100 p-4 rounded mb-6 grid grid-cols-1 md:grid-cols-6 gap-4">

                <select id="account_select" class="w-full"></select>

                <input type="date" id="start_date" class="border rounded p-2">
                <input type="date" id="end_date" class="border rounded p-2">

                <button id="date_filter"
                        class="bg-teal-600 text-white rounded px-4 py-2">
                    Filter
                </button>

                <button id="clear_filter"
                        class="bg-gray-600 text-white rounded px-4 py-2">
                    Clear
                </button>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table id="cashFlowTable" class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                    <tr class="uppercase text-xs text-gray-600">
                        <th>Sl</th>
                        <th>Date</th>
                        <th>Invoice</th>
                        <th>Account Name</th>
                        <th>Description</th>
                        <th class="">Debit / Out</th>
                        <th class="">Credit / In</th>
                        <th class="">Balance</th>
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

            // account select2
            $('#account_select').select2({
                placeholder: 'Select Account',
                allowClear: true,
                ajax: {
                    url: '{{ route("report.accounts.ajax") }}',
                    dataType: 'json',
                    delay: 250,
                    data: params => ({ q: params.term || '' }),
                    processResults: data => ({ results: data.results })
                }
            });

            let table = $('#cashFlowTable').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ordering: false,
                autoWidth: false,
                responsive: true,   // make table responsive instead of scroll
                ajax: {
                    url: '{{ route("report.cash-flow") }}',
                    data: d => {
                        d.account_id = $('#account_select').val();
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex' },
                    { data: 'date' },
                    { data: 'invoice_id' },
                    { data: 'account_name' },
                    { data: 'description' },
                    { data: 'debit', className: '' },
                    { data: 'credit', className: '' },
                    { data: 'balance', className: ' font-semibold' }
                ],
                drawCallback: function (settings) {
                    $('#cashFlowTable tbody tr.opening-balance').remove();

                    let opening = settings.json?.opening_balance ?? 0;

                    let row = `
            <tr class="bg-gray-100 font-semibold opening-balance">
                <td colspan="7">Opening Balance</td>
                <td class="">${parseFloat(opening).toFixed(2)}</td>
            </tr>
        `;
                    $('#cashFlowTable tbody').prepend(row);
                }
            });


            $('#date_filter').click(function () {
                if (!$('#account_select').val()) {
                    alert('Please select an account');
                    return;
                }
                table.draw();
            });

            $('#clear_filter').click(function () {
                $('#account_select').val(null).trigger('change');
                $('#start_date, #end_date').val('');
                table.clear().draw();
            });

        });
    </script>

@endsection
