@extends('depo.master')
@section('content')
    <div class="page-wrapper bg-white p-6 rounded shadow">
        <div class="content container mx-auto px-4 overflow-y-auto">

            <!-- Page Header -->
            <div class="mb-6 flex flex-col md:flex-row items-center justify-between mt-5">
                <h3 class="text-2xl font-semibold text-gray-800">
                    Chemist House List
                </h3>

                <!-- Create Button -->
                <a href="{{ route('depo.chemist-house.create') }}"
                   class="mt-3 md:mt-0 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded shadow transition">
                    <i class="fa fa-plus"></i>
                    Create Chemist House
                </a>
            </div>

        </div>
    </div>

    <!-- Table -->
    <div class="mt-8 bg-white p-6 rounded shadow">
        <table id="chemistHouseTable" class="min-w-full table-auto border border-gray-200">
            <thead class="bg-gray-100 text-gray-700">
            <tr>
                <th>#</th>
                <th>Shop Name</th>
                <th>Owner Name</th>
                <th>Depo Name</th>
                <th>Mpo Name</th>
                <th>Bank Name</th>
                <th>Account Number</th>
                <th>Mobile</th>
                <th>Whats App</th>
                <th>Receivable Amount</th>
                <th>Address</th>
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
            $('#chemistHouseTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('depo.chemist-house.index') }}",
                    data: function(d) {
                        // Default search value is already sent as d.search.value
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'shop_name', name: 'shop_name' },
                    { data: 'owner_name', name: 'owner_name' },
                    { data: 'depo_name', name: 'depo.depo_name' }, // Depo searchable
                    { data: 'mpo', name: 'mpo' },
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
                responsive: true,
                searching: true
            });
        });
    </script>
@endsection
