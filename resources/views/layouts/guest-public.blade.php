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
<header class="w-full top-0 sticky bg-white/95 backdrop-blur-md border-b border-stadium-gray/80 z-50 shadow-sm shadow-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex h-20 items-center justify-between">
            <div class="flex items-center gap-8">
                <x-logo-wordmark :href="route('home')" :size="36"/>
                <nav class="hidden md:flex items-center gap-1.5">
                    <a href="{{ route('home') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold {{ request()->routeIs('home','venues.index') ? 'bg-blue-50 text-electric-blue border border-blue-200/60 shadow-sm' : 'text-on-surface-variant hover:text-pitch-black hover:bg-slate-50' }} transition-all">
                        @if(request()->routeIs('home','venues.index'))
                            <span class="w-2 h-2 rounded-full bg-electric-blue"></span>
                        @endif
                        Venue
                    </a>
                </nav>
            </div>

            <div class="hidden md:flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm font-bold text-on-surface-variant hover:text-pitch-black hover:bg-slate-50 rounded-xl transition">
                        Dashboard
                    </a>
                    <a href="{{ route('bookings.index') }}" class="px-4 py-2 text-sm font-bold text-electric-blue bg-blue-50 hover:bg-blue-100 rounded-xl transition">
                        Booking Saya
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-bold text-pitch-black hover:text-electric-blue transition">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="px-5 py-2.5 text-sm font-bold bg-electric-blue text-white hover:bg-blue-700 rounded-xl transition shadow-sm hover:shadow active:scale-95">
                        Daftar Gratis
                    </a>
                @endauth
            </div>

            <button @click="mobileMenu = !mobileMenu" class="md:hidden w-10 h-10 rounded-full flex items-center justify-center text-on-surface-variant hover:bg-slate-100 hover:text-pitch-black transition">
                <span class="material-symbols-outlined text-[24px]" x-show="!mobileMenu">menu</span>
                <span class="material-symbols-outlined text-[24px]" x-show="mobileMenu" x-cloak>close</span>
            </button>
        </div>
    </div>

    <div x-show="mobileMenu" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="md:hidden border-t border-stadium-gray bg-white px-4 py-3 space-y-2 shadow-lg">
        <a href="{{ route('home') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-bold {{ request()->routeIs('home','venues.index') ? 'bg-blue-50 text-electric-blue' : 'text-on-surface-variant' }}">
            <span>Venue</span>
            @if(request()->routeIs('home','venues.index'))
                <span class="w-2 h-2 rounded-full bg-electric-blue"></span>
            @endif
        </a>
        @auth
            <a href="{{ route('dashboard') }}" class="block px-3.5 py-2 text-sm font-bold text-on-surface-variant">Dashboard</a>
            <a href="{{ route('bookings.index') }}" class="block px-3.5 py-2 text-sm font-bold text-electric-blue">Booking Saya</a>
        @else
            <div class="pt-2 border-t border-stadium-gray flex flex-col gap-2">
                <a href="{{ route('login') }}" class="w-full text-center py-2 text-sm font-bold text-pitch-black bg-slate-100 rounded-xl">Masuk</a>
                <a href="{{ route('register') }}" class="w-full text-center py-2.5 text-sm font-bold text-white bg-electric-blue rounded-xl">Daftar Gratis</a>
            </div>
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