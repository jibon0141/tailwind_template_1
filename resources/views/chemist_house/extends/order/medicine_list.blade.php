@extends("chemist_house.master")

<style>
    #company_select + .select2-container .select2-selection {
        height: 42px !important;
        width: auto;
        line-height: 42px !important;
        padding: 0 10px;
        border-radius: 0.375rem;
        border: 1px solid #d1d5db;
    }

    /* Prevent select2 option text breaking */
    .select2-container .select2-selection--single {
        height: 42px !important;
        display: flex !important;
        align-items: center !important;
    }

    /* Make selected text truncate on small screens */
    .select2-selection__rendered {
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }

    /* Dropdown option text wrap fix */
    .select2-results__option {
        white-space: normal !important;
        word-break: break-word;
    }

    /* Improve mobile width */
    .select2-container {
        width: 100% !important;
    }
</style>

@section("content")

    <div class="page-wrapper bg-white p-3 md:p-6 rounded shadow">

        <div class="content container mx-auto px-2 md:px-4">

            <h3 class="text-2xl font-semibold mb-6">Medicine List</h3>

            @include('admin.include.message')

            {{-- ================= FILTER ================= --}}
            <div class="bg-gray-100 p-4 rounded mb-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">

                <select id="company_select" class="w-full"></select>

                <input
                    type="text"
                    id="medicine_search"
                    class="w-full border rounded px-3 py-2"
                    placeholder="Search Medicine..."
                >

            </div>

            {{-- ================= MEDICINE TABLE ================= --}}
            <div class="overflow-x-auto">

                <table id="medicineTable" class="min-w-[650px] w-full text-sm border">

                    <thead class="bg-gray-50">
                    <tr class="text-xs uppercase text-gray-600 text-center">
                        <th>#</th>
                        <th>Medicine</th>
                        <th>MRP</th>
                        <th>Purchase</th>
                        <th>Discount (%)</th>
                        <th>+</th>
                    </tr>
                    </thead>

                    <tbody></tbody>

                </table>

            </div>

            {{-- ================= CART ================= --}}
            <div class="mt-8">

                <h4 class="text-xl font-bold text-center mb-3">List Medicine</h4>

                <div class="overflow-x-auto">

                    <table class="min-w-[400px] w-full text-sm border" id="cartTable">

                        <thead class="bg-gray-100">
                        <tr>
                            <th>Medicine</th>
                            <th>MRP</th>
                            <th>Action</th>
                        </tr>
                        </thead>

                        <tbody></tbody>

                    </table>

                </div>

                <div class="mt-4 text-center">

                    <button
                        type="button"
                        id="buy_now"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded"
                    >
                        Buy Now
                    </button>

                </div>

            </div>

        </div>

    </div>

@endsection

