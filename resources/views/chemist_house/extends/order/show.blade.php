@extends('chemist_house.master')

@section('styles')
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #invoice, #invoice * {
                visibility: visible;
            }

            #invoice {
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

    <div class="min-h-screen bg-gray-100 p-6">

        <!-- Back Button -->
        <div class="mb-4 print:hidden flex justify-end">
            <a href="{{ route('chemist.order.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm rounded hover:bg-gray-700">
                ← Back to Sales List
            </a>
        </div>

        <!-- Invoice -->
        <div id="invoice" class="max-w-5xl mx-auto bg-white shadow rounded-lg p-6">

            <!-- Header -->
            <div class="flex justify-between items-center border-b pb-4 mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Sale Invoice</h2>

                    <p class="text-sm text-gray-500">
                        Invoice No:
                        <span class="font-medium">
                            {{ $sale->sale_voucher ?? 'N/A' }}
                        </span>
                    </p>

                    <p class="text-sm text-gray-500">
                        Date:
                        {{
                            $sale->sale_date
                                ? \Carbon\Carbon::parse($sale->sale_date)->format('d M Y')
                                : 'N/A'
                        }}
                    </p>
                </div>

                <div class="text-right">
                    <h3 class="text-lg font-semibold text-gray-700">Chemist House</h3>
                    <p>{{ $sale->chemistHouse->shop_name ?? 'N/A' }}</p>
                    <p>{{ $sale->chemistHouse->owner_name ?? 'N/A' }}</p>
                    <p>{{ $sale->chemistHouse->contact ?? 'N/A' }}</p>
                    <p>{{ $sale->chemistHouse->address ?? 'N/A' }}</p>
                </div>
            </div>



            <!-- Items Table -->
            <div class="overflow-x-auto">
                <table class="w-full border border-gray-200 text-sm">
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
                    @forelse($sale->items ?? [] as $index => $item)
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
                <div class="w-1/3 text-sm">

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

                    <div class="flex justify-between font-semibold text-lg">
                        <span>Grand Total</span>
                        <span>৳ {{ number_format($sale->final_total ?? 0, 2) }}</span>
                    </div>

                    <div class="flex justify-between mt-2">
                        <span>Paid Amount</span>
                        <span>৳ {{ number_format($sale->given_amount ?? 0, 2) }}</span>
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

            <!-- Footer -->
            <div class="mt-10 flex justify-between items-center text-sm text-gray-500 print:hidden">
                <p>
                    Generated on {{ now('Asia/Dhaka')->format('d M Y, h:i A') }}
                </p>
                <a href="{{route('chemist.order.print',$sale->id)}}">
                    <button
                        class="px-4 py-2 bg-teal-600 text-white rounded hover:bg-teal-700">
                        Print Invoice
                    </button>
                </a>

            </div>

        </div>
    </div>




@endsection
