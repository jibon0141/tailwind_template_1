@extends('depo.master')

@section('content')
    <form method="POST" action="{{ route('depo.direct-sale.create') }}">
        @csrf
        <div class="min-h-screen bg-gray-100 p-5">
            <div class="mx-auto">

                {{-- Flash / general messages --}}
                @include('depo.include.message')

                <div class="flex flex-col gap-5 md:flex-row md:justify-between md:items-center mb-4">
                    <h2 class="text-2xl font-bold text-gray-800">➕ Add New Sale</h2>
                    <a href="{{ route('depo.direct-sale.index') }}"
                       class="px-3 py-1.5 bg-indigo-600 text-white rounded text-sm">
                        📋 Sale List
                    </a>
                </div>

                {{-- Shop & Date --}}
                <div class="grid grid-cols-12 gap-3 mb-4">

                    {{-- Chemist Shop --}}
                    <div class="col-span-12 md:col-span-6 relative">
                        <label class="text-sm font-medium">Chemist Shop</label>
                        <select name="shop_id" id="shopSelect"
                                class="w-full border rounded px-3 py-2 text-lx h-10">
                            <option value="">-- Select Shop --</option>
                        </select>

                        @if(old('shop_id'))
                            <input type="hidden" id="oldShopId" value="{{ old('shop_id') }}">
                        @endif

                        @error('shop_id')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Sale Date --}}
                    <div class="col-span-12 md:col-span-3">
                        <label class="text-sm font-medium">Sale Date</label>
                        <input type="date" name="sale_date"
                               value="{{ old('sale_date', date('Y-m-d')) }}"
                               class="w-full border rounded px-3 py-2 text-sm h-10 @error('sale_date') border-red-500 @enderror">
                        @error('sale_date')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Delivery Date --}}
                    <div class="col-span-12 md:col-span-3">
                        <label class="text-sm font-medium">Delivery Date </label>
                        <input type="date" name="delivery_date" value="{{ old('delivery_date') }}"
                               class="w-full border rounded px-3 py-2 text-sm h-10 @error('delivery_date') border-red-500 @enderror">
                        @error('delivery_date')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- Items Table --}}
                <div class="grid grid-cols-12 gap-4">

                    <div class="col-span-12 lg:col-span-9 bg-white rounded shadow p-3">


                        <div class="w-full">
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
                                                   class="unit_cost w-full border text-right"
                                                   step="0.01">
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
                                                   class="stock w-full border text-right bg-gray-200"
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

                                    {{-- Error Row --}}
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

                    {{-- Sidebar totals --}}
                    <div class="col-span-12 lg:col-span-3 space-y-3">

                        {{-- Account --}}
                        <div class="bg-white p-3 rounded shadow text-sm space-y-2">
                            <label class="font-semibold">Account</label>
                            <select name="account_id" id="accountSelect"
                                    class="w-full border px-2 py-1 @error('account_id') border-red-500 @enderror">
                                <option value="">-- Select Account --</option>
                            </select>
                            @error('account_id')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-sm mt-1">Balance: <span id="accountBalance">0.00</span></p>
                        </div>

                        {{-- Totals --}}
                        <div class="bg-white p-3 rounded shadow text-sm space-y-2">

                            <div class="flex justify-between">
                                <span>Total</span>
                                <input id="total" name="total" readonly
                                       class="w-24 border text-right bg-gray-200"
                                       value="{{ old('total') }}">
                            </div>

                            <div class="flex justify-between">
                                <span>Discount</span>
                                <input type="number" id="discount" name="discount"
                                       value="{{ old('discount', 0) }}"
                                       class="w-24 border text-right @error('discount') border-red-500 @enderror">
                            </div>
                            @error('discount')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror

                            <div class="flex justify-between">
                                <span>Vat (%)</span>
                                <input type="number" id="vat" name="vat"
                                       value="{{ old('vat', 0) }}"
                                       class="w-24 border text-right @error('vat') border-red-500 @enderror">
                            </div>
                            @error('vat')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror

                            {{-- PREVIOUS DUE --}}
                            <div class="flex justify-between">
                                <span>Previous Due</span>
                                <input id="previous_due" name="previous_due" readonly
                                       value="{{ old('previous_due',0) }}"
                                       class="w-24 border text-right bg-gray-200">
                            </div>

                            <hr>

                            <div class="flex justify-between font-bold text-indigo-600">
                                <span>Final Total</span>
                                <input id="final_total" name="final_total" readonly
                                       class="w-24 border text-right bg-gray-300"
                                       value="{{ old('final_total') }}">
                            </div>

                            <div class="flex justify-between" id="givenAmountWrapper">
                                <span>Given Amount</span>
                                <input type="number"
                                       id="given_amount"
                                       name="given_amount"
                                       value="{{ old('given_amount', 0) }}"
                                       class="w-24 border text-right @error('given_amount') border-red-500 @enderror">
                            </div>
                            @error('given_amount')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror


                            <div class="flex justify-between">
                                <span>Receivable</span>
                                <input id="receivable_amount" name="receivable_amount" readonly
                                       class="w-24 border text-right bg-gray-200"
                                       value="{{ old('receivable_amount') }}">
                            </div>

                        </div>

                    </div>
                </div>

                <div class="flex justify-end mt-4">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">
                        Save Sale
                    </button>
                </div>

            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <!-- Select2 -->
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

            /* ================= INITIAL SETUP ================= */

            let rows = document.querySelectorAll('#itemsTable tr');
            let rowIndex = rows.length > 0 ? rows.length - 1 : 0;

            const totalField        = document.getElementById('total');
            const discountField     = document.getElementById('discount');
            const vatField          = document.getElementById('vat');
            const previousDueField  = document.getElementById('previous_due');
            const finalTotalField   = document.getElementById('final_total');
            const givenAmountField  = document.getElementById('given_amount');
            const receivableField   = document.getElementById('receivable_amount');
            const givenAmountWrapper = document.getElementById('givenAmountWrapper');

            const accountSelect  = document.getElementById('accountSelect');
            const accountBalance = document.getElementById('accountBalance');

            const shopSelect = $('#shopSelect');
            const oldShopId  = document.getElementById('oldShopId')?.value;

            /* ================= NUMBER INPUT BEHAVIOR ================= */

            function numberBehavior(el) {
                if (!el) return;

                el.addEventListener('focus', () => {
                    if (parseFloat(el.value) === 0) el.select();
                });

                el.addEventListener('blur', () => {
                    if (el.value === '' || isNaN(el.value)) el.value = 0;
                    calculate();
                });
            }

            [discountField, vatField, givenAmountField].forEach(el => {
                numberBehavior(el);
                el.addEventListener('input', calculate);
            });

            /* ================= DISCOUNT PER MEDICINE ================= */

            function updateDiscount(row) {
                const mrp  = parseFloat(row.querySelector('.mrp')?.value) || 0;
                const cost = parseFloat(row.querySelector('.unit_cost')?.value) || 0;
                const discountInput = row.querySelector('.medicine_discount');

                if (!discountInput) return;

                discountInput.value =
                    mrp > 0 && cost <= mrp
                        ? (((mrp - cost) / mrp) * 100).toFixed(2)
                        : 0;
            }

            /* ================= MAIN CALCULATION ================= */

            function calculate() {
                let total = 0;

                document.querySelectorAll('#itemsTable tr').forEach(row => {
                    const price = parseFloat(row.querySelector('.unit_cost')?.value) || 0;
                    const qty   = parseFloat(row.querySelector('.quantity')?.value) || 0;

                    const sub = price * qty;
                    const subInput = row.querySelector('.sub_total');

                    if (subInput) subInput.value = sub.toFixed(2);
                    total += sub;
                });

                totalField.value = total.toFixed(2);

                const discount = parseFloat(discountField.value) || 0;
                const vat      = parseFloat(vatField.value) || 0;
                const prevDue  = parseFloat(previousDueField.value) || 0;
                const given    = parseFloat(givenAmountField.value) || 0;

                const vatAmount  = (total * vat) / 100;
                const finalTotal = total - discount + vatAmount + prevDue;

                finalTotalField.value = finalTotal.toFixed(2);

                let receivable = finalTotal - given;
                if (receivable < 0) receivable = 0;

                receivableField.value = receivable.toFixed(2);
            }

            /* ================= TOGGLE GIVEN AMOUNT ================= */

            function toggleGivenAmount(isDefaultShop) {
                if (!givenAmountWrapper) return;

                if (isDefaultShop) {
                    givenAmountWrapper.classList.add('hidden');
                    givenAmountField.value = 0;
                } else {
                    givenAmountWrapper.classList.remove('hidden');
                }

                calculate();
            }

            /* ================= ROW EVENTS ================= */

            function attachRowEvents(row) {
                if (!row) return;

                const unitCost = row.querySelector('.unit_cost');
                const qty      = row.querySelector('.quantity');
                const freeQty = row.querySelector('.free_quantity');
                const stockEl  = row.querySelector('.stock');

                [unitCost, qty, freeQty].forEach(numberBehavior);

                unitCost?.addEventListener('input', () => {
                    updateDiscount(row);
                    calculate();
                });



                row.querySelector('.removeRow')?.addEventListener('click', () => {
                    row.remove();
                    calculate();
                });

                row.querySelector('.medicineInput')
                    ?.addEventListener('input', e => medicineSearch(e, row));

                updateDiscount(row);
            }

            document.querySelectorAll('#itemsTable tr').forEach(attachRowEvents);

            /* ================= ADD ROW ================= */

            document.getElementById('addRow').addEventListener('click', () => {
                rowIndex++;

                const template = document.querySelector('#itemsTable tr');
                if (!template) return;

                const row = template.cloneNode(true);
                row.dataset.index = rowIndex;

                row.querySelectorAll('[name]').forEach(el => {
                    el.name = el.name.replace(/\[\d+]/, `[${rowIndex}]`);
                });

                row.querySelectorAll('input').forEach(el => {
                    el.value =
                        el.classList.contains('mrp') ||
                        el.classList.contains('medicine_discount') ||
                        el.classList.contains('sub_total') ||
                        el.classList.contains('stock')
                            ? 0
                            : '';
                });

                attachRowEvents(row);
                document.getElementById('itemsTable').appendChild(row);
            });

            /* ================= MEDICINE SEARCH (WITH STOCK) ================= */

            function medicineSearch(e, row) {
                const query = e.target.value;

                document.querySelector('.medicine-box')?.remove();
                if (!query) return;

                fetch(`{{ route('depo.sale.searchMedicine') }}?q=${query}`)
                    .then(res => res.json())
                    .then(data => {

                        if (!data.length) return;

                        const selectedIds = Array.from(
                            document.querySelectorAll('.medicine_id')
                        ).map(i => i.value).filter(Boolean);

                        const box = document.createElement('div');
                        box.className = 'medicine-box absolute bg-white border z-50 w-full';

                        data.filter(m => !selectedIds.includes(m.id.toString()))
                            .forEach(m => {

                                const div = document.createElement('div');
                                div.className = 'px-2 py-1 hover:bg-gray-200 cursor-pointer';

                                div.innerText =
                                    `${m.medicine_name} | Price: ${m.sale_price} `;

                                div.onclick = () => {

                                    row.querySelector('.medicineInput').value = m.medicine_name;
                                    row.querySelector('.medicine_id').value = m.id;
                                    row.querySelector('.unit_cost').value = m.sale_price;
                                    row.querySelector('.mrp').value = m.mrp;
                                    row.querySelector('.stock').value = m.current_stock;

                                    updateDiscount(row);
                                    calculate();
                                    box.remove();
                                };

                                box.appendChild(div);
                            });

                        row.querySelector('.medicineInput').parentNode.appendChild(box);
                    });
            }

            /* ================= ACCOUNT SECTION ================= */

            fetch("{{ route('depo.direct-sale.getDepoAccounts') }}")
                .then(res => res.json())
                .then(data => {

                    let defaultSet = false;

                    data.accounts.forEach(acc => {

                        const opt = document.createElement('option');
                        opt.value = acc.id;
                        opt.textContent = acc.account_name;
                        opt.dataset.balance = acc.balance;

                        if (acc.is_default) {
                            opt.selected = true;
                            accountBalance.textContent = parseFloat(acc.balance).toFixed(2);
                            defaultSet = true;
                        }

                        accountSelect.appendChild(opt);
                    });

                    if (!defaultSet && data.accounts.length) {
                        accountSelect.selectedIndex = 0;
                        accountBalance.textContent =
                            parseFloat(data.accounts[0].balance).toFixed(2);
                    }
                });

            accountSelect.addEventListener('change', e => {
                const bal = e.target.selectedOptions[0]?.dataset.balance || 0;
                accountBalance.textContent = parseFloat(bal).toFixed(2);
            });

            /* ================= SHOP SELECT2 ================= */

            shopSelect.select2({
                placeholder: '-- Select Shop --',
                allowClear: true,
                width: '100%',
                ajax: {
                    url: "{{ route('depo.sale.getSaleData') }}",
                    dataType: 'json',
                    delay: 250,
                    data: params => ({ q: params.term }),
                    processResults: data => ({
                        results: data.chemistShops.map(s => ({
                            id: s.id,
                            text: `${s.shop_name} (Due: ${s.receivable_amount || 0})`,
                            due: parseFloat(s.receivable_amount) || 0,
                            is_default: !!s.is_default
                        }))
                    })
                }
            })
                .on('select2:select', e => {
                    previousDueField.value = e.params.data.due.toFixed(2);
                    toggleGivenAmount(e.params.data.is_default);
                    calculate();
                })
                .on('select2:clear', () => {
                    previousDueField.value = 0;
                    toggleGivenAmount(false);
                    calculate();
                });

            /* ================= OLD / DEFAULT SHOP ================= */

            if (oldShopId) {

                fetch(`{{ route('depo.sale.getSaleData') }}?id=${oldShopId}`)
                    .then(res => res.json())
                    .then(data => {

                        if (data.chemistShops.length) {
                            const s = data.chemistShops[0];

                            const opt = new Option(
                                `${s.shop_name} (Due: ${s.receivable_amount || 0})`,
                                s.id,
                                true,
                                true
                            );

                            shopSelect.append(opt).trigger('change');
                            previousDueField.value = parseFloat(s.receivable_amount || 0).toFixed(2);
                            toggleGivenAmount(!!s.is_default);
                            calculate();
                        }
                    });

            } else {

                fetch("{{ route('depo.sale.getSaleData') }}")
                    .then(res => res.json())
                    .then(data => {

                        const def = data.chemistShops.find(s => s.is_default);

                        if (def) {
                            const opt = new Option(
                                `${def.shop_name} (Due: ${def.receivable_amount || 0})`,
                                def.id,
                                true,
                                true
                            );

                            shopSelect.append(opt).trigger('change');
                            previousDueField.value = parseFloat(def.receivable_amount || 0).toFixed(2);
                            toggleGivenAmount(true);
                            calculate();
                        }
                    });
            }

            calculate();
        });
    </script>


@endsection
