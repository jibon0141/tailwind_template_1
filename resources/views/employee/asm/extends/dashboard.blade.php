@extends('employee.mpo.master')

@section('content')
    <div class="2xl:max-w-screen-2xl max-w-7xl mx-auto px-4 sm:px-6 lg:px-2 py-3">

        <!-- Page Title -->
        <div class="mb-8">
            <h1 class="text-xl md:text-3xl font-bold text-gray-800">
                Depo Dashboard
            </h1>
            <p class="text-base text-gray-500 mt-2">
                Overview of system performance
            </p>
        </div>

        <!-- Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 auto-rows-fr">

            <!-- Card 1 -->
            <div class="relative bg-white rounded-3xl shadow-md overflow-hidden
                    hover:shadow-xl hover:scale-105 transition transform
                    border-t-4 border-transparent
                    before:content-[''] before:absolute before:top-0 before:left-0 before:w-full before:h-1.5
                    before:rounded-t-3xl before:opacity-0 hover:before:opacity-100
                    before:bg-gradient-to-r before:from-blue-400 before:to-blue-600 auto-rows-fr">
                <div class="p-6">
                    <p class="text-sm text-gray-500">Total Chemist House</p>
                    <h2 class="text-4xl font-bold text-gray-800 mt-2">{{$totalChemistHouse}}</h2>
                    <p class="text-xs text-gray-400 mt-1">Updated Till Now</p>
                </div>
                <div class="bg-blue-600 px-6 py-3 flex justify-between items-center">
                    <span class="text-white text-sm">Under This Mpo</span>
                    <span class="text-white text-xl">👥</span>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="relative bg-white rounded-3xl shadow-md overflow-hidden
                    hover:shadow-xl hover:scale-105 transition transform
                    border-t-4 border-transparent
                    before:content-[''] before:absolute before:top-0 before:left-0 before:w-full before:h-1.5
                    before:rounded-t-3xl before:opacity-0 hover:before:opacity-100
                    before:bg-gradient-to-r before:from-green-400 before:to-green-600 auto-rows-fr">
                <div class="p-6">
                    <p class="text-sm text-gray-500">Total Sale</p>
                    <h2 class="text-4xl font-bold text-gray-800 mt-2">৳ {{$totalSales}}</h2>
                    <p class="text-xs text-gray-400 mt-1">Update Till Now</p>
                </div>
                <div class="bg-green-600 px-6 py-3 flex justify-between items-center">
                    <span class="text-white text-sm">Under This Mpo</span>
                    <span class="text-white text-xl">✅</span>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="relative bg-white rounded-3xl shadow-md overflow-hidden
                    hover:shadow-xl hover:scale-105 transition transform
                    border-t-4 border-transparent
                    before:content-[''] before:absolute before:top-0 before:left-0 before:w-full before:h-1.5
                    before:rounded-t-3xl before:opacity-0 hover:before:opacity-100
                    before:bg-gradient-to-r before:from-yellow-400 before:to-yellow-600 auto-rows-fr">
                <div class="p-6">
                    <p class="text-sm text-gray-500">Total Sale Quantity</p>
                    <h2 class="text-4xl font-bold text-gray-800 mt-2">{{$totalSaleQuantity}}</h2>
                    <p class="text-xs text-gray-400 mt-1">Update Till Now</p>
                </div>
                <div class="bg-yellow-500 px-6 py-3 flex justify-between items-center">
                    <span class="text-white text-sm">Under This Mpo</span>
                    <span class="text-white text-xl">⏳</span>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="relative bg-white rounded-3xl shadow-md overflow-hidden
                    hover:shadow-xl hover:scale-105 transition transform
                    border-t-4 border-transparent
                    before:content-[''] before:absolute before:top-0 before:left-0 before:w-full before:h-1.5
                    before:rounded-t-3xl before:opacity-0 hover:before:opacity-100
                    before:bg-gradient-to-r before:from-purple-400 before:to-purple-600 auto-rows-fr">
                <div class="p-6">
                    <p class="text-sm text-gray-500">Total Monthly Sale</p>
                    <h2 class="text-4xl font-bold text-gray-800 mt-2">৳ {{$totalMonthlySale}}</h2>
                    <p class="text-xs text-gray-400 mt-1">Update Till Now</p>
                </div>
                <div class="bg-purple-600 px-6 py-3 flex justify-between items-center">
                    <span class="text-white text-sm">Under This Mpo</span>
                    <span class="text-white text-xl">💰</span>
                </div>
            </div>

