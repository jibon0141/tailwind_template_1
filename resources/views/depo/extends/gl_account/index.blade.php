@extends('depo.master')
@section('content')
    <div class="page-wrapper bg-white p-6 rounded shadow">
        <div class="content container mx-auto px-4 overflow-y-auto">

            <!-- Page Header -->
            <div class="mb-6">
                <div class="flex flex-col md:flex-row items-center justify-between mt-5">

                    <!-- Title -->
                    <h3 class="text-2xl font-semibold text-gray-800">
                        GL Account List
                    </h3>

                </div>
            </div>

        </div>
    </div>

    <div class="mt-8 bg-white p-6 rounded shadow">
        <table id="glAccountTable" class="w-full">
            <thead>
            <tr>
                <th>#</th>
                <th>Account Name</th>
                <th>Action</th>
            </tr>
            </thead>
        </table>
    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#glAccountTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('depo.gl-account.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'account_name', name: 'account_name' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });
        });
    </script>
@endsection