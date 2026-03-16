@extends('admin.master')

@section('content')
    <div class="bg-gray-100 min-h-screen p-3 md:p-6">

        <!-- PAGE HEADER -->
        <div class="bg-white p-4 md:p-6 rounded shadow mb-6 flex flex-col md:flex-row items-center md:justify-between gap-3">

            <!-- Title -->
            <h3 class="text-xl md:text-2xl font-semibold text-gray-800 text-center md:text-left w-full md:w-auto h-11 flex items-center justify-center md:justify-start">
                Depo Sale Report
            </h3>


        </div>

        <!-- FILTERS -->
        <div class="bg-white p-4 rounded shadow mb-6">
            <div class="flex flex-col md:flex-row items-stretch md:items-center gap-3">

                <input type="date"
                       id="start_date"
                       class="h-11 w-full md:w-auto p-2 border border-gray-300 rounded">

                <input type="date"
                       id="end_date"
                       class="h-11 w-full md:w-auto p-2 border border-gray-300 rounded">

                <select id="order_status"
                        class="h-11 w-full md:w-48 p-2 border border-gray-300 rounded">
                    <option value="">All Status</option>
                    <option value="1">Pending</option>
                    <option value="2">Approved</option>
                    <option value="3">Rejected</option>
                </select>

                <button id="filterBtn"
                        class="h-11 w-full md:w-auto bg-teal-500 text-white px-4 rounded hover:bg-teal-600">
                    Filter
                </button>

                <button id="resetBtn"
                        class="h-11 w-full md:w-auto bg-gray-500 text-white px-4 rounded hover:bg-gray-600">
                    Reset
                </button>

            </div>
        </div>



        <!-- TABLE CARD (ONLY THIS SCROLLS) -->
        <div class="bg-white rounded shadow px-5 py-2">
            <div class="relative w-full overflow-x-auto">
                <table id="saleTable"
                       class="min-w-[900px] w-full table-auto">
                    <thead class="bg-gray-100">
                    <tr>
                        <th>#</th>
                        <th>Sale Voucher</th>
                        <th>Sale Date</th>
                        <th>Depo Name</th>
                        <th>Chemist House</th>
                        <th>Mpo Name</th>
                        <th>Final Total</th>
                        <th>Total Profit</th>
                        <th>Order Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

    </div>
@endsection


@section('scripts')
    <script>
        $(document).ready(function () {

            var table = $('#saleTable').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                autoWidth: false, // IMPORTANT
                ajax: {
                    url: "{{ route('report.depo.profit') }}",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.order_status = $('#order_status').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'sale_voucher' },
                    { data: 'sale_date' },
                    { data: 'depo_name' },
                    { data: 'chemist_house' },
                    { data: 'mpo_name' },
                    { data: 'final_total' },
                    { data: 'total_profit' },
                    { data: 'order_status' },
                    { data: 'action', orderable: false, searchable: false }
                ],
                order: [[2, 'desc']]
            });

            $('#filterBtn').click(() => table.ajax.reload());
            $('#resetBtn').click(() => {
                $('#start_date, #end_date').val('');
                $('#order_status').val('');
                table.ajax.reload();
            });

        });
    </script>

@endsection