{{--            <!-- Card 5 -->--}}
{{--            <div class="relative bg-white rounded-3xl shadow-md overflow-hidden--}}
{{--                    hover:shadow-xl hover:scale-105 transition transform--}}
{{--                    border-t-4 border-transparent--}}
{{--                    before:content-[''] before:absolute before:top-0 before:left-0 before:w-full before:h-1.5--}}
{{--                    before:rounded-t-3xl before:opacity-0 hover:before:opacity-100--}}
{{--                    before:bg-gradient-to-r before:from-indigo-400 before:to-indigo-600 auto-rows-fr">--}}
{{--                <div class="p-6">--}}
{{--                    <p class="text-sm text-gray-500">Total Monthly Sale</p>--}}
{{--                    <h2 class="text-4xl font-bold text-gray-800 mt-2"></h2>--}}
{{--                    <p class="text-xs text-gray-400 mt-1">Update Till Now</p>--}}
{{--                </div>--}}
{{--                <div class="bg-indigo-600 px-6 py-3 flex justify-between items-center">--}}
{{--                    <span class="text-white text-sm">Under This Mpo</span>--}}
{{--                    <span class="text-white text-xl">🏢</span>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            <!-- Card 6 -->--}}
{{--            <div class="relative bg-white rounded-3xl shadow-md overflow-hidden--}}
{{--                    hover:shadow-xl hover:scale-105 transition transform--}}
{{--                    border-t-4 border-transparent--}}
{{--                    before:content-[''] before:absolute before:top-0 before:left-0 before:w-full before:h-1.5--}}
{{--                    before:rounded-t-3xl before:opacity-0 hover:before:opacity-100--}}
{{--                    before:bg-gradient-to-r before:from-teal-400 before:to-teal-600 auto-rows-fr">--}}
{{--                <div class="p-6">--}}
{{--                    <p class="text-sm text-gray-500">Total Debit</p>--}}
{{--                    <h2 class="text-4xl font-bold text-gray-800 mt-2"></h2>--}}
{{--                    <p class="text-xs text-gray-400 mt-1">Update Till Now</p>--}}
{{--                </div>--}}
{{--                <div class="bg-teal-600 px-6 py-3 flex justify-between items-center">--}}
{{--                    <span class="text-white text-sm">Spend</span>--}}
{{--                    <span class="text-white text-xl">🆕</span>--}}
{{--                </div>--}}
{{--            </div>--}}

            <!-- Card 7 -->
{{--            <div class="relative bg-white rounded-3xl shadow-md overflow-hidden--}}
{{--                    hover:shadow-xl hover:scale-105 transition transform--}}
{{--                    border-t-4 border-transparent--}}
{{--                    before:content-[''] before:absolute before:top-0 before:left-0 before:w-full before:h-1.5--}}
{{--                    before:rounded-t-3xl before:opacity-0 hover:before:opacity-100--}}
{{--                    before:bg-gradient-to-r before:from-red-400 before:to-red-600 auto-rows-fr">--}}
{{--                <div class="p-6">--}}
{{--                    <p class="text-sm text-gray-500">Support Tickets</p>--}}
{{--                    <h2 class="text-4xl font-bold text-gray-800 mt-2">9</h2>--}}
{{--                    <p class="text-xs text-gray-400 mt-1">Open tickets</p>--}}
{{--                </div>--}}
{{--                <div class="bg-red-600 px-6 py-3 flex justify-between items-center">--}}
{{--                    <span class="text-white text-sm">Tickets</span>--}}
{{--                    <span class="text-white text-xl">🎫</span>--}}
{{--                </div>--}}
{{--            </div>--}}

            <!-- Card 8 -->
{{--            <div class="relative bg-white rounded-3xl shadow-md overflow-hidden--}}
{{--                    hover:shadow-xl hover:scale-105 transition transform--}}
{{--                    border-t-4 border-transparent--}}
{{--                    before:content-[''] before:absolute before:top-0 before:left-0 before:w-full before:h-1.5--}}
{{--                    before:rounded-t-3xl before:opacity-0 hover:before:opacity-100--}}
{{--                    before:bg-gradient-to-r before:from-emerald-400 before:to-emerald-600 auto-rows-fr">--}}
{{--                <div class="p-6">--}}
{{--                    <p class="text-sm text-gray-500">System Health</p>--}}
{{--                    <h2 class="text-4xl font-bold text-gray-800 mt-2">Good</h2>--}}
{{--                    <p class="text-xs text-gray-400 mt-1">All services running</p>--}}
{{--                </div>--}}
{{--                <div class="bg-emerald-600 px-6 py-3 flex justify-between items-center">--}}
{{--                    <span class="text-white text-sm">Status</span>--}}
{{--                    <span class="text-white text-xl">🟢</span>--}}
{{--                </div>--}}
{{--            </div>--}}

        </div>
    </div>
@endsection

