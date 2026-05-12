<!DOCTYPE html>

<html lang="id" class="h-full">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Sistem MBG') - MBG Makanan Bergizi Gratis</title>



    {{-- TailwindCSS CDN --}}

    <script src="https://cdn.tailwindcss.com"></script>

    <script>

        tailwind.config = {

            theme: {

                extend: {

                    colors: {

                        mbg: {

                            50: '#f0fdf4', 100: '#dcfce7', 200: '#bbf7d0',

                            300: '#86efac', 400: '#4ade80', 500: '#22c55e',

                            600: '#16a34a', 700: '#15803d', 800: '#166534',

                            900: '#14532d',

                        }

                    }

                }

            }

        }

    </script>



    {{-- Leaflet CSS (Maps) --}}

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    {{-- Font --}}

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">


    <style>

        body { font-family: 'Inter', sans-serif; }

        .sidebar-link { @apply flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-mbg-50 hover:text-mbg-700 transition-all duration-200 font-medium; }

        .sidebar-link.active { @apply bg-mbg-600 text-white shadow-lg; }

        .status-badge { @apply inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold; }

        .pulse-dot { animation: pulse 2s infinite; }

        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }

    </style>

    @stack('styles')

</head>

<body class="h-full bg-gray-50">

    <div class="flex h-full min-h-screen">

        {{-- Sidebar --}}

        <aside class="w-64 bg-white shadow-xl flex flex-col fixed inset-y-0 left-0 z-30">

            {{-- Logo --}}

            <div class="p-6 border-b border-gray-100">

                <div class="flex items-center gap-3">

                    <div class="w-10 h-10 bg-mbg-600 rounded-xl flex items-center justify-center">

                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>

                        </svg>

                    </div>

                    <div>

                        <h1 class="font-bold text-gray-800 text-lg leading-tight">MBG</h1>

                        <p class="text-xs text-gray-500">Makanan Bergizi Gratis</p>

                    </div>

                </div>

            </div>

            {{-- User Info --}}

            <div class="p-4 border-b border-gray-100">

                <div class="flex items-center gap-3 bg-gray-50 rounded-xl p-3">

                    <div class="w-9 h-9 rounded-full bg-mbg-600 flex items-center justify-center text-white font-bold text-sm">

                        {{ substr(auth()->user()->name, 0, 1) }}

                    </div>

                    <div class="flex-1 min-w-0">

                        <p class="text-sm font-semibold text-gray-800 truncate">{{ auth()->user()->name }}</p>

                        <span class="text-xs px-2 py-0.5 rounded-full font-medium

                            @if(auth()->user()->role === 'petugas') bg-blue-100 text-blue-700

                            @elseif(auth()->user()->role === 'guru') bg-purple-100 text-purple-700

                            @else bg-orange-100 text-orange-700 @endif">

                            {{ ucfirst(auth()->user()->role) }}

                        </span>

                    </div>

                </div>

            </div>



            {{-- Navigation --}}

            <nav class="flex-1 p-4 space-y-1 overflow-y-auto">

                @yield('sidebar-nav')

            </nav>



            {{-- Logout --}}

            <div class="p-4 border-t border-gray-100">

                <form action="{{ route('logout') }}" method="POST">

                    @csrf

                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-red-600 hover:bg-red-50 transition-all duration-200 font-medium">

                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>

                        </svg>

                        Keluar

                    </button>

                </form>

            </div>

        </aside>



        {{-- Main Content --}}

        <main class="flex-1 ml-64 flex flex-col">

            {{-- Top Bar --}}

            <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between sticky top-0 z-20">

                <div>

                    <h2 class="text-xl font-bold text-gray-800">@yield('page-title', 'Dashboard')</h2>

                    <p class="text-sm text-gray-500">{{ now()->isoFormat('dddd, D MMMM Y') }}</p>

                </div>

                <div class="flex items-center gap-4">

                    @yield('header-actions')



                    {{-- Notifikasi (untuk orang tua) --}}

                    @if(auth()->user()->isOrangTua())

                    <div class="relative">

                        <button id="notif-btn" class="relative p-2 text-gray-500 hover:text-mbg-600 hover:bg-mbg-50 rounded-xl transition-colors">

                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>

                            </svg>

                            @if(auth()->user()->unreadNotifications()->count() > 0)

                            <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-bold">

                                {{ auth()->user()->unreadNotifications()->count() }}

                            </span>

                            @endif

                        </button>

                    </div>

                    @endif

                </div>

            </header>



            {{-- Alerts --}}

            <div class="px-8 pt-4">

                @if(session('success'))

                <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 mb-4 flex items-center gap-2">

                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">

                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>

                    </svg>

                    {{ session('success') }}

                </div>

                @endif

                @if(session('error'))

                <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 mb-4 flex items-center gap-2">

                    <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">

                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>

                    </svg>

                    {{ session('error') }}

                </div>

                @endif

            </div>



            {{-- Page Content --}}

            <div class="flex-1 px-8 pb-8 pt-2">

                @yield('content')

            </div>

        </main>

    </div>



    {{-- Leaflet JS --}}

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>



    {{-- Pusher & Laravel Echo --}}

    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

    <script>

        // Laravel Echo Setup

        const pusher = new Pusher("{{ config('broadcasting.connections.pusher.key') }}", {
    cluster: "{{ config('broadcasting.connections.pusher.options.cluster') }}",
    encrypted: true
});



        window.Echo = {

            pusher: pusher,

            channel: (name) => pusher.subscribe(name),

        };

    </script>



    @stack('scripts')

</body>

</html>