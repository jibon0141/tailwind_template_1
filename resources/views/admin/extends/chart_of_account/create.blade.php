@extends("admin.master")
@section("content")

    <div class="min-h-screen mb-5 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4">

            <!-- Page Header -->
            @include('depo.include.message')
            <div class="mb-6 mt-2 pt-6 flex items-center justify-between">
                <h3 class="text-2xl font-bold text-gray-800">
                    Create New Chart of Account
                </h3>

                <a href="{{ route('admin.chart-of-account.index') }}"
                   class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
                    <i class="fa fa-reply"></i>
                    Chart of Account List
                </a>
            </div>

            <!-- Form Card -->
            <div class="mt-8 bg-white p-6 rounded-lg shadow">
                <form action="{{ route('admin.chart-of-account.create') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- GL Account -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                GL Account <span class="text-red-500">*</span>
                            </label>
                            <select name="gl_account_id" class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500">
                                <option value="">Select GL Account</option>
                                @foreach($glAccounts as $glAccount)
                                    <option value="{{ $glAccount->id }}" {{ old('gl_account_id') == $glAccount->id ? 'selected' : '' }}>
                                        {{ $glAccount->account_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('gl_account_id')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Head Type -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Head Type <span class="text-red-500">*</span>
                            </label>
                            <select name="head_type" class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500">
                                <option value="">Select Head Type</option>
                                <option value="Income" {{ old('head_type') == 'Income' ? 'selected' : '' }}>Income</option>
                                <option value="Expense" {{ old('head_type') == 'Expense' ? 'selected' : '' }}>Expense</option>
                            </select>
                            @error('head_type')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Head Name -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Head Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="head_name"
                                   value="{{ old('head_name') }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="e.g., Cash in Hand"
                                   required>
                            @error('head_name')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status"
                                    class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500">
                                <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status') === '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>

                    <!-- Submit -->
                    <div class="mt-6 text-right">
                        <button type="submit"
                                class="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-6 rounded transition">
                            Save Chart of Account
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>

@endsection
