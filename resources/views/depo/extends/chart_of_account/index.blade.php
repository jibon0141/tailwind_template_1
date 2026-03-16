@extends('depo.master')
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
                    <a href="{{ route('depo.chart-of-account.create') }}"
                       class="mt-3 md:mt-0 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded shadow transition">
                        <i class="fa fa-plus"></i>
                        Create Chart of Account
                    </a>

                </div>
            </div>

        </div>
    </div>

    <div class="mt-8 bg-white p-6 rounded shadow">
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
                ajax: "{{ route('depo.chart-of-account.index') }}",
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
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/depo/chart-of-account/delete/${id}`, {
                        method: 'DELETE',
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire(
                                    'Deleted!',
                                    data.message,
                                    'success'
                                );
                                $('#chartOfAccountTable').DataTable().ajax.reload();
                            } else {
                                Swal.fire(
                                    'Error!',
                                    data.message,
                                    'error'
                                );
                            }
                        })
                        .catch(() => {
                            Swal.fire(
                                'Oops!',
                                'Something went wrong.',
                                'error'
                            );
                        });
                }
            });
        }
    </script>
@endsection