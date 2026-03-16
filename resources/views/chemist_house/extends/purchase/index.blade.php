@extends('depo.master')

@section('content')
    <div class="page-wrapper bg-gray-100 min-h-screen p-6">

        <!-- Page Header -->
        <div class="bg-white p-6 rounded shadow mb-6 flex flex-col md:flex-row items-center justify-between">
            <h3 class="text-2xl font-semibold text-gray-800">Purchase List</h3>
            <a href="{{ route('depo.dashboard') }}"
               class="mt-3 md:mt-0 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded shadow transition">
                <i class="fa fa-reply"></i> Back To Dashboard
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-white p-6 rounded shadow mb-4 flex flex-col md:flex-row items-center gap-3">
            <input type="date" id="start_date" class="p-2 border border-gray-300 rounded" placeholder="Start Date">
            <input type="date" id="end_date" class="p-2 border border-gray-300 rounded" placeholder="End Date">
            <button id="filterBtn" class="bg-teal-500 text-white px-4 py-2 rounded hover:bg-teal-600">Filter</button>
            <button id="resetBtn" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Reset</button>
        </div>

        <!-- DataTable Card -->
        <div class="bg-white p-6 rounded shadow">
            <table id="purchaseTable" class="w-full table-auto">
                <thead class="bg-gray-100">
                <tr>
                    <th>#</th>
                    <th>Purchase Voucher</th>
                    <th>Distribute Date</th>
                    <th>Previous Due</th>
                    <th>Final Total</th>
                    <th>Payable Amount</th>
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
                ajax: {
                    url: "{{ route('depo.purchase.index') }}",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'distribute_voucher', name: 'distribute_voucher' },
                    { data: 'distribute_date', name: 'distribute_date' },
                    { data: 'previous_due', name: 'previous_due' },
                    { data: 'final_total', name: 'final_total' },
                    { data: 'receivable_amount', name: 'receivable_amount' },
                    { data: 'action', orderable: false, searchable: false },
                ]
            });

            // Filter button
            $('#filterBtn').on('click', function() {
                table.ajax.reload();
            });

            // Reset button
            $('#resetBtn').on('click', function() {
                $('#start_date').val('');
                $('#end_date').val('');
                table.ajax.reload();
            });

        });
    </script>
@endsection
