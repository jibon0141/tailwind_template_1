@extends('depo.master')
@section('content')

    <div class="min-h-screen mb-5 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4">

            <!-- Page Header -->
            @include('depo.include.message')
            <div class="mb-6 mt-2 pt-6 flex items-center justify-between">
                <h3 class="text-2xl font-bold text-gray-800">
                    Update Account
                </h3>

                <a href="{{ route('depo.account.index') }}"
                   class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
                    <i class="fa fa-reply"></i>
                    Account List
                </a>
            </div>

            <!-- Form Card -->
            <div class="mt-8 bg-white p-6 rounded-lg shadow">
                <form action="{{ route('depo.account.update', $account->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- Account No -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Account No <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="account_no"
                                   value="{{ old('account_no', $account->account_no) }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="e.g., ACC001"
                                   required>
                            @error('account_no')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

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

                        <!-- Opening Balance -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Opening Balance <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="opening_balance" step="0.01" min="0"
                                   value="{{ old('opening_balance', $account->opening_balance) }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none bg-gray-100"
                                   placeholder="0.00"
                                   required readonly>
                            @error('opening_balance')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Current Balance (Read Only) -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Current Balance
                            </label>
                            <input type="text"
                                   value="{{ number_format($account->balance, 2) }}"
                                   class="p-2 border border-gray-300 rounded bg-gray-100"
                                   readonly>
                        </div>

                        <!-- Status -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status"
                                    class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500">
                                <option value="1" {{ old('status', $account->status) == '1' || old('status', $account->status) === 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status', $account->status) == '0' || old('status', $account->status) === 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Is Default -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Default Account
                            </label>
                            <div class="flex items-center">
                                <input type="checkbox" name="is_default" value="1" id="is_default"
                                       {{ old('is_default', $account->is_default) == 1 ? 'checked' : '' }}
                                       class="w-4 h-4 text-teal-600 bg-gray-100 border-gray-300 rounded focus:ring-teal-500">
                                <label for="is_default" class="ml-2 text-sm font-medium text-gray-600">Set as default account</label>
                            </div>
                            @error('is_default')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>

                    <!-- Submit -->
                    <div class="mt-6 text-right">
                        <button type="submit"
                                class="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-6 rounded transition">
                            Update Account
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>

@endsection
