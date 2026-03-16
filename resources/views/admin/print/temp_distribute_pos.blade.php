<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="{{ asset('assets/backend_assets/js/tailwind/tailwind.js') }}"></script>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
          crossorigin="anonymous" referrerpolicy="no-referrer"/>

    <title>Voucher</title>

    <style>
        /* ===============================
           POS DEFAULT FOR WEB & PRINT
        ================================ */
        body {
            background: #e5e7eb;
        }

        #voucher {
            width: 80mm;
            max-width: 80mm;
            margin: 10px auto;
            padding: 2mm !important;
            font-size: 10px;
            line-height: 1.2;
            color: #000; /* default text black */
        }

        .print-small-text p {
            font-size: 10px;
            line-height: 1.2;
            color: #000;
            font-weight: 600; /* semi-bold */
        }

        table, tr, td, th {
            font-size: 10px;
            color: #000;
            font-weight: 600; /* semi-bold for headers, normal for body */
        }

        table tbody td {
            font-weight: normal; /* make table body normal weight */
        }

        .max-w-5xl {
            max-width: 80mm !important;
        }

        /* ===============================
           PRINT ONLY
        ================================ */
        @media print {
            body * { visibility: hidden; }
            #voucher, #voucher * { visibility: visible; color: #000 !important; font-weight: 600 !important; }

            #voucher {
                position: absolute;
                top: 0;
                left: 0;
            }

            .print\:hidden { display: none !important; }
            hr { margin: 2px 0 !important; }

            .pos-thank-you {
                text-align: center;
                font-size: 10px !important;
                margin-top: 6mm;
                line-height: 1.3;
            }

            .pos-thank-you hr {
                border-top: 1px dashed #000;
                margin: 4px 0;
            }
        }

        @page {
            size: 80mm auto;
            margin: 2mm;
        }
    </style>
</head>

<body>

