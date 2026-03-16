<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Document</title>
    <script src="{{asset('assets/backend_assets/js/tailwind/tailwind.js')}}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-MPLF0+3A...snipped_for_length..." crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>

<div class="min-h-screen bg-gray-100 p-6">

    <!-- Back Button -->
    <div class="mb-4 print:hidden flex justify-end">
        <a href="{{ route('admin.supplier-due-payment.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm rounded hover:bg-gray-700">
            ← Back to Payment List
        </a>
    </div>

    <div class="max-w-5xl mx-auto bg-white shadow rounded-lg p-6">

        <!-- Header -->
        <div class="flex justify-between items-center border-b pb-4 mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Supplier Payment Voucher</h2>

                <p class="text-sm text-gray-500">
                    Voucher No:
                    <span class="font-medium">
                            {{ $payment->payment_voucher ?? 'N/A' }}
                        </span>
                </p>

                <p class="text-sm text-gray-500">
                    Payment Date:
                    {{
                        $payment->payment_date
                            ? \Carbon\Carbon::parse($payment->payment_date)->format('d M Y')
                            : optional($payment->created_at)->format('d M Y') ?? 'N/A'
                    }}
                </p>
            </div>

            <div class="text-right">
                <h3 class="text-lg font-semibold text-gray-700">Supplier</h3>
                <p>{{ $payment->supplier_name ?? 'N/A' }}</p>
                <p>{{ $payment->phone ?? 'N/A' }}</p>
                <p>{{ $payment->supplier->email ?? 'N/A' }}</p>
                <p>{{ $payment->supplier->voucher_address ?? 'N/A' }}</p>
            </div>
        </div>

        <!-- Account Info -->
        <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
            <div>
                <h4 class="font-semibold text-gray-700 mb-1">Payment Account</h4>
                <p>{{ $payment->account->account_name ?? 'N/A' }}</p>
                <p>Account No: {{ $payment->account->account_no ?? 'N/A' }}</p>
            </div>
        </div>

        <!-- Payment Summary -->
        <div class="overflow-x-auto">
            <table class="w-full border border-gray-200 text-sm">
                <thead class="bg-gray-100">
                <tr>
                    <th class="border px-3 py-2 text-left">Description</th>
                    <th class="border px-3 py-2 text-right">Amount (৳)</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="border px-3 py-2">Payable Balance</td>
                    <td class="border px-3 py-2 text-right">{{ number_format($payment->balance, 2) }}</td>
                </tr>
                <tr>
                    <td class="border px-3 py-2">Paid Amount</td>
                    <td class="border px-3 py-2 text-right">{{ number_format($payment->paying_amount, 2) }}</td>
                </tr>
                <tr>
                    <td class="border px-3 py-2 font-semibold">Current Due</td>
                    <td class="border px-3 py-2 text-right font-semibold">
                        {{ number_format($payment->balance - $payment->paying_amount, 2) }}
                    </td>
                </tr>
                <tr>
                    <td class="border px-3 py-2 font-semibold">Payment Status</td>
                    <td class="border px-3 py-2 text-right font-semibold">
                        @switch($payment->payment_status)
                            @case(1)
                                <span class="inline-block px-4 py-1 text-xs font-semibold text-green-800 bg-green-200 rounded-full">Paid</span>
                                @break
                            @case(2)
                                <span class="inline-block px-4 py-1 text-xs font-semibold text-yellow-800 bg-yellow-200 rounded-full">Partial</span>
                                @break
                            @default
                                <span class="inline-block px-4 py-1 text-xs font-semibold text-gray-800 bg-gray-200 rounded-full">Unpaid</span>
                        @endswitch
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="mt-10 flex justify-between items-center text-sm text-gray-500">
            <p>
                Generated on {{ now('Asia/Dhaka')->format('d M Y, h:i A') }}
            </p>

            <button onclick="window.print()"
                    class="px-4 py-2 bg-teal-600 text-white rounded hover:bg-teal-700 print:hidden">
                Print Voucher
            </button>
        </div>

    </div>
</div>

</body>
</html>
