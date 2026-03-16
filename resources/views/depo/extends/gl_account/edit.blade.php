@extends('depo.master')
@section('content')

    <div class="min-h-screen mb-5 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4">

            <!-- Page Header -->
            @include('depo.include.message')
            <div class="mb-6 mt-2 pt-6 flex items-center justify-between">
                <h3 class="text-2xl font-bold text-gray-800">
                    Update GL Account
                </h3>

                <a href="{{ route('depo.gl-account.index') }}"
                   class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
                    <i class="fa fa-reply"></i>
                    GL Account List
                </a>
            </div>

            <!-- Two-Part Layout -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Left Part: Main Form -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <form action="{{ route('depo.gl-account.update', $account->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 gap-6">

                            <!-- Account Name -->
                            <div class="flex flex-col">
                                <label class="mb-2 text-sm font-medium text-gray-600">
                                    Account Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="account_name"
                                       value="{{ old('account_name', $account->account_name) }}"
                                       class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                       placeholder="e.g., Cash Account"
                                       required>
                                @error('account_name')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Additional Fields if needed -->
                            <!-- Example: Status -->
                            <div class="flex flex-col">
                                <label class="mb-2 text-sm font-medium text-gray-600">Status</label>
                                <select name="status" class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500">
                                    <option value="1" {{ $account->status == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $account->status == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                        </div>

                        <!-- Submit -->
                        <div class="mt-6 text-right">
                            <button type="submit"
                                    class="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-6 rounded transition">
                                Update GL Account
                            </button>
                        </div>

                    </form>
                </div>

                <!-- Right Part: Summary / Additional Info -->
                <div class="bg-white p-6 rounded-lg shadow space-y-4">

                    <h4 class="text-lg font-semibold text-gray-800 border-b pb-2">Account Info</h4>

                    <div class="flex justify-between">
                        <span class="text-gray-600 font-medium">Created At:</span>
                        <span class="text-gray-800">{{ $account->created_at->format('d-m-Y') }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600 font-medium">Last Updated:</span>
                        <span class="text-gray-800">{{ $account->updated_at->format('d-m-Y') }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-600 font-medium">Status:</span>
                        <span class="text-green-600 font-bold">
                        {{ $account->status == 1 ? 'Active' : 'Inactive' }}
                    </span>
                    </div>

                    <div>
                        <h5 class="text-gray-700 font-medium mt-4 mb-1">Notes</h5>
                        <p class="text-gray-600 text-sm">
                            You can use this section to display additional information about the GL account, such as linked transactions, history, or remarks.
                        </p>
                    </div>

                </div>

            </div>

        </div>
    </div>

@endsection
