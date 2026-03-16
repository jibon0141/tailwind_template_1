@extends('employee.mpo.master')
<style>
    /* 5px gap between search/filter row and table */
    div.dataTables_wrapper div.dataTables_filter {
        margin-bottom: 10px !important;
    }

    div.dataTables_wrapper div.dataTables_length {
        margin-bottom: 5px !important;
    }

    table.dataTable {
        margin-top: 0 !important;
    }
</style>
@section('content')
    <div class="page-wrapper bg-white p-6 rounded shadow">

        <!-- Page Header -->
        <div class="mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between md:flex-nowrap gap-3 mt-5">

                <!-- Title -->
                <h3 class="text-xl md:text-2xl font-semibold text-gray-800">
                    Chemist House Payment List
                </h3>

                <!-- Dashboard Button -->
                <a href="{{ route('mpo.dashboard') }}"
                   class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded shadow transition">
                    <i class="fa fa-reply"></i>
                    Mpo Dashboard
                </a>

            </div>
        </div>

        <!-- Filters -->
        <div class="flex flex-col md:flex-row md:items-end gap-3 mb-4">

            <!-- Start Date -->
            <div class="flex flex-col">
                <label class="text-md font-medium mb-1">Start Date</label>
                <input type="date" id="start_date"
                       class="h-11 w-full md:w-48 p-2 border border-gray-300 rounded">
            </div>

            <!-- End Date -->
            <div class="flex flex-col">
                <label class="text-md font-medium mb-1">End Date</label>
                <input type="date" id="end_date"
                       class="h-11 w-full md:w-48 p-2 border border-gray-300 rounded">
            </div>

            <!-- Status -->
            <div class="flex flex-col">
                <label class="text-md font-medium mb-1">Status</label>
                <select id="payment_status"
                        class="h-11 w-full md:w-48 p-2 border border-gray-300 rounded">
                    <option value="">All Status</option>
                    <option value="1">Paid</option>
                    <option value="2">Partial</option>
                    <option value="3">Advance</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex flex-col md:flex-row gap-2 md:items-end">
                <button id="filterBtn"
                        class="h-11 w-full md:w-auto bg-teal-500 text-white px-4 rounded hover:bg-teal-600">
                    Filter
                </button>
                <button id="resetBtn"
                        class="h-11 w-full md:w-auto bg-gray-500 text-white px-4 rounded hover:bg-gray-600">
                    Reset
                </button>
            </div>
        </div>


        <!-- Table -->
        <div class="mt-8 bg-white p-4 rounded shadow overflow-x-auto">
            <table id="chemistHousePaymentTable" class="min-w-full table-auto border-collapse border border-gray-200">
                <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="">#</th>
                    <th class="">Chemist House</th>
                    <th class="">Voucher No</th>
                    <th class="">Payment Date</th>
                    <th class="">Contact</th>
                    <th class="">Depo Account</th>
                    <th class="">Due Balance</th>
                    <th class="">Receiving Amount</th>
                    <th class="">Current Due</th>
                    <th class="">Note</th>
                    <th class="">Document</th>
                    <th class="">Status</th>
                    <th class="">Action</th>
                </tr>
                </thead>
            </table>
        </div>

    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            var table = $('#chemistHousePaymentTable').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                autoWidth: false, // Important to fix header alignment
                ajax: {
                    url: "{{ route('mpo.chemist-house-due-payment.index') }}",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.payment_status = $('#payment_status').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'chemist_house_name', name: 'chemist_house_name' },
                    { data: 'payment_voucher', name: 'payment_voucher' },
                    { data: 'payment_date', name: 'payment_date' },
                    { data: 'contact', name: 'contact' },
                    { data: 'depo_account', name: 'depo_account' },
                    { data: 'due_balance', name: 'due_balance' },
                    { data: 'receiving_amount', name: 'receiving_amount' },
                    { data: 'current_due', name: 'current_due' },
                    { data: 'note', name: 'note' },
                    {
                        data: 'document',
                        name: 'document',
                        render: function(data) {
                            if (!data || data.trim() === '' || data.trim().toLowerCase() === 'null' || data.trim().toLowerCase() === 'undefined') {
                                return 'N/A';
                            }
                            let ext = data.split('.').pop().toLowerCase();
                            let url = '/image/DepoDueCollection/' + data;
                            if (['jpg','jpeg','png','gif'].includes(ext)) {
                                return `<a href="${url}" target="_blank"><img src="${url}" class="w-12 h-12 object-cover rounded"/></a>`;
                            }
                            return `<a href="${url}" target="_blank" class="text-blue-600 underline">View/Download</a>`;
                        },
                        orderable: false,
                        searchable: false
                    },
                    { data: 'payment_status', name: 'payment_status', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[3, 'desc']],
            });

            $('#filterBtn').on('click', function() { table.ajax.reload(); });
            $('#resetBtn').on('click', function() {
                $('#start_date, #end_date').val('');
                $('#payment_status').val('');
                table.ajax.reload();
            });
        });
    </script>
@endsection
