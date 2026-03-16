@extends('admin.master')
@section('content')
    <div class="page-wrapper bg-gray-100 min-h-screen p-6">

        <!-- Page Header -->
        <div class="bg-white p-6 rounded shadow mb-6 flex flex-col md:flex-row items-center justify-between">
            <h3 class="text-2xl font-semibold text-gray-800">Requisition List</h3>
            <a href="{{ route('admin.requisition.create') }}"
               class="mt-3 md:mt-0 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded shadow transition">
                <i class="fa fa-plus"></i> Create Requisition
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-white p-4 rounded shadow mb-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">

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
        <div class="bg-white p-6 rounded shadow overflow-x-auto">
            <table id="requisitionTable" class="w-full table-auto">
                <thead class="bg-gray-100">
                <tr>
                    <th>#</th>
                    <th>Requisition Code</th>
                    <th>Company</th>
                    <th>Requisition Date</th>
                    <th>Total</th>
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
            var table = $('#requisitionTable').DataTable({
                processing: true,
                serverSide: true,
                scrollX:true,
                autoWidth: false,
                ajax: {
                    url: "{{ route('admin.requisition.index') }}",
                    data: function(d) {
                        d.company_name = $('#company_name').val();
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'requisition_voucher', name: 'requisition_voucher' },
                    { data: 'company_name', name: 'company_name' },
                    { data: 'requisition_date', name: 'requisition_date' },
                    { data: 'final_total', name: 'final_total' },
                    { data: 'action', orderable: false, searchable: false },
                ]
            });

            $('#filterBtn').on('click', function() {
                table.ajax.reload();
            });

            $('#resetBtn').on('click', function() {
                $('#company_name').val('');
                $('#start_date').val('');
                $('#end_date').val('');
                table.ajax.reload();
            });
        });
    </script>
@endsection
