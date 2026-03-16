@extends('admin.master')

@section('content')
    <div class="page-wrapper bg-gray-100 min-h-screen p-6">

        <!-- Page Header -->
        <div class="bg-white p-6 rounded shadow mb-6 flex flex-col md:flex-row items-center justify-between">
            <h3 class="text-2xl font-semibold text-gray-800">Stock List</h3>
            <a href="{{ route('admin.dashboard') }}"
               class="mt-3 md:mt-0 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded shadow transition">
                <i class="fa fa-reply"></i> Back To Dashboard
            </a>
        </div>

        <!-- Stock Table -->
        <div class="bg-white p-6 rounded shadow overflow-x-auto">
            <table id="stockTable" class="w-full table-auto">
                <thead class="bg-gray-100 text-sm">
                <tr>
                    <th>#</th>
                    <th>Medicine</th>
                    <th>Generic</th>
                    <th>Brand</th>
                    <th>Buying Price</th>
                    <th>Selling Price</th>
                    <th>Total Purchase</th>
                    <th>Purchase Free Quantity</th>
                    <th>Total Sale</th>
                    <th>Sale Free Quantity</th>
                    <th>Current Stock</th>
                    <th>Stock Value</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

    </div>
@endsection

@section('scripts')
    <!-- DataTables + Buttons scripts -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <script>
        $('#stockTable').DataTable({
            processing: true,
            serverSide: true,
            scrollX:true,
            autoWidth: false,
            ajax: "{{ route('admin.stock.index') }}",
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false }, // 0
                { data: 'medicine_name', name: 'medicine_name' },              // 1
                { data: 'generic_name', name: 'generic_name' },                // 2
                { data: 'brand_name', name: 'brand_name' },                    // 3
                { data: 'buying_price', name: 'buying_price' },                // 4
                { data: 'selling_price', name: 'selling_price' },              // 5
                { data: 'total_purchase', searchable: false },                 // 6
                { data: 'purchase_free_quantity', searchable: false },         // 7
                { data: 'total_sale', searchable: false },                     // 8
                { data: 'sale_free_quantity', searchable: false },             // 9
                { data: 'current_stock', searchable: false },                  // 10
                { data: 'stock_value', searchable: false },                    // 11
            ],
            dom: '<"flex flex-wrap gap-2 mb-4"B><"mb-2"l>frtip', // l = lengthMenu dropdown
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]], // options for rows per page
            buttons: [
                {
                    extend: 'csv',
                    text: 'CSV',
                    className: 'bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow text-sm',
                    exportOptions: { columns: [1,4,5,10,11] } // only these columns
                },
                {
                    extend: 'excel',
                    text: 'Excel',
                    className: 'bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded shadow text-sm',
                    exportOptions: { columns: [1,4,5,10,11] }
                },
                {
                    extend: 'pdf',
                    text: 'PDF',
                    className: 'bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded shadow text-sm',
                    exportOptions: { columns: [1,4,5,10,11] },
                    orientation: 'landscape',
                    pageSize: 'A4',
                    customize: function (doc) {
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                        doc.styles.tableHeader.alignment = 'center';
                        doc.styles.tableBodyEven.alignment = 'center';
                        doc.styles.tableBodyOdd.alignment = 'center';
                    }
                },
                {
                    extend: 'print',
                    text: 'Print',
                    className: 'bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded shadow text-sm',
                    exportOptions: { columns: [1,4,5,10,11] },
                    customize: function (win) {
                        $(win.document.body).find('table').css({
                            'margin': '0 auto',
                            'text-align': 'center'
                        });
                    }
                }
            ],
            order: [[1, 'asc']]
        });

    </script>

@endsection


