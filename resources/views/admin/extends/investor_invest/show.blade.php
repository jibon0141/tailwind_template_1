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

        {{-- Back Button --}}
        <div class="mb-4 flex justify-end print:hidden">
            <a href="{{ route('admin.investor.invest.index') }}"
               class="px-4 py-2 bg-gray-600 text-white text-sm rounded hover:bg-gray-700">
                ← Back to Invest List
            </a>
        </div>

        @include('admin.include.message')

        {{-- Invoice --}}
        <div id="invoice" class="max-w-5xl mx-auto bg-white shadow rounded-lg p-4 md:p-6">

            {{-- Header --}}
            <div class="border-b pb-4 mb-6">

                {{-- Company Info --}}
                <div class="text-center mb-3">
                    <h1 class="text-2xl font-bold text-gray-800">
                        {{ $mainCompany->company_name ?? 'Company Name' }}
                    </h1>

                    <p class="text-sm text-gray-600">
                        {{ $mainCompany->address ?? '' }}
                    </p>

                    <p class="text-sm text-gray-600">
                        Phone: {{ $mainCompany->phone ?? '' }}
                        @if($mainCompany->email)
                            | Email: {{ $mainCompany->email }}
                        @endif
                    </p>

                    <p class="text-sm text-gray-600">
                        {{ $mainCompany->website_url ?? '' }}
                    </p>

                    <p class="text-2xl font-semibold mt-2">Receipt</p>
                </div>

                <hr class="border-t border-gray-400 my-3">

                {{-- Investor & Voucher Info --}}
                <div class="flex justify-between items-start text-sm text-gray-600">
                    <div>
                        <p><strong>Voucher No:</strong> {{ $invest->invest_voucher }}</p>
                        <p><strong>Investor Code:</strong> {{ $invest->investor_code }}</p>
                        <p>
                            <strong>Payment Date:</strong>
                            {{ \Carbon\Carbon::parse($invest->payment_date)->format('d M Y') }}
                        </p>
                        <p><strong>Account:</strong> {{ $invest->account->account_name ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <p class="font-bold"><strong>Investor:</strong> {{ $invest->investor_name }}</p>
                        <p>Phone: {{ $invest->phone }}</p>
                        <p>Email: {{ $invest->investor->email ?? 'N/A' }}</p>
                        <p>
                            Address:
                            {!! nl2br(e($invest->investor->address ?? 'N/A')) !!}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Investment Details --}}
            <div class="overflow-x-auto">
                <table class="w-full border border-gray-200 text-sm">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-3 py-2 text-left">#</th>
                        <th class="border px-3 py-2 text-left">Description</th>
                        <th class="border px-3 py-2 text-right">Amount (৳)</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td class="border px-3 py-2">1</td>
                        <td class="border px-3 py-2">Previous Total Investment</td>
                        <td class="border px-3 py-2 text-right">
                            {{ number_format($invest->invest_amount ?? 0, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="border px-3 py-2">2</td>
                        <td class="border px-3 py-2">New Investing Amount</td>
                        <td class="border px-3 py-2 text-right">
                            {{ number_format($invest->investing_amount ?? 0, 2) }}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            {{-- Summary --}}
            <div class="flex justify-end mt-6">
                <div class="w-full md:w-1/3 text-sm">

                    <div class="flex justify-between mb-2">
                        <span>Previous Total</span>
                        <span>৳ {{ number_format($invest->invest_amount ?? 0, 2) }}</span>
                    </div>

                    <div class="flex justify-between mb-2">
                        <span>New Invest</span>
                        <span>৳ {{ number_format($invest->investing_amount ?? 0, 2) }}</span>
                    </div>

                    <hr class="my-2">

                    <div class="flex justify-between font-semibold text-base">
                        <span>Current Total Investment</span>
                        <span>
                        ৳ {{ number_format(
                            ($invest->invest_amount ?? 0) + ($invest->investing_amount ?? 0),
                            2
                        ) }}
                    </span>
                    </div>

                    <div class="flex justify-between mt-2">
                        <span>Payment Status</span>
                        <span class="font-semibold
                 {{ $invest->payment_status == 1 ? 'text-green-600' : 'text-red-600' }}">
        {{ $invest->payment_status == 1 ? 'Invest' : 'Withdraw' }}
    </span>
                    </div>


                </div>
            </div>

            {{-- Footer --}}
            <div class="mt-10 flex justify-between items-center text-sm text-gray-500 print:hidden">
                <p>Generated on {{ now('Asia/Dhaka')->format('d M Y, h:i A') }}</p>

               <a href="{{route('admin.investor.invest.print',$invest->id)}}">
                   <button
                       class="px-4 py-2 bg-teal-600 text-white rounded hover:bg-teal-700">
                       Print Preview
                   </button>
               </a>
            </div>

        </div>
    </div>
@endsection
