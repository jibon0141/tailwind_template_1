@extends('depo.master')
@section('content')
    <div class="page-wrapper bg-gray-100 min-h-screen p-6">

        <!-- Page Header -->
        <div class="bg-white p-6 rounded shadow mb-6 flex flex-col md:flex-row items-center justify-between">
            <h3 class="text-2xl font-semibold text-gray-800">Order List</h3>
            <a href="{{ route('depo.dashboard') }}"
               class="mt-3 md:mt-0 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded shadow transition">
                <i class="fa fa-reply"></i> Back To Dashboard
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-white p-4 rounded shadow mb-4 flex flex-col md:flex-row gap-3">
            <input type="date" id="start_date" class="p-2 border border-gray-300 rounded" placeholder="Start Date">
            <input type="date" id="end_date" class="p-2 border border-gray-300 rounded" placeholder="End Date">
            <select id="order_status" class="p-2 border border-gray-300 rounded w-48">
                <option value="">All Status</option>
                <option value="1">Pending</option>
                <option value="2">Approved</option>
                <option value="3">Delivered</option>
            </select>
            <button id="filterBtn" class="bg-teal-500 text-white px-4 py-2 rounded hover:bg-teal-600">Filter</button>
            <button id="resetBtn" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Reset</button>
        </div>

        <!-- DataTable Card -->
        <div class="bg-white p-6 rounded shadow">
            <table id="saleTable" class="w-full table-auto">
                <thead class="bg-gray-100">
                <tr>
                    <th>#</th>
                    <th>Order Voucher</th>
                    <th>Mpo Name</th>
                    <th>Order Date</th>
                    <th>Grand Total</th>
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
            var table = $('#saleTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('depo.sale.index') }}",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.order_status = $('#order_status').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'sale_voucher', name: 'sale_voucher' },
                    { data: 'mpo_name', name: 'mpo_name' },
                    { data: 'sale_date', name: 'sale_date' },
                    { data: 'final_total', name: 'final_total' },
                    { data: 'order_status', name: 'order_status' },
                    { data: 'action', orderable: false, searchable: false },
                ],
                order: [[1, 'asc']],
                responsive: true,
            });

            $('#filterBtn').on('click', function() {
                table.ajax.reload();
            });

            $('#resetBtn').on('click', function() {
                $('#start_date').val('');
                $('#end_date').val('');
                $('#order_status').val('');
                table.ajax.reload();
            });
        });
    </script>
@endsection
