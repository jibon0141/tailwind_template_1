@extends('admin.master')
@section('content')
    <div class="page-wrapper bg-white p-6 rounded shadow">
        <div class="content container mx-auto px-4 overflow-y-auto">

            <!-- Page Header -->
            <div class="mb-6">
                <div class="flex flex-col md:flex-row items-center justify-between mt-5">

                    <!-- Title -->
                    <h3 class="text-2xl font-semibold text-gray-800">
                        Purchase Admin List
                    </h3>

                    <!-- Add Button -->
                    <a href="{{ route('purchase.admin.create') }}"
                       class="mt-3 md:mt-0 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded shadow transition">
                        <i class="fa fa-plus"></i>
                        Create Purchase Admin
                    </a>

                </div>
            </div>

        </div>
    </div>


    <div class="mt-8 bg-white p-6 rounded shadow overflow-x-auto">
        <!-- Search Input -->
        <div class="mb-4">
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-600">Search:</label>
                <input type="text" id="searchInput"
                       class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500 w-64"
                       placeholder="Search admins...">
            </div>
        </div>

        <table id="purchaseAdminTable" class="w-full">
            <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            </thead>
        </table>
    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            var table = $('#purchaseAdminTable').DataTable({
                processing: true,
                serverSide: true,
                scrollX:true,
                autoWidth: false,
                ajax: "{{ route('purchase.admin.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'status', name: 'status', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });

            // Search on input change
            $('#searchInput').on('keyup change', function() {
                table.search(this.value).draw();
            });
        });
    </script>
@endsection
