<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        xintegrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Custom Theme (override warna bawaan) -->
    <link rel="stylesheet" href="{{ asset('custom-theme.css') }}">



    <!-- Anti-Flicker Script -->
    <script>
        if (localStorage.getItem('_x_expanded') === 'false') {
            document.documentElement.classList.add('sidebar-collapsed-on-load');
        }
    </script>

    <!-- Scripts Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Anti-Flicker Style -->
    <style>
        @media (min-width: 1024px) {
            html.sidebar-collapsed-on-load aside {
                width: 5rem !important; /* w-20 */
            }
            html.sidebar-collapsed-on-load main[class*="lg:ml-"],
            html.sidebar-collapsed-on-load div[class*="lg:ml-"] {
                margin-left: 5rem !important; /* ml-20 */
            }
        }
        [x-cloak] { 
            display: none !important; 
        }
    </style>

    @stack('styles')
</head>

<body class="font-sans antialiased"
    x-data="{ expanded: JSON.parse(localStorage.getItem('_x_expanded') ?? 'true'), mobileOpen: false }"
    x-init="document.documentElement.classList.remove('sidebar-collapsed-on-load')"
    :class="{ 'overflow-hidden': mobileOpen }">

    {{-- ✅ HEADER KHUSUS MOBILE (LG:HIDDEN) --}}
    {{-- Urutan diubah: Tombol dulu, baru Judul. justify-between -> justify-start gap-4 --}}
    <header x-cloak
        class="lg:hidden fixed top-0 left-0 right-0 z-30 flex items-center justify-start gap-4 h-16 px-4 bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
        <button @click="mobileOpen = true" class="text-gray-500 dark:text-gray-400 p-2 -ml-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
            <i class="fas fa-bars w-5 text-center"></i>
        </button>
        <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-200 claude-title">
            MagNet
        </h1>
    </header>
    
    <div class="flex min-h-screen bg-gray-100 dark:bg-gray-900">

        @include('layouts.sidebar')

        <div class="flex-1 flex flex-col transition-all duration-500 ease-in-out pt-16 lg:pt-0"
             :class="expanded ? 'lg:ml-64' : 'lg:ml-20'">

            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow hidden lg:block">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main class="flex-1 p-6 sm:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>

    {{-- OVERLAY MENU MOBILE --}}
    <div x-cloak x-show="mobileOpen" @click="mobileOpen = false"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 z-40 lg:hidden">
    </div>

    @stack('scripts')
</body>
</html>