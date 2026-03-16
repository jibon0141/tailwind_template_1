@extends('depo.master')
@section('content')

    <div class="min-h-screen mb-5 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4">

            @include('admin.include.message')

            <div class="mb-6 mt-2 pt-6 flex items-center justify-between">
                <h3 class="text-2xl font-bold text-gray-800">
                    Create New Payment
                </h3>

                <a href="{{ route('depo.depo-due-payment.index') }}"
                   class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
                    <i class="fa fa-reply"></i>
                    Payment List
                </a>
            </div>

            <div class="mt-8 bg-white p-6 rounded-lg shadow">
                <form action="{{ route('depo.depo-due-payment.create') }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- FIXED DEPO --}}
                        <input type="hidden" name="depo_id" value="{{ $depo->id }}">

                        <!-- Depo Name -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Depo Name
                            </label>
                            <input type="text"
                                   name="depo_name"
                                   value="{{ $depo->depo_name }}"
                                   class="p-2 border border-gray-300 rounded bg-gray-100"
                                   readonly>
                        </div>

                        <!-- Depo Phone -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Depo Phone
                            </label>
                            <input type="text"
                                   value="{{ $depo->contact }}"
                                   class="p-2 border border-gray-300 rounded bg-gray-100"
                                   readonly>
                        </div>

                        <!-- Payment Date -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Payment Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date"
                                   name="payment_date"
                                   value="{{ old('payment_date',now()->format('Y-m-d')) }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500">
                            @error('payment_date')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Depo Account -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Depo Account <span class="text-red-500">*</span>
                            </label>
                            <select name="depo_account_id"
                                    class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500">
                                <option value="">-- Select Depo Account --</option>

                                @foreach($depo->account as $account)
                                    <option value="{{ $account->id }}">
                                        {{ $account->account_name }}
                                        (Balance: {{ $account->balance }})
                                    </option>
                                @endforeach
                            </select>
                            @error('depo_account_id')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Receivable Amount -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Payable Amount
                            </label>
                            <input type="number"
                                   name="balance"
                                   value="{{ $depo->depoDueAccount->due_balance ?? 0 }}"
                                   class="p-2 border border-gray-300 rounded bg-gray-100"
                                   readonly>
                        </div>

                        <!-- Paying Amount -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Paying Amount <span class="text-red-500">*</span>
                            </label>
                            <input type="number"
                                   step="0.01"
                                   name="receiving_amount"
                                   value="{{ old('receiving_amount') }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500">
                            @error('receiving_amount')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Document -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Document
                            </label>
                            <input type="file"
                                   name="document"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500">
                        </div>

                        <!-- Note -->
                        <div class="flex flex-col md:col-span-2">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Note
                            </label>
                            <textarea name="note"
                                      rows="3"
                                      class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500">{{ old('note') }}</textarea>
                        </div>

                    </div>

                    <div class="mt-6 text-right">
                        <button type="submit"
                                class="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-6 rounded transition">
                            Save Payment
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>

@endsection
