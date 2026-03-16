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

<div class="min-h-screen bg-gray-100 p-2 md:p-6">

    <!-- Back Button -->
    <div class="mb-4 flex justify-end print:hidden">
        <a href="{{ route('admin.requisition.index') }}"
           class="px-4 py-2 bg-gray-600 text-white text-sm rounded hover:bg-gray-700">
            ← Back to Requisition List
        </a>
    </div>

    @include('admin.include.message')

    <!-- Invoice -->
    <div id="invoice" class="max-w-5xl mx-auto bg-white shadow rounded-lg p-4 md:p-6">

        <!-- Header -->
        <div  class="border-b pb-4 mb-6 text-center w-[70%] mx-auto">
            <h1 class="text-2xl font-bold text-gray-800">
                {{ $mainCompany->company_name }}
            </h1>

            <p class="text-sm text-gray-600">{{ $mainCompany->address }}</p>
            <p class="text-sm text-gray-600">
                Phone: {{ $mainCompany->phone }}
                @if($mainCompany->email) | Email: {{ $mainCompany->email }} @endif
            </p>
            <p class="text-sm text-gray-600">{{ $mainCompany->website_url }}</p>

            <p class="text-3xl font-semibold mt-2">Requisition Invoice</p>
        </div>

        <!-- Requisition Info -->
        <div class="flex justify-between text-sm mb-6">
            <div>
                <p><strong>Requisition No:</strong> {{ $requisition->requisition_voucher }}</p>
                <p><strong>Company:</strong> {{ $requisition->company_name }}</p>
            </div>
            <div class="text-right">
                <p>
                    <strong>Date:</strong>
                    {{ \Carbon\Carbon::parse($requisition->requisition_date)->format('d M Y') }}
                </p>
            </div>
        </div>

        <!-- Items Table -->
        <div class="overflow-x-auto">
            <table class="w-full border border-gray-300 text-sm">
                <thead class="bg-gray-200">
                <tr>
                    <th class="border px-2 py-1">#</th>
                    <th class="border px-2 py-1 text-left">Medicine</th>
                    <th class="border px-2 py-1 text-right">Mrp</th>
                    <th class="border px-2 py-1 text-right">Discount %</th>
                    <th class="border px-2 py-1 text-right">Purchase Price</th>
                    <th class="border px-2 py-1 text-center">Qty</th>
                    <th class="border px-2 py-1 text-right">Sub Total</th>
                </tr>
                </thead>
                <tbody>
                @forelse($requisition->requisitionItems as $item)
                    <tr>
                        <td class="border px-2 py-1">{{ $loop->iteration }}</td>
                        <td class="border px-2 py-1">{{ $item->medicine_name }}</td>
                        <td class="border px-2 py-1 text-right">
                            {{ number_format($item->mrp, 2) }}
                        </td>
                        <td class="border px-2 py-1 text-right">
                            {{ number_format($item->discount, 2) }}
                        </td>
                        <td class="border px-2 py-1 text-right">
                            {{ number_format($item->purchase_price, 2) }}
                        </td>
                        <td class="border px-2 py-1 text-center">{{ $item->quantity }}</td>
                        <td class="border px-2 py-1 text-right">
                            {{ number_format($item->sub_total, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="border px-2 py-3 text-center text-gray-500">
                            No items found
                        </td>
                    </tr>
                @endforelse
                </tbody>

                <tfoot>
                <tr class="bg-gray-100 font-semibold">
                    <td colspan="6" class="border px-2 py-2 text-right">Total</td>
                    <td class="border px-2 py-2 text-right">
                        {{ number_format($requisition->final_total, 2) }}
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>

        <!-- Print Button -->
        <div class="mt-6 flex justify-end print:hidden">
            <button onclick="window.print()"
                    class="px-4 py-2 bg-teal-600 text-white rounded hover:bg-teal-700">
                🖨 Print Preview
            </button>
        </div>

    </div>
</div>

</body>
</html>
