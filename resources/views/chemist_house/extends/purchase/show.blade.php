@extends('depo.master')

@section('content')
    <div class="min-h-screen bg-gray-100 p-6">

        <!-- Back Button -->
        <div class="mb-4 print:hidden flex justify-end">
            <a href="{{ route('depo.purchase.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm rounded hover:bg-gray-700">
                ← Back to Distribution List
            </a>
        </div>

        <div class="max-w-5xl mx-auto bg-white shadow rounded-lg p-6">

            <!-- Header -->
            <div class="flex justify-between items-center border-b pb-4 mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Distribution Voucher</h2>

                    <p class="text-sm text-gray-500">
                        Voucher No:
                        <span class="font-medium">{{ $distribute->distribute_voucher ?? 'N/A' }}</span>
                    </p>

                    <p class="text-sm text-gray-500">
                        Date:
                        {{ \Carbon\Carbon::parse($distribute->distribute_date)->format('d M Y') ?? 'N/A' }}
                    </p>
                </div>

                <div class="text-right">
                    <h3 class="text-lg font-semibold text-gray-700">Depo</h3>
                    <p>{{ $distribute->depo->depo_name ?? 'N/A' }}</p>
                    <p>{{ $distribute->depo->contact ?? 'N/A' }}</p>
                    <p>{{ $distribute->depo->email ?? 'N/A' }}</p>
                    <p>{{ $distribute->depo->address ?? 'N/A' }}</p>
                </div>
            </div>

{{--            <!-- Account Info -->--}}
{{--            <div class="flex justify-between mb-6 text-sm">--}}
{{--                <div class="w-1/2">--}}
{{--                    <h4 class="font-semibold text-gray-700 mb-1">Depo Account</h4>--}}
{{--                    <p>{{ $distribute->account->account_name ?? 'N/A' }}</p>--}}
{{--                    <p>Account No: {{ $distribute->account->account_no ?? 'N/A' }}</p>--}}
{{--                    <p>Current Balance: ৳ {{ number_format($distribute->account->balance ?? 0, 2) }}</p>--}}
{{--                </div>--}}

{{--                <div class="w-1/2 text-right">--}}
{{--                    <h4 class="font-semibold text-gray-700 mb-1">Company Account</h4>--}}
{{--                    <p>{{ $company_account->account_name ?? 'N/A' }}</p>--}}
{{--                    <p>Account No: {{$company_account->account_no ?? 'N/A' }}</p>--}}
{{--                    <p>Receive Balance: ৳  {{ number_format($distribute->given_amount ?? 0, 2) }} </p>--}}

{{--                </div>--}}
{{--            </div>--}}


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
                    @forelse($distribute->items as $index => $item)
                        <tr>
                            <td class="border px-3 py-2">{{ $index + 1 }}</td>
                            <td class="border px-3 py-2">{{ $item->medicine->medicine_name ?? 'N/A' }}</td>
                            <td class="border px-3 py-2 text-right">{{ number_format($item->unit_cost, 2) }}</td>
                            <td class="border px-3 py-2 text-right">{{ $item->quantity }}</td>
                            <td class="border px-3 py-2 text-right">{{ $item->free_quantity ?? 0 }}</td>
                            <td class="border px-3 py-2 text-right">{{ number_format($item->sub_total, 2) }}</td>
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

                    <div class="flex justify-between font-semibold text-lg">
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
                        <span>
                        @if($distribute->payment_status == 1)
                                Paid
                            @elseif($distribute->payment_status == 2)
                                Unpaid
                            @else
                                Partial
                            @endif
                    </span>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-10 flex justify-between items-center text-sm text-gray-500">
                <p>
                    Generated on {{ now('Asia/Dhaka')->format('d M Y, h:i A') }}
                </p>
               <a href="{{route('depo.purchase.print',$distribute->id)}}">
                   <button
                       class="px-4 py-2 bg-teal-600 text-white rounded hover:bg-teal-700 print:hidden">
                       Print Voucher
                   </button>
               </a>
            </div>

        </div>
    </div>
@endsection
