@extends('employee.mpo.master')
<style>
    /* 5px gap between search/filter row and table */
    div.dataTables_wrapper div.dataTables_filter {
        margin-bottom: 10px !important;
    }

    div.dataTables_wrapper div.dataTables_length {
        margin-bottom: 5px !important;
    }

    table.dataTable {
        margin-top: 0 !important;
    }
</style>

@section('content')
    <div class="bg-gray-100 min-h-screen p-3 md:p-6">

        <!-- PAGE HEADER -->
        <div class="bg-white p-4 md:p-6 rounded shadow mb-6 flex flex-col md:flex-row items-center md:justify-between gap-3">

            <!-- Title -->
            <h3 class="text-xl md:text-2xl font-semibold text-gray-800 text-center md:text-left w-full md:w-auto h-11 flex items-center justify-center md:justify-start">
                Sales List
            </h3>

            <!-- Button -->
            <a href="{{ route('mpo.sale.create') }}"
               class="h-11 w-full md:w-auto inline-flex justify-center items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 rounded shadow transition">
                <i class="fa fa-plus"></i> Create Sale
            </a>
        </div>

        <!-- FILTERS -->
        <!-- FILTERS -->
        <div class="bg-white p-4 rounded shadow mb-6">
            <div class="flex flex-col md:flex-row items-stretch md:items-end gap-3">

                <!-- Start Date -->
                <div class="flex flex-col">
                    <label class="text-md font-medium mb-1">Start Date</label>
                    <input type="date"
                           id="start_date"
                           class="h-11 w-full md:w-auto p-2 border border-gray-300 rounded text-md">
                </div>

                <!-- End Date -->
                <div class="flex flex-col">
                    <label class="text-md font-medium mb-1">End Date</label>
                    <input type="date"
                           id="end_date"
                           class="h-11 w-full md:w-auto p-2 border border-gray-300 rounded text-md">
                </div>

                <!-- Status -->
                <div class="flex flex-col">
                    <label class="text-md font-medium mb-1">Status</label>
                    <select id="order_status"
                            class="h-11 w-full md:w-48 p-2 border border-gray-300 rounded text-md">
                        <option value="">All Status</option>
                        <option value="1">Pending</option>
                        <option value="2">Approved</option>
                        <option value="3">Rejected</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="flex gap-2 md:self-end w-full md:w-auto">
                    <button id="filterBtn"
                            class="h-11 w-full md:w-auto bg-teal-500 text-white px-4 rounded hover:bg-teal-600 text-md">
                        Filter
                    </button>

                    <button id="resetBtn"
                            class="h-11 w-full md:w-auto bg-gray-500 text-white px-4 rounded hover:bg-gray-600 text-md">
                        Reset
                    </button>
                </div>

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
                        <th>Chemist House</th>
                        <th>Final Total</th>
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
                autoWidth: false,
                ajax: {
                    url: "{{ route('mpo.sale.index') }}",
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
                    { data: 'shop_name' },
                    { data: 'final_total' },
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
