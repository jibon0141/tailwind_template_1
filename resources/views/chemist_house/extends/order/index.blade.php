@extends('chemist_house.master')

@section('content')

    <div class="page-wrapper bg-gray-100 min-h-screen p-6">

        <!-- Page Header -->
        <div class="bg-white p-6 rounded shadow mb-6 flex flex-col md:flex-row items-center justify-between">
            <h3 class="text-2xl font-semibold text-gray-800">Order List</h3>

            <a href="{{ route('chemist.house.medicine.list') }}"
               class="mt-3 md:mt-0 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded shadow transition">
                <i class="fa fa-plus"></i> Create Order
            </a>
        </div>

        <!-- DataTable Card -->
        <div class="bg-white p-6 rounded shadow overflow-x-auto">

            <table id="saleTable" class="min-w-full border border-gray-200">
                <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-4 py-2 border">#</th>
                    <th class="px-4 py-2 border">Sale Voucher</th>
                    <th class="px-4 py-2 border">Mpo Name</th>
                    <th class="px-4 py-2 border">Sale Date</th>
                    <th class="px-4 py-2 border">Grand Total</th>
                    <th class="px-4 py-2 border">Order Status</th>
                    <th class="px-4 py-2 border">Action</th>
                </tr>
                </thead>

                <tbody></tbody>
            </table>

        </div>

    </div>

@endsection


@section('scripts')

    <style>
        #saleTable{
            width:100% !important;
        }
    </style>

    <script>

        $(document).ready(function () {

            var table = $('#saleTable').DataTable({

                processing: true,
                serverSide: true,
                scrollX: true,
                responsive: false,
                autoWidth: false,

                ajax: {
                    url: "{{ route('chemist.order.index') }}",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.order_status = $('#order_status').val();
                    }
                },

                columns: [

                    {data: 'DT_RowIndex', orderable:false, searchable:false},

                    {data: 'sale_voucher', name:'sale_voucher'},

                    {data: 'mpo_name', name:'mpo_name'},

                    {data: 'sale_date', name:'sale_date'},

                    {data: 'final_total', name:'final_total'},

                    {data: 'order_status', name:'order_status'},

                    {data: 'action', orderable:false, searchable:false},

                ],

                order: [[1,'asc']],

                drawCallback:function(){
                    table.columns.adjust();
                }

            });


            $('#filterBtn').on('click', function () {
                table.ajax.reload();
            });


            $('#resetBtn').on('click', function () {

                $('#start_date').val('');
                $('#end_date').val('');
                $('#order_status').val('');

                table.ajax.reload();
            });

        });

    </script>

@endsection
