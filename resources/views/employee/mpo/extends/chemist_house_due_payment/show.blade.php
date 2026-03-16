@extends('employee.mpo.master')

@section('content')
    <div class="min-h-screen bg-gray-100 p-3 md:p-6">

        <!-- Back Button -->
        <div class="mb-4 print:hidden flex justify-end">
            <a href="{{ route('mpo.chemist-house-due-payment.index') }}"
               class="inline-flex w-full md:w-auto justify-center items-center px-4 py-2 bg-gray-600 text-white text-sm rounded hover:bg-gray-700">
                ← Back to Payment List
            </a>
        </div>

        <!-- Payment Voucher -->
        <div class="max-w-5xl mx-auto bg-white shadow rounded-lg p-4 md:p-6">

            <!-- Header -->
            <div class="flex flex-col md:flex-row md:justify-between md:items-center border-b pb-4 mb-6 gap-4">

                <!-- Voucher Info -->
                <div>
                    <h2 class="text-xl md:text-2xl font-bold text-gray-800">Chemist House Payment Voucher</h2>
                    <p class="text-sm text-gray-500">
                        Voucher No: <span class="font-medium">{{ $payment->payment_voucher ?? 'N/A' }}</span>
                    </p>
                    <p class="text-sm text-gray-500">
                        Payment Date: {{
                        $payment->payment_date
                            ? \Carbon\Carbon::parse($payment->payment_date)->format('d M Y')
                            : optional($payment->created_at)->format('d M Y') ?? 'N/A'
                    }}
                    </p>
                </div>

                <!-- Chemist House Info -->
                <div class="text-left md:text-right">
                    <h3 class="text-lg font-semibold text-gray-700">Chemist House</h3>
                    <p>{{ $payment->chemist_house_name ?? 'N/A' }}</p>
                    <p>{{ $payment->contact ?? 'N/A' }}</p>
                    <p>{{ $payment->chemistHouse->owner_name ?? 'N/A' }}</p>
                    <p>{{ $payment->chemistHouse->address ?? 'N/A' }}</p>
                </div>
            </div>

            <!-- Payment Account -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 text-sm">
                <div>
                    <h4 class="font-semibold text-gray-700 mb-1">Payment Account</h4>
                    <p>{{ $payment->account->account_name ?? 'N/A' }}</p>
                    <p>Account No: {{ $payment->account->account_no ?? 'N/A' }}</p>
                </div>
            </div>

            <!-- Payment Table -->
            <div class="overflow-x-auto">
                <table class="w-full border border-gray-200 text-sm">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-2 py-1 text-left">Description</th>
                        <th class="border px-2 py-1 text-right">Amount (৳)</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td class="border px-2 py-1">Due Balance</td>
                        <td class="border px-2 py-1 text-right">{{ number_format($payment->balance, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="border px-2 py-1">Paid Amount</td>
                        <td class="border px-2 py-1 text-right">{{ number_format($payment->receiving_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="border px-2 py-1 font-semibold">Current Due</td>
                        <td class="border px-2 py-1 text-right font-semibold">
                            {{ number_format($payment->balance - $payment->receiving_amount, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="border px-2 py-1 font-semibold">Payment Status</td>
                        <td class="border px-2 py-1 text-right font-semibold">
                            @switch($payment->payment_status)
                                @case(1)
                                    <span class="inline-block px-3 py-1 text-xs font-semibold text-green-800 bg-green-200 rounded-full">Paid</span>
                                    @break
                                @case(2)
                                    <span class="inline-block px-3 py-1 text-xs font-semibold text-yellow-800 bg-yellow-200 rounded-full">Partial</span>
                                    @break
                                @default
                                    <span class="inline-block px-3 py-1 text-xs font-semibold text-gray-800 bg-gray-200 rounded-full">Partial</span>
                            @endswitch
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <!-- Footer -->
            <div class="mt-6 flex flex-col md:flex-row md:justify-between md:items-center gap-3 text-sm text-gray-500">
                <p class="text-left">
                    Generated on {{ now('Asia/Dhaka')->format('d M Y, h:i A') }}
                </p>
                <a href="{{ route('mpo.chemist-house-due-payment.print',$payment->id) }}">
                    <button class="w-full md:w-auto px-4 py-2 bg-teal-600 text-white rounded hover:bg-teal-700 print:hidden">
                        Print Voucher
                    </button>
                </a>
            </div>

        </div>
    </div>
@endsection
