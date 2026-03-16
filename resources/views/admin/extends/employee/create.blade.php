@extends('admin.master')

@section('content')
    <div class="min-h-screen bg-gray-100 p-6">
        <div class="max-w-7xl mx-auto">

            @include('admin.include.message')

            <h3 class="text-2xl font-bold mb-6">Create New Employee</h3>

            <form action="{{ route('employee.create') }}" method="POST" enctype="multipart/form-data"
                  class="bg-white p-6 rounded shadow">
                @csrf

                {{-- Employee Type --}}
                <div class="mb-4">
                    <label class="mb-2 text-sm font-medium text-gray-600">Employee Type <span class="text-red-500">*</span></label>
                    <select id="employee_type" name="employee_type" class="border p-2 rounded w-full">
                        <option value="">Select Type</option>
                        <option value="director">Director</option>
                        <option value="nsm">NSM</option>
                        <option value="rsm">RSM</option>
                        <option value="sm">SM</option>
                        <option value="asm">ASM</option>
                        <option value="mpo">MPO</option>
                    </select>
                    @error('employee_type') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                </div>

                {{-- All other fields (hidden initially) --}}
                <div id="employee_fields" class="hidden">



                    {{-- Personal Info --}}
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="mb-2 text-sm font-medium text-gray-600">Full Name</label>
                            <input type="text" name="full_name" value="{{ old('full_name') }}"
                                   class="w-full border p-2 rounded" placeholder="Full Name">
                            @error('full_name') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="mb-2 text-sm font-medium text-gray-600">Last Name</label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}"
                                   class="w-full border p-2 rounded" placeholder="Last Name">
                            @error('last_name') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="mb-2 text-sm font-medium text-gray-600">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   class="w-full border p-2 rounded" placeholder="Email">
                            @error('email') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="mb-2 text-sm font-medium text-gray-600">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                   class="w-full border p-2 rounded" placeholder="Phone">
                            @error('phone') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="mb-2 text-sm font-medium text-gray-600">Division</label>
                            <input type="text" name="division" value="{{ old('division') }}"
                                   class="w-full border p-2 rounded" placeholder="Division">
                            @error('division') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="mb-2 text-sm font-medium text-gray-600">District</label>
                            <input type="text" name="district" value="{{ old('district') }}"
                                   class="w-full border p-2 rounded" placeholder="District">
                            @error('district') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="mb-2 text-sm font-medium text-gray-600">Address</label>
                            <textarea name="address" class="w-full border p-2 rounded" placeholder="Address">{{ old('address') }}</textarea>
                            @error('address') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="mb-2 text-sm font-medium text-gray-600">Password</label>
                            <input type="password" name="password"
                                   class="w-full border p-2 rounded" placeholder="Password">
                            @error('password') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                        </div>

                        {{-- Parent (conditional) --}}
                        <div id="parent_div" class="mb-4 hidden">
                            <label class="mb-2 text-sm font-medium text-gray-600">Parent Employee</label>
                            <select id="parent_employee" name="parent_id" class="border p-2 rounded w-full">
                                <option value="">Select Parent</option>
                            </select>
                            @error('parent_id') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mt-6 text-right">
                        <button type="submit" class="bg-teal-500 hover:bg-teal-600 text-white px-6 py-2 rounded">
                            Save Employee
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {

            $('#employee_type').on('change', function () {
                let type = $(this).val();

                // Show all other fields
                $('#employee_fields').removeClass('hidden');

                // Handle Parent field visibility
                if(type === 'director') {
                    $('#parent_div').addClass('hidden');
                    $('#parent_employee').html('<option value="">No Parent</option>');
                } else {
                    $('#parent_div').removeClass('hidden');

                    // Fetch parent dynamically via AJAX
                    $.ajax({
                        url: '/admin/get-parent-employee',
                        type: 'GET',
                        data: { type: type },
                        success: function(res){
                            let placeholder = res.length > 0 ? res[0].employee_type.toUpperCase() : 'Parent';
                            let options = `<option value="">Select ${placeholder}</option>`;
                            res.forEach(emp => {
                                options += `<option value="${emp.id}">${emp.full_name}  | ${emp.employee_code || ''}</option>`;
                            });
                            $('#parent_employee').html(options);
                        }
                    });
                }
            });

        });
    </script>
@endsection
