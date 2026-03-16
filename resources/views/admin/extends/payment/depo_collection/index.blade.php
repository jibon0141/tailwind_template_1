@extends('admin.master')
@section('content')
    <div class="page-wrapper bg-gray-100 min-h-screen p-6 space-y-6">

        <!-- Header Card -->
        @include('admin.include.message')
        <div class="bg-white p-6 rounded shadow">
            <div class="mb-6 flex flex-col md:flex-row items-center justify-between">
                <h3 class="text-2xl font-semibold text-gray-800">Depo Due Collection List</h3>

                <a href="{{ route('admin.dashboard') }}"
                   class="mt-3 md:mt-0 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded shadow transition">
                    <i class="fa fa-reply"></i> Admin Dashboard
                </a>
            </div>
        </div>

        <!-- Filters Card -->
        <div class="bg-white p-4 rounded shadow mb-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-6 gap-4 items-end">

                <!-- Start Date -->
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date"
                           id="start_date"
                           class="h-10 w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-teal-500">
                </div>

                <!-- End Date -->
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date"
                           id="end_date"
                           class="h-10 w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-teal-500">
                </div>

                <!-- Payment Status -->
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">Payment Status</label>
                    <select id="payment_status"
                            class="h-10 w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-teal-500">
                        <option value="">All Payment Status</option>
                        <option value="1">Paid</option>
                        <option value="2">Partial</option>
                        <option value="3">Advance</option>
                    </select>
                </div>

                <!-- General Status -->
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="status"
                            class="h-10 w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-teal-500">
                        <option value="">All Status</option>
                        <option value="1">Pending</option>
                        <option value="2">Approved</option>
                        <option value="3">Rejected</option>
                    </select>
                </div>

                <!-- Buttons -->

                    <button id="filterBtn"
                            class="h-10 w-full bg-teal-500 text-white rounded hover:bg-teal-600 transition">
                        Filter
                    </button>
                    <button id="resetBtn"
                            class="h-10 w-full bg-gray-500 text-white rounded hover:bg-gray-600 transition">
                        Reset
                    </button>

            </div>
        </div>


        <!-- DataTable Card -->
        <div class="bg-white p-6 rounded shadow overflow-x-auto">
            <table id="dueCollectionTable" class="w-full">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Depo Name</th>
                    <th>Payment Voucher</th>
                    <th>Payment Date</th>
                    <th>Depo Account</th>
                    <th>Company Account</th>
                    <th>Due Balance</th>
                    <th>Receiving Amount</th>
                    <th>Current Due</th>
                    <th>Note</th>
                    <th>Document</th>
                    <th>Payment Status</th>
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

            var table = $('#dueCollectionTable').DataTable({
                serverSide: true,
                processing: true,
                ordering: false,
                scrollX:true,
                autoWidth: false,
                ajax: {
                    url: "{{ route('admin.depo-due-collection.index') }}",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.payment_status = $('#payment_status').val();
                        d.status = $('#status').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'depo_name', name: 'depo_name' },
                    { data: 'payment_voucher', name: 'payment_voucher' },
                    { data: 'payment_date', name: 'payment_date' },
                    { data: 'depo_account', name: 'depo_account' },
                    { data: 'company_account', name: 'company_account' },
                    { data: 'due_balance', name: 'due_balance' },
                    { data: 'receiving_amount', name: 'receiving_amount' },
                    { data: 'current_receivable', name: 'current_receivable' },
                    { data: 'note', name: 'note' },
                    {
                        data: 'document',
                        name: 'document',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
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
                    { data: 'payment_status', orderable: false, searchable: false },
                    { data: 'status', orderable: false, searchable: false },
                    { data: 'action', orderable: false, searchable: false },
                ],
                order: [[1, 'asc']],
            });

            $('#filterBtn').on('click', function() { table.ajax.reload(); });
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
