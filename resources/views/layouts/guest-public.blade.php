<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' — ' : '' }}TiketinAja</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Inter', sans-serif; } [x-cloak]{display:none!important}</style>
</head>
<body class="bg-gray-50 antialiased" x-data="{ mobileMenu: false }">

{{-- Navbar --}}
<header class="bg-white border-b border-gray-200 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <div class="flex items-center gap-8">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-sm">T</span>
                    </div>
                    <span class="font-bold text-gray-900 text-lg">TiketinAja</span>
                </a>
                <nav class="hidden md:flex items-center gap-1">
                    <a href="{{ route('home') }}"
                       class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('home','venues.index') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }}">
                        Venue
                    </a>
                </nav>
            </div>

            <div class="hidden md:flex items-center gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition">
                        Dashboard
                    </a>
                    <a href="{{ route('bookings.index') }}" class="px-4 py-2 text-sm font-medium text-indigo-600 hover:bg-indigo-50 rounded-lg transition">
                        Booking Saya
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition">Masuk</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-medium bg-indigo-600 text-white hover:bg-indigo-700 rounded-lg transition">Daftar Gratis</a>
                @endauth
            </div>

            <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!mobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    <path x-show="mobileMenu" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <div x-show="mobileMenu" x-cloak class="md:hidden border-t border-gray-100 px-4 py-3 space-y-2">
        <a href="{{ route('home') }}" class="block text-sm text-gray-700 py-2">Venue</a>
        @auth
            <a href="{{ route('dashboard') }}" class="block text-sm text-gray-700 py-2">Dashboard</a>
            <a href="{{ route('bookings.index') }}" class="block text-sm text-indigo-600 py-2 font-medium">Booking Saya</a>
        @else
            <a href="{{ route('login') }}" class="block text-sm text-gray-700 py-2">Masuk</a>
            <a href="{{ route('register') }}" class="block text-sm text-indigo-600 py-2 font-medium">Daftar Gratis</a>
        @endauth
    </div>
</header>

@if(session('success'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
        <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm">
            <svg class="w-5 h-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    </div>
@endif

<main>{{ $slot }}</main>

<footer class="bg-gray-900 text-gray-400 py-10 mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 bg-indigo-500 rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-xs">T</span>
                </div>
                <span class="text-white font-semibold">TiketinAja</span>
            </div>
            <p class="text-sm">&copy; {{ date('Y') }} TiketinAja. Platform Booking Lapangan Olahraga.</p>
        </div>
    </div>
</footer>

</body>
</html>
