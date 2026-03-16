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
    <div class="page-wrapper bg-white p-6 rounded shadow">
        <div class="content container mx-auto px-4 overflow-y-auto">

            <!-- Page Header -->
            <div class="mb-6 flex flex-col md:flex-row items-center justify-between gap-4 mt-5">
                <h3 class="text-2xl font-semibold text-gray-800">
                    Chemist House List
                </h3>

                <!-- Button -->
                <a href="{{ route('mpo.chemist-house.create') }}"
                   class="h-11 w-full md:w-auto inline-flex justify-center items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 rounded shadow transition">
                    <i class="fa fa-plus"></i> Create Chemist House
                </a>
            </div>

        </div>
    </div>

    <!-- Table -->
    <div class="mt-8 bg-white p-6 rounded shadow">
        <div class="relative overflow-x-auto w-full">
            <table id="chemistHouseTable"
                   class="min-w-[1200px] table-auto border border-gray-200">
                <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="">#</th>
                    <th class="">Shop Name</th>
                    <th class="">Owner Name</th>
                    <th class="">Mobile</th>
                    <th class="">Receivable Amount</th>
                    <th class="">Address</th>
                    <th class="">Status</th>
                    <th class="">Action</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#chemistHouseTable').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                autoWidth: false,
                ajax: "{{ route('mpo.chemist-house.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'shop_name', name: 'shop_name' },
                    { data: 'owner_name', name: 'owner_name' },
                    { data: 'contact', name: 'contact' },
                    { data: 'receivable_amount', name: 'receivable_amount' },
                    { data: 'address', name: 'address' },
                    { data: 'status', name: 'status', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                order: [[1, 'asc']],
                searching: true // Enable default DataTables search bar
            });
        });
    </script>
@endsection
