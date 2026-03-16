@extends('chemist_house.master')

@section('content')
    <form method="POST" action="{{ route('chemist.order.create') }}">
        @csrf
        <div class="min-h-screen bg-gray-100 p-4">
            <div class="mx-auto">

                @include('chemist_house.include.message')

                <div class="flex flex-col justify-between md:flex-row items-center mb-4 gap-2">
                    <h2 class="text-2xl font-bold text-gray-800">➕ Add New Purchase</h2>
                    <a href="{{ route('chemist.order.index') }}"
                       class="px-3 py-1.5 bg-indigo-600 text-white rounded text-sm">
                        📋 Purchase List
                    </a>
                </div>

                {{-- Shop & Date --}}
                <div class="grid grid-cols-12 gap-3 mb-4">
                    <div class="col-span-12 md:col-span-6">
                        <label class="text-sm font-medium">Chemist Shop</label>
                        <input type="text" id="chemistName" class="w-full border rounded px-3 py-2 text-lx h-10" readonly>
                        <input type="hidden" name="shop_id" id="shopId">
                    </div>

                    <div class="col-span-12 md:col-span-6">
                        <label class="text-sm font-medium">Sale Date</label>
                        <input type="date" name="sale_date"
                               value="{{ date('Y-m-d') }}"
                               class="w-full border rounded px-3 py-2 text-sm h-10">
                    </div>
                </div>

                {{-- Products and Summary --}}
                <div class="grid grid-cols-12 gap-4">

                    {{-- Products --}}
                    <div class="col-span-12 lg:col-span-9 bg-white rounded shadow p-3">
                        <div class="w-full overflow-x-auto">
                        <div class="flex justify-end mb-2">
{{--                            <button type="button"--}}
{{--                                    id="addRow"--}}
{{--                                    class="px-2 py-1 bg-green-600 text-white rounded text-sm">--}}
{{--                                + Add Product--}}
{{--                            </button>--}}
                        </div>

                        <table class="min-w-[700px] w-full border text-sm">
                            <thead class="bg-gray-100">
                            <tr>
                                <th>Medicine</th>
                                <th>Unit Cost</th>
                                <th>Qty</th>
                                <th>Sub Total</th>
                                <th>Action</th>
                            </tr>
                            <tbody id="itemsTable">

                            @foreach($medicines ?? [] as $index => $medicine)

                                <tr data-index="{{ $index }}">

                                    <td  class="text-center">
                                        {{ $medicine['medicine_name'] }}
                                        <input type="hidden"
                                               name="items[{{ $index }}][medicine_id]"
                                               value="{{ $medicine['id'] }}">

                                    </td>

                                    <td>
                                        <input type="number"
                                               name="items[{{ $index }}][unit_cost]"
                                               class="unit_cost w-full border text-right bg-gray-200"
                                               value="{{ $medicine['sale_price'] }}"
                                               readonly>
                                    </td>


                                    <td>
                                        <input type="number"
                                               name="items[{{ $index }}][quantity]"
                                               class="quantity w-full border text-right"
                                               value="0"
                                               min="0">
                                    </td>




                                    <td>
                                        <input type="number"
                                               name="items[{{ $index }}][sub_total]"
                                               class="sub_total w-full border text-right bg-gray-200"
                                               readonly>
                                    </td>

                                    <td>
                                        <button type="button"
                                                class="removeRow bg-red-500 text-white px-2 rounded">
                                            X
                                        </button>
                                    </td>

                                </tr>

                            @endforeach

                            </tbody>
                        </table>
                        </div>
                    </div>

                    {{-- Summary --}}
                    <div class="col-span-12 lg:col-span-3">
                        <div class="bg-white p-3 rounded shadow text-sm space-y-2">

                            <div class="flex justify-between">
                                <span>Total</span>
                                <input name="total" id="total" readonly class="w-24 border text-right bg-gray-200">
                            </div>

                            <div class="flex justify-between hidden">
                                <span>Discount</span>
                                <input type="number" name="discount" id="discount" value="0" class="w-24 border text-right">
                            </div>

                            <div class="flex justify-between  hidden">
                                <span>Vat (%)</span>
                                <input
                                    type="number"
                                    name="vat"
                                    id="vat"
                                    value="0"
                                    class="w-24 border text-right"
                                    min="0"
                                    max="100"
                                    oninput="this.value = Math.min(100, Math.max(0, this.value))"
                               >
                            </div>

                            <div class="flex justify-between">
                                <span>Previous Due</span>
                                <input name="previous_due" id="previous_due" readonly value="0"
                                       class="w-24 border text-right bg-gray-200">
                            </div>

                            <hr>

                            <div class="flex justify-between font-bold text-indigo-600">
                                <span>Final Total</span>
                                <input name="final_total" id="final_total" readonly class="w-24 border text-right bg-gray-300">
                            </div>

                            <div class="flex justify-between">
                                <span>Receivable</span>
                                <input name="receivable_amount" id="receivable_amount" readonly
                                       class="w-24 border text-right bg-gray-200">
                            </div>

                        </div>
                    </div>
                </div>

                <div class="flex justify-center md:justify-end mt-6">

                    <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-2 rounded text-base">
                        Submit Order
                    </button>

                </div>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            let rowIndex = 0;
            const totalField       = document.getElementById('total');
            const discountField    = document.getElementById('discount');
            const vatField         = document.getElementById('vat');
            const previousDueField = document.getElementById('previous_due');
            const finalTotalField  = document.getElementById('final_total');
            const receivableField  = document.getElementById('receivable_amount');

            function numberBehavior(el) {
                el.addEventListener('focus', () => el.select());
                el.addEventListener('blur', () => {
                    if (el.value === '' || isNaN(el.value)) el.value = 0;
                    calculate();
                });
            }

            [discountField, vatField].forEach(el => {
                numberBehavior(el);
                el.addEventListener('input', calculate);
            });

            // =========================
            // Load single chemist and due
            // =========================
            fetch('{{ route("chemist.order.getSaleData") }}')
                .then(res => res.json())
                .then(data => {
                    document.getElementById('chemistName').value = data.chemist.name;
                    document.getElementById('shopId').value = data.chemist.id;
                    previousDueField.value = parseFloat(data.chemist.due).toFixed(2);
                    calculate();
                });

            // =========================
            // Dynamic Item Rows
            // =========================
            const addRowBtn = document.getElementById('addRow');

            if (addRowBtn) {

                addRowBtn.addEventListener('click', () => {

                    const table = document.getElementById('itemsTable');
                    const rows = table.querySelectorAll('tr');

                    if (!rows.length) return;

                    rowIndex++;

                    const templateRow = rows[0]; // always use first row as template

                    const newRow = templateRow.cloneNode(true);

                    newRow.dataset.index = rowIndex;

                    // Update input names
                    newRow.querySelectorAll('[name]').forEach(input => {
                        input.name = input.name.replace(/\[\d+]/, `[${rowIndex}]`);
                        input.value = 0;
                    });

                    // Clear medicine text
                    const medicineText = newRow.querySelector('.medicineInput');
                    if (medicineText) medicineText.value = '';

                    // Clear hidden id
                    const medicineId = newRow.querySelector('.medicine_id');
                    if (medicineId) medicineId.value = '';

                    // Attach events again
                    newRow.querySelectorAll('.quantity, .free_quantity').forEach(input => {
                        input.removeEventListener('input', calculate);
                        input.addEventListener('input', calculate);
                    });

                    newRow.querySelector('.removeRow').onclick = () => {
                        newRow.remove();
                        calculate();
                    };

                    table.appendChild(newRow);

                    calculate();
                });
            }

            function initRows() {

                document.querySelectorAll('#itemsTable tr').forEach(row => {

                    row.querySelectorAll('.quantity, .free_quantity').forEach(input => {

                        numberBehavior(input);   // same behavior as discount & vat
                        input.addEventListener('input', calculate);

                    });

                    const removeBtn = row.querySelector('.removeRow');
                    if (removeBtn) {
                        removeBtn.onclick = () => {
                            row.remove();
                            calculate();
                        };
                    }

                });

                calculate();
            }

            initRows();


            // =========================
            // Calculation
            // =========================
            function calculate() {

                let total = 0;

                document.querySelectorAll('#itemsTable tr').forEach(row => {

                    const priceInput = row.querySelector('.unit_cost');
                    const qtyInput = row.querySelector('.quantity');
                    const subInput = row.querySelector('.sub_total');

                    let price = priceInput ? parseFloat(priceInput.value) || 0 : 0;
                    let qty   = qtyInput ? parseFloat(qtyInput.value) || 0 : 0;

                    let sub = price * qty;

                    if (subInput) {
                        subInput.value = sub.toFixed(2);
                    }

                    total += sub;
                });

                totalField.value = total.toFixed(2);

                let discount = parseFloat(discountField.value) || 0;
                let vat      = parseFloat(vatField.value) || 0;
                let prev     = parseFloat(previousDueField.value) || 0;

                let vatAmount = (total * vat) / 100;

                let final = total - discount + vatAmount + prev;

                finalTotalField.value = final.toFixed(2);
                receivableField.value = final.toFixed(2);
            }

            // =========================
            // Medicine search
            // =========================
            function medicineSearch(e, row) {
                const query = e.target.value;
                row.querySelector('.medicine-box')?.remove();
                if (!query) return;

                fetch(`{{ route('chemist.order.searchMedicine') }}?q=${query}`)
                    .then(r => r.json())
                    .then(d => {
                        if (!d.length) return;

                        const box = document.createElement('div');
                        box.className = 'medicine-box absolute bg-white border z-50 w-full';

                        d.forEach(m => {
                            const div = document.createElement('div');
                            div.className = 'px-2 py-1 hover:bg-gray-200 cursor-pointer';
                            div.innerText = `${m.medicine_name} (${m.sale_price})`;

                            div.onclick = () => {
                                row.querySelector('.medicineInput').value = m.medicine_name;
                                row.querySelector('.medicine_id').value = m.id;
                                row.querySelector('.unit_cost').value = m.sale_price;
                                box.remove();
                                calculate();
                            };

                            box.appendChild(div);
                        });

                        row.querySelector('.medicineInput').parentNode.appendChild(box);
                    });
            }

        });


    </script>
    @if(session('success'))
        <script>
            localStorage.removeItem('medicine_cart'); // matches your medicine list page cart
        </script>
    @endif
@endsection
