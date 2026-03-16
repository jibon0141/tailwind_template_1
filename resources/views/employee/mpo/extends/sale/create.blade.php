@extends('employee.mpo.master')

@section('content')
    <form method="POST" action="{{ route('mpo.sale.create') }}">
        @csrf
        <div class="min-h-screen bg-gray-100 p-1">
            <div class="mx-auto">

                @include('depo.include.message')

                <div class="flex flex-col gap-5 md:flex-row md:justify-between md:items-center mb-4">
                    <h2 class="text-2xl font-bold text-gray-800">➕ Add New Sale</h2>
                    <a href="{{ route('mpo.sale.index') }}"
                       class="px-3 py-1.5 bg-indigo-600 text-white rounded text-sm">
                        📋 Sale List
                    </a>
                </div>

                {{-- Shop & Date --}}
                <div class="grid grid-cols-12 gap-3 mb-4">

                    <div class="col-span-12 md:col-span-6">
                        <label class="text-sm font-medium">Chemist Shop</label>
                        <select name="shop_id" id="shopSelect"
                                class="w-full border rounded px-3 py-2 text-lx h-10">
                            <option value="">-- Select Shop --</option>
                        </select>

                        @if(old('shop_id'))
                            <input type="hidden" id="oldShopId" value="{{ old('shop_id') }}">
                        @endif

                    </div>

                    <div class="col-span-12 md:col-span-3">
                        <label class="text-sm font-medium">Sale Date</label>
                        <input type="date" name="sale_date"
                               value="{{ date('Y-m-d') }}"
                               class="w-full border rounded px-3 py-2 text-sm h-10">
                    </div>

                    <div class="col-span-12 md:col-span-3">
                        <label class="text-sm font-medium">Delivery Date</label>
                        <input type="date" name="delivery_date" value="{{ old('delivery_date') }}"
                               class="w-full border rounded px-3 py-2 text-sm h-10 @error('delivery_date') border-red-500 @enderror">
                        @error('delivery_date')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>


                <div class="grid grid-cols-12 gap-4 overflow-x-auto">

                    {{-- Products --}}
                    <div class="col-span-12 lg:col-span-9 bg-white rounded shadow p-3">



                        <div class="w-full overflow-x-auto">
                            <table class="min-w-[900px] w-full border text-sm">
                            <thead class="bg-gray-100">
                            <tr>
                                <th>Medicine</th>
                                <th>Unit Cost</th>
                                <th>Mrp</th>
                                <th>Discount %</th>
                                <th>Qty</th>
                                <th>Free Qty</th>
                                <th>Stock</th>
                                <th>Sub Total</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody id="itemsTable">
                            @foreach(old('items', [0 => []]) as $index => $item)

                                {{-- MAIN ITEM ROW --}}
                                <tr data-index="{{ $index }}">
                                    <td class="relative">
                                        <input type="text"
                                               name="items[{{ $index }}][medicine_name]"
                                               value="{{ $item['medicine_name'] ?? '' }}"
                                               class="medicineInput w-full border px-1">

                                        <input type="hidden"
                                               name="items[{{ $index }}][medicine_id]"
                                               class="medicine_id"
                                               value="{{ $item['medicine_id'] ?? '' }}">
                                    </td>

                                    <td>
                                        <input type="number"
                                               name="items[{{ $index }}][unit_cost]"
                                               value="{{ $item['unit_cost'] ?? '' }}"
                                               class="unit_cost w-full border text-right bg-gray-200"
                                               step="0.01" readonly>
                                    </td>

                                    <td>
                                        <input type="number"
                                               name="items[{{ $index }}][mrp]"
                                               value="{{ $item['mrp'] ?? '' }}"
                                               class="mrp w-full border text-right bg-gray-200"
                                               readonly>
                                    </td>

                                    <td>
                                        <input type="number"
                                               name="items[{{ $index }}][medicine_discount]"
                                               value="{{ $item['medicine_discount'] ?? 0 }}"
                                               class="medicine_discount w-full border text-right bg-gray-200"
                                               readonly>
                                    </td>

                                    <td>
                                        <input type="number"
                                               name="items[{{ $index }}][quantity]"
                                               value="{{ $item['quantity'] ?? 0 }}"
                                               class="quantity w-full border text-right">
                                    </td>

                                    <td>
                                        <input type="number"
                                               name="items[{{ $index }}][free_quantity]"
                                               value="{{ $item['free_quantity'] ?? 0 }}"
                                               class="free_quantity w-full border text-right">
                                    </td>

                                    <td>
                                        <input type="number"
                                               name="items[{{ $index }}][stock]"
                                               class="current_stock w-full border text-right bg-gray-200"
                                               readonly
                                               value="{{ $item['stock'] ?? 0 }}">
                                    </td>

                                    <td>
                                        <input type="number"
                                               name="items[{{ $index }}][sub_total]"
                                               value="{{ $item['sub_total'] ?? 0 }}"
                                               class="sub_total w-full border text-right bg-gray-200"
                                               readonly>
                                    </td>

                                    <td>
                                        <button type="button"
                                                class="removeRow px-2 bg-red-500 text-white rounded">
                                            X
                                        </button>
                                    </td>
                                </tr>

                                {{--  ERROR ROW (BELOW ITEM ROW) --}}
                                @if(
                                    $errors->has("items.$index.medicine_id") ||
                                    $errors->has("items.$index.unit_cost") ||
                                    $errors->has("items.$index.mrp") ||
                                    $errors->has("items.$index.quantity") ||
                                    $errors->has("items.$index.stock")
                                )
                                    <tr>
                                        <td colspan="8" class="bg-red-50 px-3 py-2">
                                            <ul class="text-xs text-red-600 list-disc pl-5 space-y-1">

                                                @error("items.$index.medicine_id")
                                                <li>{{ $message }}</li>
                                                @enderror

                                                @error("items.$index.unit_cost")
                                                <li>{{ $message }}</li>
                                                @enderror

                                                @error("items.$index.mrp")
                                                <li>{{ $message }}</li>
                                                @enderror

                                                @error("items.$index.quantity")
                                                <li>{{ $message }}</li>
                                                @enderror

                                                @error("items.$index.stock")
                                                <li>{{ $message }}</li>
                                                @enderror

                                            </ul>
                                        </td>
                                    </tr>
                                @endif

                            @endforeach
                            </tbody>

                        </table>
                        </div>

                        <div class="flex justify-end mt-2">
                            <button type="button" id="addRow"
                                    class="px-2 py-1 bg-green-600 text-white rounded text-sm">
                                + Add Product
                            </button>
                        </div>

                    </div>

                    {{-- Summary --}}
                    <div class="col-span-12 lg:col-span-3">
                        <div class="bg-white p-3 rounded shadow text-sm space-y-2">

                            <div class="flex justify-between">
                                <span>Total</span>
                                <input name="total" id="total" readonly
                                       value="{{ old('total', 0) }}"
                                       class="w-24 border text-right bg-gray-200">
                            </div>

                            <div class="flex justify-between">
                                <span>Discount</span>
                                <input type="number" name="discount" id="discount"
                                       value="{{ old('discount', 0) }}"
                                       class="w-24 border text-right">
                            </div>

                            <div class="flex justify-between">
                                <span>Vat (%)</span>
                                <input type="number" name="vat" id="vat"
                                       value="{{ old('vat', 0) }}"
                                       class="w-24 border text-right">
                            </div>

                            <div class="flex justify-between">
                                <span>Previous Due</span>
                                <input name="previous_due" id="previous_due" readonly
                                       value="{{ old('previous_due', 0) }}"
                                       class="w-24 border text-right bg-gray-200">
                            </div>

                            <div class="flex justify-between font-bold text-indigo-600">
                                <span>Final Total</span>
                                <input name="final_total" id="final_total" readonly
                                       value="{{ old('final_total', 0) }}"
                                       class="w-24 border text-right bg-gray-300">
                            </div>

                            <div class="flex justify-between">
                                <span>Receivable</span>
                                <input name="receivable_amount" id="receivable_amount" readonly
                                       value="{{ old('receivable_amount', 0) }}"
                                       class="w-24 border text-right bg-gray-200">
                            </div>


                        </div>
                    </div>
                </div>

                <div class="flex justify-end mt-4">
                    <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded">
                        Save Sale
                    </button>
                </div>

            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <!-- Select2 CSS/JS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        .select2-container .select2-selection--single {
            height: 40px !important;
            padding: 8px !important;
            border: 1px solid #d1d5db !important;
            border-radius: 0.375rem !important;
        }
        .select2-container .select2-selection--single .select2-selection__rendered {
            line-height: 24px !important;
            padding-left: 0 !important;
        }
        .select2-container .select2-selection--single .select2-selection__arrow {
            height: 38px !important;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            /* ----------------------------------
               INITIAL STATE
            ---------------------------------- */
            let rows = document.querySelectorAll('#itemsTable tr');
            let rowIndex = rows.length > 0 ? rows.length - 1 : 0;

            const totalField       = document.getElementById('total');
            const discountField    = document.getElementById('discount');
            const vatField         = document.getElementById('vat');
            const previousDueField = document.getElementById('previous_due');
            const finalTotalField  = document.getElementById('final_total');
            const receivableField  = document.getElementById('receivable_amount');
            const shopSelect       = document.getElementById('shopSelect');

            /* ----------------------------------
               NUMBER INPUT BEHAVIOR
            ---------------------------------- */
            function numberBehavior(el) {
                if (!el) return;
                el.addEventListener('focus', () => { if(parseFloat(el.value) === 0) el.select(); });
                el.addEventListener('blur', () => { if(el.value === '' || isNaN(el.value)) el.value = 0; calculate(); });
            }

            [discountField, vatField].forEach(el => { numberBehavior(el); el.addEventListener('input', calculate); });
            /* ----------------------------------
               UPDATE DISCOUNT %
            ---------------------------------- */
            function updateDiscount(row) {
                const mrpInput = row.querySelector('.mrp');
                const costInput = row.querySelector('.unit_cost');
                const discountInput = row.querySelector('.medicine_discount');

                // Skip rows that do not have required inputs
                if (!mrpInput || !costInput || !discountInput) return;

                const mrp  = parseFloat(mrpInput.value) || 0;
                const cost = parseFloat(costInput.value) || 0;

                discountInput.value = mrp > 0 && cost <= mrp ? (((mrp - cost)/mrp)*100).toFixed(2) : 0;
            }




            /* ----------------------------------
               CALCULATION
            ---------------------------------- */
            function calculate() {
                let total = 0;

                document.querySelectorAll('#itemsTable tr').forEach(row => {
                    const priceInput = row.querySelector('.unit_cost');
                    const qtyInput   = row.querySelector('.quantity');
                    const subTotalInput = row.querySelector('.sub_total');

                    // Skip rows that do not have required inputs
                    if (!priceInput || !qtyInput || !subTotalInput) return;

                    const price = parseFloat(priceInput.value) || 0;
                    const qty   = parseFloat(qtyInput.value) || 0;
                    const sub   = price * qty;

                    subTotalInput.value = sub.toFixed(2);
                    total += sub;
                });

                totalField.value = total.toFixed(2);

                const discount  = parseFloat(discountField.value) || 0;
                const vat       = parseFloat(vatField.value) || 0;
                const prevDue   = parseFloat(previousDueField.value) || 0;
                const vatAmount = (total * vat)/100;
                const final     = total - discount + vatAmount + prevDue;

                finalTotalField.value = final.toFixed(2);
                receivableField.value = final.toFixed(2);
            }

            /* ----------------------------------
               ATTACH ROW EVENTS
            ---------------------------------- */
            function attachRowEvents(row) {
                if (!row) return;

                const unitCost = row.querySelector('.unit_cost');
                const mrp      = row.querySelector('.mrp');
                const qty      = row.querySelector('.quantity');
                const freeQty  = row.querySelector('.free_quantity');

                // Attach number behavior to unit_cost, mrp, qty, freeQty
                [unitCost, mrp, qty, freeQty].forEach(numberBehavior);

                unitCost?.addEventListener('input', () => { updateDiscount(row); calculate(); });
                mrp?.addEventListener('input', () => { updateDiscount(row); calculate(); });
                qty?.addEventListener('input', calculate);
                freeQty?.addEventListener('input', calculate);

                row.querySelector('.removeRow')?.addEventListener('click', () => { row.remove(); calculate(); });
                row.querySelector('.medicineInput')?.addEventListener('input', e => medicineSearch(e,row));

                // Initialize discount for existing values
                updateDiscount(row);
            }

            /* ----------------------------------
               ATTACH EVENTS TO EXISTING ROWS
            ---------------------------------- */
            document.querySelectorAll('#itemsTable tr').forEach(row => attachRowEvents(row));

            /* ----------------------------------
               ADD NEW ROW
            ---------------------------------- */
            document.getElementById('addRow').addEventListener('click', () => {
                rowIndex++;
                const templateRow = document.querySelector('#itemsTable tr');
                if(!templateRow) return;

                const row = templateRow.cloneNode(true);
                row.dataset.index = rowIndex;

                row.querySelectorAll('[name]').forEach(el => {
                    el.name = el.name.replace(/\[\d+]/, `[${rowIndex}]`);
                });

                row.querySelectorAll('input').forEach(el => {
                    el.value = el.classList.contains('mrp') ||
                    el.classList.contains('medicine_discount') ||
                    el.classList.contains('sub_total')
                        ? 0 : '';
                });

                attachRowEvents(row);
                document.getElementById('itemsTable').appendChild(row);
            });

            /* ----------------------------------
               MEDICINE SEARCH
            ---------------------------------- */
            function medicineSearch(e, row) {
                const query = e.target.value;
                document.querySelector('.medicine-box')?.remove();
                if (!query) return;

                fetch(`{{ route('mpo.sale.searchMedicine') }}?q=${query}`)
                    .then(r => r.json())
                    .then(data => {
                        console.log(data);
                        if (!data.length) return;

                        const selectedIds = Array.from(
                            document.querySelectorAll('.medicine_id')
                        ).map(i => i.value).filter(Boolean);

                        const box = document.createElement('div');
                        box.className = 'medicine-box fixed bg-white border shadow z-[9999] max-h-60 overflow-y-auto';
                        box.style.width = e.target.offsetWidth + 'px';

                        const rect = e.target.getBoundingClientRect();
                        box.style.left = rect.left + 'px';
                        box.style.top  = rect.bottom + 'px';

                        data
                            .filter(m => !selectedIds.includes(m.id.toString()))
                            .forEach(m => {
                                const div = document.createElement('div');
                                div.className = 'px-2 py-1 hover:bg-gray-200 cursor-pointer text-sm';
                                div.innerText = `${m.medicine_name} (${m.sale_price})`;

                                div.onclick = () => {
                                    row.querySelector('.medicineInput').value = m.medicine_name;
                                    row.querySelector('.medicine_id').value = m.id;
                                    row.querySelector('.unit_cost').value = m.sale_price;
                                    row.querySelector('.mrp').value = m.mrp;
                                    row.querySelector('.current_stock').value = m.current_stock;

                                    updateDiscount(row);
                                    calculate();
                                    box.remove();
                                };



                                box.appendChild(div);
                            });

                        document.body.appendChild(box);

                        // Close on outside click
                        document.addEventListener('click', function handler(ev) {
                            if (!box.contains(ev.target) && ev.target !== e.target) {
                                box.remove();
                                document.removeEventListener('click', handler);
                            }
                        });
                    });
            }


            /* ----------------------------------
               LOAD SHOP & PREVIOUS DUE
            ---------------------------------- */
            const oldShopId = document.getElementById('oldShopId')?.value;

            $('#shopSelect').select2({
                placeholder:'-- Select Shop --',
                allowClear:true,
                width:'100%',
                ajax:{
                    url:"{{ route('mpo.sale.getSaleData') }}",
                    dataType:'json',
                    delay:250,
                    data:function(params){ return {q:params.term}; },
                    processResults:function(data){
                        return { results:data.chemistShops.map(s=>({
                                id: s.id,
                                text: s.shop_name+' (Due: '+(s.receivable_amount||0)+')',
                                due: parseFloat(s.receivable_amount)||0
                            })) };
                    }
                }
            });

            // If old shop_id exists, load it into Select2
            if(oldShopId){
                fetch(`{{ route('mpo.sale.getSaleData') }}?id=${oldShopId}`)
                    .then(res => res.json())
                    .then(data => {
                        if(data.chemistShops.length){
                            const shop = data.chemistShops[0];
                            const option = new Option(
                                shop.shop_name+' (Due: '+(shop.receivable_amount||0)+')',
                                shop.id,
                                true,
                                true
                            );
                            $('#shopSelect').append(option).trigger('change');
                            previousDueField.value = parseFloat(shop.receivable_amount||0).toFixed(2);
                            calculate();
                        }
                    });
            }


            // Update previous due when shop changes
            $('#shopSelect').on('select2:select', function(e){
                previousDueField.value = e.params.data.due.toFixed(2);
                calculate();
            });

            $('#shopSelect').on('select2:clear', function(){
                previousDueField.value = 0;
                calculate();
            });

        });
    </script>



@endsection
