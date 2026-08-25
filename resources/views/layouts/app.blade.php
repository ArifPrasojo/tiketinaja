<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($pageTitle) ? $pageTitle . ' — ' : '' }}{{ config('app.name', 'TiketinAja') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 antialiased">

<div class="min-h-screen flex flex-col">
    {{-- Top navigation bar --}}
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-40" x-data="{ mobileOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">

                {{-- Logo + primary nav --}}
                <div class="flex items-center gap-8">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-sm">T</span>
                        </div>
                        <span class="font-bold text-gray-900 text-lg">TiketinAja</span>
                    </a>

                    <div class="hidden md:flex items-center gap-1">
                        <a href="{{ route('dashboard') }}"
                           class="px-3 py-2 rounded-lg text-sm font-medium transition
                                  {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                            Dashboard
                        </a>

                        @if(auth()->user()->isCustomer())
                            <a href="{{ route('venues.index') }}"
                               class="px-3 py-2 rounded-lg text-sm font-medium transition
                                      {{ request()->routeIs('venues.*','courts.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                                Cari Venue
                            </a>
                            <a href="{{ route('bookings.index') }}"
                               class="px-3 py-2 rounded-lg text-sm font-medium transition
                                      {{ request()->routeIs('bookings.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                                Booking Saya
                            </a>
                        @endif

                        @if(auth()->user()->isManager() || auth()->user()->isAdmin())
                            <a href="{{ route('manage.venues.index') }}"
                               class="px-3 py-2 rounded-lg text-sm font-medium transition
                                      {{ request()->routeIs('manage.venues.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                                Venue
                            </a>
                            <a href="{{ route('manage.bookings.index') }}"
                               class="px-3 py-2 rounded-lg text-sm font-medium transition
                                      {{ request()->routeIs('manage.bookings.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                                Transaksi
                            </a>
                            <a href="{{ route('manage.promos.index') }}"
                               class="px-3 py-2 rounded-lg text-sm font-medium transition
                                      {{ request()->routeIs('manage.promos.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                                Promo
                            </a>
                            <a href="{{ route('manage.checkin.index') }}"
                               class="px-3 py-2 rounded-lg text-sm font-medium transition
                                      {{ request()->routeIs('manage.checkin.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                                Check-in
                            </a>
                        @endif

                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.users.index') }}"
                               class="px-3 py-2 rounded-lg text-sm font-medium transition
                                      {{ request()->routeIs('admin.users.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                                Users
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Right side: notif + profile --}}
                <div class="flex items-center gap-2">
                    {{-- Notification bell --}}
                    @php $unread = auth()->user()->unreadNotifications()->count(); @endphp
                    <a href="{{ route('notifications.index') }}"
                       class="relative p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @if($unread > 0)
                            <span class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-bold leading-none">
                                {{ $unread > 9 ? '9+' : $unread }}
                            </span>
                        @endif
                    </a>

                    {{-- Profile dropdown --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" @click.outside="open = false"
                                class="flex items-center gap-2 pl-2 pr-3 py-1.5 rounded-lg hover:bg-gray-100 transition">
                            <div class="w-7 h-7 rounded-full bg-indigo-100 flex items-center justify-center">
                                <span class="text-xs font-semibold text-indigo-700">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </span>
                            </div>
                            <span class="hidden sm:block text-sm font-medium text-gray-700 max-w-[120px] truncate">
                                {{ auth()->user()->name }}
                            </span>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                             class="absolute right-0 mt-1 w-52 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">
                            <div class="px-4 py-2 border-b border-gray-50">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                                <span class="inline-block mt-1 px-2 py-0.5 text-xs font-medium rounded-full
                                    {{ auth()->user()->isAdmin() ? 'bg-purple-100 text-purple-700' : (auth()->user()->isManager() ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700') }}">
                                    {{ ucfirst(auth()->user()->role) }}
                                </span>
                            </div>
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Profil Saya
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Mobile hamburger --}}
                    <button @click="mobileOpen = !mobileOpen"
                            class="md:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            <path x-show="mobileOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div x-show="mobileOpen" x-cloak class="md:hidden border-t border-gray-100 bg-white px-4 py-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-600' }}">Dashboard</a>
            @if(auth()->user()->isCustomer())
                <a href="{{ route('venues.index') }}" class="block px-3 py-2 rounded-lg text-sm text-gray-600">Cari Venue</a>
                <a href="{{ route('bookings.index') }}" class="block px-3 py-2 rounded-lg text-sm text-gray-600">Booking Saya</a>
            @endif
            @if(!auth()->user()->isCustomer())
                <a href="{{ route('manage.venues.index') }}" class="block px-3 py-2 rounded-lg text-sm text-gray-600">Venue</a>
                <a href="{{ route('manage.bookings.index') }}" class="block px-3 py-2 rounded-lg text-sm text-gray-600">Transaksi</a>
                <a href="{{ route('manage.promos.index') }}" class="block px-3 py-2 rounded-lg text-sm text-gray-600">Promo</a>
                <a href="{{ route('manage.checkin.index') }}" class="block px-3 py-2 rounded-lg text-sm text-gray-600">Check-in</a>
            @endif
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.users.index') }}" class="block px-3 py-2 rounded-lg text-sm text-gray-600">Users</a>
            @endif
        </div>
    </nav>

    {{-- Flash messages --}}
    @if(session('success') || session('error') || $errors->any())
        <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 pt-4">
            @if(session('success'))
                <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm">
                    <svg class="w-5 h-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm">
                    <svg class="w-5 h-5 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @endif

    {{-- Page heading --}}
    @isset($header)
        <div class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
                {{ $header }}
            </div>
        </div>
    @endisset

    {{-- Main content --}}
    <main class="flex-1">
        {{ $slot }}
    </main>

    <footer class="bg-white border-t border-gray-100 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 text-center text-xs text-gray-400">
            &copy; {{ date('Y') }} TiketinAja — Platform Booking Lapangan Olahraga
        </div>
    </footer>
</div>

</body>
</html>
