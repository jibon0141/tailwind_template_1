@extends("admin.master")
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
                    <a href="{{ route('medicine.create') }}"
                       class="mt-3 md:mt-0 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded shadow transition">
                        <i class="fa fa-plus"></i>
                        Create Medicine
                    </a>

                </div>
            </div>

        </div>
    </div>

    <!-- DataTable -->
    <div class="mt-8 bg-white p-6 rounded shadow overflow-x-auto">
        <div style="overflow-x: scroll; width: 100%; border: 1px solid #e5e7eb; border-radius: 8px;">
            <table id="medicineTable" class="display" style="width: 100%; min-width: 1200px;">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Medicine Name</th>
                    <th>Company Name</th>
                    <th>Generic Name</th>
                    <th>Brand</th>
                    <th>Category</th>
                    <th>Dosage</th>
                    <th>Strength</th>
                    <th>MRP</th>
                    <th>Purchase Percentage</th>
                    <th>Purchase Price</th>
                    <th>Sale Percentage</th>
                    <th>Sale Price</th>
                    <th>Status</th>
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
            // Initialize DataTable
            const table = $('#medicineTable').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                autoWidth: false,
                ajax: "{{ route('medicine.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'medicine_name', name: 'medicine_name' },
                    { data: 'company_name', name: 'company_name' },
                    { data: 'generic_name', name: 'generic_name' },
                    { data: 'brand', name: 'brand' },
                    { data: 'category', name: 'category' },
                    { data: 'dosage', name: 'dosage' },
                    { data: 'strength', name: 'strength' },
                    { data: 'mrp', name: 'mrp' },
                    { data: 'purchase_percentage', name: 'purchase_percentage' },
                    { data: 'purchase_price', name: 'purchase_price' },
                    { data: 'sale_percentage', name: 'sale_percentage' },
                    { data: 'sale_price', name: 'sale_price' },
                    { data: 'status', name: 'status', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });
        });

        // Delete function
        function deleteItem(id) {
            if(confirm("Are you sure you want to delete this medicine?")) {
                const token = "{{ csrf_token() }}";

                fetch(`/admin/medicine/delete/${id}`, {
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
                            $('#medicineTable').DataTable().ajax.reload();
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
