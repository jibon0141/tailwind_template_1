@extends('depo.master')

@section('content')

    <div class="page-wrapper bg-gray-100 min-h-screen p-6">

        <!-- Page Header -->
        <div class="bg-white p-6 rounded shadow mb-6 flex flex-col md:flex-row items-center justify-between">
            <h3 class="text-2xl font-semibold text-gray-800">Depo Due Payment List</h3>
            <a href="{{ route('depo.depo-due-payment.create') }}"
               class="mt-3 md:mt-0 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded shadow transition">
                <i class="fa fa-plus"></i> Create Payment
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-white p-4 rounded shadow mb-4 flex flex-col md:flex-row gap-3 items-end">
            <!-- Start Date -->
            <div class="flex flex-col">
                <label class="text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" id="start_date" class="p-2 border border-gray-300 rounded w-48">
            </div>

            <!-- End Date -->
            <div class="flex flex-col">
                <label class="text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input type="date" id="end_date" class="p-2 border border-gray-300 rounded w-48">
            </div>

            <!-- Payment Status -->
            <div class="flex flex-col">
                <label class="text-sm font-medium text-gray-700 mb-1">Payment Status</label>
                <select id="payment_status" class="p-2 border border-gray-300 rounded w-52">
                    <option value="">All Payment Status</option>
                    <option value="1">Paid</option>
                    <option value="2">Partial</option>
                    <option value="3">Advance</option>
                </select>
            </div>

            <!-- General Status -->
            <div class="flex flex-col">
                <label class="text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status" class="p-2 border border-gray-300 rounded w-52">
                    <option value="">All Status</option>
                    <option value="1">Pending</option>
                    <option value="2">Approved</option>
                    <option value="3">Rejected</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex gap-2">
                <button id="filterBtn" class="bg-teal-500 text-white px-4 py-2 rounded hover:bg-teal-600">
                    Filter
                </button>
                <button id="resetBtn" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Reset
                </button>
            </div>
        </div>

        <!-- DataTable Card -->
        <div class="bg-white p-6 rounded shadow overflow-x-auto">
            <table id="dueCollectionTable" class="w-full table-auto text-sm">
                <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2">#</th>
                    <th class="px-3 py-2">Payment Voucher</th>
                    <th class="px-3 py-2">Payment Date</th>
                    <th class="px-3 py-2">Depo Account</th>
                    <th class="px-3 py-2">Due Balance</th>
                    <th class="px-3 py-2">Paying Amount</th>
                    <th class="px-3 py-2">Current Due</th>
                    <th class="px-3 py-2">Note</th>
                    <th class="px-3 py-2">Document</th>
                    <th class="px-3 py-2">Payment Status</th>
                    <th class="px-3 py-2">Status</th>
                    <th class="px-3 py-2">Action</th>
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

            var table = $('#dueCollectionTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('depo.depo-due-payment.index') }}",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.payment_status = $('#payment_status').val();
                        d.status = $('#status').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'payment_voucher', name: 'payment_voucher' },
                    { data: 'payment_date', name: 'payment_date' },
                    { data: 'depo_account', name: 'depo_account' },
                    { data: 'due_balance', name: 'due_balance' },
                    { data: 'receiving_amount', name: 'receiving_amount' },
                    { data: 'current_receivable', name: 'current_receivable' },
                    { data: 'note', name: 'note' },
                    {
                        data: 'document',
                        name: 'document',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            if (!data || data.trim().toLowerCase() === 'null') return 'N/A';
                            let ext = data.trim().split('.').pop().toLowerCase();
                            let url = '/image/DepoDuePayment/' + data.trim();
                            if (['jpg','jpeg','png','gif'].includes(ext)) {
                                return `<a href="${url}" target="_blank">
                                        <img src="${url}" class="w-12 h-12 object-cover rounded" />
                                    </a>`;
                            }
                            return `<a href="${url}" target="_blank" class="text-blue-600 underline">View / Download</a>`;
                        }
                    },
                    { data: 'payment_status', name: 'payment_status', orderable: false, searchable: false },
                    { data: 'status', name: 'status', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                order: [[1, 'asc']],
                responsive: true,
            });

            // Filter button
            $('#filterBtn').on('click', function() {
                table.ajax.reload();
            });

            // Reset button
            $('#resetBtn').on('click', function() {
                $('#start_date').val('');
                $('#end_date').val('');
                $('#payment_status').val('');
                $('#status').val('');
                table.ajax.reload();
            });

        });
    </script>
@endsection
