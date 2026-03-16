@extends('admin.master')

@section('content')
    <div class="page-wrapper bg-white p-6 rounded shadow">
        <div class="content container mx-auto px-4">

            <!-- Header -->
            <div class="flex flex-col md:flex-row items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-800">Supplier Details</h3>
                <a href="{{ route('supplier.index') }}"
                   class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    <i class="fa fa-reply"></i> Back to List
                </a>
            </div>

            <!-- Top Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- Left: Basic Info -->
                <div class="md:col-span-2 border rounded-lg p-5">
                    <h4 class="text-lg font-semibold text-gray-700 mb-4">Basic Information</h4>

                    <table class="w-full text-sm">
                        <tr class="border-b">
                            <td class="py-2 font-medium text-gray-600">Supplier Name</td>
                            <td class="py-2">{{ $supplier->supplier_name ?? 'N/A' }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-2 font-medium text-gray-600">Supplier Code</td>
                            <td class="py-2">{{ $supplier->supplier_code ?? 'N/A' }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-2 font-medium text-gray-600">Phone</td>
                            <td class="py-2">{{ $supplier->phone ?? 'N/A' }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-2 font-medium text-gray-600">Email</td>
                            <td class="py-2">{{ $supplier->email ?? 'N/A' }}</td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-2 font-medium text-gray-600">Address</td>
                            <td class="py-2">{{ $supplier->address ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 font-medium text-gray-600">Voucher Address</td>
                            <td class="py-2">{{ $supplier->voucher_address ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>

                <!-- Right: NID Image Section -->
                <div class="border rounded-lg p-5 flex flex-col items-center justify-center">
                    <h4 class="text-lg font-semibold text-gray-700 mb-4">NID Document</h4>

                    @if($supplier->nid)
                        <a href="{{ asset('image/suppliers/nid/' . $supplier->nid) }}" target="_blank">
                            <img src="{{ asset('image/suppliers/nid/' . $supplier->nid) }}"
                                 alt="NID"
                                 class="w-full max-h-80 object-contain rounded-xl border shadow hover:scale-105 transition cursor-pointer">
                        </a>
                    @else
                        <span class="text-gray-400">N/A</span>
                    @endif
                </div>
            </div>

            <!-- Bottom Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">

                <!-- Bank Info -->
                <div class="border rounded-lg p-5">
                    <h4 class="text-lg font-semibold text-gray-700 mb-3">Bank Information</h4>
                    <pre class="text-sm text-gray-700 whitespace-pre-wrap">
{{ $supplier->bank ?? 'N/A' }}
                </pre>
                </div>

                <!-- Identity Info -->
                <div class="border rounded-lg p-5">
                    <h4 class="text-lg font-semibold text-gray-700 mb-3">Financial Information</h4>

                    @php
                        $balance = (float) ($supplier->balance ?? 0);
                    @endphp

                    <table class="w-full text-sm">

                        <!-- Balance Summary -->
                        <tr class="border-b">
                            <td class="py-2 font-medium text-gray-600">
                                Balance Summary
                            </td>
                            <td class="py-2">
                                @if($balance < 0)
                                    <span class="px-3 py-1 text-sm font-semibold text-green-700 bg-green-100 rounded">
                                    Receivable
                                </span>
                                @else
                                    <span class="px-3 py-1 text-sm font-semibold text-red-700 bg-red-100 rounded">
                                    Payable
                                </span>
                                @endif

                                <div class="mt-2 font-bold text-gray-800">
                                    {{ number_format(abs($balance), 2) }}
                                </div>
                            </td>
                        </tr>

                        <!-- Opening Balance -->
                        <tr class="border-b">
                            <td class="py-2 font-medium text-gray-600">
                                Opening Balance
                            </td>
                            <td class="py-2">
                                {{ number_format((float) ($supplier->opening_balance ?? 0), 2) }}
                            </td>
                        </tr>

                        <!-- Created At -->
                        <tr>
                            <td class="py-2 font-medium text-gray-600">
                                Created At
                            </td>
                            <td class="py-2">
                                {{ $supplier->created_at?->format('d M Y, h:i A') ?? 'N/A' }}
                            </td>
                        </tr>

                    </table>
                </div>

            </div>

        </div>
    </div>
@endsection
