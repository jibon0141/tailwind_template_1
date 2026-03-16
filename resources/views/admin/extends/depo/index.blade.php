@extends('admin.master')
@section('content')
    <div class="page-wrapper bg-white p-6 rounded shadow">
        <div class="content container mx-auto px-4 overflow-y-auto">

            <!-- Page Header -->
            <div class="mb-6">
                <div class="flex flex-col md:flex-row items-center justify-between mt-5">

                    <!-- Title -->
                    <h3 class="text-2xl font-semibold text-gray-800">
                        Depo List
                    </h3>

                    <!-- Add Account Button -->
                    <a href="{{ route('depo.create') }}"
                       class="mt-3 md:mt-0 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded shadow transition">
                        <i class="fa fa-plus"></i>
                        Create Depo
                    </a>

                </div>
            </div>

        </div>
    </div>


    <div class="mt-8 bg-white p-6 rounded shadow overflow-x-auto">
        <table id="depoTable" class="w-full">
            <thead>
            <tr>
                <th>#</th>
                <th>User Name</th>
                <th>Area Code</th>
                <th>Email</th>
                <th>Depo Name</th>
                <th>Person Name</th>
                <th>Contact</th>
                <th>Address</th>
                <th>Due Balance</th>
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
            $('#depoTable').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                autoWidth: false,
                ajax: "{{ route('depo.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'user_name', name: 'user_name' },
                    { data: 'area_code', name: 'area_code' },
                    { data: 'email', name: 'email' },
                    { data: 'depo_name', name: 'depo_name' },
                    { data: 'person_name', name: 'depo_name' },
                    { data: 'contact', name: 'contact' },
                    { data: 'address', name: 'address' },
                    { data: 'due_balance', name: 'due_balance' },
                    { data: 'status', name: 'status', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });
        });


    </script>
@endsection
