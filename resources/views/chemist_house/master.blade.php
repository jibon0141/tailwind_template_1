<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    @php
        use App\Models\CompanySetting;
        $company = CompanySetting::first();
    @endphp

    <title>{{ $company->company_name ?? 'Tahsin Pharma' }}</title>

    @if($company && $company->logo)
        <link rel="icon" type="image/png"
              href="{{ asset('image/company_logo/'.$company->logo) }}">
    @elseif($company && $company->favicon)
        <link rel="icon" type="image/png"
              href="{{ asset('image/company_favicon/'.$company->favicon) }}">
    @endif

    @include('chemist_house.include.css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>

    <style>
        .s2-wrapper .select2-container { width: 100% !important; }
        .select2-container--default .select2-selection--single {
            height: 42px;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
        }
        .select2-selection__rendered { line-height: 42px !important; }
        .select2-selection__arrow { height: 42px !important; }
    </style>
</head>

<body class="bg-gray-200 font-sans antialiased overflow-x-hidden">

<button id="sidebar-toggle"
        class="fixed top-4 left-4 md:hidden z-[999] p-3 rounded bg-teal-700 text-white shadow-lg">
    ☰
</button>

<div class="min-h-screen">

    <!-- SIDEBAR -->
    <aside id="sidebar"
           class="bg-slate-800 h-screen w-64 fixed top-0 left-0 z-20
                  transform -translate-x-full md:translate-x-0
                  transition-transform duration-300 ease-in-out
                  overflow-y-auto">
        @include('chemist_house.include.asidebar')
    </aside>

    <!-- MAIN CONTENT -->
    <div class="flex flex-col md:ml-64 min-h-screen">
        @include('chemist_house.include.header')

        <main class="flex-grow px-2 sm:px-6 py-8 overflow-x-auto">
            @yield('content')
        </main>

        <footer class="bg-white shadow">
            @include('chemist_house.include.footer')
        </footer>


    </div>

</div>

@include('chemist_house.include.js')
@yield('scripts')

<script>
    $(document).ready(function () {
        const $sidebar = $('#sidebar');
        const $toggle = $('#sidebar-toggle');

        if ($sidebar.length && $toggle.length) {
            $toggle.on('click', function (e) {
                e.stopPropagation();
                $sidebar.toggleClass('-translate-x-full');
            });

            $sidebar.on('click', function (e) { e.stopPropagation(); });

            $(document).on('click', function (e) {
                if (
                    !$sidebar.hasClass('-translate-x-full') &&
                    !$(e.target).closest('#sidebar, #sidebar-toggle').length
                ) {
                    $sidebar.addClass('-translate-x-full');
                }
            });

            $('#sidebar a').on('click', function () {
                if (window.innerWidth < 768) {
                    $sidebar.addClass('-translate-x-full');
                }
            });
        }
    });


</script>

<script>
    function toggleProfileDropdown() {
        const menu = document.getElementById('profile-menu');
        if (menu) {
            menu.classList.toggle('hidden');
        }
    }

    // Optional: hide dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const menu = document.getElementById('profile-menu');
        const button = event.target.closest('button');
        if (!button || !button.contains(event.target)) {
            if (menu && !menu.classList.contains('hidden')) {
                menu.classList.add('hidden');
            }
        }
    });
</script>



</body>
</html>
