<header class="bg-white border-b bg-gradient-to-r from-teal-700 to-teal-600 p-4 flex justify-between items-center">
    <span class="text-white  font-semibold">Dashboard</span>
    <div class="relative">
        <button onclick="toggleProfileDropdown()" class="flex items-center gap-3 focus:outline-none">
            <span class="text-white font-semibold">{{Auth::user()->name}}</span>
            <img src="{{asset('assets/backend_assets/images/avatar.png')}}" class="h-8 w-8 rounded-full" alt="User">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                 xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                </path>
            </svg>
        </button>
        <div id="profile-menu"
             class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-20 border border-gray-200">
            <a href="{{url('/logout')}}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Log Out</a>
        </div>
    </div>
</header>
