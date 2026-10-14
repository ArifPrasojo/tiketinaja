<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle ?? config('app.name', 'TiketinAja') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="antialiased bg-white text-pitch-black selection:bg-electric-blue selection:text-white">

<div class="min-h-screen flex">

    {{-- ── Left panel: branding + visual ─────────────────── --}}
    <div class="hidden lg:flex lg:w-[52%] xl:w-[55%] relative flex-col bg-surface-light overflow-hidden">

        {{-- Background pattern --}}
        <div class="absolute inset-0 overflow-hidden">
            {{-- Large circle top-right --}}
            <div class="absolute -top-32 -right-32 w-[480px] h-[480px] rounded-full"
                 style="background: radial-gradient(circle, rgba(0,82,255,0.10) 0%, transparent 70%)"></div>
            {{-- Small circle bottom-left --}}
            <div class="absolute -bottom-20 -left-20 w-72 h-72 rounded-full"
                 style="background: radial-gradient(circle, rgba(0,82,255,0.07) 0%, transparent 70%)"></div>
            {{-- Grid dots --}}
            <svg class="absolute inset-0 w-full h-full opacity-[0.05]" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="dots" x="0" y="0" width="28" height="28" patternUnits="userSpaceOnUse">
                        <circle cx="1.5" cy="1.5" r="1.5" fill="white"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#dots)"/>
            </svg>
        </div>

        {{-- Content --}}
        <div class="relative flex flex-col h-full px-12 py-10">

            {{-- Logo --}}
            <x-logo-wordmark :href="route('home')" :size="40"/>

            {{-- Main copy --}}
            <div class="mt-auto mb-16">
                <p class="text-electric-blue text-sm font-medium mb-3 tracking-wide uppercase">Platform Booking Lapangan</p>
                <h2 class="text-4xl xl:text-5xl font-bold text-pitch-black leading-[1.15] mb-5">
                    Pesan lapangan<br>
                    <span class="text-electric-blue">kapan saja,</span><br>
                    di mana saja.
                </h2>
                <p class="text-on-surface-variant text-base leading-relaxed max-w-sm">
                    Futsal, badminton, basket, tenis — satu platform untuk semua kebutuhan booking lapangan olahraga Anda.
                </p>

                {{-- Feature list --}}
                <ul class="mt-8 space-y-3">
                    @foreach([
                        'Cek ketersediaan slot real-time',
                        'Pembayaran aman & terkonfirmasi instan',
                        'QR check-in tanpa antre di kasir',
                    ] as $feat)
                    <li class="flex items-center gap-3 text-sm text-on-surface-variant">
                        <span class="w-5 h-5 rounded-full bg-blue-50 border border-electric-blue/40 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[12px] text-electric-blue">check</span>
                        </span>
                        {{ $feat }}
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    {{-- ── Right panel: form ───────────────────────────────── --}}
    <div class="flex-1 flex flex-col min-h-screen bg-white">

        {{-- Mobile header (only visible on small screens) --}}
        <div class="lg:hidden flex items-center justify-between px-4 sm:px-6 py-4 border-b border-stadium-gray bg-white/95 sticky top-0 z-40 backdrop-blur-md">
            <x-logo-wordmark :href="route('home')" :size="30"/>
            <a href="{{ route('home') }}" class="text-xs sm:text-sm font-bold text-on-surface-variant hover:text-pitch-black flex items-center gap-0.5">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                Beranda
            </a>
        </div>

        {{-- Form area --}}
        <div class="flex-1 flex items-center justify-center px-4 py-8 sm:px-10 sm:py-12">
            <div class="w-full max-w-[420px]">
                {{ $slot }}
            </div>
        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 text-center">
            <p class="text-xs text-on-surface-variant">&copy; {{ date('Y') }} TiketinAja. All rights reserved.</p>
        </div>
    </div>

</div>

</body>
</html>