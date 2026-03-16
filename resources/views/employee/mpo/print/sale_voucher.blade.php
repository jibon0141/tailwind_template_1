<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="{{ asset('assets/backend_assets/js/tailwind/tailwind.js') }}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-MPLF0+3A...snipped_for_length..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Sale Invoice</title>
</head>
<body class="bg-gray-100">

@php
    $activeUser = session('userObj') ?? Auth::user();
@endphp
<div class="min-h-screen bg-gray-100 p-2 md:p-6">

    <!-- Back Button -->
    <div class="mb-4 flex justify-end print:hidden">
        <a href="{{ route('mpo.sale.index') }}"
           class="inline-flex w-full md:w-auto justify-center items-center px-4 py-2 bg-gray-600 text-white text-sm rounded hover:bg-gray-700">
            ← Back to Sales List
        </a>
    </div>


    <!-- Invoice -->
    <div id="invoice" class="max-w-5xl mx-auto bg-white shadow rounded-lg p-4 md:p-6">

        <!-- Header -->
        <div class="border-b pb-4 mb-6">

            <!-- Company Info -->
            <div class="text-center mb-1">
                <h1 class="text-2xl font-bold text-gray-800">
                    {{ $mainCompany->company_name ?? 'Company Name' }}
                </h1>

                <p class="text-sm text-gray-600">
                    {{ $mainCompany->address ?? '' }}
                </p>

                <p class="text-sm text-gray-600">
                    Phone : {{ $mainCompany->phone ?? '' }}
                    @if(!empty($mainCompany->email))
                        | Email : {{ $mainCompany->email }}
                    @endif
                </p>

                <p class="text-2xl font-semibold text-gray-800">
                    Sale Invoice
                </p>
            </div>

            <!-- Customer Info -->

            <div class="flex flex-row justify-between items-center mb-4">
                <div class="">
                    <p class="text-sm text-gray-600">Customer Id : {{$sale->chemistHouse->id}}</p>
                    <p class="text-sm text-gray-600">Customer Name : {{ $sale->chemistHouse->shop_name ?? 'N/A' }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-600">Printing Date : {{
                                $sale->sale_date
                                    ? \Carbon\Carbon::now()->format('d M Y')
                                    : 'N/A'
                            }}
                    </p>
                </div>
            </div>


            <hr class="border-t border-gray-400 my-3">

            <div class="flex flex-row justify-between items-center">
                <div class="">
                    <p class="text-sm text-gray-600 mb-1">{{$sale->chemistHouse->address ?? 'N/A' }}</p>
                    <p class="text-sm text-gray-600 mb-1">{{ $sale->chemistHouse->contact ?? 'N/A' }}</p>
                    <p class="text-sm text-gray-600 font-bold">Submitted By : <span class="text-base">{{$sale->depo->person_name}}</span> </p>
                    <p></p>
                </div>

                <div>
                    <p class="text-sm text-gray-600 mb-1">Order By : {{$sale->mpo->full_name}} ({{$sale->mpo->employee_code}})</p>
                    <p class="text-sm text-gray-600 mb-1">Invoice Date : {{
                                $sale->sale_date
                                    ? \Carbon\Carbon::parse($sale->sale_date)->format('d M Y')
                                    : 'N/A'
                            }} </p>
                    <p class="text-sm text-gray-600 mb-1">Delivery Date : {{
                                $sale->sale_date
                                    ? \Carbon\Carbon::parse($sale->delivery_date)->format('d M Y')
                                    : 'N/A'
                            }} </p>
                    <p class="text-sm text-gray-600 mb-1">Invoice Type : Cash </p>
                </div>

            </div>

            <hr class="border-t border-gray-400 my-3">

            <div class="flex flex-col md:flex-row md:justify-evenly gap-2 text-center md:text-left">
                <p class="text-sm font-semibold">Invoice No : {{ $sale->sale_voucher ?? 'N/A' }}</p>
                <p class="text-sm font-semibold">Order No : {{ $sale->id ?? 'N/A' }}</p>
                <p class="text-sm font-semibold">
                    Order Date :
                    {{ $sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') : 'N/A' }}
                </p>
            </div>




        </div>


        <!-- Items Table -->
        <div class="overflow-x-auto">
            <table class="w-full border border-gray-200 text-xs md:text-sm">
                <thead class="bg-gray-100">
                <tr>
                    <th class="border px-3 py-2 text-left">#</th>
                    <th class="border px-3 py-2 text-right">Medicine</th>
                    <th class="border px-3 py-2 text-right">Unit Cost</th>
                    <th class="border px-3 py-2 text-right">Mrp</th>
                    <th class="border px-3 py-2 text-right">Discount</th>
                    <th class="border px-3 py-2 text-right">Qty</th>
                    <th class="border px-3 py-2 text-right">Free Qty</th>
                    <th class="border px-3 py-2 text-right">Sub Total</th>
                </tr>
                </thead>
                <tbody>
                @forelse($sale->items ?? [] as $index => $item)
                    <tr>
                        <td class="border px-3 py-2 ">{{ $index + 1 }}</td>
                        <td class="border px-3 py-2 text-right">
                            {{ $item->medicine->medicine_name ?? 'N/A' }}
                        </td>
                        <td class="border px-3 py-2 text-right">
                            {{ number_format($item->unit_cost ?? 0, 2) }}
                        </td>
                        <td class="border px-3 py-2 text-right">
                            {{ number_format($item->mrp ?? 0, 2) }}
                        </td>
                        <td class="border px-3 py-2 text-right">
                            {{ number_format($item->medicine_discount ?? 0, 2) }}
                        </td>
                        <td class="border px-3 py-2 text-right">
                            {{ $item->quantity ?? 0 }}
                        </td>
                        <td class="border px-3 py-2 text-right">
                            {{ $item->free_quantity ?? 0 }}
                        </td>
                        <td class="border px-3 py-2 text-right">
                            {{ number_format($item->sub_total ?? 0, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="border px-3 py-4 text-center text-gray-500">
                            No items found
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <!-- Summary -->
        <div class="flex justify-end mt-6">
            <div class="w-full md:w-1/3 text-sm">

                <div class="flex justify-between mb-2">
                    <span>Total</span>
                    <span>৳ {{ number_format($sale->total ?? 0, 2) }}</span>
                </div>

                <div class="flex justify-between mb-2">
                    <span>Discount</span>
                    <span>৳ {{ number_format($sale->discount ?? 0, 2) }}</span>
                </div>

                <div class="flex justify-between mb-2">
                    <span>VAT</span>
                    <span>{{ number_format($sale->vat ?? 0, 2) }} %</span>
                </div>

                <hr class="my-2">

                <div class="flex justify-between font-semibold text-base md:text-lg">
                    <span>Grand Total</span>
                    <span>৳ {{ number_format($sale->final_total ?? 0, 2) }}</span>
                </div>

                <div class="flex justify-between mt-2">
                    <span>Receivable Amount</span>
                    <span>৳ {{ number_format($sale->receivable_amount ?? 0, 2) }}</span>
                </div>

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
        </div>


        <!-- Signature Part -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mt-16 text-center">
            <div>
                <hr class="border-t border-gray-400 mb-2">
                <p class="text-sm">Customer Signature</p>
            </div>

            <div>
                <hr class="border-t border-gray-400 mb-2">
                <p class="text-sm">Prepared By</p>
            </div>

            <div>
                <hr class="border-t border-gray-400 mb-2">
                <p class="text-sm">Delivered By</p>
            </div>

            <div>
                <hr class="border-t border-gray-400 mb-2">
                <p class="text-sm">Authorized Signature</p>
            </div>
        </div>


        <!-- Footer -->
        <div class="mt-10 flex flex-col md:flex-row md:justify-between md:items-center gap-3 text-sm text-gray-500 print:hidden">
            <p>
                Generated on {{ now('Asia/Dhaka')->format('d M Y, h:i A') }} ||
                <span>Powered By : Easy IT Solution</span>
            </p>

            <a onclick="window.print()">
                <button
                    class="w-full md:w-auto px-4 py-2 bg-teal-600 text-white rounded hover:bg-teal-700">
                    Print Invoice
                </button>
            </a>
        </div>

        <div class="hidden print:block mt-6 text-center text-xs text-gray-500">
            Powered By : Easy IT Solution
        </div>

    </div>
</div>
</body>
</html>
