@extends('admin.master')

@section('content')
    <!-- Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 auto-rows-fr">
        @if(!(Auth::user()->user_type == 'admin' && Auth::user()->role == 'purchase_admin'))
        @php
            $cardClasses = "relative rounded-3xl shadow-md overflow-hidden hover:shadow-xl hover:scale-105 transition transform border-t-4 border-transparent before:content-[''] before:absolute before:top-0 before:left-0 before:w-full before:h-1.5 before:rounded-t-3xl before:opacity-0 hover:before:opacity-100";
        @endphp

            <!-- Today Purchase -->
        <div class="{{ $cardClasses }} bg-lime-50 before:bg-gradient-to-r before:from-lime-400 before:to-lime-600">
            <div class="p-4">
                <p class="text-xs text-black">Today Purchase</p>
                <h2 class="text-2xl font-bold text-black mt-1">৳ {{$todayPurchase}}</h2>
                <p class="text-[10px] text-gray-500 mt-0.5">Today</p>
            </div>
            <div class="bg-lime-600 px-4 py-2 flex justify-between items-center">
                <span class="text-white text-xs">Purchase</span>
                <span class="text-white text-lg">🧾</span>
            </div>
        </div>

        <!-- Today Distribute -->
        <div class="{{ $cardClasses }} bg-sky-50 before:bg-gradient-to-r before:from-sky-400 before:to-sky-600">
            <div class="p-4">
                <p class="text-xs text-black">Today Distribute</p>
                <h2 class="text-2xl font-bold text-black mt-1">৳ {{$todayDistribute}}</h2>
                <p class="text-[10px] text-gray-500 mt-0.5">Today</p>
            </div>
            <div class="bg-sky-600 px-4 py-2 flex justify-between items-center">
                <span class="text-white text-xs">Distribute</span>
                <span class="text-white text-lg">📤</span>
            </div>
        </div>

        <!-- Total Purchase -->
        <div class="{{ $cardClasses }} bg-indigo-50 before:bg-gradient-to-r before:from-indigo-400 before:to-indigo-600">
            <div class="p-4">
                <p class="text-xs text-black">Total Purchase</p>
                <h2 class="text-2xl font-bold text-black mt-1">৳ {{$totalPurchases}}</h2>
                <p class="text-[10px] text-gray-500 mt-0.5">Update Till Now</p>
            </div>
            <div class="bg-indigo-600 px-4 py-2 flex justify-between items-center">
                <span class="text-white text-xs">Purchase</span>
                <span class="text-white text-lg">💰</span>
            </div>
        </div>

        <!-- Total Distribute Value -->
        <div class="{{ $cardClasses }} bg-orange-50 before:bg-gradient-to-r before:from-orange-400 before:to-orange-600">
            <div class="p-4">
                <p class="text-xs text-black">Total Distribute Value</p>
                <h2 class="text-2xl font-bold text-black mt-1">৳ {{$totalDistributeValue}}</h2>
                <p class="text-[10px] text-gray-500 mt-0.5">Update Till Now</p>
            </div>
            <div class="bg-orange-600 px-4 py-2 flex justify-between items-center">
                <span class="text-white text-xs">Distribute Stock</span>
                <span class="text-white text-lg">🚚</span>
            </div>
        </div>

        <!-- Total Sale -->
        <div class="{{ $cardClasses }} bg-pink-50 before:bg-gradient-to-r before:from-pink-400 before:to-pink-600">
            <div class="p-4">
                <p class="text-xs text-black">Total Sale</p>
                <h2 class="text-2xl font-bold text-black mt-1">৳ {{$totalSale}}</h2>
                <p class="text-[10px] text-gray-500 mt-0.5">Update Till Now</p>
            </div>
            <div class="bg-pink-600 px-4 py-2 flex justify-between items-center">
                <span class="text-white text-xs">Sale</span>
                <span class="text-white text-lg">📈</span>
            </div>
        </div>

        <!-- Total Purchase Stock -->
        <div class="{{ $cardClasses }} bg-teal-50 before:bg-gradient-to-r before:from-teal-400 before:to-teal-600">
            <div class="p-4">
                <p class="text-xs text-black">Total Purchase Value Stock</p>
                <h2 class="text-2xl font-bold text-black mt-1">৳ {{$totalPurchaseStockValue}}</h2>
                <p class="text-[10px] text-gray-500 mt-0.5">Update Till Now</p>
            </div>
            <div class="bg-teal-600 px-4 py-2 flex justify-between items-center">
                <span class="text-white text-xs">Purchase Stock</span>
                <span class="text-white text-lg">🏢</span>
            </div>
        </div>

        <!-- Total Distribute Value Stock -->
        <div class="{{ $cardClasses }} bg-orange-50 before:bg-gradient-to-r before:from-orange-400 before:to-orange-600">
            <div class="p-4">
                <p class="text-xs text-black">Total Distribute Value Stock</p>
                <h2 class="text-2xl font-bold text-black mt-1">৳ {{$totalDistributeStockValue}}</h2>
                <p class="text-[10px] text-gray-500 mt-0.5">Update Till Now</p>
            </div>
            <div class="bg-orange-600 px-4 py-2 flex justify-between items-center">
                <span class="text-white text-xs">Distribute Stock</span>
                <span class="text-white text-lg">🚚</span>
            </div>
        </div>

        <!-- Total Purchase In Stock Value -->
        <div class="{{ $cardClasses }} bg-emerald-50 before:bg-gradient-to-r before:from-emerald-400 before:to-emerald-600">
            <div class="p-4">
                <p class="text-xs text-black">Total Purchase In Stock Value</p>
                <h2 class="text-2xl font-bold text-black mt-1">
                    ৳ {{ $totalPurchaseInStockValue }}
                </h2>
                <p class="text-[10px] text-gray-500 mt-0.5">Update Till Now</p>
            </div>
            <div class="bg-emerald-600 px-4 py-2 flex justify-between items-center">
                <span class="text-white text-xs">Stock Value</span>
                <span class="text-white text-lg">📦</span>
            </div>
        </div>

        <!-- Total Sale In Stock Value -->
        <div class="{{ $cardClasses }} bg-violet-50 before:bg-gradient-to-r before:from-violet-400 before:to-violet-600">
            <div class="p-4">
                <p class="text-xs text-black">Total Sale In Stock Value</p>
                <h2 class="text-2xl font-bold text-black mt-1">
                    ৳ {{ $totalSaleInStockValue }}
                </h2>
                <p class="text-[10px] text-gray-500 mt-0.5">Update Till Now</p>
            </div>
            <div class="bg-violet-600 px-4 py-2 flex justify-between items-center">
                <span class="text-white text-xs">Sale Stock</span>
                <span class="text-white text-lg">📊</span>
            </div>
        </div>

        <!-- Payable Due -->
        <div class="{{ $cardClasses }} bg-red-100 before:bg-gradient-to-r before:from-red-400 before:to-red-600">
            <div class="p-4">
                <p class="text-xs text-black">Payable Due</p>
                <h2 class="text-2xl font-bold text-black mt-1">
                    ৳ {{ number_format($payableDue, 2) }}
                </h2>
                <p class="text-[10px] text-gray-500 mt-0.5">Current Outstanding</p>
            </div>
            <div class="bg-red-600 px-4 py-2 flex justify-between items-center">
                <span class="text-white text-xs">Payable</span>
                <span class="text-white text-lg">📉</span>
            </div>
        </div>

        <!-- Receivable Due -->
        <div class="{{ $cardClasses }} bg-green-100 before:bg-gradient-to-r before:from-green-400 before:to-green-600">
            <div class="p-4">
                <p class="text-xs text-black">Receivable Due</p>
                <h2 class="text-2xl font-bold text-black mt-1">
                    ৳ {{ number_format($receivableDue, 2) }}
                </h2>
                <p class="text-[10px] text-gray-500 mt-0.5">Current Outstanding</p>
            </div>
            <div class="bg-green-600 px-4 py-2 flex justify-between items-center">
                <span class="text-white text-xs">Receivable</span>
                <span class="text-white text-lg">📈</span>
            </div>
        </div>


        <!-- Total Expense -->
        <div class="{{ $cardClasses }} bg-red-50 before:bg-gradient-to-r before:from-red-400 before:to-red-600">
            <div class="p-4">
                <p class="text-xs text-black">Total Expense</p>
                <h2 class="text-2xl font-bold text-black mt-1">৳ {{ number_format($totalExpense, 2) }}</h2>
                <p class="text-[10px] text-gray-500 mt-0.5">Update Till Now</p>
            </div>
            <div class="bg-red-600 px-4 py-2 flex justify-between items-center">
                <span class="text-white text-xs">Expense</span>
                <span class="text-white text-lg">💸</span>
            </div>
        </div>

        <!-- Today Expense -->
        <div class="{{ $cardClasses }} bg-rose-50 before:bg-gradient-to-r before:from-rose-400 before:to-rose-600">
            <div class="p-4">
                <p class="text-xs text-black">Today Expense</p>
                <h2 class="text-2xl font-bold text-black mt-1">
                    ৳ {{ number_format($todayExpense, 2) }}
                </h2>
                <p class="text-[10px] text-gray-500 mt-0.5">Update Till Now</p>
            </div>
            <div class="bg-rose-600 px-4 py-2 flex justify-between items-center">
                <span class="text-white text-xs">Expense</span>
                <span class="text-white text-lg">💸</span>
            </div>
        </div>


        <!-- Total Employees -->
        <div class="{{ $cardClasses }} bg-blue-50 before:bg-gradient-to-r before:from-blue-400 before:to-blue-600">
            <div class="p-4">
                <p class="text-xs text-black">Total Employees</p>
                <h2 class="text-2xl font-bold text-black mt-1">{{$employees}}</h2>
                <p class="text-[10px] text-gray-500 mt-0.5">Update Till Now</p>
            </div>
            <div class="bg-blue-600 px-4 py-2 flex justify-between items-center">
                <span class="text-white text-xs">Employees</span>
                <span class="text-white text-lg">👥</span>
            </div>
        </div>

        <!-- Total Depo -->
        <div class="{{ $cardClasses }} bg-green-50 before:bg-gradient-to-r before:from-green-400 before:to-green-600">
            <div class="p-4">
                <p class="text-xs text-black">Total Depo</p>
                <h2 class="text-2xl font-bold text-black mt-1">{{$depos}}</h2>
                <p class="text-[10px] text-gray-500 mt-0.5">Update Till Now</p>
            </div>
            <div class="bg-green-600 px-4 py-2 flex justify-between items-center">
                <span class="text-white text-xs">Depo</span>
                <span class="text-white text-lg">🏬</span>
            </div>
        </div>

        <!-- Total Supplier -->
        <div class="{{ $cardClasses }} bg-yellow-50 before:bg-gradient-to-r before:from-yellow-400 before:to-yellow-600">
            <div class="p-4">
                <p class="text-xs text-black">Total Supplier</p>
                <h2 class="text-2xl font-bold text-black mt-1">{{$suppliers}}</h2>
                <p class="text-[10px] text-gray-500 mt-0.5">Update Till Now</p>
            </div>
            <div class="bg-yellow-600 px-4 py-2 flex justify-between items-center">
                <span class="text-white text-xs">Suppliers</span>
                <span class="text-white text-lg">📦</span>
            </div>
        </div>

        <!-- Total NSM -->
        <div class="{{ $cardClasses }} bg-red-50 before:bg-gradient-to-r before:from-red-400 before:to-red-600">
            <div class="p-4">
                <p class="text-xs text-black">Total NSM</p>
                <h2 class="text-2xl font-bold text-black mt-1">{{$nsms}}</h2>
                <p class="text-[10px] text-gray-500 mt-0.5">Update Till Now</p>
            </div>
            <div class="bg-red-600 px-4 py-2 flex justify-between items-center">
                <span class="text-white text-xs">NSM</span>
                <span class="text-white text-lg">🆕</span>
            </div>
        </div>

        <!-- Total RSM -->
        <div class="{{ $cardClasses }} bg-purple-50 before:bg-gradient-to-r before:from-purple-400 before:to-purple-600">
            <div class="p-4">
                <p class="text-xs text-black">Total RSM</p>
                <h2 class="text-2xl font-bold text-black mt-1">{{$rsms}}</h2>
                <p class="text-[10px] text-gray-500 mt-0.5">Update Till Now</p>
            </div>
            <div class="bg-purple-600 px-4 py-2 flex justify-between items-center">
                <span class="text-white text-xs">RSM</span>
                <span class="text-white text-lg">🎯</span>
            </div>
        </div>

        <!-- Total SM -->
        <div class="{{ $cardClasses }} bg-pink-100 before:bg-gradient-to-r before:from-pink-400 before:to-pink-600">
            <div class="p-4">
                <p class="text-xs text-black">Total SM</p>
                <h2 class="text-2xl font-bold text-black mt-1">{{$sms}}</h2>
                <p class="text-[10px] text-gray-500 mt-0.5">Update Till Now</p>
            </div>
            <div class="bg-pink-600 px-4 py-2 flex justify-between items-center">
                <span class="text-white text-xs">SM</span>
                <span class="text-white text-lg">💡</span>
            </div>
        </div>

        <!-- Total ASM -->
        <div class="{{ $cardClasses }} bg-cyan-50 before:bg-gradient-to-r before:from-cyan-400 before:to-cyan-600">
            <div class="p-4">
                <p class="text-xs text-black">Total ASM</p>
                <h2 class="text-2xl font-bold text-black mt-1">{{$asms}}</h2>
                <p class="text-[10px] text-gray-500 mt-0.5">Update Till Now</p>
            </div>
            <div class="bg-cyan-600 px-4 py-2 flex justify-between items-center">
                <span class="text-white text-xs">ASM</span>
                <span class="text-white text-lg">🌀</span>
            </div>
        </div>

        <!-- Total MPO -->
        <div class="{{ $cardClasses }} bg-orange-100 before:bg-gradient-to-r before:from-orange-400 before:to-orange-600">
            <div class="p-4">
                <p class="text-xs text-black">Total MPO</p>
                <h2 class="text-2xl font-bold text-black mt-1">{{$mpos}}</h2>
                <p class="text-[10px] text-gray-500 mt-0.5">Update Till Now</p>
            </div>
            <div class="bg-orange-600 px-4 py-2 flex justify-between items-center">
                <span class="text-white text-xs">MPO</span>
                <span class="text-white text-lg">🔶</span>
            </div>
        </div>
            @endif
    </div>
@endsection
