@extends('admin.master')

@section('content')

    <div class="page-wrapper bg-gray-100 min-h-screen p-6">

        <!-- Page Header -->
        <div class="bg-white p-6 rounded shadow mb-6">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <h3 class="text-2xl font-semibold text-gray-800">
                    Supplier List
                </h3>

                <a href="{{ route('supplier.create') }}"
                   class="mt-3 md:mt-0 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded shadow transition">
                    <i class="fa fa-plus"></i>
                    Create Supplier
                </a>
            </div>
        </div>

        <!-- DataTable Card -->
        <div class="bg-white p-6 rounded shadow overflow-x-auto">
            <table id="supplierTable" class="w-full table-auto">
                <thead class="bg-gray-100">
                <tr>
                    <th>#</th>
                    <th>Supplier Name</th>
                    <th>Supplier Id</th>
                    <th>Company Name</th>
                    <th>Phone</th>
                    <th>Opening Balance</th>
                    <th>Balance</th>
                    <th>Balance Status</th>
                    <th>Created At</th>
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

            $('#supplierTable').DataTable({
                processing: true,
                serverSide: true,
                scrollX:true,
                autoWidth: false,
                ajax: "{{ route('supplier.index') }}",
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'supplier_name', name: 'supplier_name' },
                    { data: 'supplier_code', name: 'supplier_code' },
                    { data: 'company_name', name: 'company_name' },
                    { data: 'phone', name: 'phone' },
                    { data: 'opening_balance', name: 'opening_balance' },
                    { data: 'balance', name: 'balance' },
                    { data: 'payment_status', name: 'payment_status' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', orderable: false, searchable: false },
                ]
            });

        });

        // Delete Supplier
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
                    const token = "{{ csrf_token() }}";

                    fetch(`/admin/supplier/delete/${id}`, {
                        method: 'DELETE',
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": token
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
                                $('#supplierTable').DataTable().ajax.reload();
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

