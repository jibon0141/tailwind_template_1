<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>POS Invoice</title>
    <script src="{{asset('assets/backend_assets/js/tailwind/tailwind.js')}}"></script>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <style>
        /* ===============================
           POS STYLE FOR WEB & PRINT
        ================================ */
        body {
            background: #e5e7eb;
        }

        #invoice {
            width: 80mm;
            max-width: 80mm;
            margin: 10px auto;
            padding: 2mm !important;
            font-size: 10px;
            line-height: 1.2;
            background: #ffffff;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }

        /* Company colors */
        .company-color {
            color: {{ $mainCompany->primary_color ?? '#1f2937' }};
        }

        table, tr, td, th {
            font-size: 10px;
            border-collapse: collapse;
            color: #000;
            font-weight: 600; /* semi-bold by default */
        }

        thead {
            background-color: #d1d5db;
        }

        thead th {
            color: #111827;
            border: 1px solid #d1d5db;
            padding: 2px 4px;
            font-weight: 600;
        }

        td {
            color: #000;
            border: 1px solid #d1d5db;
            padding: 2px 4px;
        }

        .summary-left {
            margin-left: 1mm;
        }

        .thank-you {
            text-align: center;
            margin-top: 4mm;
            font-size: 10px;
            font-weight: 600; /* semi-bold */
            color: #000;
        }

        .pos-button-container {
            text-align: center;
            margin-top: 2mm;
        }

        .pos-print-btn {
            background-color: {{ $mainCompany->primary_color ?? '#1f2937' }};
            color: #fff;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            cursor: pointer;
        }

        .pos-print-btn:hover {
            opacity: 0.9;
        }

        hr {
            border-top: 1px dashed #000;
            margin: 2px 0;
        }

        /* ===============================
           PRINT STYLING
        ================================ */
        @media print {
            html, body {
                margin: 0 !important;
                padding: 0 !important;
            }

            body * { visibility: hidden; }
            #invoice, #invoice * { visibility: visible; }

            #invoice {
                position: absolute;
                top: 0;
                left: 0;
                width: 80mm;
                max-width: 100%;
                margin: 0 !important;
                padding: 2mm !important;
                box-sizing: border-box;
            }

            .print\:hidden { display: none !important; }
        }

        @page { size:80mm auto; margin: 0; }
    </style>
</head>
<body>

@php
    $activeUser = session('userObj') ?? Auth::user();
@endphp

