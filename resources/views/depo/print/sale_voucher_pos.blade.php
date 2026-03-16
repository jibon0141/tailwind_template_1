<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>POS Sale Invoice</title>
    <script src="{{ asset('assets/backend_assets/js/tailwind/tailwind.js') }}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

    <style>
        body {
            background: #e5e7eb;
            color: #000; /* make all text black */
        }

        #invoice {
            width: 80mm;
            max-width: 80mm;
            margin: 10px auto;
            padding: 2mm !important;
            font-size: 10px;
            line-height: 1.2;
            background: #fff;
            color: #000;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }

        h1, h2, h3, h4, h5, h6, p, span, td, th {
            color: #000; /* force all text black */
        }

        table, tr, td, th {
            font-size: 10px;
            border-color: #000; /* black borders */
        }

        thead {
            background-color: #fff; /* remove background color */
        }

        td {
            color: #000;
        }

        .summary-left span {
            display: flex;
            justify-content: space-between;
            padding: 1px 0;
            color: #000;
        }

        .pos-thank-you {
            text-align: center;
            font-size: 10px;
            margin-top: 4mm;
            margin-bottom: 2mm;
            line-height: 1.3;
            color: #000;
        }

        .pos-thank-you hr { border-top: 1px dashed #000; margin: 2px 0; }

        @media print {
            body * { visibility: hidden; }
            #invoice, #invoice * { visibility: visible; }
            #invoice { position: absolute; top: 0; left: 0; width: 80mm; max-width: 100%; }
            .print\:hidden { display: none !important; }
        }

        @page { size:80mm auto; margin: 2mm; }
    </style>
</head>
<body>

@php
    $activeUser = session('userObj') ?? Auth::user();
@endphp

<div class="min-h-screen p-2 md:p-6">

    <!-- Back Button -->
    <div class="mb-4 flex justify-end print:hidden">
        <a href="{{ route('depo.sale.index') }}"
           class="inline-flex w-full md:w-auto justify-center items-center px-4 py-2 bg-gray-600 text-white text-sm rounded hover:bg-gray-700">
            ← Back to Sales List
        </a>
    </div>

    <!-- Invoice -->
    <div id="invoice" class="max-w-5xl mx-auto">

        <!-- Header -->
        <div class="text-center mb-2">
            <h1 class="text-2xl font-bold">{{ $depo->depo_name ?? 'Company Name' }}</h1>
            <p class="text-xs">{{ $depo->address ?? '' }}</p>
            <p class="text-xs">
                Phone: {{ $depo->contact ?? '' }}
                @if(!empty($depo->email)) | Email: {{ $depo->email }} @endif
            </p>
            <p class="text-lg font-semibold mt-1">Invoice</p>
        </div>

        <!-- Customer & Sale Info -->
        <div class="text-xs mb-2">
            <p>Customer Id: {{ $sale->chemistHouse->id }}</p>
            <p>Customer Name: {{ $sale->chemistHouse->shop_name ?? 'N/A' }}</p>
            <p>Address: {{ $sale->chemistHouse->address ?? 'N/A' }}</p>
            <p class="mb-2">Contact: {{ $sale->chemistHouse->contact ?? 'N/A' }}</p>
            <p>Submitted By: <strong>{{ $sale->depo->person_name }}</strong></p>
            <p>Order By: {{ $sale->depo->person_name }} ({{ $sale->depo->depo_name }})</p>
            <p>Invoice Date: {{ $sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') : 'N/A' }}</p>
            <p>Delivery Date: {{ $sale->delivery_date ? \Carbon\Carbon::parse($sale->delivery_date)->format('d M Y') : 'N/A' }}</p>
            <p>Invoice Type: Cash</p>
        </div>

        <hr>

        <!-- Invoice / Order Info -->
        <div class="flex justify-between text-xs font-semibold mb-2 print:flex-col  justify-center items-center">
            <span>Invoice No: {{ $sale->sale_voucher ?? 'N/A' }}</span>
            <span>Order No: {{ $sale->id ?? 'N/A' }}</span>
            <span>Order Date: {{ $sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') : 'N/A' }}</span>
        </div>

        <!-- Items Table -->
        <div>
            <table class="w-full border border-black text-[8px]">
                <thead>
                <tr>
                    <th class="border px-1 py-0.5">#</th>
                    <th class="border px-1 py-0.5 text-left">Medicine</th>
                    <th class="border px-1 py-0.5">Unit</th>
                    <th class="border px-1 py-0.5">Mrp</th>
                    <th class="border px-1 py-0.5">Disc</th>
                    <th class="border px-1 py-0.5">Qty</th>
                    <th class="border px-1 py-0.5">Free</th>
                    <th class="border px-1 py-0.5">Sub</th>
                </tr>
                </thead>
                <tbody>
                @forelse($sale->items ?? [] as $index => $item)
                    <tr>
                        <td class="border px-1 py-0.5">{{ $index + 1 }}</td>
                        <td class="border px-1 py-0.5 text-left">{{ Str::limit($item->medicine->medicine_name ?? 'N/A', 15) }}</td>
                        <td class="border px-1 py-0.5">{{ number_format($item->unit_cost ?? 0, 2) }}</td>
                        <td class="border px-1 py-0.5">{{ number_format($item->mrp ?? 0, 2) }}</td>
                        <td class="border px-1 py-0.5">{{ number_format($item->medicine_discount ?? 0, 2) }}</td>
                        <td class="border px-1 py-0.5">{{ $item->quantity ?? 0 }}</td>
                        <td class="border px-1 py-0.5">{{ $item->free_quantity ?? 0 }}</td>
                        <td class="border px-1 py-0.5">{{ number_format($item->sub_total ?? 0, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="border px-1 py-1 text-center">No items found</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <!-- Summary -->
        <div class="flex justify-start mt-2 summary-left text-xs">
            <div class="w-full">
                <div class="flex justify-between"><span>Total</span><span>৳ {{ number_format($sale->total ?? 0,2) }}</span></div>
                <div class="flex justify-between"><span>Discount</span><span>৳ {{ number_format($sale->discount ?? 0,2) }}</span></div>
                <div class="flex justify-between"><span>VAT</span><span>{{ number_format($sale->vat ?? 0,2) }}%</span></div>
                <hr class="my-1 border-black">
                <div class="flex justify-between font-semibold"><span>Grand Total</span><span>৳ {{ number_format($sale->final_total ?? 0,2) }}</span></div>
                <div class="flex justify-between mt-1"><span>Receivable</span><span>৳ {{ number_format($sale->receivable_amount ?? 0,2) }}</span></div>
                @php
                    if (($sale->receivable_amount ?? 0) <= 0) {
                        $status = 'paid';
                    } elseif (($sale->given_amount ?? 0) > 0) {
                        $status = 'partial';
                    } else {
                        $status = 'due';
                    }
                @endphp
                <div class="flex justify-between mt-1">
                    <span>Payment Status</span>
                    <span class="font-semibold">{{ ucfirst($status) }}</span>
                </div>
            </div>
        </div>

        <!-- Thank You -->
        <div class="pos-thank-you">
            <hr>
            <p>Thank You! Visit Again</p>
            <hr>
        </div>

        <!-- Print Button -->
        <div class="flex justify-center gap-2 mt-2 print:hidden">
            <a onclick="window.print()">
                <button class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Print View</button>
            </a>
        </div>

    </div>
</div>

</body>
</html>
