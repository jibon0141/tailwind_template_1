<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>POS Sale Invoice</title>
    <script src="{{ asset('assets/backend_assets/js/tailwind/tailwind.js') }}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

    <style>
        body { background: #e5e7eb; font-family: sans-serif; }
        #invoice {
            width: 80mm; max-width: 80mm;
            margin: 10px auto; padding: 4px;
            background: #fff; box-shadow: 0 0 5px rgba(0,0,0,0.1);
            font-size: 10px; line-height: 1.2;
        }
        table { width: 100%; border-collapse: collapse; font-size: 10px; }
        th, td { border: 1px solid #ddd; padding: 2px 4px; text-align: right; }
        th { background: #f3f4f6; color: #111827; font-weight: bold; }
        td.text-left { text-align: left; }

        .summary-left { margin-left: 2mm; }
        .pos-thank-you { text-align: center; font-size: 10px; margin: 6px 0; color: #111827; }
        .pos-thank-you hr { border-top: 1px dashed #000; margin: 2px 0; }

        /* PRINT STYLES */
        @media print {
            body * { visibility: hidden; }
            #invoice, #invoice * { visibility: visible; }
            #invoice { position: absolute; top: 0; left: 0; width: 80mm; max-width: 100%; }
            .print\:hidden { display: none !important; }
        }
        @page { size: 80mm auto; margin: 2mm; }
    </style>
</head>
<body>

@php $activeUser = session('userObj') ?? Auth::user(); @endphp

<div class="min-h-screen p-2">

    <!-- POS Invoice -->
    <div id="invoice">

        <!-- Header -->
        <div class="text-center mb-2">
            <h1 class="text-base font-bold text-black-700">{{ $mainCompany->company_name ?? 'Company Name' }}</h1>
            <p class="text-xs">{{ $mainCompany->address ?? '' }}</p>
            <p class="text-xs">
                Phone: {{ $mainCompany->phone ?? '' }}
                @if(!empty($mainCompany->email)) | Email: {{ $mainCompany->email }} @endif
            </p>
            <p class="text-sm font-semibold text-black-700 mt-1">Sale Invoice</p>
        </div>

        <!-- Customer & Sale Info -->
        <div class="text-xs mb-2">
            <p>Customer ID: {{ $sale->chemistHouse->id }}</p>
            <p>Customer: {{ $sale->chemistHouse->shop_name ?? 'N/A' }}</p>
            <p>Address: {{ $sale->chemistHouse->address ?? 'N/A' }}</p>
            <p>Contact: {{ $sale->chemistHouse->contact ?? 'N/A' }}</p>
            <p>Submitted By: {{ $sale->depo->person_name }}</p>
            <p>Order By: {{ $sale->mpo->full_name ?? '' }} ({{ $sale->mpo->employee_code ?? '' }})</p>
            <p>Invoice Date: {{ $sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') : 'N/A' }}</p>
            <p>Delivery Date: {{ $sale->delivery_date ? \Carbon\Carbon::parse($sale->delivery_date)->format('d M Y') : 'N/A' }}</p>
            <p>Invoice Type: Cash</p>
        </div>

        <hr class="my-1 border-gray-400">

        <!-- Invoice Numbers -->
        <div class="flex justify-between text-xs mb-2">
            <p>Invoice No: {{ $sale->sale_voucher ?? 'N/A' }}</p>
            <p>Order No: {{ $sale->id ?? 'N/A' }}</p>
            <p>Order Date: {{ $sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') : 'N/A' }}</p>
        </div>

        <!-- Items Table -->
        <table>
            <thead>
            <tr>
                <th>#</th>
                <th class="text-left">Medicine</th>
                <th>Unit</th>
                <th>MRP</th>
                <th>Disc</th>
                <th>Qty</th>
                <th>Free</th>
                <th>Sub</th>
            </tr>
            </thead>
            <tbody>
            @forelse($sale->items ?? [] as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-left">{{ $item->medicine->medicine_name ?? 'N/A' }}</td>
                    <td>{{ number_format($item->unit_cost ?? 0,2) }}</td>
                    <td>{{ number_format($item->mrp ?? 0,2) }}</td>
                    <td>{{ number_format($item->medicine_discount ?? 0,2) }}</td>
                    <td>{{ $item->quantity ?? 0 }}</td>
                    <td>{{ $item->free_quantity ?? 0 }}</td>
                    <td>{{ number_format($item->sub_total ?? 0,2) }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-gray-500">No items found</td></tr>
            @endforelse
            </tbody>
        </table>

        <!-- Summary -->
        <div class="mt-2 text-xs summary-left">
            <div class="flex justify-between"><span>Total</span> <span>৳ {{ number_format($sale->total ?? 0,2) }}</span></div>
            <div class="flex justify-between"><span>Discount</span> <span>৳ {{ number_format($sale->discount ?? 0,2) }}</span></div>
            <div class="flex justify-between"><span>VAT</span> <span>{{ number_format($sale->vat ?? 0,2) }}%</span></div>
            <hr class="my-1 border-gray-400">
            <div class="flex justify-between font-semibold"><span>Grand Total</span> <span>৳ {{ number_format($sale->final_total ?? 0,2) }}</span></div>
            <div class="flex justify-between"><span>Receivable</span> <span>৳ {{ number_format($sale->receivable_amount ?? 0,2) }}</span></div>
            @php
                if (($sale->receivable_amount ?? 0) <= 0) {
                    $status = 'paid';
                } elseif (($sale->given_amount ?? 0) > 0) {
                    $status = 'partial';
                } else {
                    $status = 'due';
                }
            @endphp

            <div class="flex justify-between mt-2">
                <span>Payment Status</span>
                <span class="font-semibold
                            @if($status === 'paid') text-green-600
                            @elseif($status === 'partial') text-yellow-600
                            @else text-red-600
                            @endif
                        ">
                            {{ ucfirst($status) }}
                        </span>
            </div>
        </div>

        <!-- Signature Part -->
        <div class="flex justify-end mt-16 text-center text-xs">
            <div><hr class="border-t border-gray-400 mb-1"><p>Prepared By</p></div>
        </div>

        <!-- Thank You Note -->
        <div class="pos-thank-you mt-2">
            <hr>
            <p>Thank You for Your Purchase!</p>
            <hr>
        </div>

        <!-- Thank You Note -->
        <div class=" mt-2 text-center">
            <hr>
            <p> Powered By : Easy IT Solution</p>
            <hr>
        </div>

        <!-- Footer Buttons -->
        <div class="mt-10 flex flex-col flex-col md:justify-between md:items-center gap-3 text-sm text-gray-500 print:hidden">
            <a onclick="window.print()">
                <button
                    class="w-full md:w-auto px-4 py-2 bg-teal-600 text-white rounded hover:bg-teal-700">
                    Print Invoice
                </button>
            </a>
        </div>

    </div>
</div>
</body>
</html>
