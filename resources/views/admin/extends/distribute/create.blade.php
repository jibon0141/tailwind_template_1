@extends('admin.master')
@section('content')
    <form method="POST" action="{{ route('admin.distribute.create') }}">
        @csrf

        <div class="min-h-screen bg-gray-100 p-4">
            <div class="mx-auto">

                <!-- Header -->
                @include('admin.include.message')
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold text-gray-800">➕ Add New Distribute</h2>
                    <a href="{{ route('admin.distribute.index') }}" class="px-3 py-1.5 bg-indigo-600 text-white rounded text-sm">
                        📋 Distribute List
                    </a>
                </div>

                <!-- Depo + Date -->
                <div class="grid grid-cols-12 gap-3 mb-4">
                    <div class="col-span-12 md:col-span-6">
                        <label class="text-sm font-medium">Depo</label>
                        <select name="depo_id" id="depoSelect" class="w-full border rounded px-2 py-1">
                            <option value="">-- Select Depo --</option>
                        </select>
                        @error('depo_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-span-12 md:col-span-6 gap-3">
                        <label class="text-sm font-medium">Distribute Date</label>
                        <input type="date" name="distribute_date" value="{{ old('distribute_date', date('Y-m-d')) }}" class="w-full border rounded px-2 py-1">
                        @error('distribute_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Main Grid -->
                <div class="grid grid-cols-12 gap-4">

                    <!-- LEFT -->
                    <div class="col-span-12 lg:col-span-9 bg-white rounded shadow p-3">
                        <div class="flex justify-end mb-2">
                            <button type="button" id="addRow" class="px-2 py-1 bg-green-600 text-white rounded text-sm">+ Add Product</button>
                        </div>

                        <table class="w-full border text-sm">
                            <thead class="bg-gray-100">
                            <tr>
                                <th>Medicine</th>
                                <th>Unit Cost</th>
                                <th>Qty</th>
                                <th>Free Qty</th>
                                <th>Sub Total</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody id="itemsTable">
                            <tr data-index="0">
                                <td class="relative">
                                    <input type="text" class="medicineInput w-full border px-1" name="items[0][medicine_name]" value="{{ old('items.0.medicine_name') }}">
                                    <input type="hidden" name="items[0][medicine_id]" class="medicine_id" value="{{ old('items.0.medicine_id') }}">
                                    @error('items.0.medicine_id')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </td>
                                <td><input type="number" name="items[0][unit_cost]" class="unit_cost w-full border text-right bg-gray-200" readonly value="{{ old('items.0.unit_cost', 0) }}"></td>
                                <td><input type="number" name="items[0][quantity]" class="quantity w-full border text-right" value="{{ old('items.0.quantity', 0) }}"></td>
                                <td><input type="number" name="items[0][free_quantity]" class="free_quantity w-full border text-right" value="{{ old('items.0.free_quantity', 0) }}"></td>
                                <td><input type="number" name="items[0][sub_total]" class="sub_total w-full border text-right bg-gray-200" readonly value="{{ old('items.0.sub_total', 0) }}"></td>
                                <td><button type="button" class="removeRow px-2 bg-red-500 text-white rounded">X</button></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- RIGHT -->
                    <div class="col-span-12 lg:col-span-3 space-y-3">

                        <!-- Calculation Box -->
                        <div class="bg-white p-3 rounded shadow text-sm space-y-2">
                            <div class="flex justify-between"><span>Total</span><input name="total" id="total" readonly class="w-24 border text-right bg-gray-200" value="{{ old('total', 0) }}"></div>
                            <div class="flex justify-between"><span>Discount</span><input name="discount" id="discount" value="{{ old('discount',0) }}" class="w-24 border text-right"></div>
                            <div class="flex justify-between"><span>Vat (%)</span><input name="vat" id="vat" value="{{ old('vat',0) }}" class="w-24 border text-right"></div>
                            <div class="flex justify-between"><span>Previous Due</span><input name="previous_due" id="previous_due" value="0.00" class="w-24 border text-right" readonly></div>
                            <hr>
                            <div class="flex justify-between font-bold text-indigo-600"><span>Final Total</span><input name="final_total" id="final_total" readonly class="w-24 border text-right bg-gray-300" value="{{ old('final_total',0) }}"></div>
                            <div class="flex justify-between font-semibold text-red-600"><span>Receivable</span><input name="receivable_amount" id="receivable_amount" readonly class="w-24 border text-right bg-gray-200"></div>
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
    <!-- Select2 CSS/JS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        .select2-container .select2-selection--single {
            height: 32px !important;
            padding: 4px 8px !important;
            border: 1px solid #d1d5db !important;
            border-radius: 0.375rem !important;
        }
        .select2-container .select2-selection--single .select2-selection__rendered {
            line-height: 24px !important;
            padding-left: 0 !important;
        }
        .select2-container .select2-selection--single .select2-selection__arrow {
            height: 30px !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let rowIndex = 0;

            const depoSelect = document.getElementById('depoSelect');
            const discountInput = document.getElementById('discount');
            const vatInput = document.getElementById('vat');

            function clearZeroOnFocus(input) {
                input.addEventListener('focus', () => {
                    if (parseFloat(input.value) === 0) input.value = '';
                });
            }

            [discountInput, vatInput].forEach(clearZeroOnFocus);

            // --- Select2 Depo ---
            $('#depoSelect').select2({
                placeholder: '-- Select Depo --',
                allowClear: true,
                width: '100%',
                ajax: {
                    url: "{{ route('admin.distribute.getDistributeData') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return { q: params.term };
                    },
                    processResults: function(data) {
                        return {
                            results: data.depos.map(depo => ({
                                id: depo.id,
                                text: depo.Depo_name + ' || ' + depo.contact,
                                balance: parseFloat(depo.receivable_amount) || 0
                            }))
                        };
                    }
                }
            });

            $('#depoSelect').on('select2:select', function(e) {
                const balance = e.params.data.balance || 0;
                document.getElementById('previous_due').value = balance.toFixed(2);
                calculate();
            });

            $('#depoSelect').on('select2:clear', function() {
                document.getElementById('previous_due').value = '0.00';
                calculate();
            });

            // Recalculate totals on input
            [discountInput, vatInput].forEach(el => el.addEventListener('input', calculate));

            // Add product row
            document.getElementById('addRow').addEventListener('click', () => {
                rowIndex++;
                const row = document.querySelector('#itemsTable tr').cloneNode(true);
                row.dataset.index = rowIndex;

                row.querySelectorAll('input').forEach(i => {
                    if(!i.hasAttribute('readonly')) i.value = 0;
                    if(i.classList.contains('unit_cost') || i.classList.contains('sub_total')) i.value = 0;
                    if(i.classList.contains('medicineInput')) i.value = '';
                });

                row.querySelectorAll('[name]').forEach(el => el.name = el.name.replace(/\[\d+]/, `[${rowIndex}]`));

                document.getElementById('itemsTable').appendChild(row);
                bindRow(row);
            });

            function bindRow(row) {
                row.querySelector('.removeRow').onclick = () => { row.remove(); calculate(); };

                ['quantity','free_quantity'].forEach(cls => {
                    const input = row.querySelector(`.${cls}`);
                    if(input) {
                        clearZeroOnFocus(input);
                        input.addEventListener('input', calculate);
                    }
                });

                const medInput = row.querySelector('.medicineInput');
                if(medInput) medInput.addEventListener('input', e => medicineSearch(e, row));
            }

            document.querySelectorAll('#itemsTable tr').forEach(bindRow);

            function calculate() {
                let total = 0;
                document.querySelectorAll('#itemsTable tr').forEach(row => {
                    let price = parseFloat(row.querySelector('.unit_cost').value) || 0;
                    let qty = parseFloat(row.querySelector('.quantity').value) || 0;
                    let sub = price * qty;
                    row.querySelector('.sub_total').value = sub.toFixed(2);
                    total += sub;
                });

                document.getElementById('total').value = total.toFixed(2);

                let discount = parseFloat(discountInput.value) || 0;
                let vatPercent = parseFloat(vatInput.value) || 0;
                let previousDue = parseFloat(document.getElementById('previous_due').value) || 0;

                let vatAmount = (total * vatPercent) / 100;
                let finalTotal = total - discount + vatAmount + previousDue;

                document.getElementById('final_total').value = finalTotal.toFixed(2);
                // Receivable now equals final total
                document.getElementById('receivable_amount').value = finalTotal.toFixed(2);
            }

            function medicineSearch(e, row) {
                const input = e.target;
                const query = input.value.trim();
                const parent = input.parentNode;
                const oldBox = parent.querySelector('.medicine-suggestion-box');
                if (oldBox) oldBox.remove();
                if (!query) return;

                fetch(`{{ route('admin.distribute.searchMedicine') }}?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                        if (!data.length) return;
                        const box = document.createElement('div');
                        box.className = 'medicine-suggestion-box absolute bg-white border z-50 w-full shadow max-h-60 overflow-auto';
                        data.forEach(m => {
                            const div = document.createElement('div');
                            div.className = 'px-2 py-1 hover:bg-gray-200 cursor-pointer';
                            div.innerText = `${m.medicine_name} (${m.purchase_price})`;
                            div.onclick = () => {
                                input.value = m.medicine_name;
                                row.querySelector('.medicine_id').value = m.id;
                                row.querySelector('.unit_cost').value = m.purchase_price;
                                box.remove();
                                calculate();
                            };
                            box.appendChild(div);
                        });
                        parent.appendChild(box);
                    });
            }
        });
    </script>
@endsection
