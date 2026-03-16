@extends('admin.master')
@section('content')
    <div class="page-wrapper bg-white p-6 rounded shadow">
        <div class="content container mx-auto px-4 overflow-y-auto">

            <!-- Page Header -->
            <div class="mb-6">
                <div class="flex flex-col md:flex-row items-center justify-between mt-5">

                    <!-- Title -->
                    <h3 class="text-2xl font-semibold text-gray-800">
                        Investor List
                    </h3>

                    <!-- Add Button -->
                    <a href="{{ route('admin.investor.create') }}"
                       class="mt-3 md:mt-0 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded shadow transition">
                        <i class="fa fa-plus"></i>
                        Create Investor
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
                       placeholder="Search accounts...">
            </div>
        </div>

        <table id="investorTable" class="w-full">
            <thead>
            <tr>
                <th>#</th>
                <th>Investor Name</th>
                <th>Investor Code</th>
                <th>Contact</th>
                <th>Balance</th>
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
            var table = $('#investorTable').DataTable({
                processing: true,
                serverSide: true,
                scrollX:true,
                autoWidth: false,
                ajax: "{{ route('admin.investor.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'investor_code', name: 'investor_code' },
                    { data: 'contact', name: 'contact' },
                    { data: 'invest_amount', name: 'invest_amount' },
                    { data: 'status', name: 'status', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });

            // Search on input change
            $('#searchInput').on('keyup change', function() {
                table.search(this.value).draw();
            });
        });

        function deleteItem(id) {
            if (confirm('Are you sure you want to delete this account?')) {
                $.ajax({
                    url: '/admin/account/delete/' + id,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#accountTable').DataTable().ajax.reload();
                            alert(response.message);
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function() {
                        alert('Error deleting account');
                    }
                });
            }
        }
    </script>
@endsection


