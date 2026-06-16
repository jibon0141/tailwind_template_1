<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Easy IT Solution LTD.')</title>

    @include('admin.include.css')
    @yield('app_styles')
</head>

<body class="bg-gray-100 font-sans antialiased">

<div id="loading-overlay" class="loading-overlay">
    <div class="spinner"></div>
</div>

<div id="sidebar-overlay" class="sidebar-overlay"></div>

@include('admin.include.asidebar')

<div class="main-content flex flex-col min-h-screen">
    @include('admin.include.header')

    @hasSection('breadcrumbs')
    <div class="breadcrumb-bar px-4 sm:px-6 lg:px-8">
        <nav class="breadcrumb">
            <a href="{{ url('/admin/dashboard') }}">Dashboard</a>
            @yield('breadcrumbs')
        </nav>
    </div>
    @endif

    <main class="flex-grow px-3 sm:px-5 lg:px-8 py-5">
        @include('admin.include.message')
        @yield('content')
    </main>

    @include('admin.include.footer')
</div>

@include('admin.include.js')
@yield('app_scripts')
@yield('scripts')

{{-- Search Modal --}}
<div id="search-modal" class="search-modal">
    <div id="search-backdrop" class="backdrop"></div>
    <div class="search-panel">
        <div class="search-input-wrap">
            <svg class="search-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input id="search-input" type="text" placeholder="Search pages..." autocomplete="off" spellcheck="false">
            <span class="search-hint">ESC</span>
        </div>
        <div id="search-results" class="search-results"></div>
    </div>
</div>

{{-- Back to Top --}}
<button id="back-to-top" class="back-to-top" title="Back to top">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
    </svg>
</button>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var overlay = document.getElementById('loading-overlay');
        if (overlay) {
            overlay.classList.add('active');
            window.addEventListener('load', function () {
                setTimeout(function () { overlay.classList.remove('active'); }, 300);
            });
            setTimeout(function () { overlay.classList.remove('active'); }, 1500);
        }
    });
</script>

</body>
</html>
