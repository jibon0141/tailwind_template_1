@extends("admin.master")

<style>
    #company_select + .select2-container .select2-selection,
    #category_select + .select2-container .select2-selection {
        height: 42px !important;
        line-height: 42px !important;
        padding: 0 10px;
        border-radius: 0.375rem;
        border: 1px solid #d1d5db;
    }

    #company_select + .select2-container .select2-selection__arrow,
    #category_select + .select2-container .select2-selection__arrow {
        height: 42px !important;
    }

    #company_select + .select2-container .select2-selection__rendered,
    #category_select + .select2-container .select2-selection__rendered {
        line-height: 42px !important;
    }
</style>

@section("content")
    <div class="page-wrapper bg-white p-6 rounded shadow">
        <div class="content container mx-auto px-4">

            <h3 class="text-2xl font-semibold mb-6">Medicine Requisition</h3>

            @include('admin.include.message')

            <!-- Filters -->
            <div class="bg-gray-100 p-4 rounded mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <select id="company_select" class="w-full"></select>
                <select id="category_select" class="w-full"></select>
            </div>

            <form method="POST" action="{{ route('admin.requisition.create') }}">
                @csrf
                <input type="hidden" name="company_name" id="hidden_company_name">
                <input type="hidden" name="final_total" id="hidden_final_total">

                <div class="overflow-x-auto">
                    <table id="medicineTable" class="min-w-full text-sm border">
                        <thead class="bg-gray-50">
                        <tr class="text-xs uppercase text-gray-600">
                            <th>SL</th>
                            <th>Medicine</th>
                            <th>MRP</th>
                            <th>Discount (%)</th>
                            <th>Purchase</th>
                            <th>Qty</th>
                            <th>Sub Total</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                        <tr class="bg-gray-200 font-semibold">
                            <td colspan="6" class="text-right px-3 py-2">Final Total</td>
                            <td class="px-3 py-2">
                                <input type="text" id="final_total" readonly class="w-full border rounded bg-gray-100 p-1">
                            </td>
                        </tr>
                        </tfoot>
                    </table>

                    <div class="mt-4 text-right">
                        <button type="submit" class="bg-teal-600 text-white px-4 py-2 rounded">
                            Create Requisition
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {

            /* ====== Select2 Company ====== */
            $('#company_select').select2({
                placeholder: 'Select Company',
                allowClear: true,
                ajax: {
                    url: '{{ route("admin.requisition.getCompanyAjax") }}',
                    dataType: 'json',
                    data: params => ({ q: params.term }),
                    processResults: data => ({ results: data.results })
                }
            });

            /* ====== Select2 Category ====== */
            $('#category_select').select2({
                placeholder: 'Select Category',
                allowClear: true,
                ajax: {
                    url: '{{ route("admin.requisition.getCategoryAjax") }}',
                    dataType: 'json',
                    data: params => ({
                        q: params.term,
                        company_id: $('#company_select').val()
                    }),
                    processResults: data => ({ results: data.results })
                }
            });

            /* ====== Load Medicines ====== */
            $('#company_select, #category_select').on('change', loadMedicines);

            function loadMedicines() {
                let company_id  = $('#company_select').val();
                let category_id = $('#category_select').val();

                if (!company_id && !category_id) {
                    $('#medicineTable tbody').empty();
                    $('#hidden_company_name').val('');
                    $('#hidden_final_total').val('0.00');
                    $('#final_total').val('0.00');
                    return;
                }

                $.get('{{ route("admin.requisition.getMedicine") }}', { company_id, category_id }, function(res) {
                    let rows = '';
                    res.medicines.forEach((m, i) => {
                        rows += `
<tr>
    <td>${i+1}</td>

    <td>
        ${m.name}
        <input type="hidden" name="medicine_id[]" value="${m.id}">
        <input type="hidden" name="medicine_name[]" value="${m.name}">
    </td>

    <td>
        <input type="number" name="mrp[]" class="mrp w-full border p-1 bg-gray-100" readonly value="${m.mrp}">
    </td>

    <td>
        <input type="number" name="discount[]" class="discount w-full border p-1" value="0" min="0" max="100">
    </td>

    <td>
        <input type="number" name="purchase_price[]" class="purchase w-full border p-1 bg-gray-100" readonly>
    </td>

    <td>
        <input type="number" name="quantity[]" class="qty w-full border p-1" value="0" min="0">
    </td>

    <td>
        <input type="text" name="sub_total_price[]" class="total w-full border p-1 bg-gray-100" readonly>
    </td>
</tr>`;
                    });

                    $('#medicineTable tbody').html(rows);
                    $('#hidden_company_name').val($('#company_select option:selected').text());
                    calculateAll();
                });
            }

            /* ====== Auto-clear 0 on focus ====== */
            $('#medicineTable').on('focus', '.discount, .qty', function() {
                if ($(this).val() == '0') $(this).val('');
            });

            /* ====== Auto calculation ====== */
            $('#medicineTable').on('input', '.mrp, .discount, .qty', calculateAll);

            function calculateAll() {
                let finalTotal = 0;

                $('#medicineTable tbody tr').each(function () {
                    let row = $(this);
                    let mrp      = parseFloat(row.find('.mrp').val()) || 0;
                    let discount = parseFloat(row.find('.discount').val()) || 0;
                    let qty      = parseFloat(row.find('.qty').val()) || 0;

                    let purchase = mrp - (mrp * discount / 100);
                    let subtotal = purchase * qty;

                    row.find('.purchase').val(purchase.toFixed(2));
                    row.find('.total').val(subtotal.toFixed(2));

                    finalTotal += subtotal;
                });

                $('#final_total').val(finalTotal.toFixed(2));
                $('#hidden_final_total').val(finalTotal.toFixed(2));
            }

        });
    </script>
@endsection
