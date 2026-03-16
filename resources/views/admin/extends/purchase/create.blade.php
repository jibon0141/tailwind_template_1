@extends('admin.master')

@section('content')
    <form method="POST" action="{{ route('admin.purchase.create') }}">
        @csrf
        <div class="min-h-screen bg-gray-100 p-4">
            <div class="mx-auto">

                <!-- Header -->
                @include('admin.include.message')
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold text-gray-800">➕ Add New Purchase</h2>
                    <a href="{{ route('admin.purchase.index') }}" class="px-3 py-1.5 bg-indigo-600 text-white rounded text-sm">
                        📋 Purchase List
                    </a>
                </div>

                <!-- Supplier + Date -->
                <div class="grid grid-cols-12 gap-3 mb-4">
                    <div class="col-span-12 md:col-span-6">
                        <label class="text-sm font-medium">Supplier</label>
                        <select name="supplier_id" id="supplierSelect" class="w-full" required></select>
                        @error('supplier_id')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-span-12 md:col-span-6">
                        <label class="text-sm font-medium">Purchase Date</label>
                        <input type="date" name="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" class="w-full border rounded px-2 py-1">
                        @error('purchase_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Main Grid -->
                <div class="grid grid-cols-12 gap-4">

                    <!-- LEFT -->
                    <div class="col-span-12 lg:col-span-9 bg-white rounded shadow p-3">

                        <table class="w-full border text-sm">
                            <thead class="bg-gray-100">
                            <tr>
                                <th>Medicine</th>
                                <th>Mrp</th>
                                <th>Discount %</th>
                                <th>Unit Cost</th>
                                <th>Qty</th>
                                <th>Free Qty</th>
                                <th>Expire Date</th>
                                <th>Sub Total</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody id="itemsTable">
                            <!-- Template Row -->
                            <tr id="templateRow" data-index="0">
                                <td class="relative">
                                    <input type="text" class="medicineInput w-full border px-1" name="items[0][medicine_name]">
                                    <input type="hidden" name="items[0][medicine_id]" class="medicine_id">
                                </td>
                                <td><input type="number" name="items[0][mrp]" class="mrp w-full border text-right bg-gray-200" step="0.01" readonly></td>
                                <td><input type="number" name="items[0][medicine_discount]" class="medicine_discount w-full border text-right" step="0.01" min="0" max="100"></td>
                                <td><input type="number" name="items[0][unit_cost]" class="unit_cost w-full border text-right bg-gray-200" step="0.01" readonly></td>
                                <td><input type="number" name="items[0][quantity]" class="quantity w-full border text-right" value="0"></td>
                                <td><input type="number" name="items[0][free_quantity]" class="free_quantity w-full border text-right" value="0"></td>
                                <td><input type="date" name="items[0][expire_date]" class="free_quantity w-full border text-right" value=""></td>
                                <td><input type="number" name="items[0][sub_total]" class="sub_total w-full border text-right bg-gray-200" readonly></td>
                                <td><button type="button" class="removeRow px-2 bg-red-500 text-white rounded">X</button></td>
                            </tr>
                            </tbody>
                        </table>
                        <div class="flex justify-end my-2">
                            <button type="button" id="addRow" class="px-2 py-1 bg-green-600 text-white rounded text-sm">+ Add Product</button>
                        </div>
                    </div>

                    <!-- RIGHT -->
                    <div class="col-span-12 lg:col-span-3 space-y-3">
                        <div class="bg-white p-3 rounded shadow">
                            <label class="font-semibold">Account</label>
                            <select name="account_id" id="accountSelect" class="w-full border px-2 py-1">
                                <option value="">-- Select Account --</option>
                            </select>
                            @error('account_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-sm mt-1">Balance: <span id="accountBalance">0.00</span></p>
                        </div>

                        <div class="bg-white p-3 rounded shadow text-sm space-y-2">
                            <div class="flex justify-between"><span>Total</span><input name="total" id="total" readonly class="w-24 border text-right bg-gray-200" value="0"></div>
                            <div class="flex justify-between"><span>Discount</span><input name="discount" id="discount" value="0" class="w-24 border text-right"></div>
                            <div class="flex justify-between"><span>Vat (%)</span><input name="vat" id="vat" value="0" class="w-24 border text-right"></div>

                            <div class="flex justify-between" style="display:none;">
                                <span>Advance</span>
                                <input name="advance" id="advance" value="0" class="w-24 border text-right">
                            </div>

                            <div class="flex justify-between">
                                <span>Payable / Receivable</span>
                                <input name="previous_due" id="previous_due" value="0.00" class="w-24 border text-right" readonly>
                            </div>

                            <hr>
                            <div class="flex justify-between font-bold text-indigo-600"><span>Final Total</span><input name="final_total" id="final_total" readonly class="w-24 border text-right bg-gray-300" value="0"></div>

                            <div class="flex justify-between"><span>Given</span><input name="given_amount" id="given_amount" value="0" class="w-24 border text-right"></div>

                            <div class="flex justify-between font-semibold text-red-600"><span>Payable</span><input name="payable_amount" id="payable_amount" readonly class="w-24 border text-right bg-gray-200"></div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-2 mt-4">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Save Purchase</button>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>

    <style>
        .select2-container { width:100%!important; }
        .select2-selection--single { height:38px!important; border:1px solid #d1d5db!important; }
        .select2-selection__rendered { line-height:38px!important; }
        .medicine-suggestion-box { max-height: 200px; overflow-y: auto; position: absolute; background: #fff; z-index:50; border:1px solid #ccc; width:100%; }
        .medicine-suggestion-box div { padding:2px 6px; cursor:pointer; }
        .medicine-suggestion-box div:hover { background:#f0f0f0; }
    </style>

    <script>
        $(document).ready(function(){

            let rowIndex = 0;

            // ================== Supplier Select2 ==================
            $('#supplierSelect').select2({
                placeholder: 'Select Supplier',
                ajax: {
                    url: "{{ route('admin.purchase.getSuppliers') }}",
                    dataType: 'json',
                    delay: 250,
                    data: params => ({ q: params.term || '' }),
                    processResults: data => ({ results: data.results })
                }
            }).on('select2:select', function(e){
                let supplier = e.params.data;
                $('#previous_due').val(parseFloat(supplier.balance||0).toFixed(2));
                calculate();
            });

            // ================== Load Accounts ==================
            fetch("{{ route('admin.purchase.getPurchaseData') }}")
                .then(res => res.json())
                .then(data => {
                    let defaultSelected = false;
                    data.accounts.forEach(a => {
                        const option = document.createElement('option');
                        option.value = a.id;
                        option.textContent = a.account_name;
                        option.dataset.balance = a.balance;
                        if (a.is_default) {
                            option.selected = true;
                            defaultSelected = true;
                            $('#accountBalance').text(parseFloat(a.balance).toFixed(2));
                        }
                        document.getElementById('accountSelect').appendChild(option);
                    });
                    if (!defaultSelected) $('#accountBalance').text('0.00');
                });

            // ================== Account Balance Update ==================
            $('#accountSelect').change(function () {
                let bal = $(this).find(':selected').data('balance') || 0;
                $('#accountBalance').text(parseFloat(bal).toFixed(2));
            });

            // ================== Add Row ==================
            $('#addRow').click(function(){
                rowIndex++;
                let newRow = $('#templateRow').clone().removeAttr('id').attr('data-index', rowIndex);

                newRow.find('input').each(function(){
                    if(!$(this).hasClass('unit_cost') && !$(this).hasClass('sub_total')) $(this).val(0);
                    if($(this).hasClass('medicineInput')) $(this).val('');
                    if($(this).hasClass('medicine_discount')) $(this).val('');
                });

                newRow.find('[name]').each(function(){
                    this.name = this.name.replace(/\[\d+\]/, `[${rowIndex}]`);
                });

                $('#itemsTable').append(newRow);
                bindRow(newRow);
            });

            // ================== Bind Row ==================
            function bindRow(row){

                // Remove row
                row.find('.removeRow').click(function () {
                    row.remove();
                    calculate();
                });

                // Qty & Free Qty → clear only if 0
                row.find('.quantity, .free_quantity')
                    .on('focus', function () {
                        if (parseFloat($(this).val()) === 0) $(this).val('');
                    })
                    .on('input', calculate);

                // Medicine Discount → recalc unit cost
                row.find('.medicine_discount').attr({min:0, max:100, step:0.01}).on('input', function(){
                    let mrp = parseFloat(row.find('.mrp').val()) || 0;
                    let discount = parseFloat($(this).val()) || 0;
                    if(discount > 100) discount = 100;
                    if(discount < 0) discount = 0;

                    // Unit cost = MRP - discount%
                    row.find('.unit_cost').val((mrp - (mrp * discount / 100)).toFixed(2));
                    calculate();
                });

                // Medicine search
                row.find('.medicineInput').on('input', function (e) {
                    medicineSearch(e, row);
                });

                // Unit cost never clears on click
                row.find('.unit_cost').off('mousedown input');
            }

            bindRow($('#templateRow'));

            // ================== Medicine Search ==================
            function medicineSearch(e, row) {
                const rowEl = row[0];
                const query = e.target.value.trim();

                rowEl.querySelector('.medicine-box')?.remove();
                if (!query) return;

                fetch(`{{ route('admin.purchase.searchMedicine') }}?q=${encodeURIComponent(query)}`)
                    .then(r => r.json())
                    .then(data => {
                        if (!data.length) return;

                        const selectedIds = Array.from(document.querySelectorAll('.medicine_id'))
                            .map(i => i.value)
                            .filter(Boolean);

                        const box = document.createElement('div');
                        box.className = 'medicine-box absolute bg-white border z-50 w-full';

                        data.filter(m => !selectedIds.includes(m.id.toString())).forEach(m => {
                            const div = document.createElement('div');
                            div.className = 'px-2 py-1 hover:bg-gray-200 cursor-pointer';
                            div.innerText = `${m.medicine_name} (MRP: ${m.mrp})`;

                            div.onclick = () => {
                                row.find('.medicineInput').val(m.medicine_name);
                                row.find('.medicine_id').val(m.id);

                                // Set MRP
                                row.find('.mrp').val(parseFloat(m.mrp).toFixed(2));

                                // Set medicine_discount from purchase_percentage
                                let discount = parseFloat(m.purchase_percentage) || 0;
                                row.find('.medicine_discount').val(discount.toFixed(2));

                                // Calculate unit_cost = MRP - discount%
                                row.find('.unit_cost')
                                    .val((m.mrp - (m.mrp * discount / 100)).toFixed(2))
                                    .data('purchase_price', parseFloat(m.purchase_price));

                                calculate();
                                box.remove();
                            };

                            box.appendChild(div);
                        });

                        rowEl.querySelector('.medicineInput').parentNode.appendChild(box);
                    });
            }

            // ================== Overall Calculation ==================
            $('#discount,#vat,#advance,#given_amount').each(function(){
                $(this).focus(function(){ if(parseFloat($(this).val())===0) $(this).val(''); })
                    .on('input', calculate);
            });

            function calculate(){
                let total = 0;
                $('#itemsTable tr').each(function(){
                    let price = parseFloat($(this).find('.unit_cost').val())||0;
                    let qty = parseFloat($(this).find('.quantity').val())||0;
                    let sub = price * qty;
                    $(this).find('.sub_total').val(sub.toFixed(2));
                    total += sub;
                });
                $('#total').val(total.toFixed(2));

                let discount = parseFloat($('#discount').val())||0;
                let vat = parseFloat($('#vat').val())||0;
                let advance = parseFloat($('#advance').val())||0;
                let previousDue = parseFloat($('#previous_due').val())||0;
                let given = parseFloat($('#given_amount').val())||0;

                let vatAmount = total*vat/100;
                let finalTotal = total - discount + vatAmount - advance + previousDue;
                $('#final_total').val(finalTotal.toFixed(2));

                $('#payable_amount').val((finalTotal - given).toFixed(2));
            }

            // ================== Form Validation ==================
            $('form').on('submit', function () {
                let valid = true;
                $('.unit_cost').each(function () {
                    if (!$(this).val()) {
                        alert('Unit cost cannot be empty');
                        $(this).focus();
                        valid = false;
                        return false;
                    }
                });
                return valid;
            });

        });
    </script>
@endsection

