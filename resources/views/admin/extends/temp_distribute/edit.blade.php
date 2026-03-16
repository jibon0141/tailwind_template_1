@extends('admin.master')
@section('content')
    <form method="POST" action="{{ route('admin.temp-distribute.update', $distribute->id) }}">
        @csrf
        @method('PUT')

        <div class="min-h-screen bg-gray-100 p-4">
            <div class="mx-auto">

                {{-- Messages --}}
                @include('admin.include.message')

                {{-- Header --}}
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold text-gray-800">✏️ Edit Distribute</h2>
                    <a href="{{ route('admin.temp-distribute.index') }}"
                       class="px-3 py-1.5 bg-indigo-600 text-white rounded text-sm">
                        📋 Distribute List
                    </a>
                </div>


                <input type="number" name="id" value="{{$distribute->id}}" hidden>

                {{-- Depo + Date --}}
                <div class="grid grid-cols-12 gap-3 mb-4">
                    <div class="col-span-12 md:col-span-6">
                        <label class="text-sm font-medium">Depo</label>

                        {{-- Readonly display --}}
                        <input type="text"
                               class="w-full border rounded px-2 py-1 bg-gray-200"
                               value="{{ $distribute->depo->depo_name }} || {{ $distribute->depo->contact }}"
                               readonly>

                        {{-- Real submitted value --}}
                        <input type="hidden" name="depo_id" value="{{ $distribute->depo_id }}">
                    </div>

                    <div class="col-span-12 md:col-span-6">
                        <label class="text-sm font-medium">Distribute Date</label>
                        <input type="date"
                               name="distribute_date"
                               value="{{ old('distribute_date', $distribute->distribute_date) }}"
                               class="w-full border rounded px-2 py-1">
                    </div>
                </div>

                {{-- Main Grid --}}
                <div class="grid grid-cols-12 gap-4">

                    {{-- LEFT SIDE --}}
                    <div class="col-span-12 lg:col-span-9 bg-white rounded shadow p-3">
                        <div class="flex justify-end mb-2">
                            <button type="button"
                                    id="addRow"
                                    class="px-2 py-1 bg-green-600 text-white rounded text-sm">
                                + Add Product
                            </button>
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
                            @foreach($distribute->items as $i => $item)
                                <tr data-index="{{ $i }}">
                                    <td class="relative">
                                        <input type="text"
                                               class="medicineInput w-full border px-1"
                                               name="items[{{ $i }}][medicine_name]"
                                               value="{{ $item->medicine->medicine_name }}">

                                        <input type="hidden"
                                               name="items[{{ $i }}][medicine_id]"
                                               class="medicine_id"
                                               value="{{ $item->medicine_id }}">
                                    </td>

                                    <td>
                                        <input type="number"
                                               name="items[{{ $i }}][unit_cost]"
                                               class="unit_cost w-full border text-right bg-gray-200"
                                               readonly
                                               value="{{ $item->unit_cost }}">
                                    </td>

                                    <td>
                                        <input type="number"
                                               name="items[{{ $i }}][quantity]"
                                               class="quantity w-full border text-right"
                                               value="{{ $item->quantity }}">
                                    </td>

                                    <td>
                                        <input type="number"
                                               name="items[{{ $i }}][free_quantity]"
                                               class="free_quantity w-full border text-right"
                                               value="{{ $item->free_quantity }}">
                                    </td>

                                    <td>
                                        <input type="number"
                                               class="sub_total w-full border text-right bg-gray-200"
                                               readonly
                                               value="{{ $item->sub_total }}">
                                    </td>

                                    <td>
                                        <button type="button"
                                                class="removeRow px-2 bg-red-500 text-white rounded">
                                            X
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- RIGHT SIDE --}}
                    <div class="col-span-12 lg:col-span-3 space-y-3">

                        <div class="bg-white p-3 rounded shadow text-sm space-y-2">
                            <div class="flex justify-between">
                                <span>Total</span>
                                <input id="total"
                                       readonly
                                       class="w-24 border text-right bg-gray-200"
                                       value="{{ $distribute->total }}">
                            </div>

                            <div class="flex justify-between">
                                <span>Discount</span>
                                <input name="discount"
                                       id="discount"
                                       value="{{ $distribute->discount }}"
                                       class="w-24 border text-right">
                            </div>

                            <div class="flex justify-between">
                                <span>Vat (%)</span>
                                <input name="vat"
                                       id="vat"
                                       value="{{ $distribute->vat }}"
                                       class="w-24 border text-right">
                            </div>

                            <div class="flex justify-between">
                                <span>Previous Due</span>
                                <input name="previous_due"
                                       id="previous_due"
                                       value="{{ $distribute->previous_due }}"
                                       class="w-24 border text-right bg-gray-200"
                                       readonly>
                            </div>

                            <hr>

                            <div class="flex justify-between font-bold text-indigo-600">
                                <span>Final Total</span>
                                <input id="final_total"
                                       readonly
                                       class="w-24 border text-right bg-gray-300"
                                       value="{{ $distribute->final_total }}">
                            </div>

                            <div class="flex justify-between font-semibold text-red-600">
                                <span>Receivable</span>
                                <input name="receivable_amount"
                                       id="receivable_amount"
                                       readonly
                                       class="w-24 border text-right bg-gray-200"
                                       value="{{ $distribute->receivable_amount }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex justify-end gap-2 mt-4">
                    <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded">
                        Update Distribution
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

            /* ===============================
               INITIAL SETUP
            =============================== */

            // Start index from existing rows
            let rowIndex = document.querySelectorAll('#itemsTable tr').length - 1;

            const discountInput = document.getElementById('discount');
            const vatInput = document.getElementById('vat');

            function clearZeroOnFocus(input) {
                input.addEventListener('focus', () => {
                    if (parseFloat(input.value) === 0) input.value = '';
                });
            }

            [discountInput, vatInput].forEach(clearZeroOnFocus);

            /* ===============================
               ADD ROW
            =============================== */

            document.getElementById('addRow').addEventListener('click', () => {
                rowIndex++;

                const template = document.querySelector('#itemsTable tr');
                const row = template.cloneNode(true);
                row.dataset.index = rowIndex;

                // Reset values
                row.querySelectorAll('input').forEach(input => {
                    if (input.classList.contains('medicineInput')) input.value = '';
                    if (input.classList.contains('medicine_id')) input.value = '';
                    if (input.classList.contains('unit_cost')) input.value = 0;
                    if (input.classList.contains('quantity')) input.value = 0;
                    if (input.classList.contains('free_quantity')) input.value = 0;
                    if (input.classList.contains('sub_total')) input.value = 0;
                });

                // Update input names
                row.querySelectorAll('[name]').forEach(el => {
                    el.name = el.name.replace(/\[\d+]/, `[${rowIndex}]`);
                });

                document.getElementById('itemsTable').appendChild(row);
                bindRow(row);
            });

            /* ===============================
               ROW BINDINGS
            =============================== */

            function bindRow(row) {

                // Remove row
                row.querySelector('.removeRow').onclick = () => {
                    row.remove();
                    calculate();
                };

                // Quantity recalculation
                ['quantity', 'free_quantity'].forEach(cls => {
                    const input = row.querySelector(`.${cls}`);
                    if (input) {
                        clearZeroOnFocus(input);
                        input.addEventListener('input', calculate);
                    }
                });

                // Medicine search
                const medInput = row.querySelector('.medicineInput');
                if (medInput) {
                    medInput.addEventListener('input', e => medicineSearch(e, row));
                }
            }

            document.querySelectorAll('#itemsTable tr').forEach(bindRow);

            /* ===============================
               CALCULATION
            =============================== */

            function calculate() {
                let total = 0;

                document.querySelectorAll('#itemsTable tr').forEach(row => {
                    const price = parseFloat(row.querySelector('.unit_cost').value) || 0;
                    const qty = parseFloat(row.querySelector('.quantity').value) || 0;
                    const sub = price * qty;

                    row.querySelector('.sub_total').value = sub.toFixed(2);
                    total += sub;
                });

                document.getElementById('total').value = total.toFixed(2);

                const discount = parseFloat(discountInput.value) || 0;
                const vat = parseFloat(vatInput.value) || 0;
                const previousDue = parseFloat(document.getElementById('previous_due').value) || 0;

                const vatAmount = (total * vat) / 100;
                const finalTotal = total - discount + vatAmount + previousDue;

                document.getElementById('final_total').value = finalTotal.toFixed(2);
                document.getElementById('receivable_amount').value = finalTotal.toFixed(2);
            }

            /* ===============================
               MEDICINE SEARCH (NO DUPLICATE)
            =============================== */

            function getSelectedMedicineIds(exceptRow = null) {
                const ids = [];
                document.querySelectorAll('.medicine_id').forEach(input => {
                    if (exceptRow && exceptRow.contains(input)) return;
                    if (input.value) ids.push(parseInt(input.value));
                });
                return ids;
            }

            function medicineSearch(e, row) {
                const input = e.target;
                const query = input.value.trim();
                const parent = input.parentNode;

                parent.querySelector('.medicine-suggestion-box')?.remove();
                if (!query) return;

                const selectedIds = getSelectedMedicineIds(row);

                fetch(`{{ route('admin.distribute.searchMedicine') }}?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {

                        const filtered = data.filter(m => !selectedIds.includes(m.id));
                        if (!filtered.length) return;

                        const box = document.createElement('div');
                        box.className = 'medicine-suggestion-box absolute bg-white border z-50 w-full shadow max-h-60 overflow-auto';

                        filtered.forEach(m => {
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

            calculate(); // initial calculation
        });
    </script>

@endsection
