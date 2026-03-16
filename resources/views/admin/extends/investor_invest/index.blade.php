@extends('admin.master')
@section('content')
    <div class="page-wrapper bg-gray-100 min-h-screen p-6">

        <!-- Page Header -->
        <div class="bg-white p-6 rounded shadow mb-6 flex flex-col md:flex-row items-center justify-between">
            <h3 class="text-2xl font-semibold text-gray-800">Invest List</h3>

            <a href="{{ route('admin.investor.invest.create') }}"
               class="mt-3 md:mt-0 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded shadow transition">
                <i class="fa fa-plus"></i> Create Invest
            </a>
        </div>

        <!-- Filters -->
        @include('admin.include.message')
        <div class="bg-white p-4 rounded shadow mb-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">

                <!-- Start Date -->
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date"
                           id="start_date"
                           class="h-10 w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-teal-500">
                </div>

                <!-- End Date -->
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date"
                           id="end_date"
                           class="h-10 w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-teal-500">
                </div>

                <!-- Payment Status (if needed later) -->
                {{--
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">Payment Status</label>
                    <select id="payment_status"
                            class="h-10 w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-teal-500">
                        <option value="">All Payment Status</option>
                        <option value="1">Paid</option>
                        <option value="2">Partial</option>
                    </select>
                </div>
                --}}

                <!-- Buttons -->

                <button id="filterBtn"
                        class="h-10 w-full bg-teal-500 text-white rounded hover:bg-teal-600 transition">
                    Filter
                </button>

                <button id="resetBtn"
                        class="h-10 w-full bg-gray-500 text-white rounded hover:bg-gray-600 transition">
                    Reset
                </button>

            </div>
        </div>

        <!-- DataTable Card -->
        <div class="bg-white p-6 rounded shadow overflow-x-auto">
            <table id="investorInvestTable" class="w-full table-auto text-sm">
                <thead class="bg-gray-100">
                <tr>
                    <th>#</th>
                    <th>Investor Name</th>
                    <th>Investor Code</th>
                    <th>Invest Voucher</th>
                    <th>Payment Date</th>
                    <th>Phone</th>
                    <th>Company Account</th>
                    <th>Investing Amount</th>
                    <th>Balance</th>
                    <th>Payment Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            var table = $('#investorInvestTable').DataTable({
                processing: true,
                serverSide: true,
                scrollX:true,
                autoWidth: false,
                ajax: {
                    url: "{{ route('admin.investor.invest.index') }}",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.payment_status = $('#payment_status').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'investor_name', name: 'investor_name' },
                    { data: 'investor_code', name: 'investor_code' },
                    { data: 'invest_voucher', name: 'invest_voucher' },
                    { data: 'payment_date', name: 'payment_date' },
                    { data: 'phone', name: 'phone' },
                    { data: 'company_account', name: 'company_account' },
                    { data: 'investing_amount', name: 'investing_amount' },
                    { data: 'current_total_invest', name: 'current_total_invest' },
                    { data: 'payment_status', name: 'payment_status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                order: [[1, 'asc']],

            });

            // Filter button
            $('#filterBtn').on('click', function() {
                table.ajax.reload();
            });

            // Reset button
            $('#resetBtn').on('click', function() {
                $('#start_date').val('');
                $('#end_date').val('');
                $('#payment_status').val('');
                table.ajax.reload();
            });
        });
    </script>
@endsection
