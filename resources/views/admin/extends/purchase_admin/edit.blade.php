@extends('admin.master')

@section('content')
    <div class="min-h-screen mb-5 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4">

            <!-- Page Header -->
            @include('admin.include.message')
            <div class="mb-6 mt-2 pt-6 flex items-center justify-between">
                <h3 class="text-2xl font-bold text-gray-800">
                    Update Purchase Admin
                </h3>

                <a href="{{ route('purchase.admin.index') }}"
                   class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
                    <i class="fa fa-reply"></i>
                    Admin List
                </a>
            </div>

            <!-- Form Card -->
            <div class="mt-8 bg-white p-6 rounded-lg shadow">
                <form action="{{ route('purchase.admin.update', $admin->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- Name -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $admin->name) }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="Full Name" required>
                            @error('name') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Email -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email', $admin->email) }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="example@mail.com" required>
                            @error('email') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Password -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">Password (Leave blank to keep current)</label>
                            <input type="password" name="password"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="New Password">
                            @error('password') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Password Confirmation -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">Confirm Password</label>
                            <input type="password" name="password_confirmation"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="Confirm Password">
                        </div>

                        <!-- Status -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">Status <span class="text-red-500">*</span></label>
                            <select name="status" class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500" required>
                                <option value="1" {{ old('status', $admin->status) == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status', $admin->status) == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                    </div>

                    <!-- Submit -->
                    <div class="mt-6 text-right">
                        <button type="submit" class="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-6 rounded transition">
                            Update Admin
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
@endsection
