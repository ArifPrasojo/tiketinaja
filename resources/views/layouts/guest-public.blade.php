<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' — ' : '' }}TiketinAja</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Inter', sans-serif; } [x-cloak]{display:none!important}</style>
</head>
<body class="bg-surface-light text-pitch-black antialiased selection:bg-electric-blue selection:text-white" x-data="{ mobileMenu: false }">

{{-- Navbar --}}
<header class="bg-white border-b border-stadium-gray sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <div class="flex items-center gap-8">
                <x-logo-wordmark :href="route('home')" :size="32"/>
                <nav class="hidden md:flex items-center gap-1">
                    <a href="{{ route('home') }}"
                       class="px-3 py-2 rounded-xl text-sm font-medium transition {{ request()->routeIs('home','venues.index') ? 'bg-blue-50 text-electric-blue border border-electric-blue/20' : 'text-on-surface-variant hover:bg-slate-50 hover:text-pitch-black' }}">
                        Venue
                    </a>
                </nav>
            </div>

            <div class="hidden md:flex items-center gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm font-medium text-on-surface-variant hover:bg-slate-50 hover:text-pitch-black rounded-xl transition">
                        Dashboard
                    </a>
                    <a href="{{ route('bookings.index') }}" class="px-4 py-2 text-sm font-medium text-electric-blue hover:bg-blue-700/10 rounded-xl transition">
                        Booking Saya
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-on-surface-variant hover:bg-slate-50 hover:text-pitch-black rounded-xl transition">Masuk</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-medium bg-electric-blue text-white hover:bg-blue-700 rounded-xl transition shadow-lg shadow-blue-500/20">Daftar Gratis</a>
                @endauth
            </div>

            <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 rounded-xl text-on-surface-variant hover:bg-slate-50 hover:text-pitch-black">
                <span class="material-symbols-outlined text-[24px]" x-show="!mobileMenu">menu</span>
                <span class="material-symbols-outlined text-[24px]" x-show="mobileMenu" x-cloak>close</span>
            </button>
        </div>
    </div>

    <div x-show="mobileMenu" x-cloak class="md:hidden border-t border-stadium-gray px-4 py-3 space-y-2">
        <a href="{{ route('home') }}" class="block text-sm text-on-surface-variant py-2">Venue</a>
        @auth
            <a href="{{ route('dashboard') }}" class="block text-sm text-on-surface-variant py-2">Dashboard</a>
            <a href="{{ route('bookings.index') }}" class="block text-sm text-electric-blue py-2 font-medium">Booking Saya</a>
        @else
            <a href="{{ route('login') }}" class="block text-sm text-on-surface-variant py-2">Masuk</a>
            <a href="{{ route('register') }}" class="block text-sm text-electric-blue py-2 font-medium">Daftar Gratis</a>
        @endauth
    </div>
</header>

@if(session('success'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
        <div class="flex items-center gap-3 bg-blue-50 border border-electric-blue/30 text-electric-blue px-4 py-3 rounded-2xl text-sm">
            <span class="material-symbols-outlined text-[20px] text-electric-blue shrink-0">check_circle</span>
            {{ session('success') }}
        </div>
    </div>
@endif

<main>{{ $slot }}</main>

<footer class="bg-white border-t border-stadium-gray text-on-surface-variant py-10 mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <x-logo-wordmark :href="route('home')" :size="28"/>
            <p class="text-sm">&copy; {{ date('Y') }} TiketinAja. Platform Booking Lapangan Olahraga.</p>
        </div>
    </div>
</footer>

</body>
</html>