<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'TiketinAja') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Noto+Serif:ital,wght@0,400;0,700;1,700;1,800&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-surface-light text-pitch-black antialiased min-h-screen flex flex-col">
    <header class="w-full sticky top-0 bg-white shadow-sm shadow-electric-blue/10 z-50">
        <div class="flex justify-between items-center w-full px-6 max-w-7xl mx-auto h-20">
            <x-logo-wordmark :href="url('/')" :size="36"/>
            <nav class="flex items-center gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-2.5 text-sm font-bold text-pitch-black hover:text-electric-blue transition">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2.5 text-sm font-medium text-on-surface-variant hover:text-electric-blue transition">Masuk</a>
                    <a href="{{ route('register') }}" class="px-4 py-2.5 bg-athletic-orange text-white text-sm font-bold rounded-lg hover:bg-orange-600 transition active:scale-95">Daftar</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="flex-1 flex items-center">
        <div class="max-w-7xl mx-auto px-6 py-20 text-center">
            <p class="text-electric-blue text-sm font-bold uppercase tracking-wide mb-3">Platform Booking Lapangan</p>
            <h1 class="font-display text-5xl sm:text-6xl font-extrabold italic text-pitch-black leading-tight mb-5">
                Elevate Your Game
            </h1>
            <p class="text-on-surface-variant text-lg max-w-xl mx-auto mb-8">
                Futsal, badminton, basket, tenis — booking lapangan olahraga kapan saja, di mana saja.
            </p>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-athletic-orange text-white text-sm font-bold rounded-lg hover:bg-orange-600 transition active:scale-95">
                Cari Venue
            </a>
        </div>
    </main>

    <footer class="bg-pitch-black">
        <div class="max-w-7xl mx-auto px-6 py-6 flex flex-col sm:flex-row items-center justify-between gap-3">
            <x-logo-wordmark :size="28" :dark="true"/>
            <p class="text-xs text-slate-400">&copy; {{ date('Y') }} TiketinAja — Elevate Your Game</p>
        </div>
    </footer>
</body>
</html>
