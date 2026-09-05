<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('code') — TiketinAja</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-white antialiased min-h-screen flex items-center justify-center px-4">
    <div class="text-center max-w-md w-full">
        <div class="mb-6">
            <x-logo-wordmark :href="url('/')" :size="36"/>
        </div>

        <div class="bg-white border border-stadium-gray rounded-xl sporty-shadow p-10">
            <p class="text-7xl font-black text-electric-blue/20 mb-1">@yield('code')</p>
            <h1 class="text-xl font-bold text-pitch-black mb-2">@yield('title')</h1>
            <p class="text-on-surface-variant text-sm mb-8">@yield('message')</p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ url()->previous('javascript:history.back()') !== url()->current() ? url()->previous() : url('/') }}"
                   onclick="if(document.referrer) { history.back(); return false; }"
                   class="w-full sm:w-auto px-5 py-2.5 text-sm font-medium bg-slate-100 text-pitch-black hover:bg-slate-200 rounded-xl transition">
                    ← Kembali
                </a>
                <a href="{{ url('/') }}"
                   class="w-full sm:w-auto px-5 py-2.5 text-sm font-medium text-white bg-electric-blue hover:bg-blue-700 rounded-xl transition">
                    Ke Beranda
                </a>
            </div>
        </div>

        <p class="mt-6 text-xs text-on-surface-variant">&copy; {{ date('Y') }} TiketinAja</p>
    </div>
</body>
</html>