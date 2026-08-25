<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle ?? config('app.name', 'TiketinAja') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="antialiased bg-white text-gray-900">

<div class="min-h-screen flex">

    {{-- ── Left panel: branding + visual ─────────────────── --}}
    <div class="hidden lg:flex lg:w-[52%] xl:w-[55%] relative flex-col bg-gray-950 overflow-hidden">

        {{-- Background pattern --}}
        <div class="absolute inset-0 overflow-hidden">
            {{-- Large circle top-right --}}
            <div class="absolute -top-32 -right-32 w-[480px] h-[480px] rounded-full"
                 style="background: radial-gradient(circle, rgba(99,102,241,0.25) 0%, transparent 70%)"></div>
            {{-- Small circle bottom-left --}}
            <div class="absolute -bottom-20 -left-20 w-72 h-72 rounded-full"
                 style="background: radial-gradient(circle, rgba(139,92,246,0.2) 0%, transparent 70%)"></div>
            {{-- Grid dots --}}
            <svg class="absolute inset-0 w-full h-full opacity-[0.04]" xmlns="http://www.w3.org/2000/svg">
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
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 w-fit">
                <div class="w-9 h-9 bg-indigo-500 rounded-xl flex items-center justify-center">
                    <span class="text-white font-bold text-base tracking-tight">T</span>
                </div>
                <span class="text-white font-bold text-lg tracking-tight">TiketinAja</span>
            </a>

            {{-- Main copy --}}
            <div class="mt-auto mb-16">
                <p class="text-indigo-400 text-sm font-medium mb-3 tracking-wide uppercase">Platform Booking Lapangan</p>
                <h2 class="text-4xl xl:text-5xl font-bold text-white leading-[1.15] mb-5">
                    Pesan lapangan<br>
                    <span class="text-indigo-400">kapan saja,</span><br>
                    di mana saja.
                </h2>
                <p class="text-gray-400 text-base leading-relaxed max-w-sm">
                    Futsal, badminton, basket, tenis — satu platform untuk semua kebutuhan booking lapangan olahraga Anda.
                </p>

                {{-- Feature list --}}
                <ul class="mt-8 space-y-3">
                    @foreach([
                        'Cek ketersediaan slot real-time',
                        'Pembayaran aman & terkonfirmasi instan',
                        'QR check-in tanpa antre di kasir',
                    ] as $feat)
                    <li class="flex items-center gap-3 text-sm text-gray-300">
                        <span class="w-5 h-5 rounded-full bg-indigo-500/20 border border-indigo-500/40 flex items-center justify-center shrink-0">
                            <svg class="w-3 h-3 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </span>
                        {{ $feat }}
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- Bottom social proof --}}
            <div class="flex items-center gap-4 pb-2">
                <div class="flex -space-x-2">
                    @foreach(['bg-indigo-400','bg-violet-400','bg-sky-400','bg-pink-400'] as $c)
                        <div class="w-8 h-8 rounded-full {{ $c }} border-2 border-gray-950 flex items-center justify-center">
                            <span class="text-white text-xs font-bold">{{ chr(random_int(65,90)) }}</span>
                        </div>
                    @endforeach
                </div>
                <p class="text-sm text-gray-400">
                    <span class="text-white font-semibold">2.000+</span> pengguna aktif
                </p>
            </div>
        </div>
    </div>

    {{-- ── Right panel: form ───────────────────────────────── --}}
    <div class="flex-1 flex flex-col min-h-screen">

        {{-- Mobile header (only visible on small screens) --}}
        <div class="lg:hidden flex items-center justify-between px-6 py-5 border-b border-gray-100">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-sm">T</span>
                </div>
                <span class="font-bold text-gray-900">TiketinAja</span>
            </a>
            <a href="{{ route('home') }}" class="text-sm text-gray-500 hover:text-gray-700">← Beranda</a>
        </div>

        {{-- Form area --}}
        <div class="flex-1 flex items-center justify-center px-6 py-10 sm:px-12">
            <div class="w-full max-w-[420px]">
                {{ $slot }}
            </div>
        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 text-center">
            <p class="text-xs text-gray-400">&copy; {{ date('Y') }} TiketinAja. All rights reserved.</p>
        </div>
    </div>

</div>

</body>
</html>
