@extends("admin.master")
@section("content")

    <div class="min-h-screen mb-5 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4">

            <!-- Page Header -->
            @include('admin.include.message')
            <div class="mb-6 mt-2 pt-6 flex items-center justify-between">
                <h3 class="text-2xl font-bold text-gray-800">
                    Create New Depo
                </h3>

                <a href="{{ route('depo.index') }}"
                   class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
                    <i class="fa fa-reply"></i>
                    Depo List
                </a>
            </div>

            <!-- Form Card -->
            <div class="mt-8 bg-white p-6 rounded-lg shadow">
                <form action="{{ route('depo.create') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                        <!-- Depo Name (required) -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Depo Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="depo_name"
                                   value="{{ old('depo_name') }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="e.g., Dhaka Central Depo"
                                   required>
                            @error('depo_name')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Depo Name (required) -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Person Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="person_name"
                                   value="{{ old('person_name') }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="e.g., depo manager or owner name"
                                   required>
                            @error('person_name')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Area Code (required) -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                               Area Code <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="area_code"
                                   value="{{ old('area_code') }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="e.g., Area Code"
                                   required>
                            @error('area_code')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>

                    <!-- Password Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">

                        <!-- Email (required) -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email"
                                   value="{{ old('email') }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="e.g., john@example.com"
                                   required>
                            @error('email')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Division (required) -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Division <span class="text-red-500">*</span>
                            </label>
                            <select name="division_id" id="division_id"
                                    class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                    required>
                                <option value="">Select Division</option>
                                @foreach($divisions as $division)
                                    <option value="{{ $division->id }}">
                                        {{ $division->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('division')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>


                    </div>




                    <!-- Contact + Balance on same line -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">

                        <!-- District (required) -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                District <span class="text-red-500">*</span>
                            </label>
                            <select name="district_id" id="district_id"
                                    class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                    required>
                                <option value="">Select District</option>
                            </select>
                            @error('district')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Contact -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Contact <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="contact"
                                   value="{{ old('contact') }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="e.g., 01XXXXXXXXX"
                                   required>
                            @error('contact')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>


                    </div>


                    <!-- Account Details -->
                    <div class="flex flex-col mt-6">
                        <label class="mb-2 text-sm font-medium text-gray-600">
                            Account Details <span class="text-red-500">*</span>
                        </label>
                        <textarea name="account_no"
                                  rows="3"
                                  class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                  placeholder="Bank Name, Account Number, Branch Name"
                                  required>{{ old('account_no') }}</textarea>
                        @error('account_no')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div class="mt-6 flex flex-col">
                        <label class="mb-2 text-sm font-medium text-gray-600">
                            Address <span class="text-red-500">*</span>
                        </label>
                        <textarea name="address" rows="3"
                                  class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                  placeholder="Depo full address"
                                  required>{{ old('address') }}</textarea>
                        @error('address')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <!-- Password (required) -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="Enter password"
                                   required>
                            @error('password')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Confirm Password (required) -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Confirm Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password_confirmation"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="Confirm password"
                                   required>
                        </div>

                    </div>

                    <!-- Submit -->
                    <div class="mt-6 text-right">
                        <button type="submit"
                                class="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-6 rounded transition">
                            Save Depo
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function () {

            $("#division_id").change(function () {
                let id = $(this).val();
                console.log('working');

                // Show loading
                $("#district_id").html('<option value="">Loading...</option>');

                if (id) {
                    $.ajax({
                        url: "{{ route('get.district', '') }}/" + id,
                        type: "GET",
                        success: function (response) {
                            let districts = response.data;
                            $("#district_id").empty();
                            $("#district_id").append('<option value="">Select District</option>');
                            $.each(districts, function (index, district) {
                                $("#district_id").append(
                                    '<option value="' + district.id + '">' + district.name + '</option>'
                                );
                            });
                        },
                        error: function () {
                            $("#district_id").html('<option value="">Something went wrong</option>');
                        }
                    });
                } else {
                    $("#district_id").html('<option value="">Select District</option>');
                }
            });

        });
    </script>
@endsection
