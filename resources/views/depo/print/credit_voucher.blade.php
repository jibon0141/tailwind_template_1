<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="{{asset('assets/backend_assets/js/tailwind/tailwind.js')}}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-MPLF0+3A...snipped_for_length..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Document</title>
</head>
<body>

<div class="min-h-screen bg-gray-100 p-6">

    <!-- Back Button -->
    <div class="mb-4 print:hidden flex justify-end">
        <a href="{{ route('depo.credit-voucher.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm rounded hover:bg-gray-700">
            ← Back to Credit Vouchers
        </a>
    </div>

    <!-- Voucher Container -->
    <div id="voucher" class="max-w-5xl mx-auto bg-white shadow rounded-lg p-6">

        <!-- Header -->
        <div class="flex justify-between items-center border-b pb-4 mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Credit Voucher</h2>
                <p class="text-sm text-gray-500">
                    Voucher No:
                    <span class="font-medium">{{ $creditVoucher->credit_voucher ?? 'N/A' }}</span>
                </p>
                <p class="text-sm text-gray-500">
                    Payment Date:
                    {{ $creditVoucher->payment_date
                        ? \Carbon\Carbon::parse($creditVoucher->payment_date)->format('d M Y')
                        : 'N/A' }}
                </p>
            </div>

            <div class="text-right">
                <h3 class="text-lg font-semibold text-gray-700">Party</h3>
                <p class="text-sm">{{ $creditVoucher->party->party_name ?? 'N/A' }}</p>
                <p class="text-sm">{{ $creditVoucher->party->phone ?? 'N/A' }}</p>
                <p class="text-sm">{{ $creditVoucher->party->email ?? 'N/A' }}</p>
                <p class="text-sm">{{ $creditVoucher->party->address ?? 'N/A' }}</p>
            </div>
        </div>

        <!-- Account Info -->
        <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
            <div>
                <h4 class="font-semibold text-gray-700 mb-1">Receive Account</h4>
                <p>{{ $creditVoucher->account->account_name ?? 'N/A' }}</p>
                <p>Account No: {{ $creditVoucher->account->account_no ?? 'N/A' }}</p>
            </div>

        </div>

        <!-- Items Table -->
        <div class="overflow-x-auto">
            <table class="w-full border border-gray-200 text-sm">
                <thead class="bg-gray-100">
                <tr>
                    <th class="border px-3 py-2 text-left">#</th>
                    <th class="border px-3 py-2 text-left">Chart of Account</th>
                    <th class="border px-3 py-2 text-left">Description</th>
                    <th class="border px-3 py-2 text-right">Received Amount</th>
                </tr>
                </thead>
                <tbody>
                @foreach($creditVoucher->items as $index => $item)
                    <tr>
                        <td class="border px-3 py-2">{{ $index + 1 }}</td>
                        <td class="border px-3 py-2">{{ $item->coa->head_name ?? 'N/A' }}</td>
                        <td class="border px-3 py-2">{{ $item->description ?? 'N/A' }}</td>
                        <td class="border px-3 py-2 text-right">
                            ৳ {{ number_format($item->paid_amount ?? 0, 2) }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <!-- Summary -->
        <div class="flex justify-end mt-6">
            <div class="w-1/3 text-sm">
                <div class="flex justify-between mb-2">
                    <span>Total Received</span>
                    <span>
                            ৳ {{ number_format($creditVoucher->items->sum('paid_amount') ?? 0, 2) }}
                        </span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-10 flex justify-between items-center text-sm text-gray-500 print:hidden">
            <p>
                Generated on
                {{ \Carbon\Carbon::now('Asia/Dhaka')->format('d M Y, h:i A') }}
            </p>
            <button onclick="window.print()"
                    class="px-4 py-2 bg-teal-600 text-white rounded hover:bg-teal-700">
                Print Voucher
            </button>
        </div>

    </div>
</div>

</body>
</html>
