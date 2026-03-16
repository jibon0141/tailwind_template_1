@extends('employee.mpo.master')

@section('content')
    @php
        $activeUser = session('userObj') ?? Auth::user();
    @endphp
    <div class="2xl:max-w-screen-2xl max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-1">


        <!-- Page Title / User Info Card -->
        <div class="mb-6 md:hidden">
            <div class="bg-blue-300 rounded-2xl shadow-md overflow-hidden flex items-center p-4 md:p-6 transition-transform duration-200
        hover:shadow-xl hover:scale-[1.02]">

                <!-- User Icon -->
                <div class="flex-shrink-0 mr-1">
                    <span class="text-8xl md:text-8xl leading-none">👨‍💼</span>
                </div>

                <!-- User Info -->
                <div>
                    <p class="text-lg md:text-3xl font-bold text-gray-800">{{$activeUser->name ?? 'N/A'}}</p>
                    <p class="text-sm md:text-lg text-gray-700 mt-1">
                        Employee Id: {{ $activeUser->employee->employee_code ?? 'N/A' }}
                    </p>
                    <p class="text-sm md:text-lg text-gray-700 mt-1">Designation: MPO</p>
                    <p class="text-sm md:text-lg text-gray-700 mt-1">
                        Joining Date: {{ $activeUser->created_at
    ? \Carbon\Carbon::parse($activeUser->created_at)->format('d-F-Y')
    : 'N/A' }}
                    </p>
                </div>

            </div>
        </div>


        <!-- Quick Action Icons -->
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-4 block md:hidden">

            <!-- Sale -->
            <a href="{{ route('mpo.sale.create') }}"
               class="flex flex-col items-center justify-center bg-gradient-to-r from-teal-600 to-teal-500
                  hover:from-teal-500 hover:to-teal-400 transition p-4 rounded-2xl shadow text-white">
                <span class="text-3xl sm:text-4xl">🛒</span>
                <span class="mt-2 text-sm sm:text-base font-semibold">Sale</span>
            </a>

            <!-- Stock -->
            <a href="{{ route('mpo.stock.index') }}"
               class="flex flex-col items-center justify-center bg-gradient-to-r from-indigo-600 to-indigo-500
      hover:from-indigo-500 hover:to-indigo-400 transition p-4 rounded-2xl shadow text-white">
                <span class="text-3xl sm:text-4xl">📦</span>
                <span class="mt-2 text-sm sm:text-base font-semibold">Stock</span>
            </a>


            <!-- Chemist Shop -->
            <a href="{{ route('mpo.chemist-house.index') }}"
               class="flex flex-col items-center justify-center bg-gradient-to-r from-blue-600 to-blue-500
                  hover:from-blue-500 hover:to-blue-400 transition p-4 rounded-2xl shadow text-white">
                <span class="text-3xl sm:text-4xl">🏪</span>
                <span class="mt-2 text-sm sm:text-base font-semibold">Shop</span>
            </a>

            <!-- Due Collection -->
            <a href="{{ route('mpo.chemist-house-due-payment.index') }}"
               class="flex flex-col items-center justify-center bg-gradient-to-r from-yellow-500 to-yellow-400
                  hover:from-yellow-400 hover:to-yellow-300 transition p-4 rounded-2xl shadow text-white">
                <span class="text-3xl sm:text-4xl">💳</span>
                <span class="mt-2 text-sm sm:text-base font-semibold">Collection</span>
            </a>


            <!-- Route Plan -->
            <a href="#"
               class="flex flex-col items-center justify-center bg-gradient-to-r from-pink-500 to-pink-400
          hover:from-pink-400 hover:to-pink-300 transition p-4 rounded-2xl shadow text-white">
                <span class="text-3xl sm:text-4xl">🗺️</span>
                <span class="mt-2 text-sm sm:text-base font-semibold">Route Plan</span>
            </a>

            <!-- Doctor Call -->
            <a href="#"
               class="flex flex-col items-center justify-center bg-gradient-to-r from-purple-500 to-purple-400
          hover:from-purple-400 hover:to-purple-300 transition p-4 rounded-2xl shadow text-white">
                <span class="text-3xl sm:text-4xl">👨‍⚕️</span>
                <span class="mt-2 text-sm sm:text-base font-semibold">Doctor Call</span>
            </a>

            <!-- Report -->
            <a href="#"
               class="flex flex-col items-center justify-center bg-gradient-to-r from-green-500 to-green-400
          hover:from-green-400 hover:to-green-300 transition p-4 rounded-2xl shadow text-white">
                <span class="text-3xl sm:text-4xl">📄</span>
                <span class="mt-2 text-sm sm:text-base font-semibold">Report</span>
            </a>

            <!-- Location -->
            <a href="#"
               class="flex flex-col items-center justify-center bg-gradient-to-r from-yellow-500 to-yellow-400
          hover:from-yellow-400 hover:to-yellow-300 transition p-4 rounded-2xl shadow text-white">
                <span class="text-3xl sm:text-4xl">📍</span>
                <span class="mt-2 text-sm sm:text-base font-semibold">Location</span>
            </a>

            <!-- Notice -->
            <a href="#"
               class="flex flex-col items-center justify-center bg-gradient-to-r from-orange-500 to-orange-400
          hover:from-orange-400 hover:to-orange-300 transition p-4 rounded-2xl shadow text-white">
                <span class="text-3xl sm:text-4xl">📢</span>
                <span class="mt-2 text-sm sm:text-base font-semibold">Notice</span>
            </a>

            <!-- Dummy / Extra -->
            <a href="#"
               class="flex flex-col items-center justify-center bg-gradient-to-r from-gray-600 to-gray-500
      hover:from-gray-500 hover:to-gray-400 transition p-4 rounded-2xl shadow text-white">
                <span class="text-3xl sm:text-4xl">🗄️</span>
                <span class="mt-2 text-sm sm:text-base font-semibold">Master Data</span>
            </a>

        </div>

        <!-- Cards Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 md:gap-4 sm:gap-3">

            <!-- Card 1: Total Chemist House -->
            <div class="relative bg-white rounded-sm md:rounded-2xl shadow-sm overflow-hidden
                transition-transform duration-200
                md:hover:shadow-xl md:hover:scale-105
                border-t-4 border-transparent
                before:content-[''] before:absolute before:top-0 before:left-0 before:w-full before:h-1
                before:rounded-t-2xl before:opacity-0 md:hover:before:opacity-100
                before:bg-gradient-to-r before:from-blue-400 before:to-blue-600">

                <div class="p-2 sm:p-3 md:p-6">
                    <p class="text-[10px] sm:text-xs text-gray-500">Total Chemist House</p>
                    <h2 class="text-xl sm:text-2xl md:text-4xl font-bold text-gray-800 mt-1">{{$totalChemistHouse}}</h2>
                    <p class="text-[9px] sm:text-[10px] text-gray-400 mt-0.5">Updated Till Now</p>
                </div>

                <div class="bg-blue-600 px-2 sm:px-3 py-1.5 flex justify-between items-center">
                    <span class="text-[10px] sm:text-[11px] text-white">Under This MPO</span>
                    <span class="text-lg sm:text-xl text-white">👥</span>
                </div>
            </div>

            <!-- Card 2: Total Sale -->
            <div class="relative bg-white rounded-sm md:rounded-2xl shadow-sm overflow-hidden
                transition-transform duration-200
                md:hover:shadow-xl md:hover:scale-105
                border-t-4 border-transparent
                before:content-[''] before:absolute before:top-0 before:left-0 before:w-full before:h-1
                before:rounded-t-2xl before:opacity-0 md:hover:before:opacity-100
                before:bg-gradient-to-r before:from-green-400 before:to-green-600">

                <div class="p-2 sm:p-3 md:p-6">
                    <p class="text-[10px] sm:text-xs text-gray-500">Total Sale</p>
                    <h2 class="text-xl sm:text-2xl md:text-4xl font-bold text-gray-800 mt-1">৳ {{$totalSales}}</h2>
                    <p class="text-[9px] sm:text-[10px] text-gray-400 mt-0.5">Updated Till Now</p>
                </div>

                <div class="bg-green-600 px-2 sm:px-3 py-1.5 flex justify-between items-center">
                    <span class="text-[10px] sm:text-[11px] text-white">Under This MPO</span>
                    <span class="text-lg sm:text-xl text-white">✅</span>
                </div>
            </div>

            <!-- Card 3: Total Sale Quantity -->
            <div class="relative bg-white rounded-sm md:rounded-2xl shadow-sm overflow-hidden
                transition-transform duration-200
                md:hover:shadow-xl md:hover:scale-105
                border-t-4 border-transparent
                before:content-[''] before:absolute before:top-0 before:left-0 before:w-full before:h-1
                before:rounded-t-2xl before:opacity-0 md:hover:before:opacity-100
                before:bg-gradient-to-r before:from-yellow-400 before:to-yellow-600">

                <div class="p-2 sm:p-3 md:p-6">
                    <p class="text-[10px] sm:text-xs text-gray-500">Total Sale Quantity</p>
                    <h2 class="text-xl sm:text-2xl md:text-4xl font-bold text-gray-800 mt-1">{{$totalSaleQuantity}}</h2>
                    <p class="text-[9px] sm:text-[10px] text-gray-400 mt-0.5">Updated Till Now</p>
                </div>

                <div class="bg-yellow-500 px-2 sm:px-3 py-1.5 flex justify-between items-center">
                    <span class="text-[10px] sm:text-[11px] text-white">Under This MPO</span>
                    <span class="text-lg sm:text-xl text-white">⏳</span>
                </div>
            </div>

            <!-- Card 4: Total Monthly Sale -->
            <div class="relative bg-white rounded-sm md:rounded-2xl shadow-sm overflow-hidden
                transition-transform duration-200
                md:hover:shadow-xl md:hover:scale-105
                border-t-4 border-transparent
                before:content-[''] before:absolute before:top-0 before:left-0 before:w-full before:h-1
                before:rounded-t-2xl before:opacity-0 md:hover:before:opacity-100
                before:bg-gradient-to-r before:from-purple-400 before:to-purple-600">

                <div class="p-2 sm:p-3 md:p-6">
                    <p class="text-[10px] sm:text-xs text-gray-500">Total Monthly Sale</p>
                    <h2 class="text-xl sm:text-2xl md:text-4xl font-bold text-gray-800 mt-1">
                        ৳ {{$totalMonthlySale ?? 0}}</h2>
                    <p class="text-[9px] sm:text-[10px] text-gray-400 mt-0.5">Updated Till Now</p>
                </div>

                <div class="bg-purple-600 px-2 sm:px-3 py-1.5 flex justify-between items-center">
                    <span class="text-[10px] sm:text-[11px] text-white">Under This MPO</span>
                    <span class="text-lg sm:text-xl text-white">💰</span>
                </div>
            </div>

        </div>
    </div>
@endsection
