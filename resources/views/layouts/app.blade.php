<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Action Hub') }}</title>

        <!-- Fonts -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.datatables.net/v/dt/dt-3.1.4/datatables.min.css" rel="stylesheet">

        <!-- Sidebar state before first paint (no flash of the expanded sidebar) -->
        <script>
            if (localStorage.getItem('sidebar-collapsed') === 'true') {
                document.documentElement.classList.add('is-collapsed');
            }
        </script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div x-data="appShell()" x-effect="document.body.classList.toggle('overflow-hidden', open && ! window.matchMedia('(min-width: 1024px)').matches)" class="min-h-screen bg-gray-100">
            @include('layouts.sidebar')

            <!-- Mobile drawer trigger (no topbar, just a floating button) -->
            <button
                type="button"
                @click="open = true"
                class="fixed left-4 top-4 z-20 inline-flex h-10 w-10 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-600 shadow-sm transition-colors duration-150 hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 lg:hidden"
                aria-label="{{ __('Open navigation') }}"
            >
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 7h16M4 12h16M4 17h16" />
                </svg>
            </button>

            <div class="app-shell-main pt-16 transition-[padding] duration-300 ease-in-out lg:pl-64 lg:pt-0">
                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main>
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/v/dt/dt-3.1.4/datatables.min.js"></script>

    <!-- Page-specific scripts -->
    @stack('scripts')
</html>
