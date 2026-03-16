@extends('admin.master')
@section('content')
    <div class="page-wrapper bg-white p-6 rounded shadow">
        <div class="content container mx-auto px-4 overflow-y-auto">

            <!-- Page Header -->
            <div class="mb-6">
                <div class="flex flex-col md:flex-row items-center justify-between mt-5">

                    <!-- Title -->
                    <h3 class="text-2xl font-semibold text-gray-800">
                        Account List
                    </h3>

                    <!-- Add Button -->
                    <a href="{{ route('admin.account.create') }}"
                       class="mt-3 md:mt-0 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded shadow transition">
                        <i class="fa fa-plus"></i>
                        Create Account
                    </a>

                </div>
            </div>

        </div>
    </div>

    <div class="mt-8 bg-white p-6 rounded shadow overflow-x-auto">

        <table id="accountTable" class="w-full">
            <thead>
            <tr>
                <th>#</th>
                <th>Account No</th>
                <th>Account Name</th>
                <th>Opening Balance</th>
                <th>Current Balance</th>
                <th>Status</th>
                <th>Default</th>
                <th>Action</th>
            </tr>
            </thead>
        </table>
    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            var table = $('#accountTable').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                autoWidth: false,
                ajax: "{{ route('admin.account.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'account_no', name: 'account_no' },
                    { data: 'account_name', name: 'account_name' },
                    { data: 'opening_balance', name: 'opening_balance' },
                    { data: 'balance', name: 'balance' },
                    { data: 'status', name: 'status', orderable: false, searchable: false },
                    { data: 'is_default', name: 'is_default', orderable: false, searchable: false },
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
