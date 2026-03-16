@php
    $activeUser = session('userObj') ?? Auth::user();
@endphp

<header class="bg-gradient-to-r from-teal-700 to-teal-600 flex justify-between items-center p-4 pl-16">

    <!-- Left Title (Mobile) -->
    <span class="text-white text-xl font-semibold md:hidden">
        Tahsin Pharma
    </span>

    <!-- Dashboard (Desktop) -->
    <span class="text-white font-semibold hidden md:block">
        Dashboard
    </span>

    <!-- Center Company Name (Desktop Only) -->
    <span class="text-white font-semibold text-2xl hidden md:block absolute left-1/2 transform -translate-x-1/2">
        Tahsin Pharma
    </span>

    <!-- Right Side -->
    <div class="relative">

        <!-- 🔹 Desktop Profile -->
        <button onclick="toggleProfileDropdown()"
                class="hidden md:flex items-center gap-3 focus:outline-none">
            <span class="text-white font-semibold">{{$activeUser->name}}</span>
            <img src="{{asset('assets/backend_assets/images/avatar.png')}}"
                 class="h-8 w-8 rounded-full" alt="User">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>



        <!-- Mobile Power Icon -->
        <a href="{{url('/logout')}}"
           class="md:hidden bg-red-600 hover:bg-red-700 text-white p-2 rounded-full shadow-md transition duration-200 flex items-center justify-center">

            <svg xmlns="http://www.w3.org/2000/svg"
                 class="h-6 w-6"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 3v9m6.364-6.364a9 9 0 11-12.728 0"/>
            </svg>

        </a>



        <!-- Dropdown Menu (Desktop Only) -->
        <div id="profile-menu"
             class="hidden absolute right-0 mt-2 w-28 bg-white rounded-md shadow-lg py-1 z-20 border border-gray-200">
            <a href="{{url('/logout')}}"
               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                Log Out
            </a>
        </div>

    </div>
</header>