<div class="min-h-screen p-2 md:p-6">

    <!-- Back Button -->
    <div class="mb-4 flex justify-end print:hidden">
        <a href="{{ route('depo.direct-sale.index') }}"
           class="inline-flex w-full md:w-auto justify-center items-center px-4 py-2 bg-gray-600 text-white text-sm rounded hover:bg-gray-700">
            ← Back to Sales List
        </a>
    </div>

    <!-- POS Invoice -->
    <div id="invoice">

        <!-- Header -->
        <div class="border-b pb-2 mb-2 text-center">
            <h1 class="text-xl font-bold company-color">{{ $depo->depo_name ?? 'Company Name' }}</h1>
            <p class="text-xs font-semibold text-black">{{ $depo->address ?? '' }}</p>
            <p class="text-xs font-semibold text-black">
                Phone: {{ $depo->contact ?? '' }}
                @if(!empty($depo->email))
                    | Email: {{ $depo->email }}
                @endif
            </p>
            <p class="text-lg font-bold company-color mt-1"> Invoice</p>
        </div>

        <!-- Customer & Sale Info -->
        <div class="flex flex-col justify-between mb-2 text-xs">
            <div class="mb-1">
                <p class="font-semibold text-black">Customer Id: {{ $sale->chemistHouse->id }}</p>
                <p class="font-semibold text-black">Customer Name: {{ $sale->chemistHouse->shop_name ?? 'N/A' }}</p>
                <p class="font-semibold text-black">Address: {{ $sale->chemistHouse->address ?? 'N/A' }}</p>
                <p class="font-semibold text-black">Contact: {{ $sale->chemistHouse->contact ?? 'N/A' }}</p>
                <p class="font-semibold text-black">Submitted By: <strong>{{ $sale->depo->person_name }}</strong></p>
            </div>
            <div class="mb-1">
                <p class="font-semibold text-black">Order By: {{ $sale->depo->person_name }} ({{ $sale->depo->depo_name }})</p>
                <p class="font-semibold text-black">Invoice Date: {{ $sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') : 'N/A' }}</p>
                <p class="font-semibold text-black">Delivery Date: {{ $sale->delivery_date ? \Carbon\Carbon::parse($sale->delivery_date)->format('d M Y') : 'N/A' }}</p>
                <p class="font-semibold text-black">Invoice Type: Cash</p>
            </div>
        </div>

        <hr class="border-t border-gray-400 my-1">

        <!-- Invoice Numbers -->
        <div class="flex justify-between text-xs mb-2">
            <p class="font-semibold text-black">Invoice No: {{ $sale->sale_voucher ?? 'N/A' }}</p>
            <p class="font-semibold text-black">Order No: {{ $sale->id ?? 'N/A' }}</p>
            <p class="font-semibold text-black">Order Date: {{ $sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') : 'N/A' }}</p>
        </div>

        <!-- Items Table -->
        <div class="overflow-x-auto">
            <table class="w-full border border-gray-200 text-xs">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Medicine</th>
                    <th class="text-right">Unit</th>
                    <th class="text-right">Mrp</th>
                    <th class="text-right">Discount</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Free</th>
                    <th class="text-right">Sub Total</th>
                </tr>
                </thead>
                <tbody>
                @forelse($sale->items ?? [] as $index => $item)
                    <tr>
                        <td class="font-semibold text-black">{{ $index + 1 }}</td>
                        <td class="font-semibold text-black">{{ $item->medicine->medicine_name ?? 'N/A' }}</td>
                        <td class="text-right font-semibold text-black">{{ number_format($item->unit_cost ?? 0, 2) }}</td>
                        <td class="text-right font-semibold text-black">{{ number_format($item->mrp ?? 0, 2) }}</td>
                        <td class="text-right font-semibold text-black">{{ number_format($item->medicine_discount ?? 0, 2) }}</td>
                        <td class="text-right font-semibold text-black">{{ $item->quantity ?? 0 }}</td>
                        <td class="text-right font-semibold text-black">{{ $item->free_quantity ?? 0 }}</td>
                        <td class="text-right font-semibold text-black">{{ number_format($item->sub_total ?? 0, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center font-semibold text-black">No items found</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <!-- Summary -->
        <div class="flex justify-start mt-2 summary-left text-xs">
            <div class="w-full">
                <div class="flex justify-between font-semibold text-black"><span>Total</span><span>৳ {{ number_format($sale->total ?? 0,2) }}</span></div>
                <div class="flex justify-between font-semibold text-black"><span>Discount</span><span>৳ {{ number_format($sale->discount ?? 0,2) }}</span></div>
                <div class="flex justify-between font-semibold text-black"><span>VAT</span><span>{{ number_format($sale->vat ?? 0,2) }}%</span></div>
                <hr class="my-1 border-gray-400">
                <div class="flex justify-between font-bold text-black"><span>Grand Total</span><span>৳ {{ number_format($sale->final_total ?? 0,2) }}</span></div>
                <div class="flex justify-between font-semibold text-black mt-1"><span>Given Amount</span><span>৳ {{ number_format($sale->given_amount ?? 0,2) }}</span></div>

                <div class="flex justify-between font-semibold text-black mt-1"><span>Receivable</span><span>৳ {{ number_format($sale->receivable_amount ?? 0,2) }}</span></div>
                @php
                    // Assuming $status comes as a number
                    $statusText = '';
                    $statusClass = '';

                    switch ($sale->payment_status) {
                        case 1:
                            $statusText = 'Paid';
                            $statusClass = 'text-green-600';
                            break;
                        case 2:
                            $statusText = 'Unpaid';
                            $statusClass = 'text-red-600';
                            break;
                        case 3:
                            $statusText = 'Partial';
                            $statusClass = 'text-yellow-600';
                            break;
                        default:
                            $statusText = 'Unknown';
                            $statusClass = 'text-gray-600';
                    }
                @endphp

                <div class="flex justify-between mt-2">
                    <span>Payment Status</span>
                    <span class="font-semibold {{ $statusClass }}">
        {{ $statusText }}
    </span>
                </div>
            </div>
        </div>

        <!-- Signatures -->
        <div class="flex justify-end mt-16 text-xs">
            <div><hr class="border-t border-gray-400 my-1"><p class="font-semibold text-black">Prepared By</p></div>
        </div>

        <!-- Thank You -->
        <div class="thank-you">
            Thank You For Your Purchase!
        </div>

        <div class="thank-you">
           Powered By : Easy It Solution
        </div>

        <!-- Print Button -->
        <div class="mt-6 flex justify-center text-sm text-gray-700 print:hidden">
            <button onclick="window.print()" class="px-3 py-1 bg-teal-800 text-white rounded hover:bg-teal-900">
                Print Voucher
            </button>
        </div>
    </div>
</div>

</body>
</html>
