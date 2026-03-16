@extends('admin.master')

@section('content')
    <div class="min-h-screen bg-gray-100 p-6">
        <div class="max-w-7xl mx-auto">
            <!-- Page Header -->
            @include('admin.include.message')
            <div class="mb-6 mt-2 pt-6 flex items-center justify-between">
                <h3 class="text-2xl font-bold text-gray-800">
                    Update Employee
                </h3>

                <a href="{{ route('employee.index') }}"
                   class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
                    <i class="fa fa-reply"></i>
                    Employee List
                </a>
            </div>

            <form action="{{ route('employee.update', $employee->id) }}" method="POST" enctype="multipart/form-data"
                  class="bg-white p-6 rounded shadow">
                @csrf
                @method('PUT')

                {{-- ================= Personal Info ================= --}}
                <h4 class="font-semibold mb-4 text-lg">Personal Information</h4>

                <div class="grid md:grid-cols-2 gap-4">

                    {{-- Full Name --}}
                    <div>
                        <label class="block text-sm font-medium mb-1">Full Name</label>
                        <input type="text" name="full_name" value="{{ old('full_name', $employee->full_name) }}"
                               class="w-full border p-2 rounded" placeholder="Full Name">
                        @error('full_name') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                    </div>




                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-medium mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $employee->email) }}"
                               class="w-full border p-2 rounded" placeholder="Email">
                        @error('email') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label class="block text-sm font-medium mb-1">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}"
                               class="w-full border p-2 rounded" placeholder="Phone">
                        @error('phone') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                    </div>

                    {{-- Division --}}
                    <div>
                        <label class="block text-sm font-medium mb-1">Division</label>
                        <input type="text" name="division" value="{{ old('division', $employee->division) }}"
                               class="w-full border p-2 rounded" placeholder="Division">
                        @error('division') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                    </div>

                    {{-- District --}}
                    <div>
                        <label class="block text-sm font-medium mb-1">District</label>
                        <input type="text" name="district" value="{{ old('district', $employee->district) }}"
                               class="w-full border p-2 rounded" placeholder="District">
                        @error('district') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                    </div>

                    {{-- Address --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Address</label>
                        <textarea name="address" class="w-full border p-2 rounded"
                                  placeholder="Address">{{ old('address', $employee->address) }}</textarea>
                        @error('address') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label class="block text-sm font-medium mb-1">Password</label>
                        <input type="password" name="password"
                               class="w-full border p-2 rounded"
                               placeholder="Leave blank if not changing">
                        @error('password') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                    </div>

                </div>

                {{-- ================= Employee Assign ================= --}}
                <h4 class="font-semibold mt-8 mb-4 text-lg">Employee Assign Information</h4>

                <div class="grid md:grid-cols-2 gap-4">

                    {{-- Employee Type --}}
                    <div>
                        <label class="block text-sm font-medium mb-1">Employee Type</label>
                        <select id="employee_type" class="border p-2 rounded w-full" disabled>
                            <option value="director" {{ $employee->employee_type == 'director' ? 'selected' : '' }}>Director</option>
                            <option value="nsm" {{ $employee->employee_type == 'nsm' ? 'selected' : '' }}>NSM</option>
                            <option value="rsm" {{ $employee->employee_type == 'rsm' ? 'selected' : '' }}>RSM</option>
                            <option value="sm" {{ $employee->employee_type == 'sm' ? 'selected' : '' }}>SM</option>
                            <option value="asm" {{ $employee->employee_type == 'asm' ? 'selected' : '' }}>ASM</option>
                            <option value="mpo" {{ $employee->employee_type == 'mpo' ? 'selected' : '' }}>MPO</option>
                        </select>
                        <input type="hidden" name="employee_type" value="{{ $employee->employee_type }}">
                    </div>

                    {{-- Parent --}}
                    <div>
                        <label class="block text-sm font-medium mb-1">Parent Employee</label>
                        <select id="parent_employee" name="parent_id" class="border p-2 rounded w-full">
                            <option value="">Select Parent</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ $employee->parent_id == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->full_name }} {{ $emp->last_name }} | {{ $emp->employee_code ?? '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('parent_id') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                    </div>

                </div>

                {{-- ================= Submit Button ================= --}}
                <div class="mt-6 text-right">
                    <button type="submit"
                            class="bg-teal-500 hover:bg-teal-600 text-white px-6 py-2 rounded">
                        Update Employee
                    </button>
                </div>

            </form>
        </div>
    </div>
@endsection


@section('scripts')
    <script>
        $(document).ready(function () {
            // Employee type is fixed, but still dynamically load parent if needed
            let type = $('#employee_type').val();

            if(type !== 'director') {
                $.ajax({
                    url: '/admin/get-parent-employee',
                    type: 'GET',
                    data: { type: type },
                    success: function(res){
                        let placeholder = res.length > 0 ? res[0].employee_type.toUpperCase() : 'Parent';
                        let options = `<option value="">Select ${placeholder}</option>`;
                        res.forEach(emp => {
                            let selected = emp.id == {{ $employee->parent_id ?? 0 }} ? 'selected' : '';
                            options += `<option value="${emp.id}" ${selected}>${emp.full_name} | ${emp.employee_code || ''}</option>`;
                        });
                        $('#parent_employee').html(options);
                    }
                });
            } else {
                $('#parent_employee').html('<option value="">No Parent</option>');
            }
        });
    </script>
@endsection
