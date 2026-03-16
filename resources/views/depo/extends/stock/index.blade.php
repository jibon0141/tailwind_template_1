@extends('depo.master')

@section('content')
    <div class="page-wrapper bg-gray-100 min-h-screen p-6">

        <!-- Page Header -->
        <div class="bg-white p-6 rounded shadow mb-6 flex flex-col md:flex-row items-center justify-between">
            <h3 class="text-2xl font-semibold text-gray-800">Stock List</h3>
            <a href="{{ route('depo.dashboard') }}"
               class="mt-3 md:mt-0 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded shadow transition">
                <i class="fa fa-reply"></i> Back To Dashboard
            </a>
        </div>

        <!-- Stock Table -->
        <div class="bg-white p-6 rounded shadow">
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
    <script>
        $(document).ready(function () {
            $('#stockTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('depo.stock.index') }}",
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'medicine_name', name: 'medicine.name' },
                    { data: 'generic_name', name: 'generic.name' },
                    { data: 'brand_name', name: 'brand.name' },
                    { data: 'buying_price', name: 'buying_price' },
                    { data: 'selling_price', name: 'selling_price' },
                    { data: 'total_purchase', searchable: false },
                    { data: 'purchase_free_quantity', searchable: false },
                    { data: 'total_sale', searchable: false },
                    { data: 'sale_free_quantity', searchable: false },
                    { data: 'current_stock', searchable: false },
                    { data: 'stock_value', searchable: false },
                ]
            });
        });
    </script>
@endsection
