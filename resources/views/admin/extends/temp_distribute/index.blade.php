@extends('admin.master')
{{--@include('layout.support',['data'=>['data_table']])--}}
@section('content')
    <div class="page-wrapper bg-gray-100 min-h-screen p-6">

        <!-- Page Header -->
        <div class="bg-white p-6 rounded shadow mb-6 flex flex-col md:flex-row items-center justify-between">
            <h3 class="text-2xl font-semibold text-gray-800">Distribute List</h3>
            <a href="{{ route('admin.temp-distribute.create') }}"
               class="mt-3 md:mt-0 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded shadow transition">
                <i class="fa fa-plus"></i> Create Distribute
            </a>
        </div>


        <!-- Filters -->
        <div class="bg-white p-4 rounded shadow mb-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-6 gap-4 items-end">

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

                <!-- Order Status -->
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">Order Status</label>
                    <select id="order_status"
                            class="h-10 w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-teal-500">
                        <option value="">All Status</option>
                        <option value="1">Pending</option>
                        <option value="2">Approved</option>
                        <option value="3">Delivered</option>
                        <option value="4">Rejected</option>
                    </select>
                </div>

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
        <div class="bg-white p-6 rounded shadow">
            <table id="purchaseTable" class="w-full table-auto">
                <thead class="bg-gray-100">
                <tr>
                    <th>#</th>
                    <th>Distribute Voucher</th>
                    <th>Distribute Date</th>
                    <th>Depo</th>
                    <th>Voucher Total</th>
                    <th>Order Status</th>
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
            var table = $('#purchaseTable').DataTable({
                processing: true,
                serverSide: true,
                scrollX:true,
                autoWidth: false,
                ajax: {
                    url: "{{ route('admin.temp-distribute.index') }}",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.order_status = $('#order_status').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'distribute_voucher', name: 'distribute_voucher' },
                    { data: 'distribute_date', name: 'distribute_date' },
                    { data: 'depo_name', name: 'depo_name' },
                    { data: 'voucher_total', name: 'voucher_total' },
                    { data: 'order_status', name: 'order_status' },
                    { data: 'action', orderable: false, searchable: false },
                ],
            });

            // Filter button
            $('#filterBtn').on('click', function() {
                table.ajax.reload();
            });

            // Reset button
            $('#resetBtn').on('click', function() {
                $('#start_date').val('');
                $('#end_date').val('');
                $('#order_status').val('');
                table.ajax.reload();
            });
        });
    </script>

@endsection
