@extends("depo.master")
@section("content")

    <div class="page-wrapper bg-white p-6 rounded shadow">
        <div class="content container mx-auto px-4 overflow-y-auto">

            <!-- Page Header -->
            <div class="mb-6">
                <div class="flex flex-col md:flex-row items-center justify-between mt-5">

                    <!-- Title -->
                    <h3 class="text-2xl font-semibold text-gray-800">
                        Medicine List
                    </h3>

                    <!-- Create Button -->
                    <a href="{{ route('depo.medicine.create') }}"
                       class="mt-3 md:mt-0 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded shadow transition">
                        <i class="fa fa-plus"></i>
                        Create Medicine
                    </a>

                </div>
            </div>

        </div>
    </div>

    <!-- DataTable -->
    <div class="mt-8 bg-white p-6 rounded shadow">
        <table id="medicineTable" class="w-full">
            <thead>
            <tr>
                <th>#</th>
                <th>Medicine Name</th>
                <th>Generic Name</th>
                <th>Brand</th>
                <th>Category</th>
                <th>Dosage</th>
                <th>Strength</th>
                <th>Purchase Price</th>
                <th>Sale Price</th>
                <th>MRP</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // Initialize DataTable
            const table = $('#medicineTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('depo.medicine.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'medicine_name', name: 'medicine_name' },
                    { data: 'generic_name', name: 'generic_name' },
                    { data: 'brand', name: 'brand' },
                    { data: 'category', name: 'category' },
                    { data: 'dosage', name: 'dosage' },
                    { data: 'strength', name: 'strength' },
                    { data: 'purchase_price', name: 'purchase_price' },
                    { data: 'sale_price', name: 'sale_price' },
                    { data: 'mrp', name: 'mrp' },
                    { data: 'status', name: 'status', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });
        });

        // Delete function
        function deleteItem(id) {
            if (confirm("Are you sure you want to delete this medicine?")) {
                const token = "{{ csrf_token() }}";

                fetch(`/depo/depo-medicine/delete/${id}`, {  // Correct URL using template literal
                    method: 'DELETE',
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": token
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            $('#medicineTable').DataTable().ajax.reload(); // reload table
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
