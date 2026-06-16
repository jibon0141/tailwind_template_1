<header class="top-header sticky top-0 z-30 flex items-center justify-between px-4 h-18 px-5 py-4 lg:px-6">
    <div class="flex items-center gap-2">
        <button id="sidebar-toggle"
                class="header-btn md:hidden">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        <button id="sidebar-collapse-btn"
                class="header-btn hidden md:flex">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
            </svg>
        </button>
        <div class="ml-2">
            <h2 class="text-sm lg:text-base font-semibold text-slate-800 leading-tight">@yield('title', 'Dashboard')</h2>
            <p class="text-xs text-slate-400 hidden sm:block">Easy IT Solution LTD. Administration</p>
        </div>
    </div>

    <div class="flex items-center gap-1 sm:gap-2">

        <button id="fullscreen-btn" class="header-btn hidden sm:flex" title="Toggle Fullscreen">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
            </svg>
        </button>

        <div class="relative">
            <button id="profile-dropdown-btn"
                    class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-slate-100 transition-colors">
                <img src="{{ asset('assets/backend_assets/images/avatar.png') }}"
                     class="h-7 w-7 sm:h-8 sm:w-8 rounded-full ring-2 ring-slate-200" alt="User">
                <span class="hidden sm:block text-slate-700 text-sm font-medium max-w-[100px] truncate">
                    {{ Auth::user()->name }}
                </span>
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div id="profile-menu"
                 class="profile-dropdown hidden absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-lg py-1 z-50 border border-slate-200">
                <div class="px-4 py-3 border-b border-slate-100">
                    <p class="text-sm font-semibold text-slate-800">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-slate-400">{{ Auth::user()->email ?? 'Administrator' }}</p>
                </div>
                <a href="{{ route('main.company.index') }}"
                   class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Settings
                </a>
                <div class="border-t border-slate-100"></div>
                <a href="{{ url('/logout') }}"
                   class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Log Out
                </a>
            </div>
        </div>
    </div>
</header>
