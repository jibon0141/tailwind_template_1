@extends("depo.master")

<style>
    #depo_select + .select2-container .select2-selection {
        height: 42px !important;
        line-height: 42px !important;
        padding: 0 10px;
        border-radius: 0.375rem;
        border: 1px solid #d1d5db;
    }
    #depo_select + .select2-container .select2-selection__arrow {
        height: 42px !important;
    }
    #depo_select + .select2-container .select2-selection__rendered {
        line-height: 42px !important;
    }
</style>

@section("content")
    <div class="page-wrapper bg-white p-6 rounded shadow">
        <div class="container mx-auto px-4">

            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-semibold">Chemist House Ledger Report</h3>
                <a href="{{ route('depo.dashboard') }}" class="bg-teal-600 text-white px-4 py-2 rounded">
                    <i class="fa fa-reply"></i>
                    Back To Dashboard
                </a>
            </div>

            <!-- Filters -->
            <div class="bg-gray-100 p-4 rounded grid grid-cols-1 md:grid-cols-6 gap-4">
                <div>
                    <select id="depo_select" class="w-full"></select>
                </div>

                <div>
                    <input type="date" id="start_date" class="border rounded p-2 w-full">
                </div>

                <div>
                    <input type="date" id="end_date" class="border rounded p-2 w-full">
                </div>

                <div>
                    <button id="date_filter" class="bg-teal-600 text-white px-4 py-2 rounded w-full">
                        Filter
                    </button>
                </div>

                <div>
                    <button id="clear_filter" class="bg-gray-600 text-white px-4 py-2 rounded w-full">
                        Clear
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="mt-6 ">
                <table id="ledgerTable" class="min-w-full text-sm">
                    <thead>
                    <tr>
                        <th>Sl</th>
                        <th>Date</th>
                        <th>Invoice</th>
                        <th>Chemist House</th>
                        <th>Purpose</th>
                        <th>Voucher Amount</th>
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

            $('#depo_select').select2({
                placeholder: 'Select Depo...',
                allowClear: true,
                ajax: {
                    url: '{{ route("report.chemist-houses.ajax") }}',
                    dataType: 'json',
                    delay: 250,
                    data: params => ({ q: params.term || '' }),
                    processResults: data => ({ results: data.results }),
                    cache: true
                },
                templateResult: function (data) {
                    if (!data.id) return data.text;
                    return `${data.text} (${data.owner_name})`;
                },
                templateSelection: function (data) {
                    if (!data.id) return data.text;
                    return `${data.text} (${data.owner_name})`;
                }
            });


            let table = $('#ledgerTable').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ordering: false,
                ajax: {
                    url: '{{ route("depo.chemist-house-ledger-report.index") }}',
                    data: d => {
                        d.custom_search = $('#depo_select').val();
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex' },
                    { data: 'date' },
                    { data: 'invoice_id' },
                    { data: 'chemist_house' },
                    { data: 'purpose' },
                    { data: 'voucher_amount' },
                    { data: 'debit' },
                    { data: 'credit' },
                    { data: 'balance' }
                ],
                drawCallback: function (settings) {
                    $('#ledgerTable tbody tr.opening-balance').remove();

                    let opening = settings.json?.opening_balance ?? 0;

                    $('#ledgerTable tbody').prepend(`
                <tr class="opening-balance font-semibold bg-gray-100">
                    <td colspan="8">Opening Balance</td>
                    <td>${Number(opening).toFixed(2)}</td>
                </tr>
            `);
                }
            });

            $('#date_filter').click(() => {
                if (!$('#depo_select').val()) {
                    alert('Select a depo first');
                    return;
                }
                table.draw();
            });

            $('#clear_filter').click(() => {
                $('#depo_select').val(null).trigger('change');
                $('#start_date').val('');
                $('#end_date').val('');
                table.clear().draw();
            });

        });
    </script>
@endsection
