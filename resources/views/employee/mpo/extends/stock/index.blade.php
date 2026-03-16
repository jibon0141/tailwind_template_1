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

        <!-- Page Header -->
        <div class="bg-white p-4 md:p-6 rounded shadow mb-6 flex flex-col md:flex-row items-center justify-between gap-3">
            <h3 class="text-xl md:text-2xl font-semibold text-gray-800 text-center md:text-left w-full md:w-auto h-11 flex items-center justify-center md:justify-start">
                Stock List
            </h3>
            <a href="{{ route('mpo.dashboard') }}"
               class="h-11 w-full md:w-auto inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 rounded shadow transition">
                <i class="fa fa-reply"></i> Back To Dashboard
            </a>
        </div>

        <!-- Stock Table -->
        <div class="bg-white rounded shadow px-5 py-2">
            <div class="relative w-full overflow-x-auto">
                <table id="stockTable" class="min-w-[900px] w-full table-auto">
                    <thead class="bg-gray-100">
                    <tr>
                        <th>#</th>
                        <th>Medicine</th>
                        <th>Generic</th>
                        <th>Brand</th>
                        <th>Current Stock</th>
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
            $('#stockTable').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true, // Horizontal scroll for small screens
                autoWidth: false, // Fix column widths
                ajax: "{{ route('mpo.stock.index') }}",
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'medicine_name', name: 'medicine.name' },
                    { data: 'generic_name', name: 'generic.name' },
                    { data: 'brand_name', name: 'brand.name' },
                    { data: 'current_stock', searchable: false },
                ],
                order: [[1, 'asc']], // Optional: order by medicine
            });
        });
    </script>
@endsection
