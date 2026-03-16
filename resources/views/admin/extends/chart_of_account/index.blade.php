@extends('admin.master')
@section('content')
    <div class="page-wrapper bg-white p-6 rounded shadow">
        <div class="content container mx-auto px-4 overflow-y-auto">

            <!-- Page Header -->
            <div class="mb-6">
                <div class="flex flex-col md:flex-row items-center justify-between mt-5">

                    <!-- Title -->
                    <h3 class="text-2xl font-semibold text-gray-800">
                        Chart of Account List
                    </h3>

                    <!-- Add Button -->
                    <a href="{{ route('admin.chart-of-account.create') }}"
                       class="mt-3 md:mt-0 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded shadow transition">
                        <i class="fa fa-plus"></i>
                        Create Chart of Account
                    </a>

                </div>
            </div>

        </div>
    </div>

    <div class="mt-8 bg-white p-6 rounded shadow overflow-x-auto">
        <table id="chartOfAccountTable" class="w-full">
            <thead>
            <tr>
                <th>#</th>
                <th>GL Account</th>
                <th>Head Type</th>
                <th>Head Name</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            </thead>
        </table>
    </div>

@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            $('#chartOfAccountTable').DataTable({
                processing: true,
                serverSide: true,
                scrollX:true,
                autoWidth: false,
                ajax: "{{ route('admin.chart-of-account.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'gl_account_name', name: 'gl_account_name' },
                    { data: 'head_type', name: 'head_type' },
                    { data: 'head_name', name: 'head_name' },
                    { data: 'status', name: 'status', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });
        });

        function deleteItem(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This Chart of Account will be permanently deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {

                if (result.isConfirmed) {

                    $.ajax({
                        url: '/admin/chart-of-account/delete/' + id,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                                // Reload DataTable instantly without losing pagination
                                $('#chartOfAccountTable').DataTable().ajax.reload(null, false);

                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Cannot Delete',
                                    text: response.message
                                });
                            }
                        },
                        error: function(xhr) {
                            console.log(xhr.responseText); // for debugging
                            Swal.fire('Error', 'Something went wrong', 'error');
                        }
                    });

                }
            });
        }

    </script>
@endsection
