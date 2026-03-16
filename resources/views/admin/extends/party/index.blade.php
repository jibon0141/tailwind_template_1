@extends('admin.master')

@section('content')

    <div class="page-wrapper bg-gray-100 min-h-screen p-6">

        <!-- Page Header -->
        <div class="bg-white p-6 rounded shadow mb-6">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <h3 class="text-2xl font-semibold text-gray-800">
                    Party List
                </h3>

                <a href="{{ route('admin.party.create') }}"
                   class="mt-3 md:mt-0 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded shadow transition">
                    <i class="fa fa-plus"></i>
                    Create Party
                </a>
            </div>
        </div>

        <!-- DataTable Card -->
        <div class="bg-white p-6 rounded shadow overflow-x-auto">
            <table id="partyTable" class="w-full table-auto">
                <thead class="bg-gray-100">
                <tr>
                    <th>#</th>
                    <th>Party Id</th>
                    <th>Party Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

    </div>

@endsection


@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {

            $('#partyTable').DataTable({
                processing: true,
                serverSide: true,
                scrollX:true,
                autoWidth: false,
                ajax: "{{ route('admin.party.index') }}",
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'party_code', name: 'party_code' },
                    { data: 'party_name', name: 'party_name' },
                    { data: 'email', name: 'email' },
                    { data: 'phone', name: 'phone' },
                    { data: 'address', name: 'address' },
                    { data: 'action', orderable: false, searchable: false },
                ]
            });

        });

        // Delete Supplier
        function deleteItem(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This party will be permanently deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {

                if (result.isConfirmed) {

                    fetch(`/admin/party/delete/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                            'Content-Type': 'application/json'
                        }
                    })
                        .then(res => res.json())
                        .then(data => {

                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: data.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                                $('#partyTable').DataTable().ajax.reload(null, false);

                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Failed!',
                                    text: data.message
                                });
                            }
                        })
                        .catch(() => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Something went wrong. Please try again.'
                            });
                        });
                }
            });
        }
    </script>
@endsection

