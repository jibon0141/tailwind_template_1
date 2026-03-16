@extends('employee.mpo.master')

@section('content')
    <div class="page-wrapper bg-gray-100 min-h-screen p-6">

        <!-- Page Header -->
        <div class="bg-white p-6 rounded shadow mb-6 flex flex-col md:flex-row items-center justify-between">
            <h3 class="text-2xl font-semibold text-gray-800">Sales List</h3>
            <a href="{{ route('mpo.sale.create') }}"
               class="mt-3 md:mt-0 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded shadow transition">
                <i class="fa fa-plus"></i> Create Sale
            </a>
        </div>

        <!-- DataTable Card -->
        <div class="bg-white p-6 rounded shadow">
            <table id="saleTable" class="w-full table-auto">
                <thead class="bg-gray-100">
                <tr>
                    <th>#</th>
                    <th>Sale Voucher</th>
                    <th>Chemist House</th>
                    <th>Account</th>
                    <th>Sale Date</th>
                    <th>Total</th>
                    <th>Given</th>
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
            $('#saleTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('mpo.sale.index') }}",
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'sale_voucher', name: 'sale_voucher' },
                    { data: 'shop_name', name: '' }, // display only, no server-side search
                    { data: 'account_name', name: '' }, // display only
                    { data: 'sale_date', name: 'sale_date' },
                    { data: 'total', name: 'total' },
                    { data: 'given_amount', name: 'given_amount' },
                    { data: 'action', orderable: false, searchable: false },
                ]
            });

        });
    </script>
@endsection
