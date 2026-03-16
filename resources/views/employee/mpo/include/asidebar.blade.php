<div class="flex flex-col">

    <!-- User Profile Header -->

    @php
        $activeUser = session('userObj') ?? Auth::user();
    @endphp
    <div class="flex items-center gap-3 bg-gradient-to-r from-teal-700 to-teal-600 shadow-xl
            h-16 p-2 md:h-16 md:p-4 pl-4 md:pl-6">

        <!-- Avatar: hidden on mobile, shown on md+ -->
        <img src="{{ asset('assets/backend_assets/images/avatar.png') }}"
             class="h-10 w-10 hidden md:block rounded-full border-2 border-white"
             alt="User">

        <div class="flex flex-col justify-center  pl-20 md:pl-2">
            <p class="text-teal-200 text-xs md:text-sm">{{ $activeUser->name }}</p>
            <span class="text-white font-bold text-base md:text-lg truncate">MPO</span>
        </div>

    </div>


    <!-- Navigation -->
    <nav class="flex flex-col mt-4 md:mt-6 space-y-2 font-medium text-slate-300">

        <a href="{{ url('/mpo/dashboard') }}"
           class="p-2 rounded-l-xl border-l-4 border-teal-500 bg-gradient-to-r from-slate-800 to-slate-700 hover:from-teal-600 hover:to-teal-500 transition flex items-center gap-2 shadow hover:shadow-lg">
            <span class="text-xl">🏠</span> Dashboard
        </a>

        <a href="{{ route('mpo.sale.create') }}"
           class="p-2 rounded-l-xl border-l-4 border-transparent bg-slate-800 hover:bg-gradient-to-r hover:from-teal-600 hover:to-teal-500 transition flex items-center gap-2 shadow hover:shadow-lg">
            <span class="text-xl">🛒</span> Sale
        </a>

        <!-- Stock (Added below Sale) -->
        <a href="{{ route('mpo.stock.index') }}"
           class="p-2 rounded-l-xl border-l-4 border-transparent bg-slate-800 hover:bg-gradient-to-r hover:from-teal-600 hover:to-teal-500 transition flex items-center gap-2 shadow hover:shadow-lg">
            <span class="text-xl">📦</span> Stock
        </a>

        <a href="{{ route('mpo.chemist-house.index') }}"
           class="p-2 rounded-l-xl border-l-4 border-transparent bg-slate-800 hover:bg-gradient-to-r hover:from-teal-600 hover:to-teal-500 transition flex items-center gap-2 shadow hover:shadow-lg">
            <span class="text-xl">🏪</span> Chemist Shop
        </a>

        <a href="{{ route('mpo.chemist-house-due-payment.index') }}"
           class="p-2 rounded-l-xl border-l-4 border-transparent bg-slate-800 hover:bg-gradient-to-r hover:from-teal-600 hover:to-teal-500 transition flex items-center gap-2 shadow hover:shadow-lg">
            <span class="text-xl">💳</span> Due Collection
        </a>

    </nav>
</div>
