@extends('depo.master')

@section('content')
    <form method="POST" action="{{ route('depo.purchase.create') }}">
        @csrf

        <div class="min-h-screen bg-gray-100 p-4">
            <div class="mx-auto">

                <!-- Header -->
                @include('depo.include.message')
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold text-gray-800">➕ Add New Purchase</h2>
                    <a href="{{ route('depo.purchase.index') }}" class="px-3 py-1.5 bg-indigo-600 text-white rounded text-sm">
                        📋 Purchase List
                    </a>
                </div>

                <!-- Supplier + Date -->
                <div class="grid grid-cols-12 gap-3 mb-4">
                    <div class="col-span-12 md:col-span-6">
                        <label class="text-sm font-medium">Supplier</label>
                        <select name="supplier_id" id="supplierSelect" class="w-full border rounded px-2 py-1">
                            <option value="">-- Select Supplier --</option>
                        </select>
                    </div>

                    <div class="col-span-12 md:col-span-6">
                        <label class="text-sm font-medium">Purchase Date</label>
                        <input type="date" name="purchase_date" value="{{ date('Y-m-d') }}" class="w-full border rounded px-2 py-1">
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
                                    <input type="text" class="medicineInput w-full border px-1">
                                    <input type="hidden" name="items[0][medicine_id]" class="medicine_id">
                                </td>
                                <td><input type="number" name="items[0][unit_cost]" class="unit_cost w-full border text-right bg-gray-200" readonly></td>
                                <td><input type="number" name="items[0][quantity]" class="quantity w-full border text-right" value="0"></td>
                                <td><input type="number" name="items[0][free_quantity]" class="free_quantity w-full border text-right" value="0"></td>
                                <td><input type="number" name="items[0][sub_total]" class="sub_total w-full border text-right bg-gray-200" readonly></td>
                                <td><button type="button" class="removeRow px-2 bg-red-500 text-white rounded">X</button></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- RIGHT -->
                    <div class="col-span-12 lg:col-span-3 space-y-3">
                        <div class="bg-white p-3 rounded shadow">
                            <label class="font-semibold">Account</label>
                            <select name="account_id" id="accountSelect" class="w-full border px-2 py-1">
                                <option value="">-- Select Account --</option>
                            </select>
                            <p class="text-sm mt-1">Balance: <span id="accountBalance">0.00</span></p>
                        </div>

                        <div class="bg-white p-3 rounded shadow text-sm space-y-2">
                            <div class="flex justify-between"><span>Total</span><input name="total" id="total" readonly class="w-24 border text-right bg-gray-200"></div>
                            <div class="flex justify-between"><span>Discount</span><input name="discount" id="discount" value="0" class="w-24 border text-right"></div>
                            <div class="flex justify-between"><span>Vat (%)</span><input name="vat" id="vat" value="0" class="w-24 border text-right"></div>

                            <!-- Hidden Advance -->
                            <div class="flex justify-between" style="display:none;">
                                <span>Advance</span>
                                <input name="advance" id="advance" value="0" class="w-24 border text-right">
                            </div>

                            <div class="flex justify-between"><span>Previous Due</span><input name="previous_due" id="previous_due" value="0.00" class="w-24 border text-right" readonly></div>

                            <hr>
                            <div class="flex justify-between font-bold text-indigo-600"><span>Final Total</span><input name="final_total" id="final_total" readonly class="w-24 border text-right bg-gray-300"></div>
                            <div class="flex justify-between"><span>Given</span><input name="given_amount" value="0" class="w-24 border text-right"></div>
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
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let rowIndex = 0;
            const supplierSelect = document.getElementById('supplierSelect');
            const accountSelect = document.getElementById('accountSelect');

            // Load suppliers & accounts
            fetch("{{ route('depo.purchase.getPurchaseData') }}")
                .then(r => r.json())
                .then(d => {
                    d.suppliers.forEach(s => {
                        let bal = parseFloat(s.balance) || 0;
                        supplierSelect.innerHTML += `<option value="${s.id}" data-balance="${bal}">${s.supplier_name}</option>`;
                    });
                    d.accounts.forEach(a => {
                        let bal = parseFloat(a.balance) || 0;
                        accountSelect.innerHTML += `<option value="${a.id}" data-balance="${bal}">${a.account_name}</option>`;
                    });

                    // Set previous due immediately if first supplier selected
                    updatePreviousDue();
                });

            // Event: Supplier change -> update previous due
            supplierSelect.addEventListener('change', updatePreviousDue);

            // Event: Account change -> update balance display
            accountSelect.addEventListener('change', updateAccountBalance);

            // Live calculation events
            ['discount','vat','advance'].forEach(id => {
                document.getElementById(id).addEventListener('input', calculate);
            });

            // Add product row
            document.getElementById('addRow').addEventListener('click', () => {
                rowIndex++;
                const row = document.querySelector('#itemsTable tr').cloneNode(true);
                row.dataset.index = rowIndex;
                row.querySelectorAll('input').forEach(i => i.value = 0);
                row.querySelector('.medicineInput').value = '';
                row.querySelectorAll('[name]').forEach(el => el.name = el.name.replace(/\[\d+]/, `[${rowIndex}]`));
                document.getElementById('itemsTable').appendChild(row);
                bindRow(row);
            });

            // Bind initial row
            bindRow(document.querySelector('#itemsTable tr'));

            function bindRow(row) {
                row.querySelector('.removeRow').onclick = () => { row.remove(); calculate(); };
                row.querySelectorAll('.quantity, .unit_cost').forEach(i => i.addEventListener('input', calculate));
                row.querySelector('.medicineInput').addEventListener('input', e => medicineSearch(e, row));
            }

            function updatePreviousDue() {
                let selected = supplierSelect.options[supplierSelect.selectedIndex];
                let due = selected ? parseFloat(selected.dataset.balance) || 0 : 0;
                document.getElementById('previous_due').value = due.toFixed(2);
                calculate();
            }

            function updateAccountBalance() {
                let selected = accountSelect.options[accountSelect.selectedIndex];
                let bal = selected ? parseFloat(selected.dataset.balance) || 0 : 0;
                document.getElementById('accountBalance').innerText = bal.toFixed(2);
            }

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

                let discount = parseFloat(document.getElementById('discount').value) || 0;
                let vatPercent = parseFloat(document.getElementById('vat').value) || 0;
                let advance = parseFloat(document.getElementById('advance').value) || 0;
                let previousDue = parseFloat(document.getElementById('previous_due').value) || 0;

                let vatAmount = (total * vatPercent) / 100;
                let finalTotal = total - discount + vatAmount - advance + previousDue;
                document.getElementById('final_total').value = finalTotal.toFixed(2);
            }

            function medicineSearch(e, row) {
                fetch(`{{ route('depo.sale.searchMedicine') }}?q=${e.target.value}`)
                    .then(r => r.json())
                    .then(d => {
                        if (!d.length) return;
                        const box = document.createElement('div');
                        box.className = 'absolute bg-white border z-50 w-full';
                        d.forEach(m => {
                            const div = document.createElement('div');
                            div.className = 'px-2 py-1 hover:bg-gray-200 cursor-pointer';
                            div.innerText = `${m.medicine_name} (${m.purchase_price})`;
                            div.onclick = () => {
                                row.querySelector('.medicineInput').value = m.medicine_name;
                                row.querySelector('.medicine_id').value = m.id;
                                row.querySelector('.unit_cost').value = m.purchase_price;
                                calculate();
                                box.remove();
                            };
                            box.appendChild(div);
                        });
                        row.querySelector('td').appendChild(box);
                    });
            }

        });
    </script>
@endsection
