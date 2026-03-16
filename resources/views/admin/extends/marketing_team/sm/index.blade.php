@extends('admin.master')

@section('content')
    <div class="page-wrapper bg-white p-6 rounded shadow">
        <div class="content container mx-auto px-4 overflow-y-auto">

            <!-- Page Header -->
            <div class="mb-6">
                <div class="flex flex-col md:flex-row items-center justify-between mt-5">

                    <!-- Title -->
                    <h3 class="text-2xl font-semibold text-gray-800">
                        SM List
                    </h3>

                    <!-- Add Account Button -->
                    <a href="{{ route('sm.create') }}"
                       class="mt-3 md:mt-0 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded shadow transition">
                        <i class="fa fa-plus"></i>
                        Create SM
                    </a>

                </div>
            </div>

        </div>
    </div>

    <div class="mt-8 bg-white p-6 rounded shadow overflow-x-auto">
                <table id="sm-table" class="table-auto w-full border-collapse border border-gray-200">
                    <thead>
                    <tr class="bg-gray-200 text-left">
                        <th class="border px-4 py-2">#</th>
                        <th class="border px-4 py-2">Employee Id</th>
                        <th class="border px-4 py-2">Full Name</th>
                        <th class="border px-4 py-2">Email</th>
                        <th class="border px-4 py-2">Phone</th>
                        <th class="border px-4 py-2">Division</th>
                        <th class="border px-4 py-2">District</th>
                        <th class="border px-4 py-2">Address</th>
                        <th class="border px-4 py-2">Employee Type</th>
                        <th class="border px-4 py-2">Actions</th>
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
        $(document).ready(function() {
            $('#sm-table').DataTable({
                processing: true,
                serverSide: true,
                scrollX:true,
                autoWidth: false,
                ajax: "{{ route('sm.index') }}",
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
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });
        });

        function deleteItem(id) {
            if(confirm("Are you sure you want to delete this SM?")) {
                window.location.href = "/admin/sm/delete/" + id;
            }
        }
    </script>
@endsection
