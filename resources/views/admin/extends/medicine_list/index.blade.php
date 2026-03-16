@extends('admin.master')

@section('content')
    <div class="page-wrapper bg-white p-6 rounded shadow">
        <div class="content container mx-auto px-4 overflow-y-auto">

            <!-- Page Header -->
            <div class="mb-6">
                <div class="flex flex-col md:flex-row items-center justify-between mt-5">
                    <h3 class="text-2xl font-semibold text-gray-800">
                        Medicine List
                    </h3>
                </div>
            </div>

        </div>
    </div>

    <!-- DataTable -->
    <div class="mt-8 bg-white p-6 rounded shadow overflow-x-auto">
        <div style="overflow-x:auto; width:100%; border:1px solid #e5e7eb; border-radius:8px;">
            <table id="medicineTable" class="display nowrap" style="width:100%;">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Medicine Name</th>
                    <th>Voucher No</th>
                    <th>Generic Name</th>
                    <th>Company</th>
                    <th>Brand</th>
                    <th>Category</th>
                    <th>Dosage Form</th>
                    <th>Expire Date</th>
                    <th>Status</th>
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
            $('#medicineTable').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                autoWidth: false,
                ajax: "{{ route('medicine.list') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'medicine_name', name: 'medicine_name' },
                    { data: 'voucher_no', name: 'voucher_no' },
                    { data: 'generic_name', name: 'generic_name' },
                    { data: 'company_name', name: 'company_name' },
                    { data: 'brand_name', name: 'brand_name' },
                    { data: 'category_name', name: 'category_name' },
                    { data: 'dosage_form', name: 'dosage_form' },
                    { data: 'expire_date', name: 'expire_date' },
                    { data: 'status', name: 'status', orderable: false, searchable: false },
                ]
            });
        });
    </script>
@endsection
