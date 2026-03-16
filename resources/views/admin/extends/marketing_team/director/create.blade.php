@extends('admin.master')

@section('content')
    <div class="min-h-screen bg-gray-100 p-6">
        <div class="max-w-7xl mx-auto">

            @include('admin.include.message')

            <!-- Page Header -->
            <div class="mb-6">
                <div class="flex flex-col md:flex-row items-center justify-between mt-5">

                    <!-- Title -->
                    <h3 class="text-2xl font-semibold text-gray-800">
                        Create New Director
                    </h3>

                    <!-- Add Account Button -->
                    <a href="{{ route('director.index') }}"
                       class="mt-3 md:mt-0 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded shadow transition">
                        <i class="fa fa-reply"></i>
                        Director List
                    </a>

                </div>
            </div>

            <form action="{{ route('director.create') }}" method="POST" enctype="multipart/form-data"
                  class="bg-white p-6 rounded shadow">
                @csrf

                {{-- ================= Personal Info ================= --}}
                <h4 class="font-semibold mb-4 text-lg">Personal Information</h4>

                <div class="grid md:grid-cols-2 gap-4">

                    <div>
                        <label class="mb-2 text-sm font-medium text-gray-600">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="full_name" value="{{ old('full_name') }}"
                               class="w-full border p-2 rounded" placeholder="Full Name">
                        @error('full_name') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-2 text-sm font-medium text-gray-600">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="w-full border p-2 rounded" placeholder="Email">
                        @error('email') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-2 text-sm font-medium text-gray-600">
                            Phone <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                               class="w-full border p-2 rounded" placeholder="Phone">
                        @error('phone') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-2 text-sm font-medium text-gray-600">
                            Division <span class="text-red-500">*</span>
                        </label>
                        <select name="division_id" id="division_id" class="w-full border p-2 rounded">
                            <option value="" disabled>-- Select Division --</option>
                            @foreach ($divisions as $division)
                                <option value="{{ $division->id }}">
                                    {{ $division->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('division_id')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 text-sm font-medium text-gray-600">
                            District <span class="text-red-500">*</span>
                        </label>
                        <select id="district_id" name="district_id" class="w-full border p-2 rounded">
                            <option value="">-- Select District --</option>
                        </select>
                        @error('district_id') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <!-- Address (NOT REQUIRED) -->
                    <div class="md:col-span-2">
                        <label class="mb-2 text-sm font-medium text-gray-600">
                            Address  <span class="text-red-500">*</span>
                        </label>
                        <textarea name="address" class="w-full border p-2 rounded"
                                  placeholder="Address">{{ old('address') }}</textarea>
                        @error('address') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-2 text-sm font-medium text-gray-600">
                          Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password"
                               class="w-full border p-2 rounded" placeholder="Password">
                        @error('password') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-2 text-sm font-medium text-gray-600">
                          Confirm Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password_confirmation"
                               class="w-full border p-2 rounded" placeholder="Confirm Password">
                    </div>
                </div>

                {{-- ================= Hidden Employee Type ================= --}}
                <input type="hidden" name="employee_type" value="director">

                {{-- ================= Submit Button ================= --}}
                <div class="mt-6 text-right">
                    <button type="submit" class="bg-teal-500 hover:bg-teal-600 text-white px-6 py-2 rounded">
                        Save Director
                    </button>
                </div>

            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {

            $("#division_id").change(function () {
                let id = $(this).val();

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
