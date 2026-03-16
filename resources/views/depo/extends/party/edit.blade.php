@extends("depo.master")
@section("content")

    <div class="min-h-screen mb-5 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4">

            @include('admin.include.message')

            <!-- Page Header -->
            <div class="mb-6 mt-2 pt-6 flex items-center justify-between">
                <h3 class="text-2xl font-bold text-gray-800">
                    Edit Party
                </h3>

                <a href="{{ route('depo.party.index') }}"
                   class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
                    <i class="fa fa-reply"></i>
                    Party List
                </a>
            </div>

            <!-- Form Card -->
            <div class="mt-8 bg-white p-6 rounded-lg shadow">
                <form action="{{ route('depo.party.update', $party->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- Party Name -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Party Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="party_name"
                                   value="{{ old('party_name', $party->party_name) }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="Enter party name">
                            @error('party_name')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Party Code (readonly) -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Party Code
                            </label>
                            <input type="text"
                                   value="{{ $party->party_code }}"
                                   class="p-2 border border-gray-300 rounded bg-gray-100 cursor-not-allowed"
                                   readonly>
                        </div>

                        <!-- Phone -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Phone <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="phone"
                                   value="{{ old('phone', $party->phone) }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="Enter phone number">
                            @error('phone')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Email
                            </label>
                            <input type="email"
                                   name="email"
                                   value="{{ old('email', $party->email) }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="Enter email">
                            @error('email')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="flex flex-col md:col-span-2">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Address
                            </label>
                            <textarea name="address" rows="3"
                                      class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                      placeholder="Enter address">{{ old('address', $party->address) }}</textarea>
                            @error('address')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>

                    <!-- Submit -->
                    <div class="mt-6 text-right">
                        <button type="submit"
                                class="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-6 rounded transition">
                            Update Party
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>

@endsection
