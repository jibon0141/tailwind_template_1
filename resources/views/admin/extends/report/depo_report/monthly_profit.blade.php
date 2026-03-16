@extends('admin.master')

@section('content')
    <div class="bg-gray-100 min-h-screen p-3 md:p-6">

        <!-- PAGE HEADER -->
        <div class="bg-white p-4 md:p-6 rounded shadow mb-6 flex flex-col md:flex-row items-center md:justify-between gap-3">
            <h3 class="text-xl md:text-2xl font-semibold text-gray-800 text-center md:text-left w-full md:w-auto h-11 flex items-center justify-center md:justify-start">
                Depo Profit Report
            </h3>
        </div>

        <!-- FILTERS -->
        <div class="bg-white p-4 rounded shadow mb-6">
            <div class="flex flex-col md:flex-row items-stretch md:items-center gap-3">

                <input type="date"
                       id="start_date"
                       class="h-11 w-full md:w-auto p-2 border border-gray-300 rounded">

                <input type="date"
                       id="end_date"
                       class="h-11 w-full md:w-auto p-2 border border-gray-300 rounded">

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

        <!-- TABLE CARD -->
        <div class="bg-white rounded shadow px-5 py-2">
            <div class="relative w-full overflow-x-auto">
                <table id="profitTable"
                       class="min-w-[400px] w-full table-auto">
                    <thead class="bg-gray-100">
                    <tr>
                        <th>#</th>
                        <th>Depo Name</th>
                        <th>Month</th>
                        <th>Profit</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script>
        // Set default date to today
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        const currentDate = `${yyyy}-${mm}-${dd}`;

        document.getElementById('start_date').value = currentDate;
        document.getElementById('end_date').value = currentDate;

        $(document).ready(function () {

            var table = $('#profitTable').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                autoWidth: false,
                ajax: {
                    url: "{{ route('report.depo.monthly.profit') }}",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'depo_name' },
                    { data: 'month' },
                    { data: 'profit' }
                ],
                order: [[1, 'asc']]
            });

            $('#filterBtn').click(() => table.ajax.reload());

            $('#resetBtn').click(() => {
                $('#start_date, #end_date').val(currentDate);
                table.ajax.reload();
            });

        });
    </script>
@endsection
