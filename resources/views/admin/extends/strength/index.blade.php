@extends("admin.master")
@section("content")

    <div class="page-wrapper bg-white p-6 rounded shadow">
        <div class="content container mx-auto px-4 overflow-y-auto">

            <!-- Page Header -->
            <div class="mb-6">
                <div class="flex flex-col md:flex-row items-center justify-between mt-5">

                    <!-- Title -->
                    <h3 class="text-2xl font-semibold text-gray-800">
                        Strength List
                    </h3>

                    <!-- Add Account Button -->
                    <a href="{{ route('strength.create') }}"
                       class="mt-3 md:mt-0 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded shadow transition">
                        <i class="fa fa-plus"></i>
                       Create Strength
                    </a>

                </div>
            </div>

        </div>
    </div>


    <div class="mt-8 bg-white p-6 rounded shadow">
        <table id="strengthTable" class="w-full">
            <thead>
            <tr>
                <th>#</th>
                <th>Strength Name</th>
                <th>Dosage Name</th>
                <th>Strength Description</th>
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
            $('#strengthTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('strength.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'strength_name', name: 'strength_name' },
                    { data: 'dosage_name', name: 'dosage_name' },
                    { data: 'strength_description', name: 'strength_description' },
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

                fetch("{{ url('/dosage/delete/') }}" + id, {
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
                            $('#dosageTable').DataTable().ajax.reload(); // reload table
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

