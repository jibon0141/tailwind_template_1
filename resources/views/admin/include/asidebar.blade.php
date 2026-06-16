<aside id="sidebar"
       class="sidebar fixed top-0 left-0 h-screen overflow-hidden flex flex-col">

    <div class="sidebar-brand flex items-center gap-3 h-18 px-5 py-4 bg-gradient-to-r from-slate-900 to-slate-800 flex-shrink-0">
        <div class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center flex-shrink-0">
            <span class="text-white font-bold text-lg">T</span>
        </div>
        <div class="sidebar-brand-text min-w-0 flex-1">
            <h1 class="text-white font-bold text-sm leading-tight truncate">Easy IT Solution LTD.</h1>
            <span class="text-white/50 text-[10px]">Administration</span>
        </div>
        <button id="sidebar-close-btn"
                class="md:hidden p-1.5 rounded-lg text-white/60 hover:text-white hover:bg-white/10 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <nav class="sidebar-nav flex-1 overflow-y-auto overflow-x-hidden py-3" id="sidebar-nav">

        <span class="sidebar-section-title">Main</span>

        <a href="{{ url('/admin/dashboard') }}">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="sidebar-nav-text">Dashboard</span>
        </a>


    </nav>

    <div class="sidebar-footer-text px-3 py-3 border-t border-slate-700/50 flex-shrink-0">
        <a href="{{ url('/') }}"
           class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs text-slate-500 hover:text-white hover:bg-slate-800 transition-colors">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span class="text-white">Back to Website</span>
        </a>
    </div>
</aside>
