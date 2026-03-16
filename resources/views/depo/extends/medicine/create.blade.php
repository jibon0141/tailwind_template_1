@extends("depo.master")
@section("content")

    <div class="min-h-screen mb-5 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4">

            <!-- Page Header -->
            @include('admin.include.message')
            <div class="mb-6 mt-2 pt-6 flex items-center justify-between">
                <h3 class="text-2xl font-bold text-gray-800">
                    Create New Medicine
                </h3>

                <a href="{{ route('depo.medicine.index') }}"
                   class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
                    <i class="fa fa-reply"></i>
                    Medicine List
                </a>
            </div>

            <!-- Form Card -->
            <div class="mt-8 bg-white p-6 rounded-lg shadow">
                <form action="{{ route('depo.medicine.create') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Medicine Name -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Medicine Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="medicine_name" value="{{ old('medicine_name') }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="Enter medicine name">
                            @error('medicine_name')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Generic Name -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Generic Name <span class="text-red-500">*</span>
                            </label>
                            <select name="generic_name_id"
                                    class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500">
                                <option value="">Select Generic Name</option>
                                @foreach($genericNames as $generic)
                                    <option value="{{ $generic->id }}" {{ old('generic_name_id') == $generic->id ? 'selected' : '' }}>
                                        {{ $generic->generic_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('generic_name_id')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Brand -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Brand <span class="text-red-500">*</span>
                            </label>
                            <select name="brand_id"
                                    class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500">
                                <option value="">Select Brand</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->brand_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('brand_id')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Category <span class="text-red-500">*</span>
                            </label>
                            <select name="medicine_category_id"
                                    class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('medicine_category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->category_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('medicine_category_id')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Dosage Form -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Dosage Form <span class="text-red-500">*</span>
                            </label>
                            <select name="medicine_dosage_form_id" id="medicine_dosage_form_id"
                                    class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500">
                                <option value="">Select Dosage Form</option>
                                @foreach($dosageForms as $dosage)
                                    <option value="{{ $dosage->id }}" {{ old('medicine_dosage_form_id') == $dosage->id ? 'selected' : '' }}>
                                        {{ $dosage->dosage_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('medicine_dosage_form_id')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Strength -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Strength <span class="text-red-500">*</span>
                            </label>

                            <input type="text"
                                   name="strength_name"
                                   id="strength_name"
                                   value="{{ old('strength_name') }}"
                                   placeholder="e.g. 500 mg, 5 mg/5 ml"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500">

                            @error('strength')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>


                        <!-- Purchase Price -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Purchase Price <span class="text-red-500">*</span>
                            </label>
                            <input type="number" step="0.01" name="purchase_price" value="{{ old('purchase_price') }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="Enter purchase price">
                            @error('purchase_price')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Sale Price -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Sale Price <span class="text-red-500">*</span>
                            </label>
                            <input type="number" step="0.01" name="sale_price" value="{{ old('sale_price') }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="Enter sale price">
                            @error('sale_price')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- MRP -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                MRP <span class="text-red-500">*</span>
                            </label>
                            <input type="number" step="0.01" name="mrp" value="{{ old('mrp') }}"
                                   class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500"
                                   placeholder="Enter MRP">
                            @error('mrp')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status" class="p-2 border border-gray-300 rounded focus:outline-none focus:border-teal-500">
                                <option disabled>--Select Option--</option>
                                <option value="1" >Active</option>
                                <option value="0">Inactive</option>
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
                            Save Medicine
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

        $("#medicine_dosage_form_id").change(function () {
            let id = $(this).val();
            console.log('working');

            $("#strength_id").html('<option value="">Loading...</option>');

            if (id) {
                $.ajax({
                    url: "{{ route('get.strengths', '') }}/" + id,
                    type: "GET",
                    success: function (data) {
                        $("#strength_id").empty();
                        $("#strength_id").append('<option value="">Select Strength</option>');

                        $.each(data, function (key, value) {
                            $("#strength_id").append('<option value="' + key + '">' + value + '</option>');
                        });
                    }
                });
            } else {
                $("#strength_id").html('<option value="">Select Strength</option>');
            }
        });

    });
</script>
@endsection

