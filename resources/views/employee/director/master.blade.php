<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>Pharma Soft Dashboard</title>



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
    <aside class="bg-slate-800 h-screen w-64 fixed z-10">
@include('employee.director.include.asidebar')
    </aside>

    <!-- MAIN CONTENT -->
    <div class="flex flex-col flex-grow ml-64">

        <!-- HEADER -->
@include('employee.director.include.header')

        <!-- PAGE CONTENT -->
        <main class="flex-grow px-6 py-8">

            @yield('content')

        </main>

        <!-- FOOTER -->
      @include('employee.director.include.footer')

    </div>
</div>

@include('employee.mpo.include.js')

@yield('scripts') <!-- Add this -->

<script>


    function toggleDropdown(id) {
        $(`#${id}`).toggleClass('hidden');
    }

    function toggleProfileDropdown() {
        $('#profile-menu').toggleClass('hidden');
    }
</script>

</body>

</html>
