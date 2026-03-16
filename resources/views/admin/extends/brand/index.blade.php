@extends("admin.master")
@section("content")

    <div class="page-wrapper bg-white p-6 rounded shadow">
        <div class="content container mx-auto px-4 overflow-y-auto">

            <!-- Page Header -->
            <div class="mb-6">
                <div class="flex flex-col md:flex-row items-center justify-between mt-5">

                    <!-- Title -->
                    <h3 class="text-2xl font-semibold text-gray-800">
                        Brand List
                    </h3>

                    <!-- Add Account Button -->
                    <a href="{{ route('brand.create') }}"
                       class="mt-3 md:mt-0 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded shadow transition">
                        <i class="fa fa-plus"></i>
                       Create Brand
                    </a>

                </div>
            </div>

        </div>
    </div>


    <div class="mt-8 bg-white p-6 rounded shadow overflow-x-auto">
        <table id="strengthTable" class="w-full">
            <thead>
            <tr>
                <th>#</th>
                <th>Brand Name</th>
                <th>Brand Description</th>
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
            $('#strengthTable').DataTable({
                processing: true,
                serverSide: true,
                scrollX:true,
                autoWidth: false,
                ajax: "{{ route('brand.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'brand_name', name: 'brand_name' },
                    { data: 'brand_description', name: 'brand_description' },
                    { data: 'status', name: 'status', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });
        });


    //     Delete Ajax
        function deleteItem(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This brand will be permanently deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {

                if (result.isConfirmed) {
                    fetch(`/admin/brand/delete/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                            'Accept': 'application/json'
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: data.message ?? 'Brand deleted successfully',
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                                $('#strengthTable').DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Failed!',
                                    text: data.message
                                });
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Something went wrong'
                            });
                        });
                }
            });
        }


    </script>
@endsection

