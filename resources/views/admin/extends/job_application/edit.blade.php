@extends("admin.master")
@section("content")

    <div class="min-h-screen mb-5 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4">

            <!-- Page Header -->
            @include('admin.include.message')
            <div class="mb-6 mt-2 pt-6 flex items-center justify-between">
                <h3 class="text-2xl font-bold text-gray-800">
                    Update Job Application
                </h3>

                <a href="{{ route('admin.job.application.index') }}"
                   class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
                    <i class="fa fa-reply"></i>
                    Application List
                </a>
            </div>

            <!-- Form Card -->
            <div class="mt-8 bg-white p-6 rounded-lg shadow">
                <form action="{{ route('admin.job.application.update', $application->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label class="block mb-1 text-sm font-medium">Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $application->name) }}"
                                   class="w-full p-2 border rounded">
                            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Mobile -->
                        <div>
                            <label class="block mb-1 text-sm font-medium">Mobile <span class="text-red-500">*</span></label>
                            <input type="text" name="mobile" value="{{ old('mobile', $application->mobile) }}"
                                   class="w-full p-2 border rounded">
                            @error('mobile') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Father/Husband Name -->
                        <div>
                            <label class="block mb-1 text-sm font-medium">Father / Husband Name <span class="text-red-500">*</span></label>
                            <input type="text" name="father_or_husband_name"
                                   value="{{ old('father_or_husband_name', $application->father_or_husband_name) }}"
                                   class="w-full p-2 border rounded">
                            @error('father_or_husband_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Mother Name -->
                        <div>
                            <label class="block mb-1 text-sm font-medium">Mother Name <span class="text-red-500">*</span></label>
                            <input type="text" name="mother_name" value="{{ old('mother_name', $application->mother_name) }}"
                                   class="w-full p-2 border rounded">
                            @error('mother_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block mb-1 text-sm font-medium">Email</label>
                            <input type="email" name="email" value="{{ old('email', $application->email) }}"
                                   class="w-full p-2 border rounded">
                            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- NID -->
                        <div>
                            <label class="block mb-1 text-sm font-medium">NID No</label>
                            <input type="text" name="nid_no" value="{{ old('nid_no', $application->nid_no) }}"
                                   class="w-full p-2 border rounded">
                            @error('nid_no') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Marital Status -->
                        <div>
                            <label class="block mb-1 text-sm font-medium">Marital Status</label>
                            <select name="marital_status" class="w-full p-2 border rounded">
                                <option value="">Select</option>
                                @foreach($maritalStatus as $key => $status)
                                    <option value="{{ $key }}" {{ old('marital_status', $application->marital_status) == $key ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                            @error('marital_status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Blood Group -->
                        <div>
                            <label class="block mb-1 text-sm font-medium">Blood Group</label>
                            <select name="blood_group" class="w-full p-2 border rounded">
                                <option value="">Select</option>
                                @foreach($bloodGroups as $key => $bg)
                                    <option value="{{ $key }}" {{ old('blood_group', $application->blood_group) == $key ? 'selected' : '' }}>
                                        {{ $bg }}
                                    </option>
                                @endforeach
                            </select>
                            @error('blood_group') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- DOB -->
                        <div>
                            <label class="block mb-1 text-sm font-medium">Date of Birth <span class="text-red-500">*</span></label>
                            <input type="date" name="date_of_birth"
                                   value="{{ old('date_of_birth', $application->date_of_birth) }}"
                                   class="w-full p-2 border rounded">
                            @error('date_of_birth') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Age -->
                        <div>
                            <label class="block mb-1 text-sm font-medium">Age <span class="text-red-500">*</span></label>
                            <input type="number" name="age" value="{{ old('age', $application->age) }}"
                                   class="w-full p-2 border rounded">
                            @error('age') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Nationality -->
                        <div>
                            <label class="block mb-1 text-sm font-medium">Nationality</label>
                            <input type="text" name="nationality" value="{{ old('nationality', $application->nationality) }}"
                                   class="w-full p-2 border rounded">
                        </div>

                        <!-- Religion -->
                        <div>
                            <label class="block mb-1 text-sm font-medium">Religion <span class="text-red-500">*</span></label>
                            <select name="religion" class="w-full p-2 border rounded">
                                <option value="">Select</option>
                                @foreach($religions as $key => $religion)
                                    <option value="{{ $key }}" {{ old('religion', $application->religion) == $key ? 'selected' : '' }}>
                                        {{ $religion }}
                                    </option>
                                @endforeach
                            </select>
                            @error('religion') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Designation -->
                        <div>
                            <label class="block mb-1 text-sm font-medium">Designation <span class="text-red-500">*</span></label>
                            <input type="text" name="designation" value="{{ old('designation', $application->designation) }}"
                                   class="w-full p-2 border rounded">
                            @error('designation') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Branch -->
                        <div>
                            <label class="block mb-1 text-sm font-medium">Branch</label>
                            <input type="text" name="branch" value="{{ old('branch', $application->branch) }}"
                                   class="w-full p-2 border rounded">
                        </div>

                        <!-- Application Date -->
                        <div>
                            <label class="block mb-1 text-sm font-medium">Application Date</label>
                            <input type="date" name="application_date"
                                   value="{{ old('application_date', $application->application_date ?? date('Y-m-d')) }}"
                                   class="w-full p-2 border rounded">
                        </div>

                    </div>

                    <!-- Experience -->
                    <div class="mt-6">
                        <label class="block mb-1 text-sm font-medium">Experience</label>
                        <textarea name="experience" rows="3"
                                  class="w-full p-2 border rounded">{{ old('experience', $application->experience) }}</textarea>
                    </div>

                    <!-- Current Address -->
                    <div class="mt-4">
                        <label class="block mb-1 text-sm font-medium">Current Address <span class="text-red-500">*</span></label>
                        <textarea name="current_address" rows="3"
                                  class="w-full p-2 border rounded">{{ old('current_address', $application->current_address) }}</textarea>
                    </div>

                    <!-- Permanent Address -->
                    <div class="mt-4">
                        <label class="block mb-1 text-sm font-medium">Permanent Address <span class="text-red-500">*</span></label>
                        <textarea name="permanent_address" rows="3"
                                  class="w-full p-2 border rounded">{{ old('permanent_address', $application->permanent_address) }}</textarea>
                    </div>

                    <!-- Submit -->
                    <div class="mt-6 text-right">
                        <button type="submit"
                                class="bg-teal-500 hover:bg-teal-600 text-white px-6 py-2 rounded">
                            Update Application
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>

@endsection
