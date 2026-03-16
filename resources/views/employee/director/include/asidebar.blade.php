<!-- User Profile Header -->
<div class="p-6 flex items-center gap-3 bg-gradient-to-r from-teal-700 to-teal-600 rounded-b-2xl shadow-xl">
    <img src="{{ asset('assets/backend_assets/images/avatar.png') }}"
         class="h-12 w-12 rounded-full border-2 border-white" alt="User">
    <div>
        <p class="text-white font-bold text-lg truncate">  {{ Auth::user()->name }}</p>
        <span class="text-teal-200 text-sm">Director</span>
    </div>
</div>

<!-- Navigation -->
<nav class="flex flex-col mt-6 space-y-2 font-medium text-slate-300">

    <!-- Dashboard -->
    <a href="{{ url('/director/dashboard') }}"
       class="p-4 rounded-l-xl border-l-4 border-teal-500 bg-gradient-to-r from-slate-800 to-slate-700 hover:from-teal-600 hover:to-teal-500 transition flex items-center gap-2 shadow hover:shadow-lg">
        <span class="text-xl">🏠</span>
        Dashboard
    </a>

    <!-- Medicine -->
{{--    <a href="{{ route('mpo.sale.create') }}"--}}
{{--       class="p-4 rounded-l-xl border-l-4 border-transparent bg-slate-800 hover:bg-gradient-to-r hover:from-teal-600 hover:to-teal-500 transition flex items-center gap-2 shadow hover:shadow-lg">--}}
{{--        <span class="text-xl">🛒</span>--}}
{{--        Sale--}}
{{--    </a>--}}




</nav>

<!-- Dropdown JS -->
<script>
    function toggleDropdown(id){
        const menu = document.getElementById(id);
        menu.classList.toggle('hidden');
        const svg = menu.previousElementSibling.querySelector('svg');
        svg.classList.toggle('rotate-180');
    }
</script>
