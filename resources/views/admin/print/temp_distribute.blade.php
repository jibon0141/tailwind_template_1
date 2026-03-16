<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="{{ asset('assets/backend_assets/js/tailwind/tailwind.js') }}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-MPLF0+3A...snipped_for_length..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Voucher</title>
    <style>
        @media print {
            body * { visibility: hidden; }
            #voucher, #voucher * { visibility: visible; }
            #voucher { position: absolute; top: 0; left: 0; width: 100%; }
            .print\:hidden { display: none !important; }

            /* Force md layout in print */
            .md\:flex-row { flex-direction: row !important; }
            .md\:justify-between { justify-content: space-between !important; }
            .md\:items-start { align-items: flex-start !important; }
            .md\:w-1\/2 { width: 50% !important; }
            .md\:justify-end { justify-content: flex-end !important; }
            .md\:text-left { text-align: left !important; }
            .md\:text-right { text-align: right !important; }

            table, tr, td, th { page-break-inside: avoid; }

            body { font-size: 12px; }
        }

        @page { size: auto; margin: 12mm; }
    </style>
</head>
<body class="bg-gray-100">

<div class="min-h-screen p-2 md:p-6">

    <!-- Back Button -->
    <div class="mb-4 flex justify-end print:hidden">
        <a href="{{ route('admin.temp-distribute.index') }}"
           class="inline-flex w-full md:w-auto justify-center items-center px-4 py-2 bg-gray-700 text-white text-sm rounded hover:bg-gray-800">
            ← Back to Distribution List
        </a>
    </div>

    <!-- Voucher -->
    <div id="voucher" class="max-w-5xl mx-auto bg-white shadow rounded-lg p-4 md:p-6">

        <!-- Header -->
        <div class="border-b pb-4 mb-2">
            <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-5">

                <!-- Company Info -->
                <div class="md:w-1/2">
                    <h1 class="text-2xl font-bold text-gray-800">
                        {{ $mainCompany->company_name ?? 'Tahsin Pharma' }}
                    </h1>
                    <p class="text-sm text-gray-700">{{ $mainCompany->address ?? '' }}</p>
                    <p class="text-sm text-gray-700">Email : {{ $mainCompany->email ?? '' }}</p>
                    <p class="text-sm text-gray-700">Phone : {{ $mainCompany->phone ?? '' }}</p>
                </div>

                <!-- Depo Info -->
                <div class="md:w-1/2 flex md:justify-end">
                    <div class="text-left">
                        <p class="text-2xl font-bold text-gray-800">{{ $distribute->depo->depo_name ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-700">Area Code : {{ $distribute->depo->area_code ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-700">{{ $distribute->depo->address ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-700">Email : {{ $distribute->depo->email ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-700">Contact : {{ $distribute->depo->contact ?? 'N/A' }}</p>
                    </div>
                </div>

            </div>
        </div>

        <div class="text-center mb-2 ">
            <h2 class="text-lg font-bold text-gray-800 ">
                Distribute Voucher
            </h2>
        </div>

        <!-- Invoice Info -->
        <div class="flex flex-col md:flex-row print:flex-row
            justify-evenly items-start
            space-y-2 md:space-y-0 print:space-y-0
            text-center md:text-left mb-4">
            <p class="text-sm font-semibold text-gray-700">Invoice No : {{ $distribute->distribute_voucher ?? 'N/A' }}</p>
            <p class="text-sm font-semibold text-gray-700">Temp. Distribute No : {{ $distribute->id ?? 'N/A' }}</p>
            <p class="text-sm font-semibold text-gray-700">
                Distribute Date :
                {{ $distribute->distribute_date ? \Carbon\Carbon::parse($distribute->distribute_date)->format('d M Y') : 'N/A' }}
            </p>
        </div>

        <!-- Items Table -->
        <div class="overflow-x-auto">
            <table class="w-full border border-gray-200 text-xs md:text-sm">
                <thead class="bg-gray-200">
                <tr>
                    <th class="border px-3 py-2 text-left text-gray-700">#</th>
                    <th class="border px-3 py-2 text-left text-gray-700">Medicine</th>
                    <th class="border px-3 py-2 text-right text-gray-700">Unit Cost</th>
                    <th class="border px-3 py-2 text-right text-gray-700">Qty</th>
                    <th class="border px-3 py-2 text-right text-gray-700">Free Qty</th>
                    <th class="border px-3 py-2 text-right text-gray-700">Sub Total</th>
                </tr>
                </thead>
                <tbody>
                @forelse($distribute->items ?? [] as $index => $item)
                    <tr>
                        <td class="border px-3 py-2 text-gray-700">{{ $index + 1 }}</td>
                        <td class="border px-3 py-2 text-gray-700">{{ $item->medicine->medicine_name ?? 'N/A' }}</td>
                        <td class="border px-3 py-2 text-right text-gray-700">{{ number_format($item->unit_cost ?? 0, 2) }}</td>
                        <td class="border px-3 py-2 text-right text-gray-700">{{ $item->quantity ?? 0 }}</td>
                        <td class="border px-3 py-2 text-right text-gray-700">{{ $item->free_quantity ?? 0 }}</td>
                        <td class="border px-3 py-2 text-right text-gray-700">{{ number_format($item->sub_total ?? 0, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="border px-3 py-4 text-center text-gray-500">No items found</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <!-- Summary -->
        <div class="flex justify-end mt-6">
            <div class="w-full md:w-1/3 text-sm">
                <div class="flex justify-between mb-2">
                    <span class="text-gray-700">Total</span>
                    <span class="text-gray-700">৳ {{ number_format($distribute->total ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-gray-700">Discount</span>
                    <span class="text-gray-700">৳ {{ number_format($distribute->discount ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-gray-700">VAT</span>
                    <span class="text-gray-700">{{ number_format($distribute->vat ?? 0, 2) }} %</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-gray-700">Previous Due</span>
                    <span class="text-gray-700">৳ {{ number_format($distribute->previous_due ?? 0, 2) }}</span>
                </div>

                <hr class="my-2 border-gray-400">

                <div class="flex justify-between font-semibold text-base md:text-lg">
                    <span class="text-gray-800">Grand Total</span>
                    <span class="text-gray-800">৳ {{ number_format($distribute->final_total ?? 0, 2) }}</span>
                </div>

                <div class="flex justify-between mt-2">
                    <span class="text-gray-700">Paid Amount</span>
                    <span class="text-gray-700">৳ {{ number_format($distribute->given_amount ?? 0, 2) }}</span>
                </div>

                <div class="flex justify-between mt-2">
                    <span class="text-gray-700">Receivable Amount</span>
                    <span class="text-gray-700">৳ {{ number_format($distribute->receivable_amount ?? 0, 2) }}</span>
                </div>

                <div class="flex justify-between mt-2">
                    <span class="text-gray-700">Payment Status</span>
                    <span class="font-semibold
                        @if($distribute->payment_status == 1) text-green-700
                        @elseif($distribute->payment_status == 3) text-yellow-700
                        @else text-red-700
                        @endif">
                        @if($distribute->payment_status == 1) Paid
                        @elseif($distribute->payment_status == 3) Partial
                        @else Unpaid
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Signatures -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-10 mt-16 text-center">
            <div><hr class="border-t border-gray-500 mb-2 w-36 mx-auto"><p class="text-sm text-gray-700">Prepared By</p></div>
            <div><hr class="border-t border-gray-500 mb-2 w-36 mx-auto"><p class="text-sm text-gray-700">Checked By</p></div>
            <div><hr class="border-t border-gray-500 mb-2 w-36 mx-auto"><p class="text-sm text-gray-700">Delivered By</p></div>
            <div><hr class="border-t border-gray-500 mb-2 w-36 mx-auto"><p class="text-sm text-gray-700">Authorized Signature</p></div>
        </div>

        <!-- Footer -->
        <div class="mt-10 flex flex-wrap md:flex-nowrap justify-between items-center gap-3 text-sm text-gray-500">

            <!-- Left: Generated time + Powered By -->
            <div class="flex items-center gap-2 flex-shrink-0 whitespace-nowrap">
                <span>Generated on {{ now('Asia/Dhaka')->format('d M Y, h:i A') }}</span>
                <span>||</span>
                <span>Powered By: Easy IT Solution</span>
            </div>

            <!-- Right: Print button (hidden in print) -->
            <div class="flex gap-2 print:hidden flex-shrink-0">
                <button onclick="window.print()"
                        class="px-4 py-2 bg-teal-700 text-white rounded hover:bg-teal-800">
                    Print Voucher
                </button>
            </div>

        </div>



    </div>
</div>

</body>
</html>
