@extends("admin.master")
@section("content")

    <div class="min-h-screen mb-5 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4">

            <!-- Page Header -->
            @include('admin.include.message')
            <div class="mb-6 mt-2 pt-6 flex items-center justify-between">
                <h3 class="text-2xl font-bold text-gray-800">
                    Create New Strength
                </h3>

                <a href="{{ route('strength.index') }}"
                   class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
                    <i class="fa fa-reply"></i>
                    Strength List
                </a>
            </div>

            <!-- Form Card -->
            <div class="mt-8 bg-white p-6 rounded-lg shadow">
                <form action="{{ route('strength.create') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Strength Name -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Strength Name
                            </label>
                            <input type="text" name="strength_name"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="e.g., 500 mg, 250 mg">
                        </div>

                        <!-- Dosage Name -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Dosage Name
                            </label>

                            <select name="medicine_dosage_form_id"
                                    class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500">
                                <option value="" disabled selected>Select Dosage</option>

                                @foreach($dosages as $dosage)
                                    <option value="{{ $dosage->id }}">
                                        {{ $dosage->dosage_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>


                        <!-- Status -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Status
                            </label>
                            <select name="status"
                                    class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mt-6 flex flex-col">
                        <label class="mb-2 text-sm font-medium text-gray-600">
                           Strength Description
                        </label>
                        <textarea name="strength_description" rows="4"
                                  class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                  placeholder="Short description"></textarea>
                    </div>

                    <!-- Submit -->
                    <div class="mt-6 text-right">
                        <button type="submit"
                                class="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-6 rounded transition">
                            Save Strength
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

@endsection
