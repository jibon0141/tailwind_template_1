<!-- Mobile Toggle Button -->
<button id="sidebar-toggle"
        class="fixed top-4 left-4 md:hidden z-[999] p-3 rounded bg-teal-700 text-white shadow-lg hover:bg-teal-800 transition-colors">
    ☰
</button>

<!-- Sidebar -->
<aside id="sidebar"
       class="fixed top-0 left-0 h-screen w-64 bg-slate-800 text-slate-200 shadow-lg flex flex-col z-50 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">

<!-- User Profile Header -->
@php
    $activeUser = session('userObj') ?? Auth::user();
@endphp

<div class="p-6 flex items-center gap-3 bg-gradient-to-r from-teal-700 to-teal-600 rounded-b-2xl shadow-xl">
    <img src="{{ asset('assets/backend_assets/images/avatar.png') }}"
         class="h-12 w-12 rounded-full border-2 border-white" alt="User">
    <div>
        <p class="text-white font-bold text-lg truncate">{{ $activeUser->name }}</p>
        <span class="text-teal-200 text-sm">Depo Admin</span>
    </div>
</div>

<!-- Navigation -->
<nav class="flex flex-col mt-6 space-y-2 font-medium text-slate-300">

    <!-- Dashboard -->
    <a href="{{ url('/depo/dashboard') }}"
       class="p-2 rounded-l-xl border-l-4 border-teal-500 bg-gradient-to-r from-slate-800 to-slate-700 hover:from-teal-600 hover:to-teal-500 transition flex items-center gap-2 shadow hover:shadow-lg">
        <span class="text-xl">🏠</span>
        Dashboard
    </a>

    <!-- Medicine -->
    <a href="{{ route('depo.medicine.index') }}"
       class="p-2 rounded-l-xl border-l-4 border-transparent bg-slate-800 hover:bg-gradient-to-r hover:from-teal-600 hover:to-teal-500 transition flex items-center gap-2 shadow hover:shadow-lg">
        <span class="text-xl">💊</span>
        Medicine
    </a>

    <!-- Depo List -->
    <a href="{{ route('depo.list.index') }}"
       class="p-2 rounded-l-xl border-l-4 border-transparent bg-slate-800 hover:bg-gradient-to-r hover:from-teal-600 hover:to-teal-500 transition flex items-center gap-2 shadow hover:shadow-lg">
        <span class="text-xl">🏬</span>
        Depo List
    </a>


    <!-- Chemist House (NEW) -->
    <a href="{{ route('depo.chemist-house.index') }}"
       class="p-2 rounded-l-xl border-l-4 border-transparent bg-slate-800 hover:bg-gradient-to-r hover:from-teal-600 hover:to-teal-500 transition flex items-center gap-2 shadow hover:shadow-lg">
        <span class="text-xl">🏪</span>
        Chemist House
    </a>

    <!-- Direct Buyer -->
    <a href="{{ route('depo.default-chemist-house.index') }}"
       class="p-2 rounded-l-xl border-l-4 border-transparent bg-slate-800 hover:bg-gradient-to-r hover:from-teal-600 hover:to-teal-500 transition flex items-center gap-2 shadow hover:shadow-lg">
        <span class="text-xl">🤝</span>
        Direct Customer
    </a>

    <!-- Direct Sale -->
    <a href="{{ route('depo.direct-sale.index') }}"
       class="p-2 rounded-l-xl border-l-4 border-transparent bg-slate-800 hover:bg-gradient-to-r hover:from-teal-600 hover:to-teal-500 transition flex items-center gap-2 shadow hover:shadow-lg">
        <span class="text-xl">🛍️</span>
        Sale / Bill
    </a>


    <div class="relative">
        <button onclick="toggleDropdown('purchase-menu')"
                class="w-full p-2 rounded-l-xl border-l-4 border-transparent bg-slate-800 hover:bg-gradient-to-r hover:from-teal-600 hover:to-teal-500 transition flex justify-between items-center shadow hover:shadow-lg">
            <span class="flex items-center gap-2">
                <span class="text-xl">📦</span>
                Purchase
            </span>
            <svg class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div id="purchase-menu" class="hidden flex-col mt-1 rounded-xl overflow-hidden shadow-lg">
            <a href="{{ route('depo.purchase.pending') }}" class="block p-2 pl-8 text-sm bg-slate-900 hover:bg-gradient-to-r hover:from-teal-600 hover:to-teal-500">Pending Purchase</a>
            <a href="{{ route('depo.purchase.index') }}" class="block p-2 pl-8 text-sm bg-slate-900 hover:bg-gradient-to-r hover:from-teal-600 hover:to-teal-500">Received Purchase</a>
        </div>
    </div>


    <!-- Sales -->
    <a href="{{ route('depo.sale.index') }}"
       class="p-2 rounded-l-xl border-l-4 border-transparent bg-slate-800 hover:bg-gradient-to-r hover:from-indigo-600 hover:to-indigo-500 transition flex items-center gap-2 shadow hover:shadow-lg">
        <span class="text-xl">🛒</span>
        Orders
    </a>

    <!-- Stock -->
    <a href="{{ route('depo.stock.index') }}"
       class="p-2 rounded-l-xl border-l-4 border-transparent bg-slate-800 hover:bg-gradient-to-r hover:from-teal-600 hover:to-teal-500 transition flex items-center gap-2 shadow hover:shadow-lg">
        <span class="text-xl">📊</span>
        Stock
    </a>

    <!-- Accounts Dropdown -->
    <div class="relative">
        <button onclick="toggleDropdown('accounts-menu')"
                class="w-full p-2 rounded-l-xl border-l-4 border-transparent bg-slate-800 hover:bg-gradient-to-r hover:from-teal-600 hover:to-teal-500 transition flex justify-between items-center shadow hover:shadow-lg">
            <span class="flex items-center gap-2">
                <span class="text-xl">🧾</span>
                Accounts
            </span>
            <svg class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div id="accounts-menu" class="hidden flex-col mt-1 rounded-xl overflow-hidden shadow-lg">
            <a href="{{ route('depo.gl-account.index') }}" class="block p-2 pl-8 text-sm bg-slate-900 hover:bg-gradient-to-r hover:from-teal-600 hover:to-teal-500">GL Account</a>
            <a href="{{ route('depo.account.index') }}" class="block p-2 pl-8 text-sm bg-slate-900 hover:bg-gradient-to-r hover:from-teal-600 hover:to-teal-500">Account</a>
            <a href="{{ route('depo.chart-of-account.index') }}" class="block p-2 pl-8 text-sm bg-slate-900 hover:bg-gradient-to-r hover:from-teal-600 hover:to-teal-500">Chart Of Account</a>
            <a href="{{ route('depo.party.index') }}" class="block p-2 pl-8 text-sm bg-slate-900 hover:bg-gradient-to-r hover:from-teal-600 hover:to-teal-500">Party</a>
            <a href="{{ route('depo.debit-voucher.index') }}" class="block p-2 pl-8 text-sm bg-slate-900 hover:bg-gradient-to-r hover:from-teal-600 hover:to-teal-500">Debit Voucher (Out)</a>
            <a href="{{ route('depo.credit-voucher.index') }}" class="block p-2 pl-8 text-sm bg-slate-900 hover:bg-gradient-to-r hover:from-teal-600 hover:to-teal-500">Credit Voucher (In)</a>
        </div>
    </div>

    <!-- Due Management -->
    <div class="relative">
        <button onclick="toggleDropdown('payment-menu')"
                class="w-full p-2 rounded-l-xl bg-slate-800 hover:bg-gradient-to-r hover:from-teal-700 hover:to-teal-600 transition flex justify-between items-center shadow">
            <span class="flex items-center gap-2">💳 Due Management</span>
            <svg class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div id="payment-menu" class="hidden flex-col mt-1 rounded-xl overflow-hidden shadow-lg">
            <a href="{{ route('depo.depo-due-payment.index') }}" class="block p-2 pl-8 text-sm bg-slate-900 hover:bg-teal-600">💸 Due Payment</a>
            <a href="{{ route('depo.chemist-house-due-payment.index') }}" class="block p-2 pl-8 text-sm bg-slate-900 hover:bg-teal-600">🏦 Chemist House Due Collection</a>
        </div>
    </div>


    <!-- Reports -->
    <div class="relative">
        <button onclick="toggleDropdown('reports-menu')"
                class="w-full p-2 rounded-l-xl border-l-4 border-transparent
                   bg-slate-800 hover:bg-gradient-to-r hover:from-indigo-600 hover:to-indigo-500
                   transition flex justify-between items-center shadow hover:shadow-lg">

        <span class="flex items-center gap-2">
            <span class="text-xl">📑</span>
            Reports
        </span>

            <svg class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div id="reports-menu" class="hidden flex-col mt-1 rounded-xl overflow-hidden shadow-lg">
            <!-- Chemist House Ledger -->
            <a href="{{ route('depo.chemist-house-ledger-report.index') }}"
               class="block p-2 pl-8 text-sm bg-slate-900
                  hover:bg-gradient-to-r hover:from-indigo-600 hover:to-indigo-500">
                🧾 Chemist House Ledger
            </a>
            <!-- Cash Flow Report -->
            <a href="{{ route('report.cash-flow') }}"
               class="block p-2 pl-8 text-sm bg-slate-900
                  hover:bg-gradient-to-r hover:from-indigo-600 hover:to-indigo-500">
                🧾 Cash Flow Ledger
            </a>
        </div>
    </div>


</nav>

</aside>
