@extends('depo.master')
{{--@include('layout.support',['data'=>['data_table']])--}}
@section('content')

    <div class="page-wrapper bg-gray-100 min-h-screen p-6">

        <!-- Page Header -->
        <div class="bg-white p-6 rounded shadow mb-6">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <h3 class="text-2xl font-semibold text-gray-800">
                    Supplier List
                </h3>

                <a href="{{ route('depo.supplier.create') }}"
                   class="mt-3 md:mt-0 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded shadow transition">
                    <i class="fa fa-plus"></i>
                    Create Supplier
                </a>
            </div>
        </div>

        <!-- DataTable Card -->
        <div class="bg-white p-6 rounded shadow">
            <table id="supplierTable" class="w-full table-auto">
                <thead class="bg-gray-100">
                <tr>
                    <th>#</th>
                    <th>Supplier Name</th>
                    <th>Supplier Id</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Opening Payable Due</th>
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
    <script>
        $(document).ready(function () {

            $('#supplierTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('depo.supplier.index') }}",
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'supplier_name', name: 'supplier_name' },
                    { data: 'supplier_code', name: 'supplier_code' },
                    { data: 'phone', name: 'phone' },
                    { data: 'email', name: 'email' },
                    { data: 'address', name: 'address' },
                    { data: 'balance', name: 'balance' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', orderable: false, searchable: false },
                ]
            });

        });

        // Delete Supplier
        function deleteItem(id) {
            if (confirm("Are you sure you want to delete this supplier?")) {

                fetch(`/depo/depo-supplier/delete/${id}`, {
                    method: 'DELETE',
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        alert(data.message);
                        if (data.success) {
                            $('#supplierTable').DataTable().ajax.reload();
                        }
                    })
                    .catch(() => alert('Something went wrong'));
            }
        }
    </script>
@endsection

