<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

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



@include('depo.include.css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>

    <style>
        /* 🔥 THIS IS THE FIX */
        .s2-wrapper .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single {
            height: 42px;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
        }

        .select2-selection__rendered {
            line-height: 42px !important;
        }

        .select2-selection__arrow {
            height: 42px !important;
        }
    </style>




</head>

<body class="bg-gray-200 font-sans antialiased">

<div class="flex min-h-screen">

    <!-- SIDEBAR -->
    @include('depo.include.asidebar')

    <!-- MAIN CONTENT -->
    <div class="flex flex-col flex-grow md:ml-64">

        <!-- HEADER -->
@include('depo.include.header')

        <!-- PAGE CONTENT -->
        <main class="flex-grow px-6 py-8">

            @yield('content')

        </main>

        <!-- FOOTER -->
      @include('depo.include.footer')

    </div>
</div>

@include('depo.include.js')

@yield('scripts') <!-- Add this -->

<script>
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
        $('#profile-menu').toggleClass('hidden');
    }

    // Mobile sidebar toggle
    $(document).ready(function() {
        $('#sidebar-toggle').on('click', function() {
            $('#sidebar').toggleClass('-translate-x-full');
        });
    });
</script>

</body>

</html>
