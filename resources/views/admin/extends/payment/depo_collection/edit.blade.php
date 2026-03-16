@extends('admin.master')

@section('content')

    <div class="page-wrapper bg-white p-6 rounded shadow">
        <div class="content container mx-auto px-4 overflow-y-auto">

            <!-- Page Header -->
            @include('admin.include.message')
            <div class="mb-6">
                <div class="flex flex-col md:flex-row items-center justify-between mt-5">

                    <h3 class="text-2xl font-semibold text-gray-800">
                        Collect Due From Depo
                    </h3>

                    <a href="{{ route('admin.depo-due-collection.index') }}"
                       class="mt-3 md:mt-0 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded shadow transition">
                        <i class="fa fa-reply"></i>
                        Due Collection List
                    </a>

                </div>
            </div>

        </div>
    </div>

    <div class="min-h-screen bg-gray-100 p-6">
        <div class="max-w-5xl mx-auto bg-white p-6 rounded shadow">


            <form action="{{ route('admin.depo-due-collection.update', $depoDueCollection->id) }}" method="POST">
                @csrf
                @method('PUT')


                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Depo Name (readonly) -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Depo Name</label>
                        <input type="text" value="{{ $depoDueCollection->depo_name }}" readonly
                               class="w-full p-2 border border-gray-300 rounded bg-gray-100">
                        @error('depo_name')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Receiving Amount (readonly) -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Paying Amount</label>
                        <input type="number" name="receiving_amount" value="{{ $depoDueCollection->receiving_amount }}" readonly
                               class="w-full p-2 border border-gray-300 rounded bg-gray-100">
                        @error('receiving_amount')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Company Account Dropdown -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Company Account</label>
                        <select name="account_id"
                                class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500">
                            <option value="">-- Select Account --</option>
                            @foreach($companyAccount as $account)
                                <option value="{{ $account->id }}"
                                    {{ $depoDueCollection->account_id == $account->id ? 'selected' : '' }}>
                                    {{ $account->account_name }} ({{ $account->balance }})
                                </option>
                            @endforeach
                        </select>
                        @error('account_id')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Status Dropdown -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Status</label>
                        <select name="status" required
                                class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500">
                            <option value="1" {{ $depoDueCollection->status == 1 ? 'selected' : '' }}>Pending</option>
                            <option value="2" {{ $depoDueCollection->status == 2 ? 'selected' : '' }}>Approved</option>
                            <option value="3" {{ $depoDueCollection->status == 3 ? 'selected' : '' }}>Rejected</option>
                        </select>
                        @error('status')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

                <!-- Submit Button -->
                <div class="mt-6">
                    <button type="submit"
                            class="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-6 rounded transition">
                        Update
                    </button>
                    <a href="{{ route('admin.depo-due-collection.index') }}"
                       class="ml-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-6 rounded transition">
                        Cancel
                    </a>
                </div>

            </form>

        </div>
    </div>

@endsection
