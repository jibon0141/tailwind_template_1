@extends('admin.master')

@section('content')

    <div class="page-wrapper bg-white p-6 rounded shadow">
        <div class="content container mx-auto px-4 overflow-y-auto">

            <!-- Page Header -->
            <div class="mb-6">
                <div class="flex flex-col md:flex-row items-center justify-between mt-5">

                    <!-- Title -->
                    <h3 class="text-2xl font-semibold text-gray-800">
                        Director NSM
                    </h3>

                    <!-- Add Account Button -->
                    <a href="{{ route('nsm.create') }}"
                       class="mt-3 md:mt-0 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded shadow transition">
                        <i class="fa fa-plus"></i>
                        Create NSM
                    </a>

                </div>
            </div>

        </div>
    </div>
            <!-- NSM Table -->
    <div class="mt-8 bg-white p-6 rounded shadow overflow-x-auto">
                <table id="nsmTable" class="min-w-full divide-y divide-gray-200 table-auto">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2">#</th>
                        <th class="px-4 py-2">Employee Id</th>
                        <th class="px-4 py-2">Full Name</th>
                        <th class="px-4 py-2">Email</th>
                        <th class="px-4 py-2">Phone</th>
                        <th class="px-4 py-2">Division</th>
                        <th class="px-4 py-2">District</th>
                        <th class="px-4 py-2">Employee Type</th>
                        <th class="px-4 py-2">Action</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <!-- DataTables will populate here -->
                    </tbody>
                </table>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#nsmTable').DataTable({
                processing: true,
                serverSide: true,
                scrollX:true,
                autoWidth: false,
                ajax: "{{ route('nsm.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'employee_code', name: 'employee_code' },
                    { data: 'full_name', name: 'full_name' },
                    { data: 'email', name: 'email' },
                    { data: 'phone', name: 'phone' },
                    { data: 'division', name: 'division' },
                    { data: 'district', name: 'district' },
                    { data: 'employee_type', name: 'employee_type' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });
        });


    </script>
@endsection
