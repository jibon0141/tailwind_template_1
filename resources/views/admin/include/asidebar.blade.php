<button id="sidebar-toggle"
        class="fixed top-4 left-4 md:hidden z-[999] p-3 rounded bg-teal-700 text-white shadow-lg hover:bg-teal-800 transition-colors">
    ☰
</button>

<!-- Sidebar -->
<aside id="sidebar"
       class="fixed top-0 left-0 h-screen w-64 bg-slate-800 text-slate-200 z-20
              transform -translate-x-full md:translate-x-0
              transition-transform duration-300 ease-in-out
              overflow-y-auto">

    <div class="flex flex-col">

        <!-- User Profile Header -->
        <div class="flex items-center gap-3 bg-gradient-to-r from-teal-700 to-teal-600 shadow-xl
                    h-16 p-2 md:p-4 pl-2 md:pl-6">

            <img src="{{ asset('assets/backend_assets/images/avatar.png') }}"
                 class="h-10 w-10 hidden md:block rounded-full border-2 border-white"
                 alt="User">

            <div class="flex flex-col justify-center pl-20 md:pl-2">
                <span class="text-white font-bold text-base md:text-lg truncate">
                    {{ Auth::user()->name }}
                </span>
                <div class="text-white font-semibold text-sm md:text-base">
                   Admin
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex flex-col mt-4 md:mt-6 space-y-2 font-medium pb-10">


            <!-- Dashboard -->
            <a href="{{ url('/admin/dashboard') }}"
               class="p-2 rounded-l-xl border-l-4 border-teal-400 bg-slate-800
                      hover:bg-gradient-to-r hover:from-teal-700 hover:to-teal-600
                      transition flex items-center gap-2 shadow">
                🏠 Dashboard
            </a>
            @if(!(Auth::user()->user_type == 'admin' && Auth::user()->role != 'purchase_admin'))

            <a href="{{ route('admin.medicine.purchase.create') }}"
               class="p-2 rounded-l-xl bg-slate-800 hover:bg-gradient-to-r hover:from-teal-700 hover:to-teal-600 shadow">
                💊 Medicine Purchase
            </a>

            @endif


            @if(!(Auth::user()->user_type == 'admin' && Auth::user()->role == 'purchase_admin'))
            <!-- Settings -->
                <a href="{{ route('purchase.admin.index') }}"
                   class="p-2 rounded-l-xl bg-slate-800 hover:bg-gradient-to-r hover:from-teal-700 hover:to-teal-600 shadow">
                    👤 Sub Admin
                </a>

                <div class="relative">
                <button onclick="toggleDropdown('company-menu')"
                        class="w-full p-2 rounded-l-xl bg-slate-800
                               hover:bg-gradient-to-r hover:from-teal-700 hover:to-teal-600
                               transition flex justify-between items-center shadow">
                    <span class="flex items-center gap-2">⚙️ Settings</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div id="company-menu" class="hidden flex-col mt-1 rounded-xl overflow-hidden">
                    <a href="{{ route('main.company.index') }}" class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">🏢 Company</a>
                    <a href="{{ route('division.index') }}" class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">🗂️ Division</a>
                    <a href="{{ route('district.index') }}" class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">📍 District</a>
                    <a href="{{ route('vat.index') }}" class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">🧾 VAT</a>
                </div>
            </div>

            <!-- Accounts -->
            <div class="relative">
                <button onclick="toggleDropdown('accounts-menu')"
                        class="w-full p-2 rounded-l-xl bg-slate-800
                               hover:bg-gradient-to-r hover:from-teal-600 hover:to-teal-500
                               transition flex justify-between items-center shadow">
                    <span class="flex items-center gap-2">🧾 Accounts</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div id="accounts-menu" class="hidden flex-col mt-1 rounded-xl overflow-hidden">
                    <a href="{{ route('admin.gl-account.index') }}" class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">GL Account</a>
                    <a href="{{ route('admin.account.index') }}" class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">Account</a>
                    <a href="{{ route('admin.chart-of-account.index') }}" class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">Chart Of Account</a>
                    <a href="{{ route('admin.party.index') }}" class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">Party</a>
                    <a href="{{ route('admin.debit-voucher.index') }}" class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">Debit Voucher (Out)</a>
                    <a href="{{ route('admin.credit-voucher.index') }}" class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">Credit Voucher (In)</a>
                </div>
            </div>

            <!-- Investor -->
            <div class="relative">
                <button onclick="toggleDropdown('investor-menu')"
                        class="w-full p-2 rounded-l-xl bg-slate-800
                               hover:bg-gradient-to-r hover:from-teal-600 hover:to-teal-500
                               transition flex justify-between items-center shadow">
                    <span class="flex items-center gap-2">💰 Investor</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div id="investor-menu" class="hidden flex-col mt-1 rounded-xl overflow-hidden">
                    <a href="{{ route('admin.investor.index') }}" class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">👤 Investor</a>
                    <a href="{{ route('admin.investor.invest.index') }}" class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">➕ Invest</a>
                    <a href="{{ route('admin.investor.withdraw.index') }}" class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">➖ Withdraw</a>
                </div>
            </div>

            <!-- Marketing Team -->
            <div class="relative">
                <button onclick="toggleDropdown('team-menu')"
                        class="w-full p-2 rounded-l-xl bg-slate-800
                               hover:bg-gradient-to-r hover:from-teal-700 hover:to-teal-600
                               transition flex justify-between items-center shadow">
                    <span class="flex items-center gap-2">👥 Marketing Team</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div id="team-menu" class="hidden flex-col mt-1 rounded-xl overflow-hidden">
                    <a href="{{ route('director.index') }}" class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">🎯 Director</a>
                    <a href="{{ route('nsm.index') }}" class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">🧭 NSM</a>
                    <a href="{{ route('rsm.index') }}" class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">📈 RSM</a>
                    <a href="{{ route('sm.index') }}" class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">📊 SM</a>
                    <a href="{{ route('asm.index') }}" class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">📍 ASM</a>
                    <a href="{{ route('mpo.index') }}" class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">👤 MPO</a>
                </div>
            </div>





            <!-- Chemist House -->
            <a href="{{ route('chemist.house.index') }}"
               class="p-2 rounded-l-xl bg-slate-800 hover:bg-gradient-to-r hover:from-teal-700 hover:to-teal-600 shadow">
                🧪 Chemist House
            </a>

            <!-- Depo -->
            <a href="{{ route('depo.index') }}"
               class="p-2 rounded-l-xl bg-slate-800 hover:bg-gradient-to-r hover:from-teal-700 hover:to-teal-600 shadow">
                🏢 Depo
            </a>

            <!-- Medicine -->
            <div class="relative">
                <button onclick="toggleDropdown('medicine-menu')"
                        class="w-full p-2 rounded-l-xl bg-slate-800
                               hover:bg-gradient-to-r hover:from-teal-700 hover:to-teal-600
                               transition flex justify-between items-center shadow">
                    <span class="flex items-center gap-2">💊 Medicine</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div id="medicine-menu" class="hidden flex-col mt-1 rounded-xl overflow-hidden">
                    <a href="{{ route('company.index') }}" class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">🏢 Company</a>
                    <a href="{{ route('generic.index') }}" class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">🧬 Generic Name</a>
                    <a href="{{ route('dosage.index') }}" class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">📏 Dosage</a>
                    <a href="{{ route('category.index') }}" class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">🗂️ Category</a>
                    <a href="{{ route('brand.index') }}" class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">🏷️ Brand</a>
                    <a href="{{ route('medicine.index') }}" class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">💊 Medicine</a>
                    <a href="{{ route('medicine.list') }}" class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">📋 Medicine List</a>
                </div>
            </div>


            <!-- Supply Chain -->
            <div class="relative">
                <button onclick="toggleDropdown('supply-chain-menu')"
                        class="w-full p-2 rounded-l-xl bg-slate-800
                   hover:bg-gradient-to-r hover:from-teal-700 hover:to-teal-600
                   transition flex justify-between items-center shadow">
                    <span class="flex items-center gap-2">🔗 Supply Chain</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div id="supply-chain-menu" class="hidden flex-col mt-1 rounded-xl overflow-hidden">
                    <a href="{{ route('supplier.index') }}"
                       class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">
                        🏭 Supplier
                    </a>

                    <!-- ✅ NEW: Requisition -->
                    <a href="{{ route('admin.requisition.index') }}"
                       class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">
                        📝 Requisition
                    </a>

                    <a href="{{ route('admin.purchase.index') }}"
                       class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">
                        🛒 Admin Purchase
                    </a>

                    <a href="{{ route('admin.subAdmin.medicine.purchase') }}"
                       class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">
                        🛒 Sub-Admin Purchase
                    </a>

                    <a href="{{ route('admin.temp-distribute.index') }}"
                       class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">
                        🚚 Create Distribution
                    </a>

                    <a href="{{ route('admin.distribute.index') }}"
                       class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">
                        🚚 Placed Distribution
                    </a>

                    <a href="{{ route('admin.stock.index') }}"
                       class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">
                        📊 Stock
                    </a>
                </div>
            </div>


            <!-- Payments & Collections -->
            <div class="relative">
                <button onclick="toggleDropdown('payment-menu')"
                        class="w-full p-2 rounded-l-xl bg-slate-800
                               hover:bg-gradient-to-r hover:from-teal-700 hover:to-teal-600
                               transition flex justify-between items-center shadow">
                    <span class="flex items-center gap-2">💳 Due Management</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div id="payment-menu" class="hidden flex-col mt-1 rounded-xl overflow-hidden">
                    <a href="{{ route('admin.supplier-due-payment.index') }}" class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">💸 Supplier Payment</a>
                    <a href="{{ route('admin.depo-due-collection.index') }}" class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">🏦 Depo Due Collection</a>
                </div>
            </div>

            <!-- Reports -->
            <div class="relative mb-6">
                <button onclick="toggleDropdown('reports-menu')"
                        class="w-full p-2 rounded-l-xl bg-slate-800
                               hover:bg-gradient-to-r hover:from-teal-700 hover:to-teal-600
                               transition flex justify-between items-center shadow">
                    <span class="flex items-center gap-2">📄 Reports</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div id="reports-menu" class="hidden flex-col mt-1 rounded-xl overflow-hidden">
                    <a href="{{ route('report.supplier.ledger') }}" class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">📄 Supplier Ledger Report</a>
                    <a href="{{ route('report.investor.ledger') }}" class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">📄 Investor Ledger Report</a>
                    <a href="{{ route('report.chemist-house-ledger-report.index') }}" class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">📄 Chemist House Ledger Report</a>
                    <a href="{{ route('report.company.cash-flow') }}" class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">📄 Company Cash Flow Report</a>
                    <a href="{{ route('report.depo.cash-flow') }}" class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">📄 Depo Cash Flow Report</a>
                    <a href="{{ route('report.depo.ledger') }}" class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">📄 Depo Ledger Report</a>
                    <a href="{{ route('report.depo.profit') }}" class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">📄 Depo Sale Report</a>
                    <a href="{{ route('report.depo.monthly.profit') }}" class="block p-2 pl-8 text-xs md:text-sm bg-slate-900 hover:bg-teal-600">📄 Depo Profit Report</a>
                </div>

            </div>
                <!-- Job Application -->
                <a href="{{ route('admin.job.application.index') }}"
                   class="p-2 rounded-l-xl bg-slate-800 hover:bg-gradient-to-r hover:from-teal-700 hover:to-teal-600 shadow">
                    📄 Job Application
                </a>

                @endif



        </nav>
    </div>
</aside>

