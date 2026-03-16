@extends('admin.master')

@section('content')
    <div class="page-wrapper bg-white p-6 rounded shadow">
        <div class="content container mx-auto px-4 overflow-y-auto">

            <!-- Page Header -->
            <div class="mb-6">
                <div class="flex flex-col md:flex-row items-center justify-between mt-5">

                    <!-- Title -->
                    <h3 class="text-2xl font-semibold text-gray-800">
                        RSM List
                    </h3>

                    <!-- Add Account Button -->
                    <a href="{{ route('rsm.create') }}"
                       class="mt-3 md:mt-0 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded shadow transition">
                        <i class="fa fa-plus"></i>
                        Create RSM
                    </a>

                </div>
            </div>

        </div>
    </div>


    <!-- DataTable -->
    <div class="mt-8 bg-white p-6 rounded shadow overflow-x-auto">
                <table id="rsm-table" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">#</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Employee Id</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Full Name</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Email</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Phone</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Division</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">District</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Address</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Type</th>
                        <th class="px-4 py-2 text-center text-sm font-medium text-gray-700">Action</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Data populated by AJAX -->
                    </tbody>
                </table>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#rsm-table').DataTable({
                processing: true,
                serverSide: true,
                scrollX:true,
                autoWidth: false,
                ajax: '{{ route("rsm.index") }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'employee_code', name: 'employee_code' },
                    { data: 'full_name', name: 'full_name' },
                    { data: 'email', name: 'email' },
                    { data: 'phone', name: 'phone' },
                    { data: 'division', name: 'division' },
                    { data: 'district', name: 'district' },
                    { data: 'address', name: 'address' },
                    { data: 'employee_type', name: 'employee_type' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
                ]
            });
        });

        function deleteItem(id) {
            if(confirm('Are you sure you want to delete this RSM?')) {
                window.location.href = '/admin/rsm/delete/' + id;
            }
        }
    </script>
@endsection