@section('scripts')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {

            /* ================= COMPANY ================= */
            $('#company_select').select2({
                placeholder: 'Select Company',
                allowClear: true,
                ajax: {
                    url: '{{ route("chemist.search.company.ajax") }}',
                    dataType: 'json',
                    data: params => ({ q: params.term }),
                    processResults: data => ({ results: data.results })
                }
            });

            /* ================= LOAD MEDICINES ================= */
            $('#company_select, #medicine_search').on('change keyup', loadMedicines);

            function loadMedicines() {

                let company_id = $('#company_select').val();
                let search = $('#medicine_search').val();

                let cart = getCart(); // get current cart

                $.get('{{ route("chemist.medicine.searchMedicineAjax") }}', { company_id, search }, function(res) {

                    let rows = '';

                    if (!res.medicines || res.medicines.length === 0) {
                        $('#medicineTable tbody').html('');
                        return;
                    }

                    res.medicines.forEach((m, i) => {

                        let mrp = parseFloat(m.mrp) || 0;
                        let purchase = parseFloat(m.purchase_price) || 0;

                        let discount = mrp > 0 ? ((mrp - purchase) / mrp * 100).toFixed(2) : 0;

                        // check if already in cart
                        let exists = cart.find(item => item.id == m.id);

                        let buttonClass = exists
                            ? "bg-green-800 cursor-not-allowed"
                            : "bg-green-600 hover:bg-green-700";

                        let buttonText = exists ? "✓ Added" : "+ Add Medicine";

                        rows += `
<tr class="text-center">
    <td>${i+1}</td>

    <td>
        ${m.name}
        <input type="hidden" name="medicine_id[]" value="${m.id}">
    </td>

    <td>
        <input type="number" class="mrp w-full border p-1 bg-gray-100" readonly value="${mrp.toFixed(2)}">
    </td>

    <td>
        <input type="number" class="purchase w-full border p-1 bg-gray-100" readonly value="${purchase.toFixed(2)}">
    </td>

    <td>
        <input type="number" class="discount w-full border p-1 bg-gray-100" readonly value="${discount}">
    </td>

    <td>
        <button type="button"
            class="add_to_cart text-white px-3 py-1 rounded ${buttonClass}"
            data-id="${m.id}"
            data-name="${m.name}"
            data-mrp="${mrp.toFixed(2)}"
            data-purchase="${purchase.toFixed(2)}"
            ${exists ? "disabled" : ""}>
            ${buttonText}
        </button>
    </td>
</tr>
            `;
                    });

                    $('#medicineTable tbody').html(rows);

                });
            }

            /* ================= CART SYSTEM ================= */
            function getCart() {
                return JSON.parse(localStorage.getItem('medicine_cart')) || [];
            }

            function saveCart(cart) {
                localStorage.setItem('medicine_cart', JSON.stringify(cart));
            }

            function renderCart() {
                let cart = getCart();
                let rows = '';


                cart.forEach((item, index) => {
                    rows += `
<tr>
    <td class="text-center">${item.name}</td>
    <td class="text-center">${item.mrp}</td>
    <td class="text-center">
        <button type="button"
            class="remove_cart bg-red-600 text-white px-2 py-1 rounded"
            data-index="${index}">
            X Remove Medicine
        </button>
    </td>
</tr>
            `;
                });

                $('#cartTable tbody').html(rows);
            }


            $(document).on('click', '.add_to_cart', function () {

                let cart = getCart();

                let id = $(this).data('id');

                let exists = cart.find(item => item.id == id);

                if (exists) {
                    exists.qty = (exists.qty || 1) + 1;

                } else {

                    cart.push({
                        id: id,
                        name: $(this).data('name'),
                        mrp: $(this).data('mrp'),
                        purchase: $(this).data('purchase'),
                        qty: 1
                    });
                }

                saveCart(cart);
                renderCart();
                loadMedicines();
            });

            /* ===== REMOVE FROM CART ===== */
            $(document).on('click', '.remove_cart', function () {
                let index = $(this).data('index');
                let cart = getCart();
                cart.splice(index, 1);
                saveCart(cart);
                renderCart();
            });

            /* ================= BUY NOW ================= */

            $('#buy_now').on('click', function () {
                let cart = JSON.parse(localStorage.getItem('medicine_cart')) || [];

                if (!cart.length) {
                    alert("Cart is empty!");
                    return;
                }

                fetch("{{ route('chemist.order.storeCart') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ cart: cart })
                })
                    .then(res => res.json())
                    .then(data => {

                        window.location.href = "{{ route('chemist.order.create') }}";
                    });
            });

            // Preload cart from session
            fetch("{{ route('chemist.cart.preload') }}")
                .then(res => res.json())
                .then(data => {
                    if (!data.length) return;

                    let cart = getCart(); // existing localStorage cart

                    data.forEach(m => {
                        // Avoid duplicate
                        let exists = cart.find(item => item.id == m.id);
                        if (!exists) {
                            cart.push({
                                id: m.id,
                                name: m.name,
                                mrp: m.mrp,
                                purchase: m.purchase_price,
                                qty: m.qty
                            });
                        }
                    });

                    saveCart(cart);
                    renderCart();
                });


            renderCart();
            loadMedicines();
        });
    </script>
@endsection
