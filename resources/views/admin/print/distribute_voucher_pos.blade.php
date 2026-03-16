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

            #voucher {
                position: absolute;
                top: 0;
                left: 0;
                width: 80mm;
                max-width: 100%;
                padding: 2mm !important;
                font-size: 10px !important;
                line-height: 1.2 !important;
            }

            .print-small-text p {
                font-size: 10px !important;
                line-height: 1.2;
            }


            .md\:flex-row { flex-direction: row !important; }
            .md\:justify-between { justify-content: space-between !important; }
            .md\:items-start { align-items: flex-start !important; }
            .md\:w-1\/2 { width: 50% !important; }
            .md\:justify-end { justify-content: flex-end !important; }
            .md\:text-left { text-align: left !important; }
            .md\:text-right { text-align: right !important; }

            table, tr, td, th {
                page-break-inside: avoid;
                font-size: 10px !important;
            }

            .print\:hidden { display: none !important; }
            hr { margin: 2px 0 !important; }
        }

        @page { size:80mm auto; margin: 2mm; }
    </style>
</head>
<body class="bg-gray-100">

<div class="min-h-screen p-2 md:p-6">

    <!-- Back Button -->
    <div class="mb-4 flex justify-end print:hidden">
        <a href="{{ route('admin.distribute.index') }}"
           class="inline-flex w-full md:w-auto justify-center items-center px-4 py-2 bg-gray-700 text-white text-sm rounded hover:bg-gray-800">
            ← Back to Distribution List
        </a>
    </div>

    <!-- Voucher -->
    <div id="voucher" class="max-w-5xl mx-auto bg-white shadow rounded-lg p-4 md:p-6">

        <!-- Header -->
        <div class="border-b pb-4 mb-2 print:text-xs print:pb-2 print:mb-2">
            <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-2">

                <!-- Company Info -->
                <div class="md:w-1/2 print:text-xs">
                    <h1 class="text-2xl font-bold text-gray-800 print:text-sm">
                        {{ $mainCompany->company_name ?? 'Tahsin Pharma' }}
                    </h1>
                    <p class="text-sm text-gray-700 print:text-xs">{{ $mainCompany->address ?? '' }}</p>
                    <p class="text-sm text-gray-700 print:text-xs">Email: {{ $mainCompany->email ?? '' }}</p>
                    <p class="text-sm text-gray-700 print:text-xs">Phone: {{ $mainCompany->phone ?? '' }}</p>
                </div>

                <!-- Depo Info -->
                <div class="md:w-1/2 flex md:justify-end print:text-xs">
                    <div class="text-left print:text-xs">
                        <p class="text-2xl font-bold text-gray-800 print:text-sm">{{ $distribute->depo->depo_name ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-700 print:text-xs">Area Code: {{ $distribute->depo->area_code ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-700 print:text-xs">{{ $distribute->depo->address ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-700 print:text-xs">Email: {{ $distribute->depo->email ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-700 print:text-xs">Contact: {{ $distribute->depo->contact ?? 'N/A' }}</p>
                    </div>
                </div>

            </div>
        </div>


        <div class="text-center mb-2 print:mb-1 print:text-xs">
            <h2 class="text-lg font-bold text-gray-800 print:text-sm">
                Distribute Voucher
            </h2>
        </div>

        <!-- Invoice Info -->
        <div class="print-small-text flex flex-col md:flex-row print:flex-row justify-evenly items-start
            space-y-2 md:space-y-0 print:space-y-0 text-center md:text-left mb-4 print:mb-2">

            <p class="font-semibold text-gray-700">
                Invoice No: {{ $distribute->distribute_voucher ?? 'N/A' }}
            </p>

            <p class="font-semibold text-gray-700">
                Temp. Distribute No: {{ $distribute->id ?? 'N/A' }}
            </p>

            <p class="font-semibold text-gray-700">
                Distribute Date:
                {{ $distribute->distribute_date ? \Carbon\Carbon::parse($distribute->distribute_date)->format('d M Y') : 'N/A' }}
            </p>
        </div>

        <!-- Items Table -->
        <div class="overflow-x-auto">
            <table class="w-full border border-gray-200 text-xs md:text-sm print:text-xs">
                <thead class="bg-gray-200">
                <tr>
                    <th class="border px-2 py-1 text-left text-gray-700">#</th>
                    <th class="border px-2 py-1 text-left text-gray-700">Medicine</th>
                    <th class="border px-2 py-1 text-right text-gray-700">Unit</th>
                    <th class="border px-2 py-1 text-right text-gray-700">Qty</th>
                    <th class="border px-2 py-1 text-right text-gray-700">Free</th>
                    <th class="border px-2 py-1 text-right text-gray-700">Subtotal</th>
                </tr>
                </thead>
                <tbody>
                @forelse($distribute->items ?? [] as $index => $item)
                    <tr>
                        <td class="border px-2 py-1 text-gray-700">{{ $index + 1 }}</td>
                        <td class="border px-2 py-1 text-gray-700">{{ $item->medicine->medicine_name ?? 'N/A' }}</td>
                        <td class="border px-2 py-1 text-right text-gray-700">{{ number_format($item->unit_cost ?? 0, 2) }}</td>
                        <td class="border px-2 py-1 text-right text-gray-700">{{ $item->quantity ?? 0 }}</td>
                        <td class="border px-2 py-1 text-right text-gray-700">{{ $item->free_quantity ?? 0 }}</td>
                        <td class="border px-2 py-1 text-right text-gray-700">{{ number_format($item->sub_total ?? 0, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="border px-2 py-2 text-center text-gray-500">No items found</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <!-- Summary -->
        <div class="flex justify-end mt-4 print:mt-2">
            <div class="w-full md:w-1/3 text-sm print:text-xs">
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
                <hr class="my-1 border-gray-400">
                <div class="flex justify-between font-semibold text-base md:text-lg print:text-sm">
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
                <div class="flex justify-between mt-1">
                    <span>Payment Status</span>
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
        <div class="flex justify-end mt-16 print:mt-8">
            <div class="text-right">
                <hr class="border-t border-gray-500 mb-1 w-28 ml-auto print:w-20">
                <p class="text-sm text-gray-700 print:text-xs">Prepared By</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-6 flex flex-col md:flex-row md:justify-between md:items-center gap-2 text-sm text-gray-500 print:hidden">
            <p>Generated on {{ now('Asia/Dhaka')->format('d M Y, h:i A') }}</p>
            <div class="flex gap-2">
                <a onclick="window.print()">
                    <button class="px-3 py-1 bg-teal-700 text-white rounded hover:bg-teal-800">
                        Print Voucher
                    </button>
                </a>
            </div>
        </div>

    </div>
</div>

</body>
</html>
