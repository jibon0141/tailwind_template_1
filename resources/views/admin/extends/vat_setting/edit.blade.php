@extends('admin.master')
@section('content')

    <div class="min-h-screen mb-5 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4">

            <!-- Page Header -->
            @include('admin.include.message')
            <div class="mb-6 mt-2 pt-6 flex flex-col md:flex-row items-center justify-between gap-4">
                <h3 class="text-2xl font-bold text-gray-800">
                    Edit VAT
                </h3>

                <a href="{{ route('vat.index') }}"
                   class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
                    <i class="fa fa-reply"></i>
                    VAT List
                </a>
            </div>

            <!-- Form Card -->
            <div class="mt-8 bg-white p-6 rounded-lg shadow">
                <form action="{{ route('vat.update', $vat->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                        <!-- Depo (required, readonly or dropdown) -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Depo <span class="text-red-500">*</span>
                            </label>
                            <select name="depo_id"
                                    class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500">
                                <option value="" disabled>Select Depo</option>
                                @foreach($depos as $depo)
                                    <option value="{{ $depo->id }}" {{ $vat->depo_id == $depo->id ? 'selected' : '' }}>
                                        {{ $depo->depo_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('depo_id')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- VAT Percentage (required) -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                VAT Percentage <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="vat_percentage" step="0.01"
                                   value="{{ old('vat_percentage', $vat->vat_percentage) }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="e.g., 5.00" required>
                            @error('vat_percentage')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Status (required) -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status"
                                    class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500">
                                <option value="1" {{ old('status', $vat->status) == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status', $vat->status) == 0 ? 'selected' : '' }}>Inactive</option>
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
                            Update VAT
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>

@endsection
