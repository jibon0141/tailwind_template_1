@extends('admin.master')
@section('content')
    <div class="page-wrapper bg-white p-6 rounded shadow">
        <div class="content container mx-auto px-4 overflow-y-auto">

            <!-- Page Header -->
            <div class="mb-6">
                <div class="flex flex-col md:flex-row items-center justify-between mt-5">

                    <!-- Title -->
                    <h3 class="text-2xl font-semibold text-gray-800">
                        Chemist House List
                    </h3>

                    <!-- Create Button -->
                    <a href="{{ route('chemist.house.create') }}"
                       class="mt-4 md:mt-0 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded shadow transition">
                        <i class="fa fa-plus"></i>
                        Create Chemist House
                    </a>

                </div>
            </div>

        </div>
    </div>

    <!-- Table -->
    <div class="mt-8 bg-white p-6 rounded shadow overflow-x-auto">
        <table id="chemistHouseTable" class="min-w-full table-auto border border-gray-200">
            <thead class="bg-gray-100 text-gray-700">
            <tr>
                <th class="px-4 py-2 text-left">#</th>
                <th class="px-4 py-2 text-left">Shop Name</th>
                <th class="px-4 py-2 text-left">Owner Name</th>
                <th class="px-4 py-2 text-left">Depo Name</th>
                <th class="px-4 py-2 text-left">Mpo Name</th>
                <th class="px-4 py-2 text-left">Bank Name</th>
                <th class="px-4 py-2 text-left">Account Number</th>
                <th class="px-4 py-2 text-left">Mobile</th>
                <th class="px-4 py-2 text-left">Whats App</th>
                <th class="px-4 py-2 text-left">Receivable Amount</th>
                <th class="px-4 py-2 text-left">Address</th>
                <th class="px-4 py-2 text-left">Status</th>
                <th class="px-4 py-2 text-left">Action</th>
            </tr>
            </thead>
        </table>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#chemistHouseTable').DataTable({
                processing: true,
                serverSide: true,
                scrollX:true,
                autoWidth: false,
                ajax: "{{ route('chemist.house.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'shop_name', name: 'shop_name' },
                    { data: 'owner_name', name: 'owner_name' },
                    { data: 'depo_name', name: 'depo.depo_name' },
                    { data: 'mpo_name', name: 'mpo_name' },
                    { data: 'bank_name', name: 'bank_name' },
                    { data: 'account_number', name: 'account_number' },
                    { data: 'contact', name: 'contact' },
                    { data: 'whatsapp', name: 'whatsapp' },
                    { data: 'receivable_amount', name: 'receivable_amount' },
                    { data: 'address', name: 'address' },
                    { data: 'status', name: 'status', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                order: [[1, 'asc']],

            });
        });
    </script>
@endsection
