@extends('admin.master')

@section('styles')
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #voucher, #voucher * {
                visibility: visible;
            }

            #voucher {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
            }

            .print\:hidden {
                display: none !important;
            }
        }
    </style>
@endsection

@section('content')
    <div class="min-h-screen bg-gray-100 p-2 md:p-6">

        <!-- Back Button -->
        <div class="mb-4 flex justify-end print:hidden">
            <a href="{{ route('admin.temp-distribute.index') }}"
               class="inline-flex w-full md:w-auto justify-center items-center px-4 py-2 bg-gray-600 text-white text-sm rounded hover:bg-gray-700">
                ← Back to Distribution List
            </a>
        </div>

        @include('admin.include.message')
        <!-- Voucher -->
        <div id="voucher" class="max-w-5xl mx-auto bg-white shadow rounded-lg p-4 md:p-6">

            <!-- Header -->
            <div class="border-b pb-4 mb-2">

                <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-5">

                    <!-- Company Info (Left) -->
                    <div class="md:w-1/2">
                        <h1 class="text-2xl font-bold text-gray-800">
                            {{ $mainCompany->company_name ?? 'Tahsin Pharma' }}
                        </h1>

                        <p class="text-sm text-gray-600">
                            {{ $mainCompany->address ?? '' }}
                        </p>

                        <p class="text-sm text-gray-600">
                            Email : {{ $mainCompany->email ?? '' }}
                        </p>

                        <p class="text-sm text-gray-600">
                            Phone : {{ $mainCompany->phone ?? '' }}
                        </p>

                    </div>

                    <!-- Depo Info (Right aligned block, left aligned text) -->
                    <div class="md:w-1/2 flex md:justify-end">
                        <div class="text-left">
                            <p class="text-2xl font-bold text-gray-800">
                                {{ $distribute->depo->depo_name ?? 'N/A' }}
                            </p>

                            <p class="text-sm text-gray-600">
                                Area Code : {{ $distribute->depo->area_code ?? 'N/A' }}
                            </p>

                            <p class="text-sm text-gray-600">
                                {{ $distribute->depo->address ?? 'N/A' }}
                            </p>

                            <p class="text-sm text-gray-600">
                                Email : {{ $distribute->depo->email ?? 'N/A' }}
                            </p>

                            <p class="text-sm text-gray-600">
                                Contact : {{ $distribute->depo->contact ?? 'N/A' }}
                            </p>
                        </div>
                    </div>


                </div>
            </div>

            <div class="text-center mb-2 ">
                <h2 class="text-lg font-bold text-gray-800 ">
                    Voucher
                </h2>
            </div>



            <div class="flex flex-col md:flex-row md:justify-evenly gap-2 text-center md:text-left mb-4">
                <p class="text-sm font-semibold">Invoice No : {{ $distribute->distribute_voucher ?? 'N/A' }}</p>
                <p class="text-sm font-semibold">Temp. Distribute No : {{ $distribute->id ?? 'N/A' }}</p>
                <p class="text-sm font-semibold">
                    Distribute Date :
                    {{ $distribute->distribute_date ? \Carbon\Carbon::parse($distribute->distribute_date)->format('d M Y') : 'N/A' }}
                </p>
            </div>

            <!-- Items Table -->
            <div class="overflow-x-auto">
                <table class="w-full border border-gray-200 text-xs md:text-sm">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-3 py-2 text-left">#</th>
                        <th class="border px-3 py-2 text-left">Medicine</th>
                        <th class="border px-3 py-2 text-right">Unit Cost</th>
                        <th class="border px-3 py-2 text-right">Qty</th>
                        <th class="border px-3 py-2 text-right">Free Qty</th>
                        <th class="border px-3 py-2 text-right">Sub Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($distribute->items ?? [] as $index => $item)
                        <tr>
                            <td class="border px-3 py-2">{{ $index + 1 }}</td>
                            <td class="border px-3 py-2">
                                {{ $item->medicine->medicine_name ?? 'N/A' }}
                            </td>
                            <td class="border px-3 py-2 text-right">
                                {{ number_format($item->unit_cost ?? 0, 2) }}
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
                        <span>৳ {{ number_format($distribute->total ?? 0, 2) }}</span>
                    </div>

                    <div class="flex justify-between mb-2">
                        <span>Discount</span>
                        <span>৳ {{ number_format($distribute->discount ?? 0, 2) }}</span>
                    </div>

                    <div class="flex justify-between mb-2">
                        <span>VAT</span>
                        <span>{{ number_format($distribute->vat ?? 0, 2) }} %</span>
                    </div>

                    <div class="flex justify-between mb-2">
                        <span>Previous Due</span>
                        <span>৳ {{ number_format($distribute->previous_due ?? 0, 2) }}</span>
                    </div>

                    <hr class="my-2">

                    <div class="flex justify-between font-semibold text-base md:text-lg">
                        <span>Grand Total</span>
                        <span>৳ {{ number_format($distribute->final_total ?? 0, 2) }}</span>
                    </div>

                    <div class="flex justify-between mt-2">
                        <span>Paid Amount</span>
                        <span>৳ {{ number_format($distribute->given_amount ?? 0, 2) }}</span>
                    </div>

                    <div class="flex justify-between mt-2">
                        <span>Receivable Amount</span>
                        <span>৳ {{ number_format($distribute->receivable_amount ?? 0, 2) }}</span>
                    </div>

                    <div class="flex justify-between mt-2">
                        <span>Payment Status</span>
                        <span class="font-semibold
                            @if($distribute->payment_status == 1) text-green-600
                            @elseif($distribute->payment_status == 3) text-yellow-600
                            @else text-red-600
                            @endif">
                            @if($distribute->payment_status == 1)
                                Paid
                            @elseif($distribute->payment_status == 3)
                                Partial
                            @else
                                Unpaid
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Signature -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-10 mt-16 text-center">
                <div>
                    <hr class="border-t border-gray-400 mb-2 w-36 mx-auto">
                    <p class="text-sm">Prepared By</p>
                </div>
                <div>
                    <hr class="border-t border-gray-400 mb-2 w-36 mx-auto">
                    <p class="text-sm">Checked By</p>
                </div>
                <div>
                    <hr class="border-t border-gray-400 mb-2 w-36 mx-auto">
                    <p class="text-sm">Delivered By</p>
                </div>
                <div>
                    <hr class="border-t border-gray-400 mb-2 w-36 mx-auto">
                    <p class="text-sm">Authorized Signature</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-10 flex md:flex-row flex-row justify-between items-center text-sm text-gray-500 print:hidden">

                <!-- LEFT: force single line -->
                <div class="flex items-center gap-2 w-full md:w-auto whitespace-nowrap overflow-hidden">
                    <span>Generated on {{ now('Asia/Dhaka')->format('d M Y, h:i A') }}</span>
                    <span>||</span>
                    <span>Powered By: Easy IT Solution</span>
                </div>

                <!-- RIGHT: buttons -->
                <div class="flex gap-2 shrink-0">
                    <a href="{{ route('admin.temp-distribute.print', $distribute->id) }}">
                        <button class="px-4 py-2 bg-teal-600 text-white rounded hover:bg-teal-700">
                            Print View
                        </button>
                    </a>

                    <a href="{{ route('admin.temp-distribute.pos.print', $distribute->id) }}">
                        <button class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                            POS View
                        </button>
                    </a>
                </div>

            </div>




        </div>
    </div>
@endsection
