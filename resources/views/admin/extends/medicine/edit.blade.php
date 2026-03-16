@extends("admin.master")

<style>
    /* Same Select2 style as create */
    #company_select + .select2-container .select2-selection,
    #generic_select + .select2-container .select2-selection,
    #brand_select + .select2-container .select2-selection,
    #category_select + .select2-container .select2-selection,
    #dosage_select + .select2-container .select2-selection {
        height: 42px !important;
        line-height: 42px !important;
        padding: 0 10px;
        border-radius: 0.375rem;
        border: 1px solid #d1d5db;
    }

    .select2-selection__arrow {
        height: 42px !important;
    }

    .select2-selection__rendered {
        line-height: 42px !important;
    }
</style>

@section("content")

    <div class="min-h-screen mb-5 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4">

            @include('admin.include.message')

            <div class="mb-6 mt-2 pt-6 flex items-center justify-between">
                <h3 class="text-2xl font-bold text-gray-800">Update Medicine</h3>

                <a href="{{ route('medicine.index') }}"
                   class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-blue-700 transition">
                    <i class="fa fa-reply"></i>
                    Medicine List
                </a>
            </div>

            <div class="mt-8 bg-white p-6 rounded-lg shadow">
                <form action="{{ route('medicine.update', $medicine->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- Medicine Name -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">Medicine Name *</label>
                            <input type="text" name="medicine_name"
                                   value="{{ old('medicine_name', $medicine->medicine_name) }}"
                                   class="p-2 border border-gray-300 rounded">
                        </div>

                        <!-- Company -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">Company *</label>
                            <select id="company_select" name="company_id" class="select2">
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}"
                                        {{ old('company_id', $medicine->company_id) == $company->id ? 'selected' : '' }}>
                                        {{ $company->company_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Generic -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">Generic *</label>
                            <select id="generic_select" name="generic_name_id" class="select2">
                                @foreach($genericNames as $generic)
                                    <option value="{{ $generic->id }}"
                                        {{ old('generic_name_id', $medicine->generic_name_id) == $generic->id ? 'selected' : '' }}>
                                        {{ $generic->generic_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Brand -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">Brand *</label>
                            <select id="brand_select" name="brand_id" class="select2">
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}"
                                        {{ old('brand_id', $medicine->brand_id) == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->brand_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Category -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">Category *</label>
                            <select id="category_select" name="medicine_category_id" class="select2">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('medicine_category_id', $medicine->medicine_category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->category_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Dosage -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">Dosage *</label>
                            <select id="dosage_select" name="medicine_dosage_form_id" class="select2">
                                @foreach($dosageForms as $dosage)
                                    <option value="{{ $dosage->id }}"
                                        {{ old('medicine_dosage_form_id', $medicine->medicine_dosage_form_id) == $dosage->id ? 'selected' : '' }}>
                                        {{ $dosage->dosage_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Strength -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">Strength *</label>
                            <input type="text" name="strength_name"
                                   value="{{ old('strength_name', $medicine->strength_name) }}"
                                   class="p-2 border border-gray-300 rounded">
                        </div>

                        <!-- MRP -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">MRP *</label>
                            <input type="number" step="0.01" id="mrp" name="mrp"
                                   value="{{ old('mrp', $medicine->mrp) }}"
                                   class="p-2 border border-gray-300 rounded">
                        </div>

                        <!-- Purchase Percentage % -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">Purchase Percentage % *</label>
                            <input type="number"
                                   id="purchase_percentage"
                                   name="purchase_percentage"
                                   value="{{ old('purchase_percentage', $medicine->purchase_percentage) }}"
                                   class="p-2 border border-gray-300 rounded"
                                   min="0"
                                   max="100"
                                   step="0.01"
                                   oninput="if(this.value>100)this.value=100;">
                        </div>


                        <!-- Purchase Price -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">Purchase Price *</label>
                            <input type="number" step="0.01" id="purchase_price" name="purchase_price"
                                   value="{{ old('purchase_price', $medicine->purchase_price) }}"
                                   class="p-2 border border-gray-300 rounded">
                        </div>

                        <!-- Sale Percentage % -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">Sale Percentage % *</label>
                            <input type="number"
                                   id="sale_percentage"
                                   name="sale_percentage"
                                   min="0"
                                   max="100"
                                   step="0.01"
                                   value="{{ old('sale_percentage', $medicine->sale_percentage) }}"
                                   class="p-2 border border-gray-300 rounded"
                                   oninput="if(this.value>100) this.value=100; if(this.value<0) this.value=0;">
                        </div>


                        <!-- Sale Price -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">Sale Price *</label>
                            <input type="number" step="0.01" id="sale_price" name="sale_price"
                                   value="{{ old('sale_price', $medicine->sale_price) }}"
                                   class="p-2 border border-gray-300 rounded">
                        </div>

                        <!-- Status -->
                        <div class="flex flex-col">
                            <label class="mb-2 text-sm font-medium text-gray-600">Status *</label>
                            <select name="status" class="p-2 border border-gray-300 rounded">
                                <option value="1" {{ $medicine->status == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ $medicine->status == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                    </div>

                    <div class="mt-6 text-right">
                        <button type="submit"
                                class="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-6 rounded">
                            Update Medicine
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {

            $('.select2').select2({ width: '100%' });

            function calculatePrices() {
                let mrp = parseFloat($('#mrp').val()) || 0;
                let purchasePercent = parseFloat($('#purchase_percentage').val()) || 0;
                let salePercent = parseFloat($('#sale_percentage').val()) || 0;

                if (mrp > 0 && purchasePercent >= 0) {
                    $('#purchase_price').val((mrp - (mrp * purchasePercent / 100)).toFixed(2));
                }

                if (mrp > 0 && salePercent >= 0) {
                    $('#sale_price').val((mrp - (mrp * salePercent / 100)).toFixed(2));
                }
            }

            $('#mrp, #purchase_percentage, #sale_percentage').on('input', calculatePrices);
        });
    </script>
@endsection
