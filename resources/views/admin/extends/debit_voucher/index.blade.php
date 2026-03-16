@extends("admin.master")
@section("content")
    <div class="page-wrapper bg-white p-6 rounded shadow">
        <div class="content container mx-auto px-4 overflow-y-auto">
            <!-- Page Header -->
            <div class="mb-6">
                <div class="flex flex-col md:flex-row items-center justify-between mt-5">
                    <!-- Title -->
                    <h3 class="text-2xl font-semibold text-gray-800">
                        Debit Voucher List
                    </h3>

                    <!-- Create Button -->
                    <a href="{{ route('admin.debit-voucher.create') }}"
                       class="mt-3 md:mt-0 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded shadow transition">
                        <i class="fa fa-plus"></i>
                        Create Debit Voucher
                    </a>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="bg-gray-100 p-4 rounded mb-6 grid grid-cols-1 md:grid-cols-6 gap-4">
                <input type="text" id="global_search" placeholder="Search Debit Vouchers..." class="border rounded p-2 w-full">
                <input type="date" id="start_date" class="border rounded p-2 w-full">
                <input type="date" id="end_date" class="border rounded p-2 w-full">
                <button id="date_filter" class="bg-teal-500 hover:bg-teal-600 text-white px-4 py-2 rounded shadow">Filter by Date</button>
                <button id="clear_filter" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded shadow">Clear</button>
            </div>

            <!-- DataTable -->
            <div class="bg-white p-6 rounded shadow overflow-x-auto">
                <table id="voucherTable" class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                    <tr class="text-left text-gray-600 uppercase text-xs font-semibold tracking-wider">
                        <th class="px-4 py-2">#</th>
                        <th class="px-4 py-2">Voucher No</th>
                        <th class="px-4 py-2">Payment Date</th>
                        <th class="px-4 py-2">Party</th>
                        <th class="px-4 py-2">Account</th>
                        <th class="px-4 py-2">Total Amount</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200"></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            let table = $('#voucherTable').DataTable({
                processing: true,
                serverSide: true,
                scrollX:true,
                autoWidth: false,
                ajax: {
                    url: '{{ route("admin.debit-voucher.index") }}',
                    data: function(d) {
                        // For custom search box (not DataTables built-in search)
                        d.custom_search = $('#global_search').val();
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                    }
                },
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'debit_voucher',
                        name: 'debit_voucher'
                    },
                    {
                        data: 'payment_date',
                        name: 'payment_date'
                    },
                    {
                        data: 'party',
                        name: 'party.party_name',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'account',
                        name: 'account.account_name',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'total_amount',
                        name: 'total_amount'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [[1, 'desc']]
            });

            // Custom search input
            let searchTimeout;
            $('#global_search').on('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    table.draw();
                }, 500);
            });

            // Date filter button
            $('#date_filter').on('click', function() {
                table.draw();
            });

            // Clear filters button
            $('#clear_filter').on('click', function() {
                $('#global_search').val('');
                $('#start_date').val('');
                $('#end_date').val('');
                table.draw();
            });
        });
    </script>
@endsection
