<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('code') — TiketinAja</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 antialiased min-h-screen flex items-center justify-center px-4">
    <div class="text-center max-w-md w-full">
        <div class="mb-6">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2">
                <div class="w-9 h-9 bg-indigo-600 rounded-xl flex items-center justify-center">
                    <span class="text-white font-bold text-base">T</span>
                </div>
                <span class="font-bold text-gray-900 text-lg">TiketinAja</span>
            </a>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-10">
            <p class="text-7xl font-black text-indigo-100 mb-1">@yield('code')</p>
            <h1 class="text-xl font-bold text-gray-900 mb-2">@yield('title')</h1>
            <p class="text-gray-500 text-sm mb-8">@yield('message')</p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ url()->previous('javascript:history.back()') !== url()->current() ? url()->previous() : url('/') }}"
                   onclick="if(document.referrer) { history.back(); return false; }"
                   class="w-full sm:w-auto px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition">
                    ← Kembali
                </a>
                <a href="{{ url('/') }}"
                   class="w-full sm:w-auto px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition">
                    Ke Beranda
                </a>
            </div>
        </div>

        <p class="mt-6 text-xs text-gray-400">&copy; {{ date('Y') }} TiketinAja</p>
    </div>
</body>
</html>
