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
    <header class="w-full sticky top-0 bg-white/95 backdrop-blur-md shadow-sm shadow-electric-blue/10 z-50">
        <div class="flex justify-between items-center w-full px-4 sm:px-6 max-w-7xl mx-auto h-16 sm:h-20">
            <x-logo-wordmark :href="url('/')" :size="32"/>
            <nav class="flex items-center gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-3.5 sm:px-4 py-2 text-xs sm:text-sm font-bold text-pitch-black hover:text-electric-blue transition">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="px-3 sm:px-4 py-2 text-xs sm:text-sm font-bold text-on-surface-variant hover:text-electric-blue transition">Masuk</a>
                    <a href="{{ route('register') }}" class="px-3.5 sm:px-4 py-2 bg-athletic-orange text-white text-xs sm:text-sm font-bold rounded-xl hover:bg-orange-600 transition active:scale-95 shadow-sm">Daftar</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="flex-1 flex items-center">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-12 sm:py-20 text-center">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-electric-blue border border-blue-200/60 text-xs font-bold uppercase tracking-wider mb-3">
                <span class="w-1.5 h-1.5 rounded-full bg-field-green"></span>
                Platform Booking Lapangan Olahraga
            </span>
            <h1 class="font-display text-3xl sm:text-5xl md:text-6xl font-extrabold italic text-pitch-black leading-tight mb-4 sm:mb-5">
                Elevate Your Game
            </h1>
            <p class="text-on-surface-variant text-sm sm:text-base md:text-lg max-w-xl mx-auto mb-8 leading-relaxed">
                Futsal, badminton, basket, tenis — booking lapangan olahraga favorit Anda kapan saja, di mana saja dengan mudah dan instan.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('home') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3.5 bg-electric-blue text-white text-sm font-bold rounded-xl hover:bg-blue-700 transition active:scale-95 shadow-lg shadow-blue-500/20">
                    <span class="material-symbols-outlined text-[18px]">search</span>
                    Cari Lapangan Sekarang
                </a>
                <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-white border border-stadium-gray text-pitch-black text-sm font-bold rounded-xl hover:bg-slate-50 transition">
                    Masuk Akun
                </a>
            </div>
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