<div class="min-h-screen p-2 md:p-6">

    <!-- Back Button -->
    <div class="mb-4 flex justify-end print:hidden">
        <a href="{{ route('admin.temp-distribute.index') }}"
           class="inline-flex w-full md:w-auto justify-center items-center px-4 py-2 bg-gray-800 text-white text-sm rounded hover:bg-gray-900">
            ← Back to Distribution List
        </a>
    </div>

    <!-- Voucher -->
    <div id="voucher" class="max-w-5xl mx-auto bg-white shadow rounded-lg p-4 md:p-6">

        <!-- Header -->
        <div class="border-b pb-4 mb-2 print:text-xs print:pb-2 print:mb-2">
            <div class="flex flex-col gap-2">

                <!-- Company Info -->
                <div class="print:text-xs">
                    <h1 class="text-2xl font-bold text-black print:text-sm">
                        {{ $mainCompany->company_name ?? 'Tahsin Pharma' }}
                    </h1>
                    <p class="text-sm text-black print:text-xs">{{ $mainCompany->address ?? '' }}</p>
                    <p class="text-sm text-black print:text-xs">Email: {{ $mainCompany->email ?? '' }}</p>
                    <p class="text-sm text-black print:text-xs">Phone: {{ $mainCompany->phone ?? '' }}</p>
                </div>

                <!-- Depo Info -->
                <div class="print:text-xs">
                    <p class="text-2xl font-bold text-black print:text-sm">{{ $distribute->depo->depo_name ?? 'N/A' }}</p>
                    <p class="text-sm text-black print:text-xs">Area Code: {{ $distribute->depo->area_code ?? 'N/A' }}</p>
                    <p class="text-sm text-black print:text-xs">{{ $distribute->depo->address ?? 'N/A' }}</p>
                    <p class="text-sm text-black print:text-xs">Email: {{ $distribute->depo->email ?? 'N/A' }}</p>
                    <p class="text-sm text-black print:text-xs">Contact: {{ $distribute->depo->contact ?? 'N/A' }}</p>
                </div>

            </div>
        </div>

        <div class="text-center mb-2 print:mb-1 print:text-xs">
            <h2 class="text-2xl font-bold text-black print:text-sm">
                Voucher
            </h2>
        </div>

        <!-- Invoice Info -->
        <div class="print-small-text flex flex-row flex-nowrap justify-between items-start text-xs print:text-[9px] mb-2">

            <p class="font-semibold text-black flex-shrink max-w-[30%] leading-tight break-words">
                Invoice No: {{ $distribute->distribute_voucher ?? 'N/A' }}
            </p>

            <p class="font-semibold text-black flex-shrink max-w-[30%] leading-tight break-words">
                Distribute No: {{ $distribute->id ?? 'N/A' }}
            </p>

            <p class="font-semibold text-black flex-shrink max-w-[30%] leading-tight break-words">
                Date: {{ $distribute->distribute_date ? \Carbon\Carbon::parse($distribute->distribute_date)->format('d M Y') : 'N/A' }}
            </p>

        </div>





        <!-- Items Table -->
        <div class="overflow-x-auto">
            <table class="w-full border border-black text-xs md:text-sm print:text-xs">
                <thead class="bg-gray-200">
                <tr>
                    <th class="border px-2 py-1 text-left text-black font-semibold">#</th>
                    <th class="border px-2 py-1 text-left text-black font-semibold">Medicine</th>
                    <th class="border px-2 py-1 text-right text-black font-semibold">Unit</th>
                    <th class="border px-2 py-1 text-right text-black font-semibold">Qty</th>
                    <th class="border px-2 py-1 text-right text-black font-semibold">Free</th>
                    <th class="border px-2 py-1 text-right text-black font-semibold">Subtotal</th>
                </tr>
                </thead>
                <tbody>
                @forelse($distribute->items ?? [] as $index => $item)
                    <tr>
                        <td class="border px-2 py-1 text-black">{{ $index + 1 }}</td>
                        <td class="border px-2 py-1 text-black">{{ $item->medicine->medicine_name ?? 'N/A' }}</td>
                        <td class="border px-2 py-1 text-right text-black">{{ number_format($item->unit_cost ?? 0, 2) }}</td>
                        <td class="border px-2 py-1 text-right text-black">{{ $item->quantity ?? 0 }}</td>
                        <td class="border px-2 py-1 text-right text-black">{{ $item->free_quantity ?? 0 }}</td>
                        <td class="border px-2 py-1 text-right text-black">{{ number_format($item->sub_total ?? 0, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="border px-2 py-2 text-center text-black">No items found</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <!-- Summary Section -->
        <div class="flex justify-center mt-4 print:mt-2">
            <div class="w-full md:w-1/2 text-sm print:text-xs ml-2">
                <div class="flex justify-between mb-1">
                    <span>Total</span>
                    <span>৳ {{ number_format($distribute->total ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between mb-1">
                    <span>Discount</span>
                    <span>৳ {{ number_format($distribute->discount ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between mb-1">
                    <span>VAT</span>
                    <span>{{ number_format($distribute->vat ?? 0, 2) }}%</span>
                </div>
                <div class="flex justify-between mb-1">
                    <span>Previous Due</span>
                    <span>৳ {{ number_format($distribute->previous_due ?? 0, 2) }}</span>
                </div>
                <hr class="my-1 border-black">
                <div class="flex justify-between font-semibold text-base  print:text-sm">
                    <span>Grand Total</span>
                    <span>৳ {{ number_format($distribute->final_total ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between mt-1">
                    <span>Paid Amount</span>
                    <span>৳ {{ number_format($distribute->given_amount ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between mt-1">
                    <span>Receivable</span>
                    <span>৳ {{ number_format($distribute->receivable_amount ?? 0, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- THANK YOU NOTE -->
        <div class="pos-thank-you text-black text-center mt-6">
            <hr class="mb-2">
            <p class="font-semibold">Thank You!</p>
            <p class="text-xs">Powered by Easy IT Solution Ltd.</p>
        </div>

        <!-- Print Button -->
        <div class="mt-4 flex justify-center text-sm text-black print:hidden">
            <button onclick="window.print()" class="px-3 py-1 bg-teal-800 text-white rounded hover:bg-teal-900">
                Print Voucher
            </button>
        </div>


    </div>
</div>

</body>
</html>
