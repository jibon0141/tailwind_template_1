@extends('admin.master')

@section('content')
    <div class="page-wrapper bg-white p-6 rounded shadow">
        <div class="content container mx-auto px-4 overflow-y-auto">

            <!-- Page Header -->
            <div class="mb-6">
                <div class="flex flex-col md:flex-row items-center justify-between mt-5">
                    <!-- Title -->
                    <h3 class="text-2xl font-semibold text-gray-800">
                        MPO List
                    </h3>
                    <!-- Add Account Button -->
                    <a href="{{ route('mpo.create') }}"
                       class="mt-3 md:mt-0 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded shadow transition">
                        <i class="fa fa-plus"></i>
                       Create MPO
                    </a>

                </div>
            </div>

        </div>
    </div>

    <!-- Table -->
    <div class="mt-8 bg-white p-6 rounded shadow overflow-x-auto">
        <table id="mpoTable" class="min-w-full divide-y divide-gray-200 table-auto">
            <thead>
            <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-700 uppercase">
                <th class="px-4 py-2">#</th>
                <th class="px-4 py-2">Employee Id</th>
                <th class="px-4 py-2">Full Name</th>
                <th class="px-4 py-2">Email</th>
                <th class="px-4 py-2">Phone</th>
                <th class="px-4 py-2">Division</th>
                <th class="px-4 py-2">District</th>
                <th class="px-4 py-2">Address</th>
                <th class="px-4 py-2">Depo Name</th>
                <th class="px-4 py-2">Type</th>
                <th class="px-4 py-2">Action</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#mpoTable').DataTable({
                processing: true,
                serverSide: true,
                scrollX:true,
                autoWidth: false,
                ajax: "{{ route('mpo.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'employee_code', name: 'employee_code' },
                    { data: 'full_name', name: 'full_name' },
                    { data: 'email', name: 'email' },
                    { data: 'phone', name: 'phone' },
                    { data: 'division', name: 'division' },
                    { data: 'district', name: 'district' },
                    { data: 'address', name: 'address' },
                    { data: 'depo_name', name: 'depo_name' },
                    { data: 'employee_type', name: 'employee_type' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });
        });

        {{--function deleteItem(id) {--}}
        {{--    if(confirm('Are you sure you want to delete this ASM?')) {--}}
        {{--        $.ajax({--}}
        {{--            url: '/admin/asm/' + id,--}}
        {{--            type: 'DELETE',--}}
        {{--            data: {_token: '{{ csrf_token() }}'},--}}
        {{--            success: function(response) {--}}
        {{--                alert(response.message || 'Deleted successfully');--}}
        {{--                $('#asmTable').DataTable().ajax.reload();--}}
        {{--            },--}}
        {{--            error: function(xhr) {--}}
        {{--                alert('Delete failed');--}}
        {{--            }--}}
        {{--        });--}}
        {{--    }--}}
        {{--}--}}
    </script>
@endsection
