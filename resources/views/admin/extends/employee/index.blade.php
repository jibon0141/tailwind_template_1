@extends('admin.master')

@section('content')

    <div class="page-wrapper bg-white p-6 rounded shadow">
        <div class="content container mx-auto px-4 overflow-y-auto">

            <!-- Page Header -->
            <div class="mb-6">
                <div class="flex flex-col md:flex-row items-center justify-between mt-5">

                    <!-- Title -->
                    <h3 class="text-2xl font-semibold text-gray-800">
                        Employee List
                    </h3>

                    <!-- Add Account Button -->
                    <a href="{{ route('employee.create') }}"
                       class="mt-3 md:mt-0 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded shadow transition">
                        <i class="fa fa-plus"></i>
                        Create Employee
                    </a>

                </div>
            </div>

        </div>
    </div>



            <div class="mt-8 bg-white p-6 rounded shadow">
                <table id="employeeTable" class="w-full table-auto border border-collapse">
                    <thead>
                    <tr class="bg-gray-200">
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Division</th>
                        <th>District</th>
                        <th>Address</th>
                        <th>Type</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>


@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#employeeTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('employee.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    { data: 'full_name', name: 'full_name' },
                    { data: 'email', name: 'email' },
                    { data: 'phone', name: 'phone' },
                    { data: 'division', name: 'division' },
                    { data: 'district', name: 'district' },
                    { data: 'address', name: 'address' },
                    { data: 'employee_type', name: 'employee_type' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[0, 'asc']]
            });
        });
    </script>
@endsection
