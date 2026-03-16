@extends('admin.master')

@section('content')
    <div class="min-h-screen bg-gray-100 p-6">
        <div class="max-w-7xl mx-auto">
            @include('admin.include.message')

            <div class="mb-6 mt-2 pt-6 flex items-center justify-between">
                <h3 class="text-2xl font-bold text-gray-800">Create New ASM</h3>
                <a href="{{ route('asm.index') }}"
                   class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
                    <i class="fa fa-reply"></i>
                    ASM List
                </a>
            </div>

            <form action="{{ route('asm.create') }}" method="POST" class="bg-white p-6 rounded shadow">
                @csrf

                <h4 class="font-semibold mb-4 text-lg">Personal Information</h4>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="full_name" value="{{ old('full_name') }}" class="w-full border p-2 rounded">
                        @error('full_name') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                    </div>


                    <div>
                        <label class="block text-sm font-medium mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" class="w-full border p-2 rounded">
                        @error('email') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Phone <span class="text-red-500">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="w-full border p-2 rounded">
                        @error('phone') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-2 text-sm font-medium text-gray-600">Division <span class="text-red-500">*</span></label>
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
                        <label class="mb-2 text-sm font-medium text-gray-600">District <span class="text-red-500">*</span></label>
                        <select id="district_id" name="district_id" class="w-full border p-2 rounded">
                            <option value="">-- Select District --</option>
                        </select>

                        @error('district_id') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Address <span class="text-red-500">*</span></label>
                        <textarea name="address" class="w-full border p-2 rounded">{{ old('address') }}</textarea>
                        @error('address') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password" class="w-full border p-2 rounded">
                        @error('password') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Confirm Password<span class="text-red-500">*</span></label>
                        <input type="password" name="password_confirmation" class="w-full border p-2 rounded">
                    </div>
                </div>

                <h4 class="font-semibold mt-8 mb-4 text-lg">Assign Parent SM</h4>
                <div class="grid md:grid-cols-2 gap-4">

                    {{-- Employee Type (readonly) --}}
                    <div>
                        <label class="block text-sm font-medium mb-1">Employee Type</label>
                        <input type="text" value="ASM" class="w-full border p-2 rounded" readonly>
                        <input type="hidden" name="employee_type" value="sm">
                    </div>

                    <div>
                    <label class="block text-sm font-medium mb-1">Parent SM <span class="text-red-500">*</span></label>
                    <select name="parent_id" class="w-full border p-2 rounded">
                        <option value="">Select SM</option>
                        @foreach($sms as $sm)
                            <option value="{{ $sm->id }}" {{ old('parent_id') == $sm->id ? 'selected' : '' }}>
                                {{ $sm->full_name }} {{ $sm->last_name }} | {{ $sm->employee_code ?? '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('parent_id') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                </div>
                </div>

                <div class="mt-6 text-right">
                    <button type="submit" class="bg-teal-500 hover:bg-teal-600 text-white px-6 py-2 rounded">Create ASM</button>
                </div>

                <input type="hidden" name="employee_type" value="asm">
            </form>
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

