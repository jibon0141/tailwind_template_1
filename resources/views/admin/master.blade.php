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

    @include('admin.include.css')
    @yield('app_styles')
</head>

<body class="bg-gray-200 font-sans antialiased overflow-x-hidden">

<div class="min-h-screen">

    <!-- SIDEBAR -->
    @include('admin.include.asidebar')

    <!-- MAIN CONTENT -->
    <div class="flex flex-col md:ml-64 min-h-screen">

        @include('admin.include.header')

        <main class="flex-grow px-2 sm:px-6 py-8 overflow-x-auto">
            @yield('content')
        </main>

        <footer class="bg-white shadow">
            @include('admin.include.footer')
        </footer>

    </div>
</div>

@include('admin.include.js')
@yield('app_scripts')
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

            $sidebar.on('click', function (e) {
                e.stopPropagation();
            });

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

    function toggleDropdown(id) {
        // Select all submenu containers inside sidebar
        const menus = document.querySelectorAll(
            '#sidebar nav .relative > div[id]'
        );

        menus.forEach(menu => {
            // Close all menus except the clicked one
            if (menu.id !== id) {
                menu.classList.add('hidden');
            }
        });

        // Toggle clicked menu
        const currentMenu = document.getElementById(id);
        currentMenu.classList.toggle('hidden');
    }

    function toggleProfileDropdown() {
        const menu = document.getElementById('profile-menu');
        menu.classList.toggle('hidden');
    }

    // Optional: close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const menu = document.getElementById('profile-menu');
        const button = e.target.closest('button');
        if (!menu.contains(e.target) && !button) {
            menu.classList.add('hidden');
        }
    });
</script>

</body>
</html>
