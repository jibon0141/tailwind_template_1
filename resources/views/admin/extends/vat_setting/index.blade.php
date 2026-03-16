@extends('admin.master')
@section('content')
    <div class="page-wrapper bg-white p-6 rounded shadow">
        <div class="content container mx-auto px-4 overflow-y-auto">

            <!-- Page Header -->
            <div class="mb-6">
                <div class="flex flex-col md:flex-row items-center justify-between mt-5">

                    <!-- Title -->
                    <h3 class="text-2xl font-semibold text-gray-800">
                        Vat List
                    </h3>

                    <!-- Add Account Button -->
                    <a href="{{ route('vat.create') }}"
                       class="mt-3 md:mt-0 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded shadow transition">
                        <i class="fa fa-plus"></i>
                        Create Vat
                    </a>

                </div>
            </div>

        </div>
    </div>


    <div class="mt-8 bg-white p-6 rounded shadow overflow-x-auto">
        <table id="vatTable" class="w-full">
            <thead>
            <tr>
                <th>#</th>
                <th>Depo Name</th>
                <th>Vat Percentage</th>
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
            $('#vatTable').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                autoWidth: false,
                ajax: "{{ route('vat.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'depo_name', name: 'depo_name' },
                    { data: 'vat_percentage', name: 'vat_percentage' },
                    { data: 'status', name: 'status', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });
        });


        //     Delete Ajax

        function deleteItem(id) {
            console.log(id)
            if(confirm("Are you sure you want to delete this item?")) {
                const token = "{{ csrf_token() }}";

                fetch('/admin/vat/delete/' + id, {
                    method: 'DELETE',
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": token
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            alert(data.message);
                            $('#vatTable').DataTable().ajax.reload(); // reload table
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Something went wrong');
                    });
            }
        }


    </script>
@endsection

