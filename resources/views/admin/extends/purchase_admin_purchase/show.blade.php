@extends('admin.master')

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
    <div class="min-h-screen bg-gray-100 p-2 md:p-6">

        <!-- Back Button -->
        <div class="mb-4 flex justify-end print:hidden">
            <a href="{{ route('admin.medicine.purchase.index') }}"
               class="px-4 py-2 bg-gray-600 text-white text-sm rounded hover:bg-gray-700">
                ← Back to Purchase List
            </a>
        </div>

        @include('admin.include.message')
        <!-- Invoice -->
        <div id="invoice" class="max-w-5xl mx-auto bg-white shadow rounded-lg p-4 md:p-6">


            <!-- Header -->
            <div class="border-b pb-4 mb-6">

                <!-- Company Info -->
                <div  class="pb-4  text-center w-[60%] mx-auto">
                    <h1 class="text-2xl font-bold text-gray-800">
                        {{ $mainCompany->company_name ?? 'Company Name' }}
                    </h1>

                    <p class="text-sm text-gray-600">
                        {{ $mainCompany->address ?? '' }}
                    </p>

                    <p class="text-sm text-gray-600">
                        Phone: {{ $mainCompany->phone ?? '' }}
                    </p>

                    <p class="text-sm text-gray-600">
                        @if($mainCompany->email)
                             Email: {{ $mainCompany->email }}
                        @endif
                    </p>

                    <p class="text-sm text-gray-600">
                        {{ $mainCompany->website_url ?? '' }}
                    </p>

                    <p class="text-3xl font-semibold mt-2">Invoice</p>
                </div>

                <hr class="border-t border-gray-400 my-3">

                <!-- Supplier & Invoice Info -->
                <div class="flex justify-between items-start text-sm text-gray-600">
                    <div class="text-left">
                        <p><strong>Invoice No:</strong> {{ $purchase->purchase_voucher ?? 'N/A' }}</p>
                        <p><strong>Supplier Id:</strong> {{ $purchase->supplier->supplier_code ?? 'N/A' }}</p>
                        <p><strong>Purchase Date:</strong>
                            {{ $purchase->purchase_date
                                ? \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y')
                                : 'N/A' }}
                        </p>
{{--                        <p><strong>Account:</strong> {{ $purchase->account->account_name ?? 'N/A' }}</p>--}}
                    </div>
                    <div>
                        <p><strong>Supplier:</strong> {{ $purchase->supplier->supplier_name ?? 'N/A' }} </p>
                        <p>
                            Address: {!! nl2br(e($purchase->supplier->voucher_address ?? 'N/A')) !!}
                        </p>

                        <p>Mobile: {{ $purchase->supplier->phone ?? 'N/A' }}</p>
                        <p>Email: {{ $purchase->supplier->email ?? 'N/A' }}</p>
                    </div>

                </div>
            </div>



            <!-- Items Table -->
            <div class="overflow-x-auto">
                <table class="w-full border border-gray-200 text-xs md:text-sm">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-3 py-2 text-left">#</th>
                        <th class="border px-3 py-2 text-left">Medicine</th>
                        <th class="border px-3 py-2 text-right">Mrp</th>
                        <th class="border px-3 py-2 text-right">Discount %</th>
                        <th class="border px-3 py-2 text-right">Unit Cost</th>
                        <th class="border px-3 py-2 text-right">Qty</th>
                        <th class="border px-3 py-2 text-right">Free Qty</th>
                        <th class="border px-3 py-2 text-right">Sub Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($purchase->items as $index => $item)
                        <tr>
                            <td class="border px-3 py-2">{{ $index + 1 }}</td>
                            <td class="border px-3 py-2">
                                {{ $item->medicine->medicine_name ?? 'N/A' }}
                            </td>
                            <td class="border px-3 py-2 text-right">
                                {{ number_format($item->mrp ?? 0, 2) }}
                            </td>
                            <td class="border px-3 py-2 text-right">
                                {{ number_format($item->medicine_discount ?? 0, 2) }}
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
                            <td colspan="6" class="text-center py-4 text-gray-500">
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
                        <span>৳ {{ number_format($purchase->total ?? 0, 2) }}</span>
                    </div>

                    <div class="flex justify-between mb-2">
                        <span>Discount</span>
                        <span>৳ {{ number_format($purchase->discount ?? 0, 2) }}</span>
                    </div>

                    <div class="flex justify-between mb-2">
                        <span>VAT</span>
                        <span>{{ number_format($purchase->vat ?? 0, 2) }} %</span>
                    </div>

                    <div class="flex justify-between mb-2">
                        <span>Previous Due</span>
                        <span>৳ {{ number_format($purchase->previous_due ?? 0, 2) }}</span>
                    </div>

                    <hr class="my-2">

                    <div class="flex justify-between font-semibold text-base md:text-lg">
                        <span>Grand Total</span>
                        <span>৳ {{ number_format($purchase->final_total ?? 0, 2) }}</span>
                    </div>

                    <div class="flex justify-between mt-2">
                        <span>Paid Amount</span>
                        <span>৳ {{ number_format($purchase->given_amount ?? 0, 2) }}</span>
                    </div>

                    <div class="flex justify-between mt-2">
                        <span>Payable Amount</span>
                        <span>৳ {{ number_format($purchase->payable_amount ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-10 flex justify-between items-center text-sm text-gray-500 print:hidden">
                <div class="flex items-center gap-2 flex-shrink-0 whitespace-nowrap">
                    <span>Generated on {{ now('Asia/Dhaka')->format('d M Y, h:i A') }}</span>
                    <span>||</span>
                    <span>Powered By: Easy IT Solution</span>
                </div>

                <a href="{{ route('admin.medicine.purchase.print', $purchase->id) }}">
                    <button class="px-4 py-2 bg-teal-600 text-white rounded hover:bg-teal-700">
                        Print Invoice
                    </button>
                </a>
            </div>

        </div>
    </div>
@endsection
